<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF BASO RINGKASAN {{ $textJenisBarang }} - RESET</title>
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

        .table-center thead tr th, .table-center tbody tr td, .table-center tfoot tr td {
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

        .table-border-outside thead tr th,{
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
                    <p style="font-size: .8rem;"><b>INDOGROSIR</b></p>
                </div>
                <div style="float: right">
                    <p>Tanggal : {{ \Carbon\Carbon::now()->format('d-m-Y') . ' | Pukul :  ' . \Carbon\Carbon::now()->format('H:i:s') }}</p>
                    <p style="text-align: right;"> Hal : <span class="page-number"></span></p>
                </div>
                <hr style="margin-top: 30px">
            </div>

            <div class="body">
                <p style="text-align: center; font-size: .85rem"><b>Berita Acara Stock Opname Sementara</b><br>Tanggal SO : 08-03-2024</p>
                <div style="margin: 0 0 40px 0">
                    <div style="float: left">
                        <p style="margin-bottom: 5px;">Jenis Barang : {{ $textJenisBarang }}</p>
                    </div>
                </div>
                <table style="border-collapse: collapse; margin-top:10px" class="table-border-outside table-center" cellpadding="2">
                    <thead>
                        <tr>
                            <th>KATEGORI</th>
                            <th>NILAI LPP</th>
                            <th>NILAI SO</th>
                            <th>NILAI ADJ</th>
                            <th>NILAI SEL</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($data as $key1 => $department)

                            <tr>
                                <td colspan="5" style="padding: 10px 0!important;font-size: .8rem;text-align: left">Departemen : <b>{{ $key1 }}</b></td>
                            </tr>

                            @php
                                $total_rphlpp = 0;
                                $total_rphso = 0;
                                $total_rphadj = 0;
                                $total_rphsel = 0;
                            @endphp

                            @foreach ($department as $key2 => $kategori)

                                @php
                                    $rphlpp = 0;
                                    $rphso = 0;
                                    $rphadj = 0;
                                    $rphsel = 0;
                                @endphp

                                @foreach ($kategori as $item)
                                    @php
                                        $rphlpp += $item->rphlpp;
                                        $rphso += $item->rphso;
                                        $rphadj += $item->rphadj;
                                        $rphsel += $item->rphsel;

                                        $total_rphlpp += $item->rphlpp;
                                        $total_rphso += $item->rphso;
                                        $total_rphadj += $item->rphadj;
                                        $total_rphsel += $item->rphsel;
                                    @endphp
                                @endforeach

                                <tr>
                                    <td style="text-align: left">{{ $key2 }}</td>
                                    <td style="text-align: right">{{ number_format($rphlpp, 2, '.', '') }}</td>
                                    <td style="text-align: right">{{ number_format($rphso, 2, '.', '') }}</td>
                                    <td style="text-align: right">{{ number_format($rphadj, 2, '.', '') }}</td>
                                    <td style="text-align: right">{{ number_format($rphsel, 2, '.', '') }}</td>
                                </tr>
                            @endforeach

                            <tr>
                                <td>** Total Nilai Per Dept. :</td>
                                <td style="text-align: right"><b>{{ number_format($total_rphlpp, 2, '.', '') }}</b></td>
                                <td style="text-align: right"><b>{{ number_format($total_rphso, 2, '.', '') }}</b></td>
                                <td style="text-align: right"><b>{{ number_format($total_rphadj, 2, '.', '') }}</b></td>
                                <td style="text-align: right"><b>{{ number_format($total_rphsel, 2, '.', '') }}</b></td>
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
