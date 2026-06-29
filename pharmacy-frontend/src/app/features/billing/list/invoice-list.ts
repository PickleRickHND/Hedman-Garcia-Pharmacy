import { Component, inject, OnInit, signal } from '@angular/core';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { debounceTime, distinctUntilChanged } from 'rxjs';
import { Invoice, PaymentMethod } from '../../../core/models/invoice.model';
import { Icon } from '../../../shared/icon/icon';
import { Pagination } from '../../../shared/pagination/pagination';
import { ToastService } from '../../../shared/toast/toast.service';
import { InvoiceQuery, InvoiceService } from '../invoice.service';

@Component({
  selector: 'app-invoice-list',
  imports: [ReactiveFormsModule, RouterLink, Icon, Pagination],
  templateUrl: './invoice-list.html',
})
export class InvoiceList implements OnInit {
  private readonly service = inject(InvoiceService);
  private readonly toast = inject(ToastService);
  private readonly router = inject(Router);

  readonly search = new FormControl('', { nonNullable: true });
  readonly dateFilter = signal<InvoiceQuery['date_filter']>(null);
  readonly statusFilter = signal<string | null>(null);
  readonly page = signal(1);

  readonly invoices = signal<Invoice[]>([]);
  readonly currentPage = signal(1);
  readonly lastPage = signal(1);
  readonly loading = signal(true);
  readonly paymentMethods = signal<PaymentMethod[]>([]);

  ngOnInit(): void {
    this.service.paymentMethods().subscribe((res) => this.paymentMethods.set(res.data));
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
        date_filter: this.dateFilter(),
        status: this.statusFilter(),
        page: this.page(),
      })
      .subscribe({
        next: (res) => {
          this.invoices.set(res.data);
          this.currentPage.set(res.meta.current_page);
          this.lastPage.set(res.meta.last_page);
          this.loading.set(false);
        },
        error: () => {
          this.toast.error('No pudimos cargar las facturas.');
          this.loading.set(false);
        },
      });
  }

  onDateFilter(value: string): void {
    this.dateFilter.set((value || null) as InvoiceQuery['date_filter']);
    this.resetAndLoad();
  }

  onStatusFilter(value: string): void {
    this.statusFilter.set(value || null);
    this.resetAndLoad();
  }

  goToPage(page: number): void {
    this.page.set(page);
    this.load();
  }

  open(invoice: Invoice): void {
    this.router.navigate(['/invoices', invoice.id]);
  }

  money(value: string): string {
    return new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(Number(value));
  }

  formatDate(iso: string | null): string {
    if (!iso) return '—';
    return new Date(iso).toLocaleDateString('es-HN', { day: '2-digit', month: '2-digit', year: 'numeric' });
  }

  private resetAndLoad(): void {
    this.page.set(1);
    this.load();
  }
}
