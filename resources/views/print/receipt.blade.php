<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->invoice_number }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background-color: #fff;
            width: 58mm; /* Standard thermal printer width */
            margin: 0 auto;
            padding: 10px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mt-1 { margin-top: 5px; }
        .mt-2 { margin-top: 10px; }
        .divider {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 2px 0;
            vertical-align: top;
        }
        .header {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .info {
            font-size: 10px;
            color: #333;
        }
        .item-name {
            display: block;
        }
        .totals td {
            font-size: 11px;
        }
        .totals .grand-total td {
            font-size: 13px;
            font-weight: bold;
        }
        @media print {
            body { max-width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Print Button (Hidden during print) -->
    <div class="no-print text-center mb-2">
        <button onclick="window.print()" style="padding: 10px 20px; font-weight:bold; cursor:pointer; background:#4F46E5; color:white; border:none; border-radius:5px;">Print Struk</button>
        <button onclick="window.close()" style="padding: 10px 20px; margin-left:10px; cursor:pointer; background:#e5e7eb; color:#374151; border:none; border-radius:5px;">Tutup</button>
    </div>

    <!-- Store Info -->
    <div class="text-center mb-2">
        <div class="header">{{ \App\Models\Setting::get('store_name', 'My POS') }}</div>
        <div class="info">{{ \App\Models\Setting::get('store_address', '') }}</div>
        <div class="info">{{ \App\Models\Setting::get('store_phone', '') }}</div>
    </div>

    <div class="divider"></div>

    <!-- Transaction Info -->
    <div class="info mb-1">
        <table width="100%">
            <tr>
                <td>No</td>
                <td>: {{ $transaction->invoice_number }}</td>
            </tr>
            <tr>
                <td>Tgl</td>
                <td>: {{ $transaction->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>: {{ $transaction->user->name ?? 'Kasir' }}</td>
            </tr>
            @if($transaction->payment)
            <tr>
                <td>Metode</td>
                <td>: {{ strtoupper($transaction->payment->method) }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="divider"></div>

    <!-- Items -->
    <table class="mb-1">
        @foreach($transaction->items as $item)
            <tr>
                <td colspan="3" class="item-name">{{ $item->product_name }}</td>
            </tr>
            <tr>
                <td width="30%">{{ $item->quantity }}x</td>
                <td width="30%" class="text-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                <td width="40%" class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <!-- Totals -->
    <table class="totals mb-2">
        <tr>
            <td>Subtotal</td>
            <td class="text-right">{{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($transaction->discount_amount > 0)
        <tr>
            <td>Diskon</td>
            <td class="text-right">-{{ number_format($transaction->discount_amount, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($transaction->tax_amount > 0)
        <tr>
            <td>Pajak (PPN)</td>
            <td class="text-right">{{ number_format($transaction->tax_amount, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="grand-total">
            <td class="mt-1 font-bold">Total</td>
            <td class="mt-1 text-right font-bold">{{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
        </tr>
        
        <!-- Payment details for cash -->
        @if($transaction->payment && $transaction->payment->method === 'cash')
            <tr>
                <td class="mt-1">Tunai</td>
                <td class="text-right mt-1">{{ number_format($transaction->payment->cash_received, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td class="text-right">{{ number_format($transaction->payment->change_amount, 0, ',', '.') }}</td>
            </tr>
        @endif
    </table>

    <div class="divider"></div>

    <!-- Footer -->
    <div class="text-center info mt-2">
        <div>{{ \App\Models\Setting::get('receipt_footer', 'Terima Kasih!') }}</div>
        <div style="margin-top:2px;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</div>
    </div>

</body>
</html>
