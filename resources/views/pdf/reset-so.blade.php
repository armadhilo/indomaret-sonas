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
            padding: 2px 2px;
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
            margin-top: 50px;
        }

        .text-center{
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div style="width: 100%">
            <div class="header">
                <div style="float: left;">
                    <p style="font-size: .8rem;"><b>PT.INTI CAKRAWALA CITRA<br>{{ strtoupper($perusahaan->prs_namacabang) }}</b></p>
                </div>
                <div style="float: right">
                    <p>Tanggal : {{ \Carbon\Carbon::now()->format('d-m-Y') . ' | Pukul :  ' . \Carbon\Carbon::now()->format('H:i:s') }}</p>
                </div>
            </div>

            <div class="body" style="">
                <p style="text-align: center; font-size: 1.35rem"><b>BUKTI ADJUST STOCK OPNAME</b></p>
                <table style="width: 100%; margin-top: 50px">
                    <tr>
                        <td style="width: 100px">TGL SO</td>
                        <td>: &nbsp; {{ $TglSO }}</td>
                    </tr>
                    <tr>
                        <td>TGL REF</td>
                        <td>: &nbsp; {{ $TglReset }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 1.35rem"><b>TELAH DILAKUKAN PENYESUAIAN</b></td>
                    </tr>
                    <tr>
                        <td>DATA OPNAME</td>
                        <td>: &nbsp; SONAS</td>
                    </tr>
                    <tr>
                        <td colspan="2">Selisih Kuantitas (barang hilang) per lokasi :</td>
                    </tr>
                    <tr>
                        <td>Barang Baik</td>
                        <td>: &nbsp; {{ 'Rp. ' . number_format($brgBaik, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Barang Retur</td>
                        <td>: &nbsp; {{ 'Rp. ' . number_format($brgRetur, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Barang Rusak</td>
                        <td>: &nbsp; {{ 'Rp. ' . number_format($brgRusak, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>TOTAL</td>
                        <td>: &nbsp; {{ 'Rp. ' . number_format($total, 2, ',', '.') }}</td>
                    </tr>
                </table>
                <table class="table" width="100%" cellpadding="2" style="margin-top: 30px">
                    <thead>
                        <tr>
                            <td width="50%" style="text-align: center; font-size: .8rem">MENGETAHUI</td>
                            <td width="50%" style="text-align: center; font-size: .8rem">PENANGGUNG JAWAB</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="50%" style="padding-top: 80px;"><div style="margin: auto; border-bottom: 1px solid black; width: 60%"></div></td>
                            <td width="50%" style="padding-top: 80px;"><div style="margin: auto; border-bottom: 1px solid black; width: 60%"></div></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td width="50%" style="text-align: center; font-size: .8rem">Store Mgr.</td>
                            <td width="50%" style="text-align: center; font-size: .8rem">Store Inventory Control Supv/Jr Supv.</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
