import { Component, inject, signal } from '@angular/core';
import {
  AbstractControl,
  FormBuilder,
  ReactiveFormsModule,
  ValidationErrors,
  Validators,
} from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { AuthService } from '../../../core/auth/auth.service';
import { ThemeService } from '../../../core/theme/theme.service';
import { ToastService } from '../../../shared/toast/toast.service';

/** Validador de grupo: la confirmación debe coincidir con la contraseña. */
function passwordsMatch(group: AbstractControl): ValidationErrors | null {
  const password = group.get('password')?.value;
  const confirm = group.get('password_confirmation')?.value;
  return password && confirm && password !== confirm ? { mismatch: true } : null;
}

/** Paso 2 de recuperación: el usuario ingresa el código y su nueva contraseña. */
@Component({
  selector: 'app-reset-password',
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './reset-password.html',
  styleUrl: '../auth.scss',
})
export class ResetPassword {
  private readonly fb = inject(FormBuilder);
  private readonly auth = inject(AuthService);
  private readonly router = inject(Router);
  private readonly route = inject(ActivatedRoute);
  private readonly toast = inject(ToastService);
  readonly theme = inject(ThemeService);

  readonly loading = signal(false);
  readonly error = signal<string | null>(null);
  readonly showPassword = signal(false);

  readonly cells = Array.from({ length: 54 }, (_, i) => i);
  readonly filled = new Set([7, 8, 14, 20, 27, 28, 33, 41, 46, 47]);

  readonly form = this.fb.nonNullable.group(
    {
      email: [this.route.snapshot.queryParamMap.get('email') ?? '', [Validators.required, Validators.email]],
      code: ['', [Validators.required, Validators.pattern(/^\d{6}$/)]],
      password: ['', [Validators.required, Validators.minLength(8)]],
      password_confirmation: ['', [Validators.required]],
    },
    { validators: passwordsMatch },
  );

  togglePassword(): void {
    this.showPassword.update((v) => !v);
  }

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.loading.set(true);
    this.error.set(null);

    this.auth.resetPassword(this.form.getRawValue()).subscribe({
      next: (res) => {
        this.toast.success(res.message ?? 'Contraseña actualizada.');
        this.router.navigate(['/login']);
      },
      error: (err) => {
        this.loading.set(false);
        // El backend devuelve el detalle específico en errors.code; el message
        // de Laravel ("The given data was invalid") es genérico, va de fallback.
        this.error.set(
          err?.error?.errors?.code?.[0] ??
            err?.error?.errors?.password?.[0] ??
            err?.error?.message ??
            'No pudimos restablecer la contraseña. Verifica el código.',
        );
      },
    });
  }
}
