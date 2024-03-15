<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF DRAFT LHSO</title>
    <style>
        body{
            font-family: sans-serif;
        }
        table{
            width: 100%;
        }

        @page { margin: 20px; }

        .header{
            margin: 0;
            padding: 0;
        }
        table th{
            font-size: 11px;
            background: #e9e7e7;
        }
        table td{
            font-size: 11px;
        }
        p{
            font-size: .7rem;
            font-weight: 400;
            margin: 0;
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
        }
        .italic {
            font-style: italic;
        }

        .table-center thead tr th, .table-center tbody tr td {
            text-align: center
        }

        .tr-not-center td {
            text-align: start!important;
        }

        .inline-block-content > *{
            display: inline-block;
        }

        .title > *{
            text-align: center;
        }

        .text-center{
            text-align: center;
        }

        .page-number:before {
            content: counter(page);
        }
    </style>
</head>
<body>
    <header class="header">
        <div style="width: 100%;">
            <div style="float: left;">
                <p style="font-size: 1rem; margin-left: 2px"><b>INDOGROSIR</b></p>
                <table>
                    <tr>
                        <td>Divisi</td>
                        <td style="width: 6%">:</td>
                        <td>1 - FOOD</td>
                        <td>S/D</td>
                        <td>6 - SERVICE</td>
                    </tr>
                    <tr>
                        <td>Divisi</td>
                        <td style="width: 6%">:</td>
                        <td>1 - FOOD</td>
                        <td>S/D</td>
                        <td>6 - SERVICE</td>
                    </tr>
                    <tr>
                        <td>Divisi</td>
                        <td style="width: 6%">:</td>
                        <td>1 - FOOD</td>
                        <td>S/D</td>
                        <td>6 - SERVICE</td>
                    </tr>
                </table>
            </div>
            <div style="float: right">
                <table>
                    <tr>
                        <td>Tgl. Cetak</td>
                        <td style="width: 6%">:</td>
                        <td style="text-align: right">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td>Pkl. Cetak</td>
                        <td style="width: 6%">:</td>
                        <td style="text-align: right">{{ \Carbon\Carbon::now()->format('H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td>User</td>
                        <td style="width: 6%">:</td>
                        <td style="text-align: right">GUN</td>
                    </tr>
                    <tr>
                        <td>Hal</td>
                        <td style="width: 6%">:</td>
                        <td style="text-align: right"><span class="page-number"></span></td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="width: 100%; display: block; margin-top: 60px">
            <p style="text-align: center; font-size: 1.2rem"><b>DRAFT LAPORAN HASIL STOCK OPNAME DI TOKO IGR.</b></p>
            <p style="text-align: center; font-size: 1rem;">Tahap : 01</p>
            <p style="text-align: center; font-size: .85rem">Tanggal : 02-Februari-2025</p>
        </div>
        <p style="text-align: right; font-weight: bold">Lokasi Barang Baik - 01</p>
    </header>
    <div class="container-fluid">
        <div style="width: 100%">
            <div class="body">
                <table border="1" style="border-collapse: collapse; margin-top: 12px" cellpadding="2">
                    <thead>
                        <tr>
                            <th style="width: 2%" rowspan="2">No.</th>
                            <th colspan="2">Item</th>
                            <th colspan="3"></th>
                            <th rowspan="2">LPP(QTY.)</th>
                            <th rowspan="2">Selisih Hasil SO(Qty.)</th>
                            <th rowspan="2">Nilai Selisih(Rp.)</th>
                        </tr>
                        <tr>
                            <th style="width: 7%">PLU</th>
                            <th style="width: 40%">DESKRIPSI</th>
                            <th>TOKO</th>
                            <th>GUDANG</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="9" style="text-align: center">Tiida Ada Data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
