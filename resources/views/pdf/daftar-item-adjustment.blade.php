<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF DAFTAR ITEM YANG SUDAH DIADJUSTMENT SETELAH RESET</title>
    <style>
        body{
            font-family: sans-serif;
        }
        table{
            width: 100%;
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

        .table-striped tbody tr:nth-child(odd), .table-striped tfoot tr:nth-child(odd) {
            background-color: #f2f2f2;
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

        table tfoot td{
            border: none!important;
        }

        @page { margin: 120px 25px 0px 25px; }
        .header { position: fixed; top: -90px; left: 0px; right: 0px; height: 120px; }
    </style>
</head>
<body>
    <header class="header">
        <div style="float: left;">
            <p style="font-size: .8rem;"><b>INDOGROSIR</b></p>
        </div>
        <div style="float: right">
            <p>Tanggal : {{ \Carbon\Carbon::now()->format('d-m-Y') . ' | Pukul :  ' . \Carbon\Carbon::now()->format('H:i:s') }}</p>
            <p style="text-align: right;"> Hal : <span class="page-number"></span></p>
        </div>
        <hr style="margin: 30px 0 15px 0;">
        <p style="text-align: center; font-size: .85rem"><b>DAFTAR ITEM YANG SUDAH DI ADJUSTMENT</b><br>Jenis Barang : 01 - Barang Baik</p>
    </header>
    <div class="container-fluid">
        <div style="width: 100%">
            <div class="body">
                <table border="1" style="border-collapse: collapse; margin-top:10px" class="table-center table-striped" cellpadding="2">
                    <thead>
                        <tr>
                            <th style="width: 9%">No</th>
                            <th>PLU</th>
                            <th>Description</th>
                            <th>Qty Adj</th>
                            <th>NILAI</th>
                            <th>TOTAL</th>
                            <th>Tanggal Adj</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php
                            $total_total = 0;
                        @endphp

                        @foreach ($data as $item)

                        @php
                            $total_total +=  $item->total;
                        @endphp

                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><b>{{ $item->adj_prdcd }}</b></td>
                            <td colspan="6" style="text-align: left">{{ $item->prd_deskripsipanjang }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{ $loop->iteration }}.1</td>
                            <td>{{ $item->adj_qty }}</td>
                            <td>{{ number_format($item->sop_lastavgcost, 2, '.', '') }}</td>
                            <td>{{ number_format($item->total, 2, '.', '') }}</td>
                            <td>{{ $item->adj_create_dt }}</td>
                            <td>{{ $item->adj_keterangan }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <td colspan="4" style="text-align: right">Total : </td>
                        <td colspan="2" style="text-align: right"><b>{{ number_format($total_total, 2, '.', '') }}</b></td>
                        <td colspan="2"></td>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "page {PAGE_NUM} / {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Verdana");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>
