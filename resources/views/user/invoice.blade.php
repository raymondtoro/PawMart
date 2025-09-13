<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $transaction->id ?? 'N/A' }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        h1 { text-align: center; margin-bottom: 5px; }
        .invoice-info { margin-bottom: 20px; }
        .invoice-info p { margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f3f3f3; }
        tfoot td { font-weight: bold; }
        .note { font-style: italic; font-size: 13px; margin-top: 5px; }
        .right { text-align: right; }
        .grand-total { font-size: 16px; background: #f3f3f3; }
    </style>
</head>
<body>
    <h1>Invoice #{{ $transaction->id ?? 'N/A' }}</h1>

    <div class="invoice-info">
        <p><strong>Tanggal:</strong> {{ $transaction->tanggal_transaksi ?? '-' }}</p>
        <p><strong>Status:</strong> {{ isset($transaction->status_transaksi) ? ucfirst($transaction->status_transaksi) : '-' }}</p>
        <p><strong>Metode:</strong> {{ $transaction->metode_transaksi ?? '-' }}</p>
    </div>

    <h3>Detail Produk</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $subtotal = 0; @endphp
            @forelse ($transaction->order->orderItems ?? [] as $item)
                @php $itemSubtotal = ($item->jumlah * $item->harga_saat_beli); @endphp
                @php $subtotal += $itemSubtotal; @endphp
                <tr>
                    <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                    <td>{{ $item->jumlah ?? 0 }}</td>
                    <td>Rp {{ isset($item->harga_saat_beli) ? number_format($item->harga_saat_beli, 0, ',', '.') : '0' }}</td>
                    <td>Rp {{ number_format($itemSubtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada produk</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="right">Subtotal Produk</td>
                <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" class="right">Ongkir</td>
                <td>Rp {{ number_format($transaction->ongkir ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr class="grand-total">
                <td colspan="3" class="right">Grand Total</td>
                <td>Rp {{ number_format($subtotal + ($transaction->ongkir ?? 0), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <p class="note">*Grand total sudah termasuk ongkir.*</p>
</body>
</html>
