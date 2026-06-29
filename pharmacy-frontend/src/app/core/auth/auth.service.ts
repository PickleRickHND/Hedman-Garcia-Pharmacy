import { computed, inject, Injectable, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { environment } from '../../../environments/environment';
import { DataEnvelope } from '../models/api.model';
import { LoginCredentials, LoginResponse, User } from '../models/user.model';

const TOKEN_KEY = 'hg-token';
const USER_KEY = 'hg-user';

/** Autenticación contra la API Laravel (token Bearer / Sanctum). */
@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly http = inject(HttpClient);
  private readonly base = environment.apiUrl;

  private readonly _user = signal<User | null>(this.restoreUser());
  readonly user = this._user.asReadonly();
  readonly isAuthenticated = computed(() => this._user() !== null);

  login(credentials: LoginCredentials): Observable<LoginResponse> {
    return this.http.post<LoginResponse>(`${this.base}/login`, credentials).pipe(
      tap((res) => this.persistSession(res.token, res.user)),
    );
  }

  /** Solicita un código de recuperación al correo indicado. */
  forgotPassword(email: string): Observable<{ message: string }> {
    return this.http.post<{ message: string }>(`${this.base}/forgot-password`, { email });
  }

  /** Restablece la contraseña con el código recibido. */
  resetPassword(payload: {
    email: string;
    code: string;
    password: string;
    password_confirmation: string;
  }): Observable<{ message: string }> {
    return this.http.post<{ message: string }>(`${this.base}/reset-password`, payload);
  }

  logout(): Observable<unknown> {
    return this.http.post(`${this.base}/logout`, {}).pipe(
      tap({
        next: () => this.clearSession(),
        error: () => this.clearSession(),
      }),
    );
  }

  /** Re-hidrata el usuario desde el backend (al recargar la app). */
  fetchMe(): Observable<DataEnvelope<User>> {
    return this.http.get<DataEnvelope<User>>(`${this.base}/me`).pipe(
      tap((res) => this._user.set(res.data)),
    );
  }

  get token(): string | null {
    return localStorage.getItem(TOKEN_KEY);
  }

  hasRole(role: string): boolean {
    return this._user()?.roles.includes(role) ?? false;
  }

  hasAnyRole(roles: string[]): boolean {
    const current = this._user()?.roles ?? [];
    return roles.some((r) => current.includes(r));
  }

  private persistSession(token: string, user: User): void {
    localStorage.setItem(TOKEN_KEY, token);
    localStorage.setItem(USER_KEY, JSON.stringify(user));
    this._user.set(user);
  }

  clearSession(): void {
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(USER_KEY);
    this._user.set(null);
  }

  private restoreUser(): User | null {
    const raw = localStorage.getItem(USER_KEY);
    if (!raw) return null;
    try {
      return JSON.parse(raw) as User;
    } catch {
      return null;
    }
  }
}
