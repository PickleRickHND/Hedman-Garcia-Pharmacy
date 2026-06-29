export interface ReturnItem {
  id: number;
  product_id: number | null;
  invoice_item_id: number;
  quantity: number;
  unit_price: string;
  subtotal: string;
  restock: boolean;
}

export interface ReturnOrder {
  id: number;
  return_number: string;
  reason: string;
  total_refund: string;
  status: string;
  invoice: { id: number; invoice_number: string } | null;
  processed_by: { id: number; name: string } | null;
  items: ReturnItem[];
  processed_at: string | null;
  created_at: string | null;
}

export interface ReturnLineInput {
  invoice_item_id: number;
  quantity: number;
  restock: boolean;
}

export interface CreateReturnPayload {
  invoice_id: number;
  reason: string;
  items: ReturnLineInput[];
}
