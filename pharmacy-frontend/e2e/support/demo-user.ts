/**
 * Datos del usuario demo determinístico para E2E.
 * Se crea en globalSetup y se borra en globalTeardown (junto con sus tokens Sanctum).
 * Rol: Administrador. Nunca se usa admin@pharmacy.hn (password desconocida).
 */
export const DEMO_USER = {
  name: 'E2E Login',
  email: 'e2e.login@pharmacy.hn',
  password: 'E2ePass123!',
  role: 'Administrador',
} as const;
