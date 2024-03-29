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

        .text-center{
            text-align: center;
        }

        .page-number:before {
            content: counter(page);
        }

        @page { margin: 120px 25px 140px 25px; }
        .header { position: fixed; top: -100px; left: 0px; right: 0px; height: 120px; }
        .footer { position: fixed; bottom: -140px; left: 0px; right: 0px; height: 140px; }
    </style>
</head>
<body>
    <header class="header">
        <div style="width: 100%;">
            <div style="float: left;">
                <p style="font-size: .8rem;"><b>INDOGROSIR</b></p>
            </div>
            <div style="float: right">
                <p>Tanggal : {{ \Carbon\Carbon::now()->format('d-m-Y') . ' | Pukul :  ' . \Carbon\Carbon::now()->format('H:i:s') }}</p>
                {{-- <p style="text-align: right;"> Page : <span class="page-number"></span> of {PAGE_NUM}</p> --}}
            </div>
        </div>
        <div style="width: 100%; display: block; margin-top: 30px">
            @php
                $selisih_so = '> 1 juta';
                if($request['selisih_so'] == '1'){
                    $selisih_so = 'ALL';
                }elseif($request['selisih_so'] == '2'){
                    $selisih_so = '< 1 juta';
                }
            @endphp
            <p style="text-align: center; font-size: .85rem"><b>*** Berita Acara Stock Opname Sementara ***<br>Tanggal SO : {{ $request['tanggal_start_so'] }}<br>Selisih SO : {{ $selisih_so }}</b></p>
        </div>
        <hr style="margin-top: 20px">
    </header>
    <footer class="footer">
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
                    <td width="25%" style="padding-top: 60px;"><div style="margin: auto; border-bottom: 1px solid black; width: 60%"></div></td>
                    <td width="25%" style="padding-top: 60px;"><div style="margin: auto; border-bottom: 1px solid black; width: 60%"></div></td>
                    <td width="25%" style="padding-top: 60px;"><div style="margin: auto; border-bottom: 1px solid black; width: 60%"></div></td>
                </tr>
            </tbody>
        </table>
    </footer>
    <div class="container-fluid">
        <div style="width: 100%">

            <div class="body">
                <table border="1" style="border-collapse: collapse;" cellpadding="2">
                    <thead>
                        <tr>
                            <th style="width: 2%">No.</th>
                            <th>PLU</th>
                            <th style="width: 30%">MERK NAMA FLAVOURS KMS SIZE</th>
                            <th>SATUAN</th>
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
                        @if(!count($data))
                        <tr>
                            <td colspan="18" style="text-align: center">Tidak Ada Data</td>
                        </tr>
                        @endif
                        @foreach ($data as $department)
                            @foreach ($department as $kategori)
                            <tr>
                                <td colspan="18">
                                    Departemen : {{ $kategori[0]->prd_kodedepartement }} - {{ $kategori[0]->dep_namadepartement }} <br>
                                    Kategori : {{ $kategori[0]->prd_kodekategoribarang }} - {{ $kategori[0]->kat_namakategori }} <br>
                                    Jenis Barang : {{ $kategori[0]->lokasi }} <br>
                                </td>
                            </tr>

                            @php
                                $total_nilai1 = 0;
                                $total_nilai2 = 0;
                                $total_nilai3 = 0;
                                $total_nilai4 = 0;
                                $total_plu1 = 0;
                                $total_plu2 = 0;
                                $total_plu3 = 0;
                                $total_plu4 = 0;
                            @endphp

                            @foreach ($kategori as $item)

                                @php
                                    if($item->rphlpp > 0){
                                        $total_nilai1 += $item->rphlpp;
                                        $total_plu1++;
                                    }

                                    if($item->rphso > 0){
                                        $total_nilai2 += $item->rphso;
                                        $total_plu2++;
                                    }

                                    if($item->rphadj > 0){
                                        $total_nilai3 += $item->rphadj;
                                        $total_plu3++;
                                    }

                                    if($item->rphsel > 0){
                                        $total_nilai4 += $item->rphsel;
                                        $total_plu4++;
                                    }
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->prd_prdcd }}</td>
                                    <td>{{ $item->prd_deskripsipanjang }}</td>
                                    <td>{{ $item->satuan }}</td>
                                    <td style="text-align: right">{{ $item->ctnlpp }}</td>
                                    <td style="text-align: right">{{ $item->pcslpp }}</td>
                                    <td style="text-align: right">{{ number_format($item->rphlpp, 2, '.', '') }}</td>
                                    <td style="text-align: right">{{ $item->ctnso }}</td>
                                    <td style="text-align: right">{{ $item->pcsso }}</td>
                                    <td style="text-align: right">{{ number_format($item->rphso, 2, '.', '') }}</td>
                                    <td style="text-align: right">{{ $item->ctnadj }}</td>
                                    <td style="text-align: right">{{ $item->pcsadj }}</td>
                                    <td style="text-align: right">{{ number_format($item->rphadj, 2, '.', '') }}</td>
                                    <td style="text-align: right">{{ $item->ctnsel }}</td>
                                    <td style="text-align: right">{{ $item->pcssel }}</td>
                                    <td style="text-align: right">{{ number_format($item->rphsel, 2, '.', '') }}</td>
                                    <td style="text-align: right">{{ number_format($item->sop_lastavgcost, 2, '.', '') }}</td>
                                    <td style="text-align: right">{{ number_format($item->hpp, 2, '.', '') }}</td>
                                </tr>
                            @endforeach

                            <tr>
                                <td colspan="3" style="text-align: right; border: 0">Total Nilai Per Kategori :</td>
                                <td colspan="4" style="text-align: right; border: 0">{{ number_format($total_nilai1, 2, '.', '') }}</td>
                                <td colspan="3" style="text-align: right; border: 0">{{ number_format($total_nilai2, 2, '.', '') }}</td>
                                <td colspan="3" style="text-align: right; border: 0">{{ number_format($total_nilai3, 2, '.', '') }}</td>
                                <td colspan="3" style="text-align: right; border: 0">{{ number_format($total_nilai4, 2, '.', '') }}</td>
                                <td colspan="2" style="border: 0"></td>
                            </tr>
                            <tr>
                                <td colspan="4" style="text-align: right; border: 0">Selisih (+) :</td>
                                <td colspan="4" style="text-align: right; border: 0">{{ number_format($total_nilai4, 2, '.', '') }}</td>
                                <td colspan="2" style="text-align: right; border: 0">Selisih (-) :</td>
                                <td colspan="8" style="border: 0"></td>
                            </tr>
                            <tr>
                                <td colspan="3" style="text-align: right; border: 0">Total PLU Per Kategori :</td>
                                <td colspan="4" style="text-align: right; border: 0">{{ $total_plu1 }}</td>
                                <td colspan="3" style="text-align: right; border: 0">{{ $total_plu2 }}</td>
                                <td colspan="3" style="text-align: right; border: 0">{{ $total_plu3 }}</td>
                                <td colspan="3" style="text-align: right; border: 0">{{ $total_plu4 }}</td>
                                <td colspan="2" style="border: 0"></td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $size = 8;
            $font = $fontMetrics->getFont("Verdana");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width + 10);
            $y = 25;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>
