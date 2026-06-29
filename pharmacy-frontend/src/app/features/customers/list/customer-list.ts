import { Component, inject, OnInit, signal } from '@angular/core';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { debounceTime, distinctUntilChanged } from 'rxjs';
import { AuthService } from '../../../core/auth/auth.service';
import { Customer } from '../../../core/models/customer.model';
import { ConfirmDialog } from '../../../shared/confirm/confirm-dialog';
import { Icon } from '../../../shared/icon/icon';
import { Pagination } from '../../../shared/pagination/pagination';
import { ToastService } from '../../../shared/toast/toast.service';
import { CustomerService } from '../customer.service';

@Component({
  selector: 'app-customer-list',
  imports: [ReactiveFormsModule, RouterLink, Icon, Pagination, ConfirmDialog],
  templateUrl: './customer-list.html',
})
export class CustomerList implements OnInit {
  private readonly service = inject(CustomerService);
  private readonly auth = inject(AuthService);
  private readonly toast = inject(ToastService);

  readonly canManage = this.auth.hasAnyRole(['Administrador', 'Cajero']);

  readonly search = new FormControl('', { nonNullable: true });
  readonly page = signal(1);
  readonly customers = signal<Customer[]>([]);
  readonly currentPage = signal(1);
  readonly lastPage = signal(1);
  readonly loading = signal(true);
  readonly toDelete = signal<Customer | null>(null);

  ngOnInit(): void {
    this.search.valueChanges
      .pipe(debounceTime(350), distinctUntilChanged())
      .subscribe(() => {
        this.page.set(1);
        this.load();
      });
    this.load();
  }

  load(): void {
    this.loading.set(true);
    this.service.list({ search: this.search.value, page: this.page() }).subscribe({
      next: (res) => {
        this.customers.set(res.data);
        this.currentPage.set(res.meta.current_page);
        this.lastPage.set(res.meta.last_page);
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('No pudimos cargar los clientes.');
        this.loading.set(false);
      },
    });
  }

  goToPage(page: number): void {
    this.page.set(page);
    this.load();
  }

  doDelete(): void {
    const customer = this.toDelete();
    if (!customer) return;
    this.service.remove(customer.id).subscribe({
      next: () => {
        this.toast.success(`Cliente «${customer.name}» eliminado.`);
        this.toDelete.set(null);
        this.load();
      },
      error: () => {
        this.toast.error('No se pudo eliminar el cliente.');
        this.toDelete.set(null);
      },
    });
  }
}
