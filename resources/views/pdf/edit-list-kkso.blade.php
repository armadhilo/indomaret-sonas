<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF EDIT LIST KKSO</title>
    <style>
        body{
            font-family: sans-serif;
        }
        table{
            width: 100%;
        }
        table th{
            font-size: 13px;
            background: #e9e7e7;
        }
        table td{
            font-size: 12px;
            padding: 10px 10px;
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

        .table-border-outside thead tr th, .table-border-outside{
            border: 1px solid gray;
        }

        .table-border-outside tbody tr:nth-child(even), .table-border-outside tfoot tr:nth-child(even) {
            background-color: #f2f2f2;
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
                    <p style="font-size: .8rem;"><b>{{ strtoupper($perusahaan->prs_namacabang) }}</b></p>
                </div>
                <div style="float: right">
                    <p>Tanggal : {{ \Carbon\Carbon::now()->format('d-m-Y') . ' | Pukul :  ' . \Carbon\Carbon::now()->format('H:i:s') }}</p>
                    <p style="text-align: right;"> Hal : <span class="page-number"></span></p>
                </div>
                <hr style="margin-top: 30px">
            </div>

            <div class="body">
                <p style="text-align: center; font-size: .95rem"><b>EDIT LIST KKSO BY LOKASI</b></p>
                <p style="text-align: center; font-size: .69rem">Tanggal SO : 27 Maret 2024<br>Lokasi : CRK / 01</p>
                <table style="border-collapse: collapse; margin-top:20px" class="table-center table-border-outside" cellpadding="2">
                    <thead>
                        <tr>
                            <th style="width: 2%">No</th>
                            <th style="width: 9%">PLU</th>
                            <th style="width: 20%">DESKRIPSI</th>
                            <th>SATUAN</th>
                            <th>CTN</th>
                            <th>PCS</th>
                            <th>TOTAL PCS</th>
                            <th>HARGA AVG</th>
                            <th style="width: 20%">TOTAL</th>
                            <th>USER</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->lso_prdcd }}</td>
                            <td>{{ $item->prd_deskripsipanjang }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td>{{ $item->ctn }}</td>
                            <td>{{ $item->pcs }}</td>
                            <td>{{ $item->lso_qty }}</td>
                            <td>{{ number_format($item->st_avgcostmonthend, 2, '.', '') }}</td>
                            <td style="text-align: right;">{{ $item->total }}</td>
                            <td>{{ $item->lso_modify_by }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="8" style="text-align: right;">Total Per Shelving</td>
                            <td style="text-align: right;">Rp. 00 (dummy)</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="8" style="text-align: right;">Total Per Tipe Rak</td>
                            <td style="text-align: right;">Rp. 00 (dummy)</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="8" style="text-align: right;">Total Per Sub Rak</td>
                            <td style="text-align: right;">Rp. 00 (dummy)</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="8" style="text-align: right;">Total Per Kode Rak</td>
                            <td style="text-align: right;">Rp. 00 (dummy)</td>
                            <td></td>
                        </tr>
                        <tr style="border: 1px solid gray; background: #E9E7E7">
                            <td colspan="8" style="text-align: center; font-weight: 700">TOTAL AKHIR</td>
                            <td style="text-align: right; font-weight: 700">Rp. 00 (dummy)</td>
                            <td></td>
                        </tr>
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
