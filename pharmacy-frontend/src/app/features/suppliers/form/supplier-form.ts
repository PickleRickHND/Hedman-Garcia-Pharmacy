import { Component, computed, inject, input, OnInit, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { SupplierPayload } from '../../../core/models/catalog.model';
import { ToastService } from '../../../shared/toast/toast.service';
import { SupplierService } from '../supplier.service';

@Component({
  selector: 'app-supplier-form',
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './supplier-form.html',
})
export class SupplierForm implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly service = inject(SupplierService);
  private readonly router = inject(Router);
  private readonly toast = inject(ToastService);

  readonly id = input<string>();
  readonly isEdit = computed(() => !!this.id());

  readonly saving = signal(false);
  readonly loading = signal(false);
  readonly error = signal<string | null>(null);

  readonly form = this.fb.nonNullable.group({
    name: ['', [Validators.required, Validators.minLength(2), Validators.maxLength(100)]],
    contact_name: ['', [Validators.maxLength(100)]],
    phone: ['', [Validators.maxLength(20)]],
    email: ['', [Validators.email]],
    address: [''],
    rtn: ['', [Validators.maxLength(20)]],
    notes: [''],
    is_active: [true],
  });

  ngOnInit(): void {
    const id = this.id();
    if (id) {
      this.loading.set(true);
      this.service.get(Number(id)).subscribe({
        next: (res) => {
          const s = res.data;
          this.form.patchValue({
            name: s.name,
            contact_name: s.contact_name ?? '',
            phone: s.phone ?? '',
            email: s.email ?? '',
            address: s.address ?? '',
            rtn: s.rtn ?? '',
            notes: s.notes ?? '',
            is_active: s.is_active,
          });
          this.loading.set(false);
        },
        error: () => {
          this.error.set('No pudimos cargar el proveedor.');
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
    const payload: SupplierPayload = {
      name: v.name.trim(),
      contact_name: nullify(v.contact_name),
      phone: nullify(v.phone),
      email: nullify(v.email),
      address: nullify(v.address),
      rtn: nullify(v.rtn),
      notes: nullify(v.notes),
      is_active: v.is_active,
    };

    const id = this.id();
    const request$ = id ? this.service.update(Number(id), payload) : this.service.create(payload);
    request$.subscribe({
      next: () => {
        this.toast.success(id ? 'Proveedor actualizado.' : 'Proveedor creado.');
        this.router.navigate(['/suppliers']);
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
    return e?.error?.message ?? 'No se pudo guardar el proveedor.';
  }
}
