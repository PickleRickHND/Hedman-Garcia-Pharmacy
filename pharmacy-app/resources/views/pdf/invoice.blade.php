<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 11pt;
            color: #1c1917;
            line-height: 1.5;
            padding: 40px;
        }
        .header { display: table; width: 100%; margin-bottom: 30px; border-bottom: 2px solid #16a34a; padding-bottom: 15px; }
        .header-left, .header-right { display: table-cell; vertical-align: top; }
        .header-right { text-align: right; }
        .brand { font-size: 16pt; font-weight: bold; color: #14532d; letter-spacing: -0.5px; }
        .tagline { color: #57534e; font-size: 9pt; margin-top: 2px; }
        .doc-title { font-size: 18pt; font-weight: bold; color: #1c1917; letter-spacing: -0.5px; }
        .doc-number { font-family: 'Courier', monospace; color: #57534e; font-size: 10pt; margin-top: 2px; }

        .parties { display: table; width: 100%; margin-bottom: 20px; }
        .party { display: table-cell; width: 50%; vertical-align: top; padding-right: 10px; }
        .label { font-size: 8pt; text-transform: uppercase; letter-spacing: 0.5px; color: #78716c; font-weight: bold; margin-bottom: 4px; }
        .value { font-size: 11pt; color: #1c1917; }
        .value.strong { font-weight: bold; }

        table.items { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        table.items thead { background: #f5f5f4; }
        table.items th { padding: 10px 8px; text-align: left; font-size: 9pt; text-transform: uppercase; letter-spacing: 0.3px; color: #44403c; border-bottom: 1px solid #d6d3d1; }
        table.items th.num { text-align: right; }
        table.items td { padding: 10px 8px; font-size: 10pt; border-bottom: 1px solid #e7e5e4; vertical-align: top; }
        table.items td.num { text-align: right; }
        .item-name { font-weight: 600; color: #1c1917; }
        .item-sku { font-family: 'Courier', monospace; font-size: 8pt; color: #78716c; margin-top: 1px; }

        .totals { width: 280px; margin-left: auto; margin-top: 10px; }
        .totals-row { display: table; width: 100%; }
        .totals-label, .totals-value { display: table-cell; padding: 4px 0; font-size: 11pt; }
        .totals-label { color: #57534e; }
        .totals-value { text-align: right; color: #1c1917; font-weight: 500; }
        .totals-total { border-top: 2px solid #1c1917; padding-top: 8px !important; margin-top: 6px; }
        .totals-total .totals-label, .totals-total .totals-value { font-size: 13pt; font-weight: bold; padding: 8px 0 0 0; }
        .totals-total .totals-value { color: #16a34a; }

        .footer { margin-top: 40px; padding-top: 15px; border-top: 1px solid #e7e5e4; font-size: 8pt; color: #78716c; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <div class="brand">Hedman Garcia Pharmacy</div>
            <div class="tagline">Tegucigalpa, Honduras</div>
        </div>
        <div class="header-right">
            <div class="doc-title">FACTURA</div>
            <div class="doc-number">{{ $invoice->invoice_number }}</div>
            <div class="doc-number">{{ $invoice->issued_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="parties">
        <div class="party">
            <div class="label">Cliente</div>
            <div class="value strong">{{ $invoice->customer_name }}</div>
            @if ($invoice->customer_rtn)
                <div class="value">RTN: {{ $invoice->customer_rtn }}</div>
            @endif
        </div>
        <div class="party">
            <div class="label">Método de pago</div>
            <div class="value strong">{{ $invoice->paymentMethod?->name ?? '—' }}</div>
            <div class="value" style="color:#78716c; margin-top: 6px;">Vendedor: {{ $invoice->seller?->name ?? '—' }}</div>
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Producto</th>
                <th class="num">Precio</th>
                <th class="num">Cant.</th>
                <th class="num">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->product_name }}</div>
                        <div class="item-sku">{{ $item->product_sku }}</div>
                    </td>
                    <td class="num">L. {{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="num">{{ $item->quantity }}</td>
                    <td class="num">L. {{ number_format((float) $item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <div class="totals-label">Subtotal</div>
            <div class="totals-value">L. {{ number_format((float) $invoice->subtotal, 2) }}</div>
        </div>
        <div class="totals-row">
            <div class="totals-label">ISV (15%)</div>
            <div class="totals-value">L. {{ number_format((float) $invoice->tax, 2) }}</div>
        </div>
        <div class="totals-row totals-total">
            <div class="totals-label">Total</div>
            <div class="totals-value">L. {{ number_format((float) $invoice->total, 2) }}</div>
        </div>
    </div>

    <div class="footer">
        Gracias por su compra · Hedman Garcia Pharmacy
    </div>
</body>
</html>
