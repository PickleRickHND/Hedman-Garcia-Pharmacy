import { Component, computed, inject, OnInit, signal } from '@angular/core';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { debounceTime, distinctUntilChanged } from 'rxjs';
import { AuthService } from '../../../core/auth/auth.service';
import { Category } from '../../../core/models/catalog.model';
import { Product } from '../../../core/models/product.model';
import { ConfirmDialog } from '../../../shared/confirm/confirm-dialog';
import { Icon } from '../../../shared/icon/icon';
import { Pagination } from '../../../shared/pagination/pagination';
import { SelectComponent, SelectOption, SelectValue } from '../../../shared/select/select';
import { ToastService } from '../../../shared/toast/toast.service';
import { ProductService } from '../product.service';

@Component({
  selector: 'app-product-list',
  imports: [ReactiveFormsModule, RouterLink, Icon, Pagination, ConfirmDialog, SelectComponent],
  templateUrl: './product-list.html',
  styleUrl: './product-list.scss',
})
export class ProductList implements OnInit {
  private readonly service = inject(ProductService);
  private readonly auth = inject(AuthService);
  private readonly toast = inject(ToastService);
  private readonly route = inject(ActivatedRoute);

  readonly canManage = this.auth.hasRole('Administrador');

  readonly search = new FormControl('', { nonNullable: true });
  readonly categoryId = signal<number | null>(null);
  readonly lowStock = signal(false);
  readonly expiringSoon = signal(false);
  readonly expired = signal(false);
  readonly page = signal(1);

  readonly products = signal<Product[]>([]);
  readonly currentPage = signal(1);
  readonly lastPage = signal(1);
  readonly total = signal(0);
  readonly loading = signal(true);
  readonly categories = signal<Category[]>([]);
  readonly toDelete = signal<Product | null>(null);

  readonly categoryOptions = computed<SelectOption[]>(() => [
    { value: null, label: 'Todas las categorías' },
    ...this.categories().map((c) => ({ value: c.id, label: c.name })),
  ]);

  ngOnInit(): void {
    this.service.categories().subscribe((res) => this.categories.set(res.data));

    // Deep-linking: aplica filtros desde la URL (ej. desde la campana o el buscador global).
    const qp = this.route.snapshot.queryParamMap;
    const search = qp.get('search');
    if (search) this.search.setValue(search, { emitEvent: false });
    this.lowStock.set(qp.get('low_stock') === '1');
    this.expiringSoon.set(qp.get('expiring_soon') === '1');
    this.expired.set(qp.get('expired') === '1');

    this.search.valueChanges
      .pipe(debounceTime(350), distinctUntilChanged())
      .subscribe(() => this.resetAndLoad());
    this.load();
  }

  load(): void {
    this.loading.set(true);
    this.service
      .list({
        search: this.search.value,
        category_id: this.categoryId(),
        low_stock: this.lowStock(),
        expiring_soon: this.expiringSoon(),
        expired: this.expired(),
        page: this.page(),
      })
      .subscribe({
        next: (res) => {
          this.products.set(res.data);
          this.currentPage.set(res.meta.current_page);
          this.lastPage.set(res.meta.last_page);
          this.total.set(res.meta.total);
          this.loading.set(false);
        },
        error: () => {
          this.toast.error('No pudimos cargar los productos.');
          this.loading.set(false);
        },
      });
  }

  onCategoryChange(value: SelectValue): void {
    this.categoryId.set(value !== null && value !== '' ? Number(value) : null);
    this.resetAndLoad();
  }

  toggleLowStock(): void {
    this.lowStock.update((v) => !v);
    this.resetAndLoad();
  }

  goToPage(page: number): void {
    this.page.set(page);
    this.load();
  }

  confirmDelete(product: Product): void {
    this.toDelete.set(product);
  }

  doDelete(): void {
    const product = this.toDelete();
    if (!product) return;
    this.service.remove(product.id).subscribe({
      next: () => {
        this.toast.success(`Producto «${product.name}» eliminado.`);
        this.toDelete.set(null);
        this.load();
      },
      error: () => {
        this.toast.error('No se pudo eliminar el producto.');
        this.toDelete.set(null);
      },
    });
  }

  money(value: string): string {
    return new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(Number(value));
  }

  private resetAndLoad(): void {
    this.page.set(1);
    this.load();
  }
}
