import { execFileSync } from 'node:child_process';
import { resolve } from 'node:path';
import { DEMO_USER } from './support/demo-user';
import { E2E_SKU_PREFIX } from './support/e2e-data';

/**
 * globalTeardown de Playwright: hard-delete de TODO lo que la corrida E2E creó, dejando la
 * DB `pharmacy` como estaba. Idempotente. El orden respeta las FKs (restrictOnDelete en
 * stock_movements, returns, return_items, cash_registers, invoices):
 *
 *   1. return_items + returns          (de facturas con productos E2E)
 *   2. stock_movements                 (que referencian productos E2E)
 *   3. invoice_items + invoices        (con líneas de productos E2E; items en cascade)
 *   4. cash_registers                  (del usuario demo)
 *   5. productos E2E-*                 (forceDelete)
 *   6. usuario demo + tokens + roles
 *
 * Al final verifica que no queden facturas/movimientos huérfanos de los productos E2E y lo
 * reporta por consola. Un fallo de limpieza NO revienta la corrida (se reporta para revisión).
 */
const BACKEND_DIR = resolve(__dirname, '..', '..', 'pharmacy-app');

function tinker(php: string): string {
  return execFileSync('php', ['artisan', 'tinker', '--execute', php], {
    cwd: BACKEND_DIR,
    encoding: 'utf-8',
    stdio: ['ignore', 'pipe', 'pipe'],
  });
}

export default async function globalTeardown(): Promise<void> {
  const php = [
    // ----- ids ancla -----
    `$pids = App\\Models\\Product::withTrashed()->where('sku', 'like', '${E2E_SKU_PREFIX}%')->pluck('id');`,
    `$demo = App\\Models\\User::where('email', '${DEMO_USER.email}')->first();`,

    // Facturas que tocan productos E2E (vía invoice_items.product_id).
    `$invIds = DB::table('invoice_items')->whereIn('product_id', $pids)->pluck('invoice_id')->unique();`,

    // 1) return_items + returns de esas facturas.
    `$retIds = DB::table('returns')->whereIn('invoice_id', $invIds)->pluck('id');`,
    `DB::table('return_items')->whereIn('return_id', $retIds)->delete();`,
    `DB::table('returns')->whereIn('id', $retIds)->delete();`,

    // 2) stock_movements que referencian productos E2E (sale/void/return/etc.).
    `DB::table('stock_movements')->whereIn('product_id', $pids)->delete();`,

    // 3) invoice_items + invoices (los items caen en cascade al borrar la factura, pero
    //    borramos explícito por claridad y para cubrir cualquier item residual).
    `DB::table('invoice_items')->whereIn('invoice_id', $invIds)->delete();`,
    `DB::table('invoices')->whereIn('id', $invIds)->delete();`,

    // 4) cajas del usuario demo (abiertas o cerradas).
    `if ($demo) { DB::table('cash_registers')->where('user_id', $demo->id)->delete(); }`,

    // 5) productos E2E (hard delete, incluso soft-deleted).
    `App\\Models\\Product::withTrashed()->whereIn('id', $pids)->forceDelete();`,

    // 6) usuario demo + tokens + roles.
    `if ($demo) { $demo->tokens()->delete(); $demo->roles()->detach(); $demo->forceDelete(); }`,

    // ----- verificación de huérfanos -----
    `$orphInv = DB::table('invoice_items')->whereIn('product_id', $pids)->count();`,
    `$orphMov = DB::table('stock_movements')->whereIn('product_id', $pids)->count();`,
    `$remProd = App\\Models\\Product::withTrashed()->whereIn('id', $pids)->count();`,
    `echo 'E2E_TEARDOWN orphan_invoice_items=' . $orphInv . ' orphan_movements=' . $orphMov . ' products_left=' . $remProd . ' user=' . ($demo ? 'deleted' : 'absent');`,
  ].join(' ');

  try {
    const out = tinker(php).trim();
    console.log(`[e2e] Teardown: ${out}`);
    const m = out.match(/orphan_invoice_items=(\d+) orphan_movements=(\d+) products_left=(\d+)/);
    if (m && (m[1] !== '0' || m[2] !== '0' || m[3] !== '0')) {
      console.warn(`[e2e] ⚠ Quedaron residuos E2E (revisar manualmente): ${out}`);
    } else {
      console.log('[e2e] ✔ DB limpia: sin facturas/movimientos/productos E2E residuales.');
    }
  } catch (err) {
    console.warn(`[e2e] Teardown falló (revisar manualmente): ${String(err)}`);
  }
}
