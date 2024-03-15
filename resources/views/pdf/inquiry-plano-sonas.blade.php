<table style="font-family: Arial; font-size: 10px;">
    <tr>
        <td colspan="4" style="font-weight: 500; font-size: 14px">INQUIRY PLANOGRAM SONAS</td>
    </tr>
    <tr><td></td></tr>
    <tr>
        <td>PLU       : </td>
        <td>{{ $data['data'][0]->prd_prdcd }}</td>
    </tr>
    <tr>
        <td>Deskripsi : </td>
        <td colspan="3">{{ $data['data'][0]->prd_deskripsipanjang }}</td>
    </tr>
    <tr>
        <td>Unit/Frac : </td>
        <td>{{ $data['data'][0]->prd_unit }} / {{ $data['data'][0]->prd_frac }}</td>
    </tr>
    <tr>
        <td>Lokasi    : </td>
        <td>
            @if($data['request']['jenis_barang'] == '01')
                Baik
            @elseif($data['request']['jenis_barang'] == '02')
                Retur
            @elseif($data['request']['jenis_barang'] == '03')
                Rusak
            @else
                All
            @endif
        </td>
    </tr>
    <tr><td></td></tr>
    <tr>
        <td style="font-weight: bold">Alamat</td>
        <td style="font-weight: bold">Lokasi</td>
        <td style="font-weight: bold">Qty CTN</td>
        <td style="font-weight: bold">Qty PCS</td>
    </tr>
    @isset($data['data1'])
        @foreach ($data['data1'] as $item)
            <tr>
                <td>{{ $item->lso_koderak .'.'. $item->lso_kodesubrak .'.'. $item->lso_tiperak .'.'. $item->lso_shelvingrak }}</td>
                <td>{{ $item->lokasi }}</td>
                <td>{{ (int)$item->lso_qty / (int)$item->prd_frac }}</td>
                <td>{{ (int)$item->lso_qty - ((int)$item->lso_qty / (int)$item->prd_frac * (int)$item->prd_frac) }}</td>
            </tr>
        @endforeach
    @endisset
    @isset($data['data2'])
        @foreach ($data['data2'] as $item)
            <tr>
                <td>{{ $item->lso_koderak .'.'. $item->lso_kodesubrak .'.'. $item->lso_tiperak .'.'. $item->lso_shelvingrak }}</td>
                <td>{{ $item->lokasi }}</td>
                <td>{{ (int)$item->lso_qty / (int)$item->prd_frac }}</td>
                <td>{{ (int)$item->lso_qty - ((int)$item->lso_qty / (int)$item->prd_frac * (int)$item->prd_frac) }}</td>
            </tr>
        @endforeach
    @endisset
    @isset($data['data3'])
        @foreach ($data['data3'] as $item)
            <tr>
                <td>{{ $item->lso_koderak .'.'. $item->lso_kodesubrak .'.'. $item->lso_tiperak .'.'. $item->lso_shelvingrak }}</td>
                <td>{{ $item->lokasi }}</td>
                <td>{{ (int)$item->lso_qty / (int)$item->prd_frac }}</td>
                <td>{{ (int)$item->lso_qty - ((int)$item->lso_qty / (int)$item->prd_frac * (int)$item->prd_frac) }}</td>
            </tr>
        @endforeach
    @endisset
    @isset($data['data4'])
        @foreach ($data['data4'] as $item)
            <tr>
                <td>{{ $item->lso_koderak .'.'. $item->lso_kodesubrak .'.'. $item->lso_tiperak .'.'. $item->lso_shelvingrak }}</td>
                <td>{{ $item->lokasi }}</td>
                <td>{{ (int)$item->lso_qty / (int)$item->prd_frac }}</td>
                <td>{{ (int)$item->lso_qty - ((int)$item->lso_qty / (int)$item->prd_frac * (int)$item->prd_frac) }}</td>
            </tr>
        @endforeach
    @endisset
    @isset($data['data5'])
        @foreach ($data['data5'] as $item)
            <tr>
                <td>{{ $item->lso_koderak .'.'. $item->lso_kodesubrak .'.'. $item->lso_tiperak .'.'. $item->lso_shelvingrak }}</td>
                <td>{{ $item->lokasi }}</td>
                <td>{{ (int)$item->lso_qty / (int)$item->prd_frac }}</td>
                <td>{{ (int)$item->lso_qty - ((int)$item->lso_qty / (int)$item->prd_frac * (int)$item->prd_frac) }}</td>
            </tr>
        @endforeach
    @endisset
    @isset($data['data6'])
        @foreach ($data['data6'] as $item)
            <tr>
                <td>{{ $item->lso_koderak .'.'. $item->lso_kodesubrak .'.'. $item->lso_tiperak .'.'. $item->lso_shelvingrak }}</td>
                <td>{{ $item->lokasi }}</td>
                <td>{{ (int)$item->lso_qty / (int)$item->prd_frac }}</td>
                <td>{{ (int)$item->lso_qty - ((int)$item->lso_qty / (int)$item->prd_frac * (int)$item->prd_frac) }}</td>
            </tr>
        @endforeach
    @endisset
</table>
