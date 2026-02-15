<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Order {{ $order->id_order }}</title>
    <style>
        @page { size: 80mm auto; margin: 4mm; }
        body {
            margin: 0;
            font-family: "Courier New", monospace;
            font-size: 12px;
            color: #111;
        }
        .wrap { width: 72mm; margin: 0 auto; }
        .center { text-align: center; }
        .bold { font-weight: 700; }
        .line { border-top: 1px dashed #111; margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 2px 0; vertical-align: top; }
        .right { text-align: right; }
        .small { font-size: 10px; }
        .actions {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin: 12px 0;
            font-family: sans-serif;
        }
        .btn {
            border: 1px solid #bbb;
            border-radius: 6px;
            background: #f8f8f8;
            padding: 6px 10px;
            cursor: pointer;
            font-size: 12px;
        }
        @media print {
            .actions { display: none; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="actions">
        <button class="btn" onclick="window.print()">Print</button>
        <button class="btn" onclick="window.close()">Tutup</button>
    </div>

    <div class="center bold" style="font-size:16px;">Kantin Sakina</div>
    <div class="center bold">STRUK PEMBAYARAN</div>
    <div class="line"></div>

    <div>Kode Order: {{ $order->id_order }}</div>
    <div>Waktu: {{ $order->waktu_order }}</div>
    <div>Kasir: {{ $order->kasirUser?->username ?? '-' }}</div>
    <div>Meja: {{ $order->meja }} / Pelanggan: {{ $order->pelanggan }}</div>
    <div>Kios: {{ $order->nama_kios }}</div>

    <div class="line"></div>

    <table>
        <thead>
        <tr>
            <th>Menu</th>
            <th class="right">Qty</th>
            <th class="right">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($rows as $row)
            @php $item = $row['item']; @endphp
            <tr>
                <td>{{ $row['menu']?->nama ?? '-' }}</td>
                <td class="right">{{ $item->jumlah }}</td>
                <td class="right">{{ number_format($row['subtotal'], 0, ',', '.') }}</td>
            </tr>
            @if(!empty($item->catatan_order))
                <tr>
                    <td colspan="3" class="small">Catatan: {{ $item->catatan_order }}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>

    <div class="line"></div>
    <table>
        <tr>
            <td>Total</td>
            <td class="right">{{ number_format($total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Diskon</td>
            <td class="right">-{{ number_format($diskon, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>PPN</td>
            <td class="right">{{ number_format($totalPpn, 0, ',', '.') }}</td>
        </tr>
        <tr class="bold">
            <td>Grand Total</td>
            <td class="right">{{ number_format($grandTotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bayar</td>
            <td class="right">{{ number_format($bayar, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembalian</td>
            <td class="right">{{ number_format($kembalian, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="line"></div>
    <div class="center small bold">Harga Sudah Termasuk Pajak</div>
    <div class="center small bold">Terima kasih atas kunjungan Anda</div>
</div>
@if(!empty($autoPrint))
<script>
    window.addEventListener('load', function () {
        setTimeout(function () {
            window.print();
        }, 120);
    });
</script>
@endif
</body>
</html>
