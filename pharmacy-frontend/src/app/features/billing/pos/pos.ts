import { Component, computed, inject, OnInit, signal } from '@angular/core';
import { FormControl, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { debounceTime, distinctUntilChanged } from 'rxjs';
import { AuthService } from '../../../core/auth/auth.service';
import { Product } from '../../../core/models/product.model';
import { CreateInvoicePayload, PaymentMethod } from '../../../core/models/invoice.model';
import { Icon } from '../../../shared/icon/icon';
import { ToastService } from '../../../shared/toast/toast.service';
import { ProductService } from '../../products/product.service';
import { InvoiceService } from '../invoice.service';

interface CartLine {
  product: Product;
  quantity: number;
  discount_percent: number;
}

const ISV_RATE = 0.15;

@Component({
  selector: 'app-pos',
  imports: [ReactiveFormsModule, Icon],
  templateUrl: './pos.html',
  styleUrl: './pos.scss',
})
export class Pos implements OnInit {
  private readonly productService = inject(ProductService);
  private readonly invoiceService = inject(InvoiceService);
  private readonly auth = inject(AuthService);
  private readonly toast = inject(ToastService);
  private readonly router = inject(Router);

  readonly canDiscount = this.auth.hasRole('Administrador');

  readonly searchControl = new FormControl('', { nonNullable: true });
  readonly searchResults = signal<Product[]>([]);
  readonly cart = signal<CartLine[]>([]);

  readonly customerName = new FormControl('Consumidor Final', {
    nonNullable: true,
    validators: [Validators.required, Validators.minLength(2), Validators.maxLength(100)],
  });
  readonly customerRtn = new FormControl('', { nonNullable: true });
  readonly paymentMethodId = signal<number | null>(null);
  readonly paymentMethods = signal<PaymentMethod[]>([]);
  readonly submitting = signal(false);

  readonly totals = computed(() => {
    let gross = 0;
    let discount = 0;
    for (const line of this.cart()) {
      const lineGross = Number(line.product.price) * line.quantity;
      gross += lineGross;
      discount += lineGross * (line.discount_percent / 100);
    }
    const total = gross - discount;
    const subtotal = total / (1 + ISV_RATE);
    return { gross, discount, subtotal, tax: total - subtotal, total };
  });

  readonly canIssue = computed(
    () => this.cart().length > 0 && this.paymentMethodId() !== null && this.customerName.valid,
  );

  ngOnInit(): void {
    this.invoiceService.paymentMethods().subscribe((res) => {
      this.paymentMethods.set(res.data);
      if (res.data.length) this.paymentMethodId.set(res.data[0].id);
    });

    this.searchControl.valueChanges
      .pipe(debounceTime(300), distinctUntilChanged())
      .subscribe((term) => this.searchProducts(term));
  }

  searchProducts(term: string): void {
    if (term.trim().length < 1) {
      this.searchResults.set([]);
      return;
    }
    this.productService.list({ search: term, per_page: 8 }).subscribe((res) => {
      this.searchResults.set(res.data);
    });
  }

  addProduct(product: Product): void {
    if (product.stock <= 0) {
      this.toast.error(`«${product.name}» está agotado.`);
      return;
    }
    this.cart.update((lines) => {
      const existing = lines.find((l) => l.product.id === product.id);
      if (existing) {
        if (existing.quantity >= product.stock) {
          this.toast.error(`Solo hay ${product.stock} unidades de «${product.name}».`);
          return lines;
        }
        return lines.map((l) =>
          l.product.id === product.id ? { ...l, quantity: l.quantity + 1 } : l,
        );
      }
      return [...lines, { product, quantity: 1, discount_percent: 0 }];
    });
    this.searchControl.setValue('');
    this.searchResults.set([]);
  }

  changeQty(index: number, delta: number): void {
    this.cart.update((lines) =>
      lines.map((l, i) => {
        if (i !== index) return l;
        const next = l.quantity + delta;
        if (next < 1) return l;
        if (next > l.product.stock) {
          this.toast.error(`Solo hay ${l.product.stock} unidades de «${l.product.name}».`);
          return l;
        }
        return { ...l, quantity: next };
      }),
    );
  }

  setDiscount(index: number, value: string): void {
    const pct = Math.max(0, Math.min(100, Number(value) || 0));
    this.cart.update((lines) => lines.map((l, i) => (i === index ? { ...l, discount_percent: pct } : l)));
  }

  removeLine(index: number): void {
    this.cart.update((lines) => lines.filter((_, i) => i !== index));
  }

  clearCart(): void {
    this.cart.set([]);
  }

  lineSubtotal(line: CartLine): number {
    const gross = Number(line.product.price) * line.quantity;
    return gross - gross * (line.discount_percent / 100);
  }

  issue(): void {
    if (!this.canIssue() || this.submitting()) return;
    this.submitting.set(true);

    const payload: CreateInvoicePayload = {
      customer_name: this.customerName.value.trim(),
      customer_rtn: this.customerRtn.value.trim() === '' ? null : this.customerRtn.value.trim(),
      customer_id: null,
      payment_method_id: this.paymentMethodId()!,
      items: this.cart().map((l) => ({
        product_id: l.product.id,
        quantity: l.quantity,
        discount_percent: this.canDiscount ? l.discount_percent : 0,
      })),
    };

    this.invoiceService.create(payload).subscribe({
      next: (res) => {
        this.toast.success(`Factura ${res.data.invoice_number} emitida.`);
        this.router.navigate(['/invoices', res.data.id]);
      },
      error: (err) => {
        this.submitting.set(false);
        this.toast.error(err?.error?.message ?? 'No se pudo emitir la factura.');
      },
    });
  }

  money(value: number): string {
    return new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(value);
  }
}
