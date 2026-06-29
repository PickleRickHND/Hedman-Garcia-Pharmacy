export interface User {
  id: number;
  name: string;
  email: string;
  initials: string;
  role: string | null;
  roles: string[];
  permissions: string[];
  must_change_password: boolean;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface LoginResponse {
  token: string;
  user: User;
}

/** Payload de alta/edición de usuario (administración). La contraseña es opcional en edición. */
export interface UserPayload {
  name: string;
  email: string;
  role: string;
  password?: string;
  password_confirmation?: string;
  must_change_password?: boolean;
}
