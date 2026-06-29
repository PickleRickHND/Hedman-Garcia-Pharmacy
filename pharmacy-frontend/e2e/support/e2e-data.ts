/**
 * Datos dedicados para los flujos operacionales E2E (POS / caja / devoluciones).
 *
 * Se crean PRODUCTOS con SKU prefijo "E2E-" en globalSetup y se borran (junto con todo
 * lo que la corrida genere: facturas, items, movimientos de stock, devoluciones y cajas
 * del usuario demo) en globalTeardown. NUNCA se tocan los productos del seed real, para
 * no corromper su stock.
 */

/** Prefijo de SKU que marca TODO lo creado por la corrida E2E. El teardown se ancla a él. */
export const E2E_SKU_PREFIX = 'E2E-';

export interface E2eProductSeed {
  sku: string;
  name: string;
  price: number; // HNL, precio bruto con ISV incluido (igual que el seed real)
  stock: number;
}

/**
 * Productos dedicados. Stock amplio para que múltiples specs facturen sin agotarlos.
 * Precios "redondos" para que las aserciones de totales/ISV sean fáciles de razonar.
 */
export const E2E_PRODUCTS: readonly E2eProductSeed[] = [
  { sku: 'E2E-001', name: 'E2E Paracetamol 500mg', price: 50.0, stock: 100 },
  { sku: 'E2E-002', name: 'E2E Ibuprofeno 400mg', price: 75.0, stock: 100 },
] as const;
