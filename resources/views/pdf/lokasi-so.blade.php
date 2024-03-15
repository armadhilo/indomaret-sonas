<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF DAFTAR MASTER LOKASI SO</title>
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
                <div>
                    <div style="float: left;">
                        <p style="font-size: .8rem;"><b>{{ $perusahaan->prs_namacabang }}</b></p>
                    </div>
                    <div style="float: right">
                        <p>Tanggal : {{ \Carbon\Carbon::now()->format('d-m-Y') . ' | Pukul :  ' . \Carbon\Carbon::now()->format('H:i:s') }}</p>
                        <p style="text-align: right;"> Hal : <span class="page-number"></span></p>
                    </div>
                </div>
                <hr style="margin-top: 30px">
            </div>

            <div class="body">
                <p style="text-align: center; font-size: .85rem; margin-top: 0px"><b>DAFTAR MASTER LOKASI SO ( STOCK OPNAME )</b></p>
                <table border="1" style="border-collapse: collapse; margin-top:10px" class="table-center" cellpadding="2">
                    <thead>
                        <tr>
                            <th style="width: 9%">PLU</th>
                            <th style="width: 34%">Deskripsi</th>
                            <th>DIV</th>
                            <th>DEPT</th>
                            <th>KAT</th>
                            <th>Kode Rak</th>
                            <th>Sub Rak</th>
                            <th>Type</th>
                            <th>Shelv</th>
                            <th>LOK</th>
                            <th>CTN</th>
                            <th>PCS</th>
                            <th>QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($data))
                        <tr>
                            <td colspan="13" style="text-align: center">Tidak Ada Data</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                <p style="width: 100%; text-align: right; margin-top: 5px;">** Akhir Dari Laporan **</p>
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
