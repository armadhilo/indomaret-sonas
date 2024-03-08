<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF JALUR CETAKAN KERTAS</title>
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
                    <p style="font-size: .8rem;"><b>INDOGROSIR</b></p>
                </div>
                <div style="float: right">
                    <p>Tanggal : {{ \Carbon\Carbon::now()->format('d-m-Y') . ' | Pukul :  ' . \Carbon\Carbon::now()->format('H:i:s') }}</p>
                    <p style="text-align: right;"> Hal : <span class="page-number"></span></p>
                </div>
                <hr style="margin-top: 30px">
            </div>

            <div class="body">
                <p style="text-align: center; font-size: .85rem"><b>KERTAS KERJA STOCK OPNAME</b><br>LOKASI : TOKO / GUDANG</p>
                <div style="margin: 0 0 40px 0">
                    <div style="float: left">
                        <p style="margin-bottom: 5px;">Jenis Barang : {{ $data[0]->lokasi }}</p>
                        <p>Kode Lokasi : CRK / 01</p>
                    </div>
                </div>
                <table border="1" style="border-collapse: collapse; margin-top:20px" class="table-center" cellpadding="2">
                    <thead>
                        <tr>
                            <th style="width: 9%">No</th>
                            <th style="width: 9%">PLU</th>
                            <th style="width: 50%">Deskripsi</th>
                            <th>CTN/0</th>
                            <th>PCS/1</th>
                            <th>Satuan</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                        <tr>
                            <td class="text-center">{{ 'Z / ' . '01/ ' . $loop->iteration }}</td>
                            <td>{{ $item->lso_prdcd }}</td>
                            <td style="text-align: unset!important">{{ $item->prd_deskripsipanjang  }}</td>
                            <td>{{ $item->lso_tmp_qtyctn }}</td>
                            <td>{{ $item->lso_tmp_qtypcs }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td>{{ '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <table class="table" width="100%" cellpadding="2" style="margin-top: 30px">
                    <thead>
                        <tr>
                            <td width="50%" style="text-align: center; font-size: .8rem">Pencatat</td>
                            <td width="50%" style="text-align: center; font-size: .8rem">Pemeriksa</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="50%" style="padding-top: 80px;"><div style="margin: auto; border-bottom: 1px solid black; width: 60%"></div></td>
                            <td width="50%" style="padding-top: 80px;"><div style="margin: auto; border-bottom: 1px solid black; width: 60%"></div></td>
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
