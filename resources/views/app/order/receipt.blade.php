<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Order {{ $order->id_order }}</title>
    <style>
        /* Samakan gaya cetak dengan legacy (struk thermal 60mm). */
        @page { size: 58mm auto; margin: 1.5mm; }
        body {
            margin: 0;
            font-family: "Courier New", monospace;
            font-size: 12px;
            color: #111;
            font-weight: 700;
            width: 58mm;
            overflow: hidden;
        }
        .wrap {
            width: 54mm;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .center { text-align: center; }
        .bold { font-weight: 900; }
        .line { border-top: 1px dashed #111; margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 2px 0; vertical-align: top; }
        .right { text-align: right; }
        .center { text-align: center; }
        .small { font-size: 10px; font-style: italic; font-weight: 400; }
        .grand-total-line {
            border-top: 2px solid #111;
            margin-top: 4px;
            padding-top: 4px;
        }
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
            /* Tombol aksi disembunyikan saat cetak */
            .actions { display: none; }
            html, body {
                width: 58mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body>
<div class="wrap">
    {{-- Tombol utilitas saat preview struk --}}
    <div class="actions">
        <button class="btn" onclick="window.print()">Print</button>
        <button class="btn" onclick="window.close()">Tutup</button>
    </div>

    {{-- Header struk --}}
    <div class="center bold" style="font-size:16px;">Kantin Sakina</div>
    <div class="center bold">Struk Pembayaran</div>
    <div class="line"></div>

    {{-- Informasi order --}}
    <div>Waktu Order: {{ $order->waktu_order }}</div>
    <div>Kode Order: {{ $order->id_order }}</div>
    <div>Meja: {{ $order->meja }} / Pelanggan: {{ $order->pelanggan }}</div>
    <div>Kios: {{ $order->nama_kios }}</div>

    <div class="line"></div>

    {{-- Daftar item yang dibeli --}}
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
                <td class="center">{{ $item->jumlah }}</td>
                <td class="right">{{ number_format($row['subtotal'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="small">@ {{ number_format($row['harga_jual'], 0, ',', '.') }}</td>
                <td colspan="2" class="small">
                    @if(!empty($item->catatan_order))
                        Catatan: {{ $item->catatan_order }}
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{-- Ringkasan nominal pembayaran --}}
    <div class="line"></div>
    <table>
        <tr>
            <td>Diskon</td>
            <td class="right">-{{ number_format($diskon, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total</td>
            <td class="right">{{ number_format(max(0, $total - $diskon), 0, ',', '.') }}</td>
        </tr>
        <tr class="bold grand-total-line">
            <td>Grand Total</td>
            <td class="right">{{ number_format($grandTotal, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- Footer struk --}}
    <div class="center small bold">Harga Sudah Termasuk Pajak</div>
    <div class="center small bold">TERIMA KASIH ATAS KUNJUNGAN ANDA!</div>
</div>
@if(!empty($autoPrint))
<script>
    // Auto print dipakai saat struk dipanggil dari mode popup.
    window.addEventListener('load', function () {
        setTimeout(function () {
            window.print();
        }, 120);
    });
</script>
@endif
</body>
</html>
