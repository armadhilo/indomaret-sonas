<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF BASO RINCIAN BAIK RESET</title>
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

        .tr-not-center td {
            text-align: start!important;
        }

        .inline-block-content > *{
            display: inline-block;
        }

        .title > *{
            text-align: center;
        }

        .body{
            margin-top: 20px;
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
    <div class="container-fluid">
        <div style="width: 100%">
            <div class="header">
                <div style="width: 100%;">
                    <div style="float: left;">
                        <p style="font-size: .8rem;"><b>INDOGROSIR</b></p>
                    </div>
                    <div style="float: right">
                        <p>Tanggal : {{ \Carbon\Carbon::now()->format('d-m-Y') . ' | Pukul :  ' . \Carbon\Carbon::now()->format('H:i:s') }}</p>
                        <p style="text-align: right;"> Hal : <span class="page-number"></span></p>
                    </div>
                </div>
                <div style="width: 100%; display: block; margin-top: 30px">
                    <p style="text-align: center; font-size: .85rem"><b>*** Berita Acara Stock Opname Sementara ***<br>Tanggal SO : 09-03-2023<br>Selisih SO : ALL</b></p>
                </div>
                <hr style="margin-top: 20px">
            </div>

            <div class="body">
                
                <table border="1" style="border-collapse: collapse; margin-top:10px; margin-bottom: 10px" class="table-center" cellpadding="2">
                    <thead>
                        <tr>
                            <th style="width: 2%">No.</th>
                            <th>PLU</th>
                            <th>MERK</th>
                            <th>NAMA</th>
                            <th>FLAVOURS</th>
                            <th>KMS</th>
                            <th>SIZE</th>
                            <th>QTY</th>
                            <th>STOCK SO LPP Fr</th>
                            <th>Nilai</th>
                            <th>QTY</th>
                            <th>STOCK OPNAME Fr</th>
                            <th>Nilai</th>
                            <th>Qty</th>
                            <th>ADJUSTMENT Fr</th>
                            <th>Nilai</th>
                            <th>Qty</th>
                            <th>SELISIH Fr</th>
                            <th>Nilai</th>
                            <th>HPP Rata-Rata</th>
                            <th>HPP Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                        <tr>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                            <td>123</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <table class="table" width="100%" cellpadding="2" style="margin-top: 30px">
                    <thead>
                        <tr>
                            <td>Nama Kota : <div style="margin: auto; border-bottom: 1px solid black; width: 60%"></div></td>
                            <td width="25%" style="text-align: center; font-size: .8rem">AUDIT</td>
                            <td width="25%" style="text-align: center; font-size: .8rem">SAM</td>
                            <td width="25%" style="text-align: center; font-size: .8rem">SM</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Tanggal : <div style="margin: auto; border-bottom: 1px solid black; width: 60%"></div></td>
                            <td width="25%" style="padding-top: 80px;"><div style="margin: auto; border-bottom: 1px solid black; width: 60%"></div></td>
                            <td width="25%" style="padding-top: 80px;"><div style="margin: auto; border-bottom: 1px solid black; width: 60%"></div></td>
                            <td width="25%" style="padding-top: 80px;"><div style="margin: auto; border-bottom: 1px solid black; width: 60%"></div></td>
                        </tr>
                    </tbody>
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
