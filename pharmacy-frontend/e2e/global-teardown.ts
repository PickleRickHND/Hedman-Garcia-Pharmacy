import { execFileSync } from 'node:child_process';
import { resolve } from 'node:path';
import { DEMO_USER } from './support/demo-user';

/**
 * globalTeardown de Playwright: elimina el Administrador demo y sus tokens Sanctum
 * (personal_access_tokens) de la DB `pharmacy`. Idempotente: no falla si ya no existe.
 */
const BACKEND_DIR = resolve(__dirname, '..', '..', 'pharmacy-app');

export default async function globalTeardown(): Promise<void> {
  const php = [
    `$u = App\\Models\\User::where('email', '${DEMO_USER.email}')->first();`,
    `if ($u) { $u->tokens()->delete(); $u->roles()->detach(); $u->forceDelete(); echo 'E2E_USER_DELETED'; }`,
    `else { echo 'E2E_USER_ABSENT'; }`,
  ].join(' ');

  try {
    const out = execFileSync('php', ['artisan', 'tinker', '--execute', php], {
      cwd: BACKEND_DIR,
      encoding: 'utf-8',
      stdio: ['ignore', 'pipe', 'pipe'],
    });
    console.log(`[e2e] Teardown usuario demo: ${out.trim()}`);
  } catch (err) {
    // No reventar la corrida por un fallo de limpieza; reportar para revisión manual.
    console.warn(`[e2e] Teardown del usuario demo falló (revisar manualmente): ${String(err)}`);
  }
}
