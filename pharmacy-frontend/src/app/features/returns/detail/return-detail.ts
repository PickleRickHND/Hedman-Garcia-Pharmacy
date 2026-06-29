import { Component, inject, input, OnInit, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { ReturnOrder } from '../../../core/models/return.model';
import { ToastService } from '../../../shared/toast/toast.service';
import { ReturnService } from '../return.service';

@Component({
  selector: 'app-return-detail',
  imports: [RouterLink],
  templateUrl: './return-detail.html',
  styleUrl: './return-detail.scss',
})
export class ReturnDetail implements OnInit {
  private readonly service = inject(ReturnService);
  private readonly toast = inject(ToastService);

  readonly id = input.required<string>();
  readonly order = signal<ReturnOrder | null>(null);
  readonly loading = signal(true);

  ngOnInit(): void {
    this.service.get(Number(this.id())).subscribe({
      next: (res) => {
        this.order.set(res.data);
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('No pudimos cargar la devolución.');
        this.loading.set(false);
      },
    });
  }

  money(value: string): string {
    return new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(Number(value));
  }

  formatDate(iso: string | null): string {
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
