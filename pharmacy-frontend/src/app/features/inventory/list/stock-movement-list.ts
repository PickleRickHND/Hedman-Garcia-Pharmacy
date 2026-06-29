import { Component, inject, OnInit, signal } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { debounceTime } from 'rxjs';
import {
  StockMovement,
  StockMovementType,
  STOCK_MOVEMENT_BADGE,
  STOCK_MOVEMENT_TYPES,
} from '../../../core/models/stock-movement.model';
import { Icon } from '../../../shared/icon/icon';
import { Pagination } from '../../../shared/pagination/pagination';
import { ToastService } from '../../../shared/toast/toast.service';
import { ProductService } from '../../products/product.service';
import { StockMovementService } from '../stock-movement.service';

interface ProductOption {
  id: number;
  name: string;
  sku: string;
}

@Component({
  selector: 'app-stock-movement-list',
  imports: [ReactiveFormsModule, Icon, Pagination],
  templateUrl: './stock-movement-list.html',
})
export class StockMovementList implements OnInit {
  private readonly service = inject(StockMovementService);
  private readonly products = inject(ProductService);
  private readonly toast = inject(ToastService);

  readonly types = STOCK_MOVEMENT_TYPES;

  readonly filters = new FormGroup({
    product_id: new FormControl<string>('', { nonNullable: true }),
    type: new FormControl<string>('', { nonNullable: true }),
    date_from: new FormControl<string>('', { nonNullable: true }),
    date_to: new FormControl<string>('', { nonNullable: true }),
  });

  readonly page = signal(1);
  readonly movements = signal<StockMovement[]>([]);
  readonly productOptions = signal<ProductOption[]>([]);
  readonly currentPage = signal(1);
  readonly lastPage = signal(1);
  readonly total = signal(0);
  readonly loading = signal(true);

  ngOnInit(): void {
    // Productos para el filtro (tope alto para traerlos todos en un select).
    this.products.list({ per_page: 200 }).subscribe({
      next: (res) => this.productOptions.set(res.data.map((p) => ({ id: p.id, name: p.name, sku: p.sku }))),
      error: () => this.toast.error('No pudimos cargar los productos para el filtro.'),
    });

    this.filters.valueChanges.pipe(debounceTime(300)).subscribe(() => {
      this.page.set(1);
      this.load();
    });

    this.load();
  }

  load(): void {
    this.loading.set(true);
    const v = this.filters.getRawValue();
    this.service
      .list({
        product_id: v.product_id ? Number(v.product_id) : null,
        type: v.type || undefined,
        date_from: v.date_from || undefined,
        date_to: v.date_to || undefined,
        page: this.page(),
      })
      .subscribe({
        next: (res) => {
          this.movements.set(res.data);
          this.currentPage.set(res.meta.current_page);
          this.lastPage.set(res.meta.last_page);
          this.total.set(res.meta.total);
          this.loading.set(false);
        },
        error: () => {
          this.toast.error('No pudimos cargar el kardex.');
          this.loading.set(false);
        },
      });
  }

  goToPage(page: number): void {
    this.page.set(page);
    this.load();
  }

  clearFilters(): void {
    this.filters.reset({ product_id: '', type: '', date_from: '', date_to: '' });
  }

  badge(type: StockMovementType): string {
    return STOCK_MOVEMENT_BADGE[type] ?? 'neutral';
  }

  /** Delta con signo: positivo si entró stock, negativo si salió. */
  delta(m: StockMovement): number {
    return m.stock_after - m.stock_before;
  }

  fmtDateTime(iso: string | null): string {
    if (!iso) return '—';
    return new Date(iso).toLocaleString('es-HN', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  }
}
