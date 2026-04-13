<?php

declare(strict_types=1);

namespace App\Livewire\Billing;

use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Services\BillingService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use RuntimeException;

#[Layout('layouts.app')]
#[Title('Nueva factura')]
class NewInvoice extends Component
{
    public string $search = '';
    public string $customer_name = '';
    public ?string $customer_rtn = null;
    public ?int $payment_method_id = null;
    public ?int $customer_id = null;
    public string $customerSearch = '';
    public bool $showCustomerDropdown = false;

    /**
     * @var array<int, array{product_id:int, sku:string, name:string, unit_price:float, quantity:int, max:int}>
     */
    public array $items = [];

    public ?string $flashMessage = null;
    public ?string $flashVariant = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Cajero']), 403);
    }

    public function addProduct(int $productId): void
    {
        $product = Product::find($productId);

        if (! $product) {
            return;
        }

        if ($product->stock === 0) {
            $this->flashVariant = 'danger';
            $this->flashMessage = "«{$product->name}» sin stock disponible.";
            return;
        }

        foreach ($this->items as $index => $item) {
            if ($item['product_id'] === $product->id) {
                if ($item['quantity'] < $product->stock) {
                    $this->items[$index]['quantity']++;
                }
                $this->search = '';
                return;
            }
        }

        $this->items[] = [
            'product_id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'unit_price' => (float) $product->price,
            'quantity' => 1,
            'max' => $product->stock,
            'discount_percent' => 0,
        ];

        $this->search = '';
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function incrementItem(int $index): void
    {
        if (! isset($this->items[$index])) {
            return;
        }

        if ($this->items[$index]['quantity'] < $this->items[$index]['max']) {
            $this->items[$index]['quantity']++;
        }
    }

    public function decrementItem(int $index): void
    {
        if (! isset($this->items[$index])) {
            return;
        }

        if ($this->items[$index]['quantity'] > 1) {
            $this->items[$index]['quantity']--;
        } else {
            $this->removeItem($index);
        }
    }

    public function selectCustomer(int $customerId): void
    {
        $customer = Customer::find($customerId);
        if ($customer) {
            $this->customer_id = $customer->id;
            $this->customer_name = $customer->name;
            $this->customer_rtn = $customer->rtn;
            $this->customerSearch = '';
            $this->showCustomerDropdown = false;
        }
    }

    public function clearCustomer(): void
    {
        $this->customer_id = null;
        $this->customer_name = '';
        $this->customer_rtn = null;
        $this->customerSearch = '';
    }

    public function updatedCustomerSearch(): void
    {
        $this->showCustomerDropdown = mb_strlen($this->customerSearch) >= 2;
    }

    #[Computed]
    public function customerResults()
    {
        if (blank($this->customerSearch) || mb_strlen($this->customerSearch) < 2) {
            return collect();
        }

        return Customer::search($this->customerSearch)
            ->limit(5)
            ->get();
    }

    public function clearCart(): void
    {
        $this->items = [];
        $this->customer_name = '';
        $this->customer_rtn = null;
        $this->customer_id = null;
        $this->customerSearch = '';
    }

    public function issue(BillingService $billing)
    {
        $this->validate([
            'customer_name' => ['required', 'string', 'min:2', 'max:100'],
            'customer_rtn' => ['nullable', 'string', 'max:20'],
            'payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ], attributes: [
            'customer_name' => 'nombre del cliente',
            'payment_method_id' => 'método de pago',
            'items' => 'carrito',
        ]);

        try {
            $invoice = $billing->issueInvoice(
                seller: auth()->user(),
                lineItems: array_map(
                    fn ($i) => [
                        'product_id' => $i['product_id'],
                        'quantity' => $i['quantity'],
                        'discount_percent' => (float) ($i['discount_percent'] ?? 0),
                    ],
                    $this->items,
                ),
                customer: [
                    'customer_name' => $this->customer_name,
                    'customer_rtn' => $this->customer_rtn,
                    'customer_id' => $this->customer_id,
                ],
                paymentMethod: PaymentMethod::findOrFail($this->payment_method_id),
            );
        } catch (RuntimeException $e) {
            $this->flashVariant = 'danger';
            $this->flashMessage = $e->getMessage();
            return;
        }

        session()->flash('billing.flash', "Factura {$invoice->invoice_number} emitida por L. ".number_format((float) $invoice->total, 2));

        return redirect()->route('billing.show', $invoice);
    }

    // -----------------------------------------------------------------------
    // Computed — derivados
    // -----------------------------------------------------------------------

    #[Computed]
    public function searchResults()
    {
        if (blank($this->search) || mb_strlen($this->search) < 2) {
            return collect();
        }

        return Product::query()
            ->search($this->search)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->limit(8)
            ->get();
    }

    #[Computed]
    public function totals(): array
    {
        $isvRate = (float) config('pharmacy.tax.isv_rate', 0.15);
        $maxDiscount = (float) config('pharmacy.billing.max_discount_percent', 30);
        $grossTotal = 0.0;
        $discountTotal = 0.0;

        foreach ($this->items as $item) {
            $lineGross = $item['unit_price'] * $item['quantity'];
            $dp = min(max((float) ($item['discount_percent'] ?? 0), 0), $maxDiscount);
            $lineDiscount = round($lineGross * ($dp / 100), 2);

            $grossTotal += $lineGross;
            $discountTotal += $lineDiscount;
        }

        $afterDiscount = round($grossTotal - $discountTotal, 2);
        $subtotal = round($afterDiscount / (1 + $isvRate), 2);
        $tax = round($afterDiscount - $subtotal, 2);
        $total = $afterDiscount;

        return [
            'subtotal' => $subtotal,
            'discount' => round($discountTotal, 2),
            'tax' => $tax,
            'total' => $total,
        ];
    }

    public function canApplyDiscount(): bool
    {
        return auth()->user()->hasRole('Administrador');
    }

    public function render(): View
    {
        return view('livewire.billing.new-invoice', [
            'paymentMethods' => PaymentMethod::active()->orderBy('name')->get(),
        ]);
    }
}
