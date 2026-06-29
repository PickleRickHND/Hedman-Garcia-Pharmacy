import { execFileSync } from 'node:child_process';
import { resolve } from 'node:path';
import { DEMO_USER } from './support/demo-user';
import { E2E_PRODUCTS } from './support/e2e-data';

/**
 * globalSetup de Playwright. Prepara, de forma idempotente, todo lo que los specs necesitan
 * en la DB `pharmacy` vía `php artisan tinker` (ejecutado dentro de `pharmacy-app/`):
 *
 *  1. Un Administrador demo determinístico (must_change_password=false → login sin fricción).
 *  2. Productos DEDICADOS con SKU prefijo "E2E-" (stock amplio, precio conocido). Los flujos
 *     de POS/devoluciones usan SOLO estos productos para no corromper el stock del seed real.
 *
 * El globalTeardown borra TODO lo anterior + lo que la corrida genere (facturas, items,
 * movimientos de stock, devoluciones y cajas del usuario demo).
 */
const BACKEND_DIR = resolve(__dirname, '..', '..', 'pharmacy-app');

function tinker(php: string): string {
  return execFileSync('php', ['artisan', 'tinker', '--execute', php], {
    cwd: BACKEND_DIR,
    encoding: 'utf-8',
    stdio: ['ignore', 'pipe', 'pipe'],
  });
}

export default async function globalSetup(): Promise<void> {
  // 1) Usuario demo: firstOrNew + set password/flags + save + syncRoles.
  const userPhp = [
    `$u = App\\Models\\User::firstOrNew(['email' => '${DEMO_USER.email}']);`,
    `$u->name = '${DEMO_USER.name}';`,
    `$u->password = Illuminate\\Support\\Facades\\Hash::make('${DEMO_USER.password}');`,
    `$u->must_change_password = false;`,
    `$u->save();`,
    `$u->syncRoles(['${DEMO_USER.role}']);`,
    `echo 'E2E_USER_READY:' . $u->id;`,
  ].join(' ');

  const userOut = tinker(userPhp);
  if (!userOut.includes('E2E_USER_READY:')) {
    throw new Error(`globalSetup: no se pudo crear el usuario demo.\nSalida tinker:\n${userOut}`);
  }
  console.log(`[e2e] Usuario demo listo: ${DEMO_USER.email}`);

  // 2) Productos E2E dedicados: updateOrCreate por SKU (resetea stock/precio en cada corrida).
  const productPhp = E2E_PRODUCTS.map(
    (p) =>
      `App\\Models\\Product::withTrashed()->updateOrCreate(['sku' => '${p.sku}'], ` +
      `['name' => '${p.name}', 'price' => ${p.price}, 'stock' => ${p.stock}, 'deleted_at' => null]);`,
  ).join(' ');
  const productOut = tinker(`${productPhp} echo 'E2E_PRODUCTS_READY';`);
  if (!productOut.includes('E2E_PRODUCTS_READY')) {
    throw new Error(`globalSetup: no se pudieron crear los productos E2E.\nSalida tinker:\n${productOut}`);
  }
  console.log(`[e2e] Productos E2E listos: ${E2E_PRODUCTS.map((p) => p.sku).join(', ')}`);
}
