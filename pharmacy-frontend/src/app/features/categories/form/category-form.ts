import { Component, computed, inject, input, OnInit, signal, ChangeDetectionStrategy } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { CategoryPayload } from '../../../core/models/catalog.model';
import { ColorPickerComponent } from '../../../shared/color-picker/color-picker';
import { ToastService } from '../../../shared/toast/toast.service';
import { CategoryService } from '../category.service';

@Component({
  selector: 'app-category-form',
  imports: [ReactiveFormsModule, RouterLink, ColorPickerComponent],
  changeDetection: ChangeDetectionStrategy.Eager,
  templateUrl: './category-form.html',
})
export class CategoryForm implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly service = inject(CategoryService);
  private readonly router = inject(Router);
  private readonly toast = inject(ToastService);

  readonly id = input<string>();
  readonly isEdit = computed(() => !!this.id());

  readonly saving = signal(false);
  readonly loading = signal(false);
  readonly error = signal<string | null>(null);

  readonly form = this.fb.nonNullable.group({
    name: ['', [Validators.required, Validators.minLength(2), Validators.maxLength(50)]],
    description: [''],
    color: ['#12a594', [Validators.required, Validators.pattern(/^#[0-9a-fA-F]{6}$/)]],
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
            description: c.description ?? '',
            color: c.color,
          });
          this.loading.set(false);
        },
        error: () => {
          this.error.set('No pudimos cargar la categoría.');
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
    const payload: CategoryPayload = {
      name: v.name.trim(),
      description: v.description.trim() === '' ? null : v.description.trim(),
      color: v.color,
    };

    const id = this.id();
    const request$ = id ? this.service.update(Number(id), payload) : this.service.create(payload);
    request$.subscribe({
      next: () => {
        this.toast.success(id ? 'Categoría actualizada.' : 'Categoría creada.');
        this.router.navigate(['/categories']);
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
    return e?.error?.message ?? 'No se pudo guardar la categoría.';
  }
}
