export interface Category {
  id: number;
  name: string;
  description?: string | null;
  color?: string;
  products_count?: number;
}

export interface Supplier {
  id: number;
  name: string;
  contact_name?: string | null;
  phone?: string | null;
  email?: string | null;
  is_active?: boolean;
}
