import { APIRequestContext, expect, test } from '@playwright/test';
import {
  apiLogin,
  cashPaymentMethodId,
  createInvoice,
  ensureNoOpenRegister,
  openRegister,
  productIdBySku,
  seedSession,
} from './support/api';
import { E2E_PRODUCTS } from './support/e2e-data';

/**
 * E2E del flujo de DEVOLUCIONES (UI bajo prueba: /returns/new → /returns/:id).
 *
 * Sobre una factura emitida con producto E2E (creada por API como prerequisito), crear una
 * devolución PARCIAL con restock=true y verificar que quedó registrada.
 *
 * Diseño:
 *  - El prerequisito (caja + factura) se monta por API; la UI se reserva para la acción bajo
 *    prueba (buscar factura, fijar cantidad parcial, procesar).
 *  - Crear devolución es exclusivo del rol Administrador → el usuario demo lo es.
 *  - Producto E2E-002 (precio 75) con cantidad comprada 4 y devolución parcial de 1.
 */

let ctx: APIRequestContext;
let token: string;
let invoiceNumber: string;

const P2 = E2E_PRODUCTS[1]; // E2E-002, precio 75
const BOUGHT = 4;
const RETURN_QTY = 1;
const UNIT = P2.price.toFixed(2); // 75.00

test.beforeAll(async () => {
  ({ ctx, token } = await apiLogin());
  await openRegister(ctx, 1000);

  const productId = await productIdBySku(ctx, P2.sku);
  const pmId = await cashPaymentMethodId(ctx);
  const invoice = await createInvoice(ctx, {
    customer_name: 'Cliente Devolución E2E',
    payment_method_id: pmId,
    items: [{ product_id: productId, quantity: BOUGHT }],
  });
  invoiceNumber = invoice.invoice_number;
});

test.afterAll(async () => {
  await ensureNoOpenRegister(ctx);
  await ctx.dispose();
});

test('crear devolución parcial (restock=true) sobre una factura emitida y verla registrada', async ({
  page,
}) => {
  await seedSession(page, token);
  await page.goto('/returns/new');
  await expect(page.locator('h1')).toHaveText('Nueva devolución');

  // --- Paso 1: buscar la factura por su número y seleccionarla ---
  await page.fill('input[aria-label="Buscar factura"]', invoiceNumber);
  await page.getByRole('button', { name: 'Buscar' }).click();
  const result = page.locator('.results__item', { hasText: invoiceNumber });
  await expect(result).toBeVisible();
  await result.click();

  // --- Paso 2: la factura quedó seleccionada y se listan sus líneas ---
  await expect(page.locator('.sel-invoice')).toContainText(invoiceNumber);
  const row = page.locator('.table tbody tr', { hasText: P2.name });
  await expect(row).toBeVisible();
  await expect(row).toContainText(String(BOUGHT)); // comprado

  // Devolución parcial: 1 de 4. (restock viene marcado por defecto).
  await row.locator('input[aria-label="Cantidad a devolver"]').fill(String(RETURN_QTY));
  await expect(row.locator('input[aria-label="Reingresar stock"]')).toBeChecked();

  // Reembolso estimado = 1 × 75.00.
  await expect(page.locator('.refund__value')).toContainText(UNIT);

  // --- Motivo + procesar ---
  await page.fill('#reason', 'Producto en mal estado (prueba E2E).');
  await page.getByRole('button', { name: 'Procesar devolución' }).click();

  // --- Verificación: redirige al detalle de la devolución y muestra los datos ---
  await expect(page).toHaveURL(/\/returns\/\d+$/);
  await expect(page.locator('h1')).toContainText('Devolución');
  // Factura origen correcta + reembolso total + reingreso de stock.
  await expect(page.locator('.meta-grid')).toContainText(invoiceNumber);
  await expect(page.locator('.meta-grid')).toContainText(UNIT);
  await expect(page.locator('.table tbody tr', { hasText: String(RETURN_QTY) })).toBeVisible();
  await expect(page.locator('.badge-success')).toHaveText('Sí'); // reingresó stock
});
