export interface Category {
  id: number;
  name: string;
  description: string | null;
  color: string;
  products_count?: number;
  created_at?: string | null;
  updated_at?: string | null;
}

export interface CategoryPayload {
  name: string;
  description: string | null;
  color: string;
}

export interface Supplier {
  id: number;
  name: string;
  contact_name: string | null;
  phone: string | null;
  email: string | null;
  address: string | null;
  rtn: string | null;
  notes: string | null;
  is_active: boolean;
  products_count?: number;
  created_at?: string | null;
  updated_at?: string | null;
}

export interface SupplierPayload {
  name: string;
  contact_name: string | null;
  phone: string | null;
  email: string | null;
  address: string | null;
  rtn: string | null;
  notes: string | null;
  is_active: boolean;
}
