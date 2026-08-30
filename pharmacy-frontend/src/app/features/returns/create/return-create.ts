import { Component, computed, inject, signal, ChangeDetectionStrategy } from '@angular/core';
import { toSignal } from '@angular/core/rxjs-interop';
import { FormControl, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { Invoice, InvoiceItem } from '../../../core/models/invoice.model';
import { CreateReturnPayload } from '../../../core/models/return.model';
import { Icon } from '../../../shared/icon/icon';
import { ToastService } from '../../../shared/toast/toast.service';
import { InvoiceService } from '../../billing/invoice.service';
import { ReturnService } from '../return.service';

interface ReturnLine {
  item: InvoiceItem;
  quantity: number;
  restock: boolean;
}

@Component({
  selector: 'app-return-create',
  imports: [ReactiveFormsModule, RouterLink, Icon],
  templateUrl: './return-create.html',
  changeDetection: ChangeDetectionStrategy.Eager,
  styleUrl: './return-create.scss',
})
export class ReturnCreate {
  private readonly invoiceService = inject(InvoiceService);
  private readonly returnService = inject(ReturnService);
  private readonly toast = inject(ToastService);
  private readonly router = inject(Router);

  readonly searchControl = new FormControl('', { nonNullable: true });
  readonly searchResults = signal<Invoice[]>([]);
  readonly searching = signal(false);

  readonly invoice = signal<Invoice | null>(null);
  readonly lines = signal<ReturnLine[]>([]);
  readonly reason = new FormControl('', {
    nonNullable: true,
    validators: [Validators.required, Validators.minLength(5), Validators.maxLength(255)],
  });
  /** Señal del texto del motivo para que los computed reaccionen a su cambio. */
  private readonly reasonText = toSignal(this.reason.valueChanges, { initialValue: '' });
  readonly submitting = signal(false);

  readonly totalRefund = computed(() =>
    this.lines().reduce((sum, l) => {
      if (l.quantity <= 0) return sum;
      const unit = l.item.quantity > 0 ? Number(l.item.subtotal) / l.item.quantity : Number(l.item.unit_price);
      return sum + unit * l.quantity;
    }, 0),
  );

  readonly canProcess = computed(
    () =>
      this.invoice() !== null &&
      this.reasonText().trim().length >= 5 &&
      this.lines().some((l) => l.quantity > 0),
  );

  searchInvoices(): void {
    const term = this.searchControl.value.trim();
    if (!term) return;
    this.searching.set(true);
    this.invoiceService.list({ search: term, status: 'emitted', per_page: 6 }).subscribe({
      next: (res) => {
        this.searchResults.set(res.data);
        this.searching.set(false);
      },
      error: () => this.searching.set(false),
    });
  }

  selectInvoice(inv: Invoice): void {
    this.invoiceService.get(inv.id).subscribe((res) => {
      this.invoice.set(res.data);
      this.lines.set(res.data.items.map((item) => ({ item, quantity: 0, restock: true })));
      this.searchResults.set([]);
      this.searchControl.setValue('');
    });
  }

  changeQty(index: number, value: string): void {
    this.lines.update((lines) =>
      lines.map((l, i) => {
        if (i !== index) return l;
        const qty = Math.max(0, Math.min(l.item.quantity, Math.floor(Number(value) || 0)));
        return { ...l, quantity: qty };
      }),
    );
  }

  toggleRestock(index: number): void {
    this.lines.update((lines) => lines.map((l, i) => (i === index ? { ...l, restock: !l.restock } : l)));
  }

  reset(): void {
    this.invoice.set(null);
    this.lines.set([]);
    this.reason.setValue('');
  }

  process(): void {
    if (!this.canProcess() || this.submitting()) return;
    this.submitting.set(true);

    const payload: CreateReturnPayload = {
      invoice_id: this.invoice()!.id,
      reason: this.reason.value.trim(),
      items: this.lines()
        .filter((l) => l.quantity > 0)
        .map((l) => ({ invoice_item_id: l.item.id, quantity: l.quantity, restock: l.restock })),
    };

    this.returnService.create(payload).subscribe({
      next: (res) => {
        this.toast.success(`Devolución ${res.data.return_number} procesada.`);
        this.router.navigate(['/returns', res.data.id]);
      },
      error: (err) => {
        this.submitting.set(false);
        this.toast.error(err?.error?.message ?? 'No se pudo procesar la devolución.');
      },
    });
  }

  money(value: string | number): string {
    return new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(Number(value));
  }
}
