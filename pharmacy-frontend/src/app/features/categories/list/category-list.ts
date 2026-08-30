import { Component, computed, inject, OnInit, signal, ChangeDetectionStrategy } from '@angular/core';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { toSignal } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { AuthService } from '../../../core/auth/auth.service';
import { Category } from '../../../core/models/catalog.model';
import { ConfirmDialog } from '../../../shared/confirm/confirm-dialog';
import { Icon } from '../../../shared/icon/icon';
import { ToastService } from '../../../shared/toast/toast.service';
import { CategoryService } from '../category.service';

@Component({
  selector: 'app-category-list',
  imports: [ReactiveFormsModule, RouterLink, Icon, ConfirmDialog],
  templateUrl: './category-list.html',
  changeDetection: ChangeDetectionStrategy.Eager,
  styles: `
    .cat-name { display: inline-flex; align-items: center; gap: 10px; }
    .swatch { width: 16px; height: 16px; border-radius: 5px; flex: none; box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.1); }
  `,
})
export class CategoryList implements OnInit {
  private readonly service = inject(CategoryService);
  private readonly auth = inject(AuthService);
  private readonly toast = inject(ToastService);

  readonly canManage = this.auth.hasRole('Administrador');

  readonly search = new FormControl('', { nonNullable: true });
  private readonly searchValue = toSignal(this.search.valueChanges, { initialValue: '' });

  readonly categories = signal<Category[]>([]);
  readonly loading = signal(true);
  readonly toDelete = signal<Category | null>(null);

  /** Filtrado en cliente: las categorías son un conjunto pequeño. */
  readonly filtered = computed(() => {
    const term = this.searchValue().trim().toLowerCase();
    if (!term) return this.categories();
    return this.categories().filter((c) => c.name.toLowerCase().includes(term));
  });

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading.set(true);
    this.service.list().subscribe({
      next: (res) => {
        this.categories.set(res.data);
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('No pudimos cargar las categorías.');
        this.loading.set(false);
      },
    });
  }

  doDelete(): void {
    const category = this.toDelete();
    if (!category) return;
    this.service.remove(category.id).subscribe({
      next: () => {
        this.toast.success(`Categoría «${category.name}» eliminada.`);
        this.toDelete.set(null);
        this.load();
      },
      error: (err) => {
        this.toDelete.set(null);
        this.toast.error(err?.error?.message ?? 'No se pudo eliminar la categoría.');
      },
    });
  }
}
