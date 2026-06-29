import { APIRequestContext, expect, Page, test } from '@playwright/test';
import { apiLogin, ensureNoOpenRegister, openRegister, seedSession } from './support/api';
import { E2E_PRODUCTS } from './support/e2e-data';

/**
 * E2E del flujo de POS / FACTURACIÓN (UI bajo prueba: /invoices/new, /invoices/:id).
 *
 *  1. Con caja abierta, buscar productos E2E y agregarlos al carrito.
 *  2. Verificar el preview de ISV (15%) y total contra la fórmula del backend.
 *  3. Emitir la factura → redirige al detalle; aparece en la lista.
 *  4. Descargar el PDF (download event) y verificar que el binario es un PDF.
 *  5. Anular la factura (Administrador) → estado "Anulada".
 *
 * Diseño:
 *  - La caja se abre por API en beforeAll (no es el flujo bajo prueba aquí; caja.spec ya lo cubre)
 *    y se cierra en afterAll, respetando la restricción de UNA caja abierta a la vez.
 *  - Se usan SOLO los productos E2E-* para no tocar el stock del seed real.
 *  - El total es bruto (precio con ISV incluido). Para 2×E2E-001 (50) = 100:
 *      total = 100.00 · subtotal = 100/1.15 = 86.96 · ISV = 13.04.
 */

let ctx: APIRequestContext;
let token: string;

const P1 = E2E_PRODUCTS[0]; // E2E-001, precio 50
const QTY = 2; // 2 × 50 = 100 bruto

// Totales esperados (misma fórmula que pos.ts / BillingService).
const TOTAL = (P1.price * QTY).toFixed(2); // 100.00
const SUBTOTAL = ((P1.price * QTY) / 1.15).toFixed(2); // 86.96
const TAX = (P1.price * QTY - Number(SUBTOTAL)).toFixed(2); // 13.04

test.beforeAll(async () => {
  ({ ctx, token } = await apiLogin());
  await openRegister(ctx, 1000); // prerequisito de estado: caja abierta (por API)
});

test.afterAll(async () => {
  await ensureNoOpenRegister(ctx);
  await ctx.dispose();
});

/** Agrega un producto E2E al carrito buscándolo por SKU en el buscador del POS. */
async function addToCart(page: Page, sku: string, name: string): Promise<void> {
  await page.fill('input[aria-label="Buscar producto"]', sku);
  const result = page.locator('.results__item', { hasText: name });
  await expect(result).toBeVisible(); // espera al debounce + respuesta
  await result.click();
}

test('emitir factura con productos E2E, verificar ISV/total, descargar PDF y anular', async ({
  page,
}) => {
  await seedSession(page, token);
  await page.goto('/invoices/new');
  await expect(page.locator('h1')).toHaveText('Nueva venta');

  // --- 1) Agregar 2 unidades de E2E-001 al carrito ---
  await addToCart(page, P1.sku, P1.name);
  await page.locator('.qty button[aria-label="Sumar"]').click(); // 1 → 2
  await expect(page.locator('.cart .qty .num')).toHaveText(String(QTY));

  // --- 2) Preview de totales (ISV 15% + total) ---
  await expect(page.locator('.totals__grand dd')).toContainText(TOTAL);
  await expect(page.locator('.totals dd').filter({ hasText: TAX })).toBeVisible();
  await expect(page.locator('.totals dd').filter({ hasText: SUBTOTAL })).toBeVisible();

  // --- 3) Emitir factura → redirige al detalle ---
  await page.fill('#customer', 'Cliente E2E');
  await page.getByRole('button', { name: 'Emitir factura' }).click();
  await expect(page).toHaveURL(/\/invoices\/\d+$/);
  await expect(page.locator('.badge-success')).toHaveText('Emitida');
  const invoiceNumber = (await page.locator('.title-row').innerText()).match(/[\w-]+$/)?.[0] ?? '';
  // El total del detalle coincide con el preview del POS.
  await expect(page.locator('.totals__grand dd')).toContainText(TOTAL);

  // Aparece en la lista de facturas.
  await page.goto('/invoices');
  await expect(page.locator('.table tbody', { hasText: invoiceNumber })).toBeVisible();

  // --- 4) Descargar el PDF y verificar que es application/pdf ---
  await page.goto(/\/invoices\/\d+$/.test(page.url()) ? page.url() : '/invoices');
  // Volver al detalle de la factura recién creada.
  await page.locator('.table tbody tr', { hasText: invoiceNumber }).first().click();
  await expect(page).toHaveURL(/\/invoices\/\d+$/);

  const [download] = await Promise.all([
    page.waitForEvent('download'),
    page.getByRole('button', { name: 'Descargar PDF' }).click(),
  ]);
  expect(download.suggestedFilename()).toContain('.pdf');
  const stream = await download.createReadStream();
  const chunks: Buffer[] = [];
  for await (const chunk of stream) chunks.push(chunk as Buffer);
  const head = Buffer.concat(chunks).subarray(0, 5).toString('latin1');
  expect(head).toBe('%PDF-'); // firma de un PDF válido

  // --- 5) Anular la factura (rol Administrador) ---
  await page.getByRole('button', { name: 'Anular', exact: true }).click();
  await expect(page.locator('.dialog[role="dialog"]')).toBeVisible();
  await page.fill('.dialog textarea', 'Anulación de prueba E2E.');
  await page.getByRole('button', { name: 'Anular factura' }).click();

  await expect(page.locator('.dialog[role="dialog"]')).toHaveCount(0);
  await expect(page.locator('.badge-danger')).toHaveText('Anulada');
  // El botón de anular desaparece (canVoid() = false tras anular).
  await expect(page.getByRole('button', { name: 'Anular', exact: true })).toHaveCount(0);
});
