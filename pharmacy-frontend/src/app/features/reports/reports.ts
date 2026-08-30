import { Component, computed, DestroyRef, inject, OnInit, signal, ChangeDetectionStrategy } from '@angular/core';
import { takeUntilDestroyed, toSignal } from '@angular/core/rxjs-interop';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { debounceTime } from 'rxjs';
import { InventoryReport, SalesReport, TopProduct } from '../../core/models/report.model';
import { BarChart, BarDatum } from '../../shared/chart/bar-chart';
import { DatePickerComponent } from '../../shared/date-picker/date-picker';
import { SelectComponent, SelectOption } from '../../shared/select/select';
import { ToastService } from '../../shared/toast/toast.service';
import { ReportService } from './report.service';

type Tab = 'sales' | 'products' | 'inventory';

@Component({
  selector: 'app-reports',
  imports: [ReactiveFormsModule, RouterLink, BarChart, SelectComponent, DatePickerComponent],
  changeDetection: ChangeDetectionStrategy.Eager,
  templateUrl: './reports.html',
})
export class Reports implements OnInit {
  private readonly service = inject(ReportService);
  private readonly toast = inject(ToastService);
  private readonly destroyRef = inject(DestroyRef);

  readonly tab = signal<Tab>('sales');

  readonly sortByOptions: SelectOption[] = [
    { value: 'quantity', label: 'Por cantidad' },
    { value: 'revenue', label: 'Por ingresos' },
  ];
  readonly limitOptions: SelectOption[] = [
    { value: 10, label: 'Top 10' },
    { value: 20, label: 'Top 20' },
    { value: 50, label: 'Top 50' },
  ];

  // --- Ventas ---
  readonly salesRange = new FormGroup({
    from: new FormControl(this.monthStart(), { nonNullable: true }),
    to: new FormControl(this.today(), { nonNullable: true }),
  });
  readonly sales = signal<SalesReport | null>(null);
  readonly salesLoading = signal(false);

  readonly salesChart = computed<BarDatum[]>(() =>
    (this.sales()?.by_payment_method ?? []).map((m) => ({
      label: m.method,
      value: m.total,
      display: this.money(m.total),
    })),
  );

  // --- Top productos ---
  readonly productsForm = new FormGroup({
    from: new FormControl(this.monthStart(), { nonNullable: true }),
    to: new FormControl(this.today(), { nonNullable: true }),
    sort_by: new FormControl<'quantity' | 'revenue'>('quantity', { nonNullable: true }),
    limit: new FormControl(10, { nonNullable: true }),
  });
  readonly top = signal<TopProduct[]>([]);
  readonly topLoading = signal(false);

  /** Señal derivada del select (no leer FormControl.value dentro de un computed). */
  private readonly sortBy = toSignal(this.productsForm.controls.sort_by.valueChanges, {
    initialValue: this.productsForm.controls.sort_by.value,
  });

  readonly topChart = computed<BarDatum[]>(() => {
    const byRevenue = this.sortBy() === 'revenue';
    // Los SUM del backend llegan como string (MySQL); coercionar en el borde.
    return this.top().map((p) => ({
      label: p.product_name,
      value: Number(byRevenue ? p.total_revenue : p.total_quantity),
      display: byRevenue ? this.money(p.total_revenue) : String(p.total_quantity),
    }));
  });

  // --- Inventario ---
  readonly inventory = signal<InventoryReport | null>(null);
  readonly inventoryLoading = signal(false);

  readonly inventoryChart = computed<BarDatum[]>(() => {
    const inv = this.inventory();
    if (!inv) return [];
    return [
      { label: 'Stock bajo', value: inv.low_stock },
      { label: 'Agotados', value: inv.out_of_stock },
      { label: 'Vencidos', value: inv.expired },
      { label: 'Por vencer', value: inv.expiring_soon },
    ];
  });

  ngOnInit(): void {
    this.loadSales();
    this.salesRange.valueChanges
      .pipe(debounceTime(300), takeUntilDestroyed(this.destroyRef))
      .subscribe(() => this.loadSales());
    this.productsForm.valueChanges
      .pipe(debounceTime(300), takeUntilDestroyed(this.destroyRef))
      .subscribe(() => this.loadTop());
  }

  setTab(tab: Tab): void {
    this.tab.set(tab);
    if (tab === 'products' && this.top().length === 0) this.loadTop();
    if (tab === 'inventory' && this.inventory() === null) this.loadInventory();
  }

  loadSales(): void {
    this.salesLoading.set(true);
    this.service.sales(this.salesRange.getRawValue()).subscribe({
      next: (res) => {
        this.sales.set(res.data);
        this.salesLoading.set(false);
      },
      error: () => {
        this.toast.error('No pudimos cargar el reporte de ventas.');
        this.salesLoading.set(false);
      },
    });
  }

  loadTop(): void {
    this.topLoading.set(true);
    const v = this.productsForm.getRawValue();
    this.service.products({ from: v.from, to: v.to, sort_by: v.sort_by, limit: Number(v.limit) }).subscribe({
      next: (res) => {
        this.top.set(res.data);
        this.topLoading.set(false);
      },
      error: () => {
        this.toast.error('No pudimos cargar el top de productos.');
        this.topLoading.set(false);
      },
    });
  }

  loadInventory(): void {
    this.inventoryLoading.set(true);
    this.service.inventory().subscribe({
      next: (res) => {
        this.inventory.set(res.data);
        this.inventoryLoading.set(false);
      },
      error: () => {
        this.toast.error('No pudimos cargar el inventario.');
        this.inventoryLoading.set(false);
      },
    });
  }

  money(value: number | string): string {
    return new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(Number(value));
  }

  private today(): string {
    return this.isoDate(new Date());
  }

  private monthStart(): string {
    const d = new Date();
    return this.isoDate(new Date(d.getFullYear(), d.getMonth(), 1));
  }

  /** Fecha local en formato yyyy-mm-dd (sin corrimiento por zona horaria). */
  private isoDate(d: Date): string {
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
  }
}
