import { Component, computed, inject, input, OnInit, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { Category, Supplier } from '../../../core/models/catalog.model';
import { ProductPayload } from '../../../core/models/product.model';
import { ToastService } from '../../../shared/toast/toast.service';
import { ProductService } from '../product.service';

@Component({
  selector: 'app-product-form',
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './product-form.html',
  styleUrl: './product-form.scss',
})
export class ProductForm implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly service = inject(ProductService);
  private readonly router = inject(Router);
  private readonly toast = inject(ToastService);

  /** Id de la ruta `:id` (enlazado por withComponentInputBinding). Ausente al crear. */
  readonly id = input<string>();
  readonly isEdit = computed(() => !!this.id());

  readonly saving = signal(false);
  readonly loading = signal(false);
  readonly error = signal<string | null>(null);
  readonly categories = signal<Category[]>([]);
  readonly suppliers = signal<Supplier[]>([]);

  readonly form = this.fb.nonNullable.group({
    sku: ['', [Validators.required, Validators.maxLength(30)]],
    name: ['', [Validators.required, Validators.minLength(2)]],
    description: [''],
    stock: [0, [Validators.required, Validators.min(0)]],
    price: [0, [Validators.required, Validators.min(0)]],
    expiration_date: [''],
    presentation: [''],
    administration_form: [''],
    storage: [''],
    packaging: [''],
    category_id: [null as number | null],
    supplier_id: [null as number | null],
  });

  ngOnInit(): void {
    this.service.categories().subscribe((res) => this.categories.set(res.data));
    this.service.suppliers().subscribe((res) => this.suppliers.set(res.data));

    const id = this.id();
    if (id) {
      this.loading.set(true);
      this.service.get(Number(id)).subscribe({
        next: (res) => {
          const p = res.data;
          this.form.patchValue({
            sku: p.sku,
            name: p.name,
            description: p.description ?? '',
            stock: p.stock,
            price: Number(p.price),
            expiration_date: p.expiration_date ?? '',
            presentation: p.presentation ?? '',
            administration_form: p.administration_form ?? '',
            storage: p.storage ?? '',
            packaging: p.packaging ?? '',
            category_id: p.category_id,
            supplier_id: p.supplier_id,
          });
          this.loading.set(false);
        },
        error: () => {
          this.error.set('No pudimos cargar el producto.');
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

    const payload = this.buildPayload();
    const id = this.id();
    const request$ = id
      ? this.service.update(Number(id), payload)
      : this.service.create(payload);

    request$.subscribe({
      next: () => {
        this.toast.success(id ? 'Producto actualizado.' : 'Producto creado.');
        this.router.navigate(['/products']);
      },
      error: (err) => {
        this.saving.set(false);
        this.error.set(this.extractError(err));
      },
    });
  }

  private buildPayload(): ProductPayload {
    const v = this.form.getRawValue();
    const nullify = (s: string) => (s.trim() === '' ? null : s.trim());
    return {
      sku: v.sku.trim(),
      name: v.name.trim(),
      description: nullify(v.description),
      stock: Number(v.stock),
      price: Number(v.price),
      expiration_date: nullify(v.expiration_date),
      presentation: nullify(v.presentation),
      administration_form: nullify(v.administration_form),
      storage: nullify(v.storage),
      packaging: nullify(v.packaging),
      category_id: v.category_id ? Number(v.category_id) : null,
      supplier_id: v.supplier_id ? Number(v.supplier_id) : null,
    };
  }

  private extractError(err: unknown): string {
    const e = err as { error?: { message?: string; errors?: Record<string, string[]> } };
    if (e?.error?.errors) {
      const first = Object.values(e.error.errors)[0];
      if (first?.length) return first[0];
    }
    return e?.error?.message ?? 'No se pudo guardar el producto. Revisa los datos.';
  }
}
