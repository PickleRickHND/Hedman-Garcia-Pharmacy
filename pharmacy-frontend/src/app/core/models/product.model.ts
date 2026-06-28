export interface Product {
  id: number;
  sku: string;
  name: string;
  description: string | null;
  stock: number;
  price: string;
  expiration_date: string | null;
  presentation: string | null;
  administration_form: string | null;
  storage: string | null;
  packaging: string | null;
  category_id: number | null;
  supplier_id: number | null;
  category: { id: number; name: string } | null;
  supplier: { id: number; name: string } | null;
  is_low_stock: boolean;
  is_out_of_stock: boolean;
  is_expired: boolean;
  is_expiring_soon: boolean;
  created_at: string | null;
  updated_at: string | null;
}

/** Cuerpo enviado al crear/actualizar un producto. */
export interface ProductPayload {
  sku: string;
  name: string;
  description: string | null;
  stock: number;
  price: number;
  expiration_date: string | null;
  presentation: string | null;
  administration_form: string | null;
  storage: string | null;
  packaging: string | null;
  category_id: number | null;
  supplier_id: number | null;
}
