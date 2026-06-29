import { execFileSync } from 'node:child_process';
import { resolve } from 'node:path';
import { DEMO_USER } from './support/demo-user';

/**
 * globalSetup de Playwright: crea (idempotente) un Administrador demo determinístico
 * en la DB `pharmacy` vía `php artisan tinker`, ejecutado dentro de `pharmacy-app/`.
 * El password se setea con must_change_password=false para que el login no fuerce cambio.
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
  // Una sola sentencia: firstOrNew + set password/flags + save + syncRoles.
  const php = [
    `$u = App\\Models\\User::firstOrNew(['email' => '${DEMO_USER.email}']);`,
    `$u->name = '${DEMO_USER.name}';`,
    `$u->password = Illuminate\\Support\\Facades\\Hash::make('${DEMO_USER.password}');`,
    `$u->must_change_password = false;`,
    `$u->save();`,
    `$u->syncRoles(['${DEMO_USER.role}']);`,
    `echo 'E2E_USER_READY:' . $u->id;`,
  ].join(' ');

  const out = tinker(php);
  if (!out.includes('E2E_USER_READY:')) {
    throw new Error(`globalSetup: no se pudo crear el usuario demo.\nSalida tinker:\n${out}`);
  }
  console.log(`[e2e] Usuario demo listo: ${DEMO_USER.email}`);
}
