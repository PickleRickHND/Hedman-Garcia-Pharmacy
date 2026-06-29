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

/** Producto dentro del snapshot de inventario (modelo crudo del backend). */
export interface InventoryProduct {
  id: number;
  name: string;
  sku: string;
  stock: number;
  price: string | number;
  category?: { id: number; name: string } | null;
}

/** GET /reports/inventory — snapshot del inventario. */
export interface InventoryReport {
  total_products: number;
  total_units: number;
  total_value: number;
  low_stock: number;
  out_of_stock: number;
  expired: number;
  expiring_soon: number;
  products: InventoryProduct[];
}
