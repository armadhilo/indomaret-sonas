<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF REGISTER KKSO I</title>
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
                <div style="float: left;">
                    <p style="font-size: .8rem;"><b>INDOGROSIR SEMARANG POST</b></p>
                </div>
                <div style="float: right">
                    <p>Tanggal : {{ \Carbon\Carbon::now()->format('d-m-Y') . ' | Pukul :  ' . \Carbon\Carbon::now()->format('H:i:s') }}</p>
                </div>
                <hr style="margin-top: 30px">
            </div>

            <div class="body">
                <div style="margin: 0 0 40px 0">
                    <div style="float: left">
                        <p style="margin-bottom: 5px;">Kode Rak : 0 s/d 25</p>
                        <p style="margin-bottom: 5px;">Kode SubRak : 0 s/d 25</p>
                    </div>
                    <div style="float: right; text-align: right">
                        <p style="margin-bottom: 5px;">Kode Shelving : 0 s/d 25</p>
                        <p style="margin-bottom: 5px;">Tipe Rak : 0 s/d 25</p>
                    </div>
                </div>
                <p style="margin: 0 0 5px 0">Jenis Barang : 01 - BARANG BAIK</p>
                <table border="1" style="border-collapse: collapse; margin-top:10px" class="table-center" cellpadding="2">
                    <thead>
                        <tr>
                            <th rowspan="2">Kode Rak</th>
                            <th rowspan="2">Sub Rak</th>
                            <th rowspan="2">Type Rak</th>
                            <th rowspan="2">Shelving</th>
                            <th rowspan="2">Jumlah Lembar</th>
                            <th rowspan="2">Jumlah Item</th>
                            <th colspan="2">Team Pelaksana I</th>
                            <th colspan="2">Team Input</th>
                        </tr>
                        <tr>
                            <th>Terima</th>
                            <th>Kembali</th>
                            <th>Terima</th>
                            <th>Kembali</th>
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="width: 100%; position: relative">
                    <p style="text-align: left; margin-top: 8px"> Jumlah Lembar : <b><span class="page-number"></span></b></p>
                    <p style="text-align: left; margin-top: 8px"> Jumlah item : <b>{{ count($data) }}</b></p>
                    <p style="position: absolute; top: 0; right: 0;">** Akhir Dari Laporan **</p>
                </div>
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
