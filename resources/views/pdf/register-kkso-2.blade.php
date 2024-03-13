<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF REGISTER KKSO II</title>
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
                <p style="text-align: center; font-size: .85rem"><b>REGISTER KKSO II</b></p>
                <div style="margin: 0 0 40px 0">
                    <div style="float: left">
                        <p style="margin-bottom: 5px;">Tanggal SO : {{ $request['tanggal_start_so'] }}</p>
                        <p style="margin-bottom: 5px;">Kode Rak : {{ $request['koderak1'] }} s/d {{ $request['koderak1'] }}</p>
                        <p style="margin-bottom: 5px;">Kode Type : {{ $request['tipe1'] }} s/d {{ $request['tipe2'] }}</p>
                    </div>
                    <div style="float: right">
                        <p style="margin-bottom: 5px;">Jenis Barang : {{ $data[0]->lokasi }}</p>
                        <p style="margin-bottom: 5px;">Kode SubRak : {{ $request['subrak1'] }} s/d {{ $request['subrak2'] }}</p>
                        <p style="margin-bottom: 5px;">Kode Shelving : {{ $request['shelving1'] }} s/d {{ $request['shelving2'] }}</p>
                    </div>
                </div>
                <table border="1" style="border-collapse: collapse; margin-top:60px; margin-bottom: 10px" class="table-center" cellpadding="2">
                    <thead>
                        <tr>
                            <th style="width: 9%">Kode Rak</th>
                            <th style="width: 9%">Kode SubRak</th>
                            <th style="width: 9%">Kode Type</th>
                            <th style="width: 9%">Kode Shelving</th>
                            <th>Jml. Lembar</th>
                            <th>Jml. Item</th>
                            <th>Jml. Terinput</th>
                            <th>Selisih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_lbr = 0;
                            $total_item = 0;
                            $total_terinput = 0;
                            $total_selisih = 0;
                        @endphp

                        @foreach ($data as $item)

                            @php
                                $total_lbr += $item->lbr;
                                $total_item += $item->item;
                                $total_terinput += $item->so;
                                $total_selisih += $item->selisih;
                            @endphp

                            <tr>
                                <td>{{ $item->lso_koderak }}</td>
                                <td>{{ $item->lso_kodesubrak }}</td>
                                <td>{{ $item->lso_tiperak }}</td>
                                <td>{{ $item->lso_shelvingrak }}</td>
                                <td>{{ $item->lbr }}</td>
                                <td>{{ $item->item }}</td>
                                <td>{{ $item->so }}</td>
                                <td>{{ $item->selisih }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p style="text-align: right">** Akhir Dari Laporan **</p>
                <table style="margin-top: 15px; width: 100%">
                    <tr>
                        <td><span>Total Lembar</span><b style="margin-left: 40px">{{ $total_lbr }}</b></td>

                        <td style="text-align: center;"><span>Total Item</span><b style="margin-left: 40px">{{ $total_item }}</b></td>

                        <td style="text-align: center;"><span>Total Terinput</span><b style="margin-left: 40px">{{ $total_terinput }}</b></td>

                        <td style="text-align: right"><span>Total Selisih</span><b style="margin-left: 40px">{{ $total_selisih }}</b></td>
                    </tr>
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
