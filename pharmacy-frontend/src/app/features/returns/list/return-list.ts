import { Component, inject, OnInit, signal } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../../core/auth/auth.service';
import { ReturnOrder } from '../../../core/models/return.model';
import { Icon } from '../../../shared/icon/icon';
import { Pagination } from '../../../shared/pagination/pagination';
import { ToastService } from '../../../shared/toast/toast.service';
import { ReturnService } from '../return.service';

@Component({
  selector: 'app-return-list',
  imports: [RouterLink, Icon, Pagination],
  templateUrl: './return-list.html',
})
export class ReturnList implements OnInit {
  private readonly service = inject(ReturnService);
  private readonly auth = inject(AuthService);
  private readonly toast = inject(ToastService);
  private readonly router = inject(Router);

  readonly canCreate = this.auth.hasRole('Administrador');

  readonly returns = signal<ReturnOrder[]>([]);
  readonly currentPage = signal(1);
  readonly lastPage = signal(1);
  readonly loading = signal(true);

  ngOnInit(): void {
    this.load(1);
  }

  load(page: number): void {
    this.loading.set(true);
    this.service.list(page).subscribe({
      next: (res) => {
        this.returns.set(res.data);
        this.currentPage.set(res.meta.current_page);
        this.lastPage.set(res.meta.last_page);
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('No pudimos cargar las devoluciones.');
        this.loading.set(false);
      },
    });
  }

  open(r: ReturnOrder): void {
    this.router.navigate(['/returns', r.id]);
  }

  money(value: string): string {
    return new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(Number(value));
  }

  formatDate(iso: string | null): string {
    if (!iso) return '—';
    return new Date(iso).toLocaleDateString('es-HN', { day: '2-digit', month: '2-digit', year: 'numeric' });
  }
}
