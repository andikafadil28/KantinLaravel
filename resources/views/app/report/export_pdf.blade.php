<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle }}</title>
    <style>
        @page { margin: 24px 20px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1f2937;
        }
        .title {
            text-align: center;
            background: #0b5394;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            padding: 8px 10px;
        }
        .subtitle {
            text-align: center;
            background: #d9e1f2;
            font-weight: 700;
            font-size: 12px;
            padding: 6px 10px;
            margin-bottom: 8px;
        }
        .meta {
            margin-bottom: 10px;
            line-height: 1.45;
        }
        .meta strong {
            display: inline-block;
            width: 100px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 5px 6px;
            word-wrap: break-word;
        }
        th {
            background: #1f4e78;
            color: #fff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
        }
        tr:nth-child(even) td {
            background: #f8fafc;
        }
        .num {
            text-align: right;
        }
        .total-row td {
            background: #fff2cc !important;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="title">SAKINA KANTIN - EXPORT DATA</div>
    <div class="subtitle">{{ $reportTitle }}</div>

    <div class="meta">
        <div><strong>Periode</strong>: {{ $periodLabel }}</div>
        <div><strong>Filter Toko</strong>: {{ $kiosLabel }}</div>
        <div><strong>Waktu Tarik</strong>: {{ $generatedAt }}</div>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($header as $h)
                    <th>{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $idx => $cell)
                        @php
                            $isMoney = in_array($idx, $moneyColumnIndexes, true);
                            $isCount = in_array($idx, $countColumnIndexes, true);
                        @endphp
                        <td class="{{ $isMoney || $isCount ? 'num' : '' }}">
                            @if($isMoney)
                                Rp {{ number_format((float) $cell, 2, ',', '.') }}
                            @elseif($isCount)
                                {{ number_format((float) $cell, 0, ',', '.') }}
                            @else
                                {{ $cell }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($header) }}" style="text-align: center;">Tidak ada data.</td>
                </tr>
            @endforelse

            <tr class="total-row">
                @foreach($header as $idx => $h)
                    @if($idx === 0)
                        <td>JUMLAH</td>
                    @elseif(in_array($idx, $moneyColumnIndexes, true))
                        <td class="num">Rp {{ number_format((float) ($totals[$idx] ?? 0), 2, ',', '.') }}</td>
                    @elseif(in_array($idx, $countColumnIndexes, true))
                        <td class="num">{{ number_format((float) ($totals[$idx] ?? 0), 0, ',', '.') }}</td>
                    @else
                        <td></td>
                    @endif
                @endforeach
            </tr>
        </tbody>
    </table>
</body>
</html>
