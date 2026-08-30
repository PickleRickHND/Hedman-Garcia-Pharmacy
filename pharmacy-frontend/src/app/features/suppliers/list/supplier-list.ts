import { Component, inject, OnInit, signal, ChangeDetectionStrategy } from '@angular/core';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { debounceTime, distinctUntilChanged } from 'rxjs';
import { AuthService } from '../../../core/auth/auth.service';
import { Supplier } from '../../../core/models/catalog.model';
import { ConfirmDialog } from '../../../shared/confirm/confirm-dialog';
import { Icon } from '../../../shared/icon/icon';
import { Pagination } from '../../../shared/pagination/pagination';
import { ToastService } from '../../../shared/toast/toast.service';
import { SupplierService } from '../supplier.service';

@Component({
  selector: 'app-supplier-list',
  imports: [ReactiveFormsModule, RouterLink, Icon, Pagination, ConfirmDialog],
  changeDetection: ChangeDetectionStrategy.Eager,
  templateUrl: './supplier-list.html',
})
export class SupplierList implements OnInit {
  private readonly service = inject(SupplierService);
  private readonly auth = inject(AuthService);
  private readonly toast = inject(ToastService);

  readonly canManage = this.auth.hasRole('Administrador');

  readonly search = new FormControl('', { nonNullable: true });
  readonly page = signal(1);
  readonly suppliers = signal<Supplier[]>([]);
  readonly currentPage = signal(1);
  readonly lastPage = signal(1);
  readonly loading = signal(true);
  readonly toDelete = signal<Supplier | null>(null);

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
        this.suppliers.set(res.data);
        this.currentPage.set(res.meta.current_page);
        this.lastPage.set(res.meta.last_page);
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('No pudimos cargar los proveedores.');
        this.loading.set(false);
      },
    });
  }

  goToPage(page: number): void {
    this.page.set(page);
    this.load();
  }

  doDelete(): void {
    const supplier = this.toDelete();
    if (!supplier) return;
    this.service.remove(supplier.id).subscribe({
      next: () => {
        this.toast.success(`Proveedor «${supplier.name}» eliminado.`);
        this.toDelete.set(null);
        this.load();
      },
      error: () => {
        this.toast.error('No se pudo eliminar el proveedor.');
        this.toDelete.set(null);
      },
    });
  }
}
