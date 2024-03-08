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
                    <p style="font-size: .8rem;"><b>{{ strtoupper($perusahaan->prs_namacabang) }}</b></p>
                </div>
                <div style="float: right">
                    <p>Tanggal : {{ \Carbon\Carbon::now()->format('d-m-Y') . ' | Pukul :  ' . \Carbon\Carbon::now()->format('H:i:s') }}</p>
                </div>
                <hr style="margin-top: 30px">
            </div>

            <div class="body">
                <div style="margin: 0 0 40px 0">
                    <div style="float: left">
                        <p style="margin-bottom: 5px;">Kode Rak : {{ $request['koderak1'] }} s/d {{ $request['koderak2'] }}</p>
                        <p style="margin-bottom: 5px;">Kode SubRak : {{ $request['subrak1'] }} s/d {{ $request['subrak2'] }}</p>
                    </div>
                    <div style="float: right; text-align: right">
                        <p style="margin-bottom: 5px;">Kode Shelving : {{ $request['shelving1'] }} s/d {{ $request['shelving2'] }}</p>
                        <p style="margin-bottom: 5px;">Tipe Rak : {{ $request['tipe1'] }} s/d {{ $request['tipe2'] }}</p>
                    </div>
                </div>
                <p style="margin: 0 0 5px 0">Jenis Barang : {{ $data[0]->lokasi }}</p>
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

                        @php
                            $jumlah_lembar = 0;
                            $jumlah_item = 0;
                        @endphp

                        @foreach ($data as $item)

                        @php
                            $jumlah_lembar += $item->lbr;
                            $jumlah_item += $item->item;
                        @endphp

                        <tr>
                            <td>{{ $item->lso_koderak }}</td>
                            <td>{{ $item->lso_kodesubrak }}</td>
                            <td>{{ $item->lso_tiperak }}</td>
                            <td>{{ $item->lso_shelvingrak }}</td>
                            <td>{{ $item->lbr }}</td>
                            <td>{{ $item->item }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="width: 100%; position: relative">
                    <p style="text-align: left; margin-top: 8px"> Jumlah Lembar : <b>{{ $jumlah_lembar }}</b></p>
                    <p style="text-align: left; margin-top: 8px"> Jumlah item : <b>{{ $jumlah_item }}</b></p>
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
