import { Component, computed, inject, input, OnInit, signal } from '@angular/core';
import { AbstractControl, FormBuilder, ReactiveFormsModule, ValidationErrors, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { UserPayload } from '../../../core/models/user.model';
import { ToastService } from '../../../shared/toast/toast.service';
import { UserAdminService } from '../user-admin.service';

/** Validador de grupo: la confirmación debe coincidir con la contraseña. */
function passwordsMatch(group: AbstractControl): ValidationErrors | null {
  const password = group.get('password')?.value ?? '';
  const confirmation = group.get('password_confirmation')?.value ?? '';
  return password === confirmation ? null : { passwordMismatch: true };
}

@Component({
  selector: 'app-user-form',
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './user-form.html',
})
export class UserForm implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly service = inject(UserAdminService);
  private readonly router = inject(Router);
  private readonly toast = inject(ToastService);

  readonly id = input<string>();
  readonly isEdit = computed(() => !!this.id());

  readonly saving = signal(false);
  readonly loading = signal(false);
  readonly error = signal<string | null>(null);
  readonly roles = signal<string[]>([]);

  readonly form = this.fb.nonNullable.group(
    {
      name: ['', [Validators.required, Validators.minLength(2), Validators.maxLength(100)]],
      email: ['', [Validators.required, Validators.email, Validators.maxLength(150)]],
      role: ['', [Validators.required]],
      password: ['', [Validators.minLength(8)]],
      password_confirmation: [''],
      must_change_password: [false],
    },
    { validators: passwordsMatch },
  );

  ngOnInit(): void {
    this.service.roles().subscribe({
      next: (res) => this.roles.set(res.data),
      error: () => this.error.set('No pudimos cargar los roles disponibles.'),
    });

    const id = this.id();
    if (id) {
      this.loading.set(true);
      this.service.get(Number(id)).subscribe({
        next: (res) => {
          const u = res.data;
          this.form.patchValue({
            name: u.name,
            email: u.email,
            role: u.role ?? '',
            must_change_password: u.must_change_password,
          });
          this.loading.set(false);
        },
        error: () => {
          this.error.set('No pudimos cargar el usuario.');
          this.loading.set(false);
        },
      });
    } else {
      // En alta la contraseña es obligatoria; en edición es opcional.
      this.form.controls.password.addValidators(Validators.required);
      this.form.controls.password.updateValueAndValidity();
    }
  }

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.saving.set(true);
    this.error.set(null);

    const v = this.form.getRawValue();
    const payload: UserPayload = {
      name: v.name.trim(),
      email: v.email.trim(),
      role: v.role,
      must_change_password: v.must_change_password,
    };
    // Solo enviar contraseña si el usuario escribió una (clave en edición).
    if (v.password) {
      payload.password = v.password;
      payload.password_confirmation = v.password_confirmation;
    }

    const id = this.id();
    const request$ = id ? this.service.update(Number(id), payload) : this.service.create(payload);
    request$.subscribe({
      next: () => {
        this.toast.success(id ? 'Usuario actualizado.' : 'Usuario creado.');
        this.router.navigate(['/users']);
      },
      error: (err) => {
        this.saving.set(false);
        this.error.set(this.extractError(err));
      },
    });
  }

  private extractError(err: unknown): string {
    const e = err as { error?: { message?: string; errors?: Record<string, string[]> } };
    if (e?.error?.errors) {
      const first = Object.values(e.error.errors)[0];
      if (first?.length) return first[0];
    }
    return e?.error?.message ?? 'No se pudo guardar el usuario.';
  }
}
