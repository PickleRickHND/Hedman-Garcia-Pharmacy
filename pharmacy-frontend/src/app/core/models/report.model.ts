/** GET /reports/sales — ventas por período (totales + por método de pago). */
export interface SalesReport {
  from: string;
  to: string;
  total_invoices: number;
  total_revenue: number;
  total_discount: number;
  total_tax: number;
  daily_average: number;
  by_payment_method: { method: string; count: number; total: number }[];
}

/** GET /reports/products — fila de top productos. */
export interface TopProduct {
  product_id: number;
  product_name: string;
  product_sku: string;
  total_quantity: number;
  total_revenue: number;
}

/** GET /reports/inventory — snapshot del inventario (solo agregados; el detalle
 *  por producto vive en el módulo de Productos, paginado). */
export interface InventoryReport {
  total_products: number;
  total_units: number;
  total_value: number;
  low_stock: number;
  out_of_stock: number;
  expired: number;
  expiring_soon: number;
}
