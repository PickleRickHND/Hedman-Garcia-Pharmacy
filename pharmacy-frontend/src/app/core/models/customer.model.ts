export interface Customer {
  id: number;
  name: string;
  rtn: string | null;
  phone: string | null;
  email: string | null;
  address: string | null;
  notes: string | null;
  invoices_count?: number;
  created_at: string | null;
  updated_at: string | null;
}

export interface CustomerPayload {
  name: string;
  rtn: string | null;
  phone: string | null;
  email: string | null;
  address: string | null;
  notes: string | null;
}
