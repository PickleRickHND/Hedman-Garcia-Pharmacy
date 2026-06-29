export interface CashRegister {
  id: number;
  status: string;
  is_open: boolean;
  opened_at: string | null;
  closed_at: string | null;
  opening_amount: string;
  expected_amount: string | null;
  actual_amount: string | null;
  difference: string | null;
  invoices_count: number | null;
  voided_count: number | null;
  total_sales: string | null;
  total_cash: string | null;
  total_card: string | null;
  total_transfer: string | null;
  notes: string | null;
  user: { id: number; name: string } | null;
  created_at: string | null;
}
