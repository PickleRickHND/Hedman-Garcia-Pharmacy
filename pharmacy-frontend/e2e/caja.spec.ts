import { APIRequestContext, expect, Page, test } from '@playwright/test';
import { apiLogin, ensureNoOpenRegister, seedSession } from './support/api';

/**
 * E2E del flujo de CAJA (UI bajo prueba: /cash-registers).
 *
 *  - Abrir caja con un monto de apertura → verificar el badge "Caja abierta" y el monto.
 *  - Cerrar con arqueo (monto real contado) → verificar que el cierre aparece en el historial
 *    con la diferencia esperada (apertura sin ventas ⇒ esperado = apertura; real − esperado = diff).
 *
 * Diseño:
 *  - El estado de caja es GLOBAL (el backend solo permite UNA caja abierta a la vez, de
 *    cualquier usuario). Por eso `ensureNoOpenRegister` (vía API) deja el estado limpio antes
 *    de abrir desde la UI, haciendo el spec auto-sanable e independiente del orden de ejecución.
 *  - La caja se abre y se cierra DENTRO del mismo test por la UI (ese es el flujo bajo prueba),
 *    así no queda ninguna caja abierta que colisione con pos.spec / devoluciones.spec.
 */

let ctx: APIRequestContext;

const OPENING = 1500;
const ACTUAL = 1450; // arqueo con faltante: diferencia esperada = 1450 − 1500 = −50

test.beforeAll(async () => {
  ({ ctx } = await apiLogin());
});

test.afterAll(async () => {
  // Red de seguridad: si el test abortó dejando caja abierta, ciérrala por API.
  await ensureNoOpenRegister(ctx);
  await ctx.dispose();
});

async function gotoCaja(page: Page): Promise<void> {
  const { token } = await apiLogin();
  await seedSession(page, token);
  await ensureNoOpenRegister(ctx); // partir SIEMPRE sin caja abierta
  await page.goto('/cash-registers');
  await expect(page.locator('h1')).toHaveText('Caja');
}

test('abrir caja, verificar estado abierta, cerrar con arqueo y ver la diferencia', async ({
  page,
}) => {
  await gotoCaja(page);

  // --- Estado inicial: no hay caja abierta → se muestra el formulario de apertura ---
  await expect(page.locator('.cash-closed')).toBeVisible();
  await expect(page.locator('#opening')).toBeVisible();

  // --- Abrir caja con el monto de apertura ---
  await page.fill('#opening', String(OPENING));
  await page.getByRole('button', { name: 'Abrir caja' }).click();

  // La UI repinta a "Caja abierta" con el monto de apertura formateado (HNL).
  await expect(page.locator('.badge-success')).toHaveText('Caja abierta');
  await expect(page.locator('.cash-open__body .stat__value')).toContainText('1,500.00');

  // --- Cerrar caja con arqueo ---
  await page.getByRole('button', { name: 'Cerrar caja' }).click();
  await expect(page.locator('.dialog[role="dialog"]')).toBeVisible();
  await page.fill('#actual', String(ACTUAL));
  await page.getByRole('button', { name: 'Confirmar cierre' }).click();

  // El modal se cierra y volvemos al estado "sin caja abierta".
  await expect(page.locator('.dialog[role="dialog"]')).toHaveCount(0);
  await expect(page.locator('.cash-closed')).toBeVisible();

  // El cierre aparece en el historial. Sin ventas: esperado = apertura (1,500.00),
  // real = 1,450.00, diferencia = −50.00 (negativa, clase diff-neg).
  const firstRow = page.locator('.table-card tbody tr').first();
  await expect(firstRow).toBeVisible();
  await expect(firstRow).toContainText('1,500.00'); // esperado
  await expect(firstRow).toContainText('1,450.00'); // real
  const diffCell = firstRow.locator('td.diff-neg');
  await expect(diffCell).toBeVisible();
  await expect(diffCell).toContainText('50.00'); // diferencia (faltante)
});
