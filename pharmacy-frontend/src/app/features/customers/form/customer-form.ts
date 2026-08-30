import { Component, computed, inject, input, OnInit, signal, ChangeDetectionStrategy } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { CustomerPayload } from '../../../core/models/customer.model';
import { ToastService } from '../../../shared/toast/toast.service';
import { CustomerService } from '../customer.service';

@Component({
  selector: 'app-customer-form',
  imports: [ReactiveFormsModule, RouterLink],
  changeDetection: ChangeDetectionStrategy.Eager,
  templateUrl: './customer-form.html',
})
export class CustomerForm implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly service = inject(CustomerService);
  private readonly router = inject(Router);
  private readonly toast = inject(ToastService);

  readonly id = input<string>();
  readonly isEdit = computed(() => !!this.id());

  readonly saving = signal(false);
  readonly loading = signal(false);
  readonly error = signal<string | null>(null);

  readonly form = this.fb.nonNullable.group({
    name: ['', [Validators.required, Validators.minLength(2), Validators.maxLength(100)]],
    rtn: ['', [Validators.maxLength(20)]],
    phone: ['', [Validators.maxLength(20)]],
    email: ['', [Validators.email]],
    address: [''],
    notes: [''],
  });

  ngOnInit(): void {
    const id = this.id();
    if (id) {
      this.loading.set(true);
      this.service.get(Number(id)).subscribe({
        next: (res) => {
          const c = res.data;
          this.form.patchValue({
            name: c.name,
            rtn: c.rtn ?? '',
            phone: c.phone ?? '',
            email: c.email ?? '',
            address: c.address ?? '',
            notes: c.notes ?? '',
          });
          this.loading.set(false);
        },
        error: () => {
          this.error.set('No pudimos cargar el cliente.');
          this.loading.set(false);
        },
      });
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
    const nullify = (s: string) => (s.trim() === '' ? null : s.trim());
    const payload: CustomerPayload = {
      name: v.name.trim(),
      rtn: nullify(v.rtn),
      phone: nullify(v.phone),
      email: nullify(v.email),
      address: nullify(v.address),
      notes: nullify(v.notes),
    };

    const id = this.id();
    const request$ = id ? this.service.update(Number(id), payload) : this.service.create(payload);
    request$.subscribe({
      next: () => {
        this.toast.success(id ? 'Cliente actualizado.' : 'Cliente creado.');
        this.router.navigate(['/customers']);
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
    return e?.error?.message ?? 'No se pudo guardar el cliente.';
  }
}
