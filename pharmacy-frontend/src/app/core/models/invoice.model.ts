export interface InvoiceItem {
  id: number;
  product_id: number | null;
  product_sku: string;
  product_name: string;
  quantity: number;
  unit_price: string;
  discount_percent: string;
  discount_amount: string;
  subtotal: string;
}

export interface Invoice {
  id: number;
  invoice_number: string;
  status: string;
  is_voided: boolean;
  customer_id: number | null;
  customer_name: string;
  customer_rtn: string | null;
  subtotal: string;
  discount_total: string;
  tax: string;
  total: string;
  payment_method: { id: number; name: string } | null;
  seller: { id: number; name: string } | null;
  items: InvoiceItem[];
  issued_at: string | null;
  voided_at: string | null;
  void_reason: string | null;
  created_at: string | null;
}

export interface PaymentMethod {
  id: number;
  name: string;
}

/** Línea enviada al emitir una factura. */
export interface InvoiceLineInput {
  product_id: number;
  quantity: number;
  discount_percent: number;
}

export interface CreateInvoicePayload {
  customer_name: string;
  customer_rtn: string | null;
  customer_id: number | null;
  payment_method_id: number;
  items: InvoiceLineInput[];
}
