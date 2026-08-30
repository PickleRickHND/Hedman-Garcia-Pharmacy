import { Component, computed, inject, input, OnInit, signal, ChangeDetectionStrategy } from '@angular/core';
import { FormControl, ReactiveFormsModule, Validators } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { AuthService } from '../../../core/auth/auth.service';
import { Invoice } from '../../../core/models/invoice.model';
import { ToastService } from '../../../shared/toast/toast.service';
import { InvoiceService } from '../invoice.service';

@Component({
  selector: 'app-invoice-detail',
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './invoice-detail.html',
  changeDetection: ChangeDetectionStrategy.Eager,
  styleUrl: './invoice-detail.scss',
})
export class InvoiceDetail implements OnInit {
  private readonly service = inject(InvoiceService);
  private readonly auth = inject(AuthService);
  private readonly toast = inject(ToastService);

  readonly id = input.required<string>();

  readonly invoice = signal<Invoice | null>(null);
  readonly loading = signal(true);
  readonly downloading = signal(false);
  readonly voiding = signal(false);
  readonly showVoidModal = signal(false);
  readonly voidReason = new FormControl('', {
    nonNullable: true,
    validators: [Validators.required, Validators.minLength(3), Validators.maxLength(255)],
  });

  readonly canVoid = computed(
    () => this.auth.hasRole('Administrador') && this.invoice() !== null && !this.invoice()!.is_voided,
  );

  ngOnInit(): void {
    this.service.get(Number(this.id())).subscribe({
      next: (res) => {
        this.invoice.set(res.data);
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('No pudimos cargar la factura.');
        this.loading.set(false);
      },
    });
  }

  downloadPdf(): void {
    const inv = this.invoice();
    if (!inv) return;
    this.downloading.set(true);
    this.service.downloadPdf(inv.id).subscribe({
      next: (blob) => {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${inv.invoice_number}.pdf`;
        a.click();
        URL.revokeObjectURL(url);
        this.downloading.set(false);
      },
      error: () => {
        this.downloading.set(false);
        this.toast.error('No se pudo descargar el PDF.');
      },
    });
  }

  confirmVoid(): void {
    if (this.voidReason.invalid) {
      this.voidReason.markAsTouched();
      return;
    }
    const inv = this.invoice();
    if (!inv) return;
    this.voiding.set(true);
    this.service.void(inv.id, this.voidReason.value.trim()).subscribe({
      next: (res) => {
        this.invoice.set(res.data);
        this.voiding.set(false);
        this.showVoidModal.set(false);
        this.toast.success(`Factura ${res.data.invoice_number} anulada.`);
      },
      error: (err) => {
        this.voiding.set(false);
        this.toast.error(err?.error?.message ?? 'No se pudo anular la factura.');
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
