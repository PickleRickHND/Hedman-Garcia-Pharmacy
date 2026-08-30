import { Component, inject, OnInit, signal, ChangeDetectionStrategy } from '@angular/core';
import { FormControl, ReactiveFormsModule, Validators } from '@angular/forms';
import { CashRegister } from '../../core/models/cash-register.model';
import { Icon } from '../../shared/icon/icon';
import { ToastService } from '../../shared/toast/toast.service';
import { CashRegisterService } from './cash-register.service';

@Component({
  selector: 'app-cash-register-index',
  imports: [ReactiveFormsModule, Icon],
  templateUrl: './cash-register-index.html',
  changeDetection: ChangeDetectionStrategy.Eager,
  styleUrl: './cash-register-index.scss',
})
export class CashRegisterIndex implements OnInit {
  private readonly service = inject(CashRegisterService);
  private readonly toast = inject(ToastService);

  readonly current = signal<CashRegister | null>(null);
  readonly history = signal<CashRegister[]>([]);
  readonly loading = signal(true);
  readonly busy = signal(false);
  readonly showCloseModal = signal(false);

  readonly openingAmount = new FormControl(0, {
    nonNullable: true,
    validators: [Validators.required, Validators.min(0)],
  });
  readonly actualAmount = new FormControl(0, {
    nonNullable: true,
    validators: [Validators.required, Validators.min(0)],
  });
  readonly closeNotes = new FormControl('', { nonNullable: true });

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading.set(true);
    this.service.current().subscribe({
      next: (res) => {
        this.current.set(res.data);
        this.loadHistory();
      },
      error: () => {
        this.toast.error('No pudimos cargar la caja.');
        this.loading.set(false);
      },
    });
  }

  private loadHistory(): void {
    this.service.list(1).subscribe({
      next: (res) => {
        this.history.set(res.data.filter((r) => !r.is_open));
        this.loading.set(false);
      },
      error: () => this.loading.set(false),
    });
  }

  open(): void {
    if (this.openingAmount.invalid || this.busy()) return;
    this.busy.set(true);
    this.service.open(this.openingAmount.value).subscribe({
      next: () => {
        this.toast.success('Caja abierta.');
        this.busy.set(false);
        this.openingAmount.setValue(0);
        this.load();
      },
      error: (err) => {
        this.busy.set(false);
        this.toast.error(err?.error?.message ?? 'No se pudo abrir la caja.');
      },
    });
  }

  close(): void {
    const reg = this.current();
    if (!reg || this.actualAmount.invalid || this.busy()) return;
    this.busy.set(true);
    this.service.close(reg.id, this.actualAmount.value, this.closeNotes.value.trim() || null).subscribe({
      next: (res) => {
        const diff = Number(res.data.difference ?? 0);
        const msg =
          diff === 0
            ? 'Caja cerrada. Arqueo cuadrado.'
            : `Caja cerrada. Diferencia: ${this.money(String(diff))}.`;
        this.toast[diff === 0 ? 'success' : 'info'](msg);
        this.busy.set(false);
        this.showCloseModal.set(false);
        this.actualAmount.setValue(0);
        this.closeNotes.setValue('');
        this.load();
      },
      error: (err) => {
        this.busy.set(false);
        this.toast.error(err?.error?.message ?? 'No se pudo cerrar la caja.');
      },
    });
  }

  money(value: string | null): string {
    return new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(Number(value ?? 0));
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
