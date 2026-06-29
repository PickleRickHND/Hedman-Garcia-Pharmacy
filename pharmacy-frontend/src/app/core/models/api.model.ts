/** Estructura de respuesta paginada de Laravel (Resource collections). */
export interface Paginated<T> {
  data: T[];
  links: {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
  };
  meta: {
    current_page: number;
    from: number | null;
    to: number | null;
    last_page: number;
    per_page: number;
    total: number;
  };
}

/** Envoltura simple { data: T } usada por recursos singulares y listas no paginadas. */
export interface DataEnvelope<T> {
  data: T;
}
