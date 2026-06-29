import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../../core/auth/auth.service';
import { ThemeService } from '../../../core/theme/theme.service';

/** Paso 1 de recuperación: el usuario pide un código a su correo. */
@Component({
  selector: 'app-forgot-password',
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './forgot-password.html',
  styleUrl: '../auth.scss',
})
export class ForgotPassword {
  private readonly fb = inject(FormBuilder);
  private readonly auth = inject(AuthService);
  private readonly router = inject(Router);
  readonly theme = inject(ThemeService);

  readonly loading = signal(false);
  readonly error = signal<string | null>(null);

  /** Grilla "blister" decorativa (misma signature que el login). */
  readonly cells = Array.from({ length: 54 }, (_, i) => i);
  readonly filled = new Set([7, 8, 14, 20, 27, 28, 33, 41, 46, 47]);

  readonly form = this.fb.nonNullable.group({
    email: ['', [Validators.required, Validators.email]],
  });

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.loading.set(true);
    this.error.set(null);

    const email = this.form.getRawValue().email;
    this.auth.forgotPassword(email).subscribe({
      next: () => {
        // La respuesta es genérica (no revela si el correo existe): siempre avanzamos
        // al paso 2 con el correo precargado.
        this.router.navigate(['/reset-password'], { queryParams: { email } });
      },
      error: (err) => {
        this.loading.set(false);
        this.error.set(
          err?.error?.message ?? 'No pudimos procesar la solicitud. Intenta de nuevo.',
        );
      },
    });
  }
}
