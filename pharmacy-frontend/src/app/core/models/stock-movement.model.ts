export type StockMovementType = 'sale' | 'purchase' | 'return' | 'void' | 'adjustment' | 'loss';

export interface StockMovement {
  id: number;
  type: StockMovementType;
  type_label: string;
  quantity: number;
  stock_before: number;
  stock_after: number;
  reference_type: string | null;
  reference_id: number | null;
  reason: string | null;
  product?: { id: number; name: string; sku: string };
  user?: { id: number; name: string };
  created_at: string | null;
}

/** Filtros del kardex. Coinciden con los query params de GET /stock-movements. */
export interface StockMovementFilters {
  product_id?: number | null;
  type?: string;
  date_from?: string;
  date_to?: string;
  page?: number;
  per_page?: number;
}

/** Tipos de movimiento para el filtro (espejo de StockMovement::TYPE_LABELS del backend). */
export const STOCK_MOVEMENT_TYPES: { value: StockMovementType; label: string }[] = [
  { value: 'sale', label: 'Venta' },
  { value: 'purchase', label: 'Compra' },
  { value: 'return', label: 'Devolución' },
  { value: 'void', label: 'Anulación' },
  { value: 'adjustment', label: 'Ajuste' },
  { value: 'loss', label: 'Merma' },
];

/** Color de badge por tipo (espejo de StockMovement::TYPE_COLORS del backend). */
export const STOCK_MOVEMENT_BADGE: Record<StockMovementType, string> = {
  sale: 'danger',
  purchase: 'success',
  return: 'info',
  void: 'warning',
  adjustment: 'neutral',
  loss: 'danger',
};
