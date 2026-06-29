import { defineConfig, devices } from '@playwright/test';
import { resolve } from 'node:path';

/**
 * Configuración Playwright E2E para el frontend Angular 20.
 *
 * Levanta DOS servidores (webServer array):
 *  1. Backend Laravel en :8001  (cwd pharmacy-app)  — el 8000 lo ocupa petlab, NUNCA tocarlo.
 *  2. Angular dev-server en :4200 con la configuración `e2e` (apunta la API a :8001).
 *
 * globalSetup crea un Administrador demo determinístico; globalTeardown lo borra.
 */

const BACKEND_DIR = resolve(__dirname, '..', 'pharmacy-app');
const isCI = !!process.env['CI'];

export default defineConfig({
  testDir: './e2e',
  fullyParallel: false,
  forbidOnly: isCI,
  retries: isCI ? 1 : 0,
  workers: 1,
  reporter: isCI ? [['github'], ['list']] : 'list',

  globalSetup: './e2e/global-setup.ts',
  globalTeardown: './e2e/global-teardown.ts',

  use: {
    baseURL: 'http://localhost:4200',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    headless: true,
  },

  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],

  webServer: [
    {
      // Backend Laravel en 8001. cwd = pharmacy-app.
      command: 'php artisan serve --port=8001',
      cwd: BACKEND_DIR,
      // /up es el health endpoint de Laravel (200). Playwright solo acepta 2xx/3xx
      // como "server arriba"; /api/login devuelve 405 a GET y no sirve para el check.
      url: 'http://localhost:8001/up',
      reuseExistingServer: !isCI,
      timeout: 120_000,
    },
    {
      // Frontend Angular con la configuración e2e (API → :8001).
      command: 'npx ng serve --configuration e2e --port 4200',
      cwd: __dirname,
      url: 'http://localhost:4200',
      reuseExistingServer: !isCI,
      timeout: 180_000,
    },
  ],
});
