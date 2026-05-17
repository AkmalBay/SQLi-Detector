<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - {{ $startDate->format('d M Y') }} s/d {{ $endDate->format('d M Y') }}</title>
    
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }

        .meta-info {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }

        .meta-info table {
            width: auto;
            border: none;
        }

        .meta-info td {
            padding: 2px 10px 2px 0;
            border: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #999;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px;
            font-size: 11px;
        }

        td {
            padding: 8px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        .total-row td {
            background-color: #f9f9f9;
            font-weight: bold;
            font-size: 14px;
        }

        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 12px;
        }

        .footer p {
            margin-bottom: 50px;
        }

        /* Print Settings */
        @media print {
            @page { size: A4; margin: 2cm; }
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.history.back()" style="padding: 10px 20px; cursor: pointer; background: #ddd; border: none; border-radius: 5px;">Kembali</button>
    </div> -->

    <div class="header">
        <h1>Laporan Penjualan</h1>
        <p>TOKO SEMBAKO BRAYAN KROYA</p>
        <p style="font-size: 12px; font-weight: normal;">Jl. Cendrawash RT 08/08, Bajing Kulon, Kroya, Cilacap | Telp: 081391331477</p>
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td><strong>Periode Laporan</strong></td>
                <td>: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Cetak</strong></td>
                <td>: {{ now()->format('d F Y, H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Dicetak Oleh</strong></td>
                <td>: {{ Auth::user()->name ?? 'Admin' }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">No. Invoice</th>
                <th style="width: 20%;">Tanggal Transaksi</th>
                <th style="width: 15%;">Jumlah Item</th>
                <th style="width: 20%;" class="text-right">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $index => $transaction)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $transaction->invoice_number }}</td>
                <td class="text-center">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                <td class="text-center">{{ $transaction->items->sum('quantity') }} Item</td>
                <td class="text-right">{{ number_format($transaction->total_price, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="padding: 20px;">Tidak ada data transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row" style="background-color: transparent;">
                <td colspan="4" class="text-right" style="border: none; padding-bottom: 2px; font-weight: normal; font-size: 12px;">Pemasukan Tunai (Cash)</td>
                <td class="text-right" style="border: none; padding-bottom: 2px; font-weight: normal; font-size: 12px;">Rp {{ number_format($totalCash, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row" style="background-color: transparent;">
                <td colspan="4" class="text-right" style="border: none; padding-top: 2px; font-weight: normal; font-size: 12px;">Pemasukan Non-Tunai (Transfer/QRIS)</td>
                <td class="text-right" style="border: none; padding-top: 2px; font-weight: normal; font-size: 12px;">Rp {{ number_format($totalNonCash, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL KESELURUHAN</td>
                <td class="text-right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Jakarta, {{ now()->format('d F Y') }}</p>
        <br><br><br>
        <p><strong>( {{ Auth::user()->name ?? 'Admin' }} )</strong><br>Penanggung Jawab</p>
    </div>

</body>
</html>