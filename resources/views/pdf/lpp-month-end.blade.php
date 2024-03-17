<table style="font-family: Arial; font-size: 10px;">
    @if(isset($lpp_baik))
    <tr>
        <td colspan="4" style="font-weight: 500; font-size: 14px">LPP BARANG BAIK</td>
    </tr>
    <tr>
        <td>periode : {{ $periode }}</td>
    </tr>
    <tr><td></td></tr>
    <tr>
        <td>Cabang</td>
        <td>Tgl Awal</td>
        <td>Tgl Akhir</td>
        <td>Divisi</td>
        <td>Dept</td>
        <td>Kategori</td>
        <td>PLU</td>
        <td>Deskripsi</td>
        <td>Unit</td>
        <td>LPP_QTYBEGBAL</td>
        <td>LPP_RPHBEGBAL</td>
        <td>LPP_QTYBELI</td>
        <td>LPP_RPHBELI</td>
        <td>LPP_QTYBONUS</td>
        <td>LPP_RPHBONUS</td>
        <td>LPP_QTYTRMCB</td>
        <td>LPP_RPHTRMCB</td>
        <td>LPP_QTYRETURSALES</td>
        <td>LPP_RPHRETURSALES</td>
        <td>LPP_RPHRAFAK</td>
        <td>LPP_QTYREPACK</td>
        <td>LPP_RPHREPACK</td>
        <td>LPP_QTYLAININ</td>
        <td>LPP_RPHLAININ</td>
        <td>LPP_QTYSALES</td>
        <td>LPP_RPHSALES</td>
        <td>LPP_QTYKIRIM</td>
        <td>LPP_RPHKIRIM</td>
        <td>LPP_QTYPREPACKING</td>
        <td>LPP_RPHPREPACKING</td>
        <td>LPP_QTYHILANG</td>
        <td>LPP_RPHHILANG</td>
        <td>LPP_QTYLAINOUT</td>
        <td>LPP_RPHLAINOUT</td>
        <td>LPP_QTYINTRANSIT</td>
        <td>LPP_RPHINTRANSIT</td>
        <td>LPP_QTYADJ</td>
        <td>LPP_RPHADJ</td>
        <td>LPP_SOADJ</td>
        <td>LPP_QTY_SELISIH_SO</td>
        <td>LPP_RPH_SELISIH_SO</td>
        <td>LPP_QTY_SELISIH_SOIC</td>
        <td>LPP_RPH_SELISIH_SOIC</td>
        <td>LPP_QTYAKHIR</td>
        <td>LPP_RPHAKHIR</td>
        <td>LPP_AVGCOST</td>
        <td>KOREKSI</td>
    </tr>
    @foreach ($lpp_baik as $item)
    <tr>
        <td>{{ $item->lpp_kodeigr }}</td>
        <td>{{ $item->lpp_tgl1 }}</td>
        <td>{{ $item->lpp_tgl2 }}</td>
        <td>{{ $item->prd_kodedivisi }}</td>
        <td>{{ $item->prd_kodedepartement }}</td>
        <td>{{ $item->prd_kodekategoribarang }}</td>
        <td>{{ $item->prd_prdcd }}</td>
        <td>{{ $item->prd_deskripsipanjang }}</td>
        <td>{{ $item->prd_unit . '/' . $item->prd_frac }}</td>
        <td>{{ $item->lpp_qtybegbal }}</td>
        <td>{{ $item->lpp_rphbegbal }}</td>
        <td>{{ $item->lpp_qtybeli }}</td>
        <td>{{ $item->lpp_rphbeli }}</td>
        <td>{{ $item->lpp_qtybonus }}</td>
        <td>{{ $item->lpp_rphbonus }}</td>
        <td>{{ $item->lpp_qtytrmcb }}</td>
        <td>{{ $item->lpp_rphtrmcb }}</td>
        <td>{{ $item->lpp_qtyretursales }}</td>
        <td>{{ $item->lpp_rphretursales }}</td>
        <td>{{ $item->lpp_rphrafak }}</td>
        <td>{{ $item->lpp_qtyrepack }}</td>
        <td>{{ $item->lpp_rphrepack }}</td>
        <td>{{ $item->lpp_qtylainin }}</td>
        <td>{{ $item->lpp_rphlainin }}</td>
        <td>{{ $item->lpp_qtysales }}</td>
        <td>{{ $item->lpp_rphsales }}</td>
        <td>{{ $item->lpp_qtykirim }}</td>
        <td>{{ $item->lpp_rphkirim }}</td>
        <td>{{ $item->lpp_qtyprepacking }}</td>
        <td>{{ $item->lpp_rphprepacking }}</td>
        <td>{{ $item->lpp_qtyhilang }}</td>
        <td>{{ $item->lpp_rphhilang }}</td>
        <td>{{ $item->lpp_qtylainout }}</td>
        <td>{{ $item->lpp_rphlainout }}</td>
        <td>{{ $item->lpp_qtyintransit }}</td>
        <td>{{ $item->lpp_rphintransit }}</td>
        <td>{{ $item->lpp_qtyadj }}</td>
        <td>{{ $item->lpp_rphadj }}</td>
        <td>{{ $item->lpp_soadj }}</td>
        <td>{{ $item->lpp_qtyakhir }}</td>
        <td>{{ $item->lpp_rphakhir }}</td>
        <td>{{ $item->lpp_avgcost }}</td>
        <td>{{ $item->lpp_qty_selisih_so }}</td>
        <td>{{ $item->lpp_rph_selisih_so }}</td>
        <td>{{ $item->lpp_qty_selisih_soic }}</td>
        <td>{{ $item->lpp_rph_selisih_soic }}</td>
        <td>{{ $item->koreksi }}</td>
    </tr>
    @endforeach
    <tr><td></td></tr>
    <tr><td></td></tr>
    @endif

    @if(isset($lpp_retur))
    <tr>
        <td colspan="4" style="font-weight: 500; font-size: 14px">LPP BARANG RETUR</td>
    </tr>
    <tr>
        <td>periode : {{ $periode }}</td>
    </tr>
    <tr><td></td></tr>
    <tr>
        <td>Cabang</td>
        <td>Tgl Awal</td>
        <td>Tgl Akhir</td>
        <td>Divisi</td>
        <td>Dept</td>
        <td>Kategori</td>
        <td>PLU</td>
        <td>Deskripsi</td>
        <td>Unit</td>
        <td>LPP_QTYBEGBAL</td>
        <td>LPP_RPHBEGBAL</td>
        <td>LPP_QTYBELI</td>
        <td>LPP_RPHBELI</td>
        <td>LPP_QTYBONUS</td>
        <td>LPP_RPHBONUS</td>
        <td>LPP_QTYTRMCB</td>
        <td>LPP_RPHTRMCB</td>
        <td>LPP_QTYRETURSALES</td>
        <td>LPP_RPHRETURSALES</td>
        <td>LPP_RPHRAFAK</td>
        <td>LPP_QTYREPACK</td>
        <td>LPP_RPHREPACK</td>
        <td>LPP_QTYLAININ</td>
        <td>LPP_RPHLAININ</td>
        <td>LPP_QTYSALES</td>
        <td>LPP_RPHSALES</td>
        <td>LPP_QTYKIRIM</td>
        <td>LPP_RPHKIRIM</td>
        <td>LPP_QTYPREPACKING</td>
        <td>LPP_RPHPREPACKING</td>
        <td>LPP_QTYHILANG</td>
        <td>LPP_RPHHILANG</td>
        <td>LPP_QTYLAINOUT</td>
        <td>LPP_RPHLAINOUT</td>
        <td>LPP_QTYINTRANSIT</td>
        <td>LPP_RPHINTRANSIT</td>
        <td>LPP_QTYADJ</td>
        <td>LPP_RPHADJ</td>
        <td>LPP_SOADJ</td>
        <td>LPP_QTY_SELISIH_SO</td>
        <td>LPP_RPH_SELISIH_SO</td>
        <td>LPP_QTY_SELISIH_SOIC</td>
        <td>LPP_RPH_SELISIH_SOIC</td>
        <td>LPP_QTYAKHIR</td>
        <td>LPP_RPHAKHIR</td>
        <td>LPP_AVGCOST</td>
        <td>KOREKSI</td>
    </tr>
    @foreach ($lpp_retur as $item)
    <tr>
        <td>{{ $item->lpp_kodeigr }}</td>
        <td>{{ $item->lpp_tgl1 }}</td>
        <td>{{ $item->lpp_tgl2 }}</td>
        <td>{{ $item->prd_kodedivisi }}</td>
        <td>{{ $item->prd_kodedepartement }}</td>
        <td>{{ $item->prd_kodekategoribarang }}</td>
        <td>{{ $item->prd_prdcd }}</td>
        <td>{{ $item->prd_deskripsipanjang }}</td>
        <td>{{ $item->prd_unit . '/' . $item->prd_frac }}</td>
        <td>{{ $item->lpp_qtybegbal }}</td>
        <td>{{ $item->lpp_rphbegbal }}</td>
        <td>{{ $item->lpp_qtybeli }}</td>
        <td>{{ $item->lpp_rphbeli }}</td>
        <td>{{ $item->lpp_qtybonus }}</td>
        <td>{{ $item->lpp_rphbonus }}</td>
        <td>{{ $item->lpp_qtytrmcb }}</td>
        <td>{{ $item->lpp_rphtrmcb }}</td>
        <td>{{ $item->lpp_qtyretursales }}</td>
        <td>{{ $item->lpp_rphretursales }}</td>
        <td>{{ $item->lpp_rphrafak }}</td>
        <td>{{ $item->lpp_qtyrepack }}</td>
        <td>{{ $item->lpp_rphrepack }}</td>
        <td>{{ $item->lpp_qtylainin }}</td>
        <td>{{ $item->lpp_rphlainin }}</td>
        <td>{{ $item->lpp_qtysales }}</td>
        <td>{{ $item->lpp_rphsales }}</td>
        <td>{{ $item->lpp_qtykirim }}</td>
        <td>{{ $item->lpp_rphkirim }}</td>
        <td>{{ $item->lpp_qtyprepacking }}</td>
        <td>{{ $item->lpp_rphprepacking }}</td>
        <td>{{ $item->lpp_qtyhilang }}</td>
        <td>{{ $item->lpp_rphhilang }}</td>
        <td>{{ $item->lpp_qtylainout }}</td>
        <td>{{ $item->lpp_rphlainout }}</td>
        <td>{{ $item->lpp_qtyintransit }}</td>
        <td>{{ $item->lpp_rphintransit }}</td>
        <td>{{ $item->lpp_qtyadj }}</td>
        <td>{{ $item->lpp_rphadj }}</td>
        <td>{{ $item->lpp_soadj }}</td>
        <td>{{ $item->lpp_qtyakhir }}</td>
        <td>{{ $item->lpp_rphakhir }}</td>
        <td>{{ $item->lpp_avgcost }}</td>
        <td>{{ $item->lpp_qty_selisih_so }}</td>
        <td>{{ $item->lpp_rph_selisih_so }}</td>
        <td>{{ $item->lpp_qty_selisih_soic }}</td>
        <td>{{ $item->lpp_rph_selisih_soic }}</td>
        <td>{{ $item->koreksi }}</td>
    </tr>
    @endforeach
    <tr><td></td></tr>
    <tr><td></td></tr>
    @endif

    @if(isset($lpp_rusak))
    <tr>
        <td colspan="4" style="font-weight: 500; font-size: 14px">LPP BARANG RUSAK</td>
    </tr>
    <tr>
        <td>periode : {{ $periode }}</td>
    </tr>
    <tr><td></td></tr>
    <tr>
        <td>Cabang</td>
        <td>Tgl Awal</td>
        <td>Tgl Akhir</td>
        <td>Divisi</td>
        <td>Dept</td>
        <td>Kategori</td>
        <td>PLU</td>
        <td>Deskripsi</td>
        <td>Unit</td>
        <td>LPP_QTYBEGBAL</td>
        <td>LPP_RPHBEGBAL</td>
        <td>LPP_QTYBELI</td>
        <td>LPP_RPHBELI</td>
        <td>LPP_QTYBONUS</td>
        <td>LPP_RPHBONUS</td>
        <td>LPP_QTYTRMCB</td>
        <td>LPP_RPHTRMCB</td>
        <td>LPP_QTYRETURSALES</td>
        <td>LPP_RPHRETURSALES</td>
        <td>LPP_RPHRAFAK</td>
        <td>LPP_QTYREPACK</td>
        <td>LPP_RPHREPACK</td>
        <td>LPP_QTYLAININ</td>
        <td>LPP_RPHLAININ</td>
        <td>LPP_QTYSALES</td>
        <td>LPP_RPHSALES</td>
        <td>LPP_QTYKIRIM</td>
        <td>LPP_RPHKIRIM</td>
        <td>LPP_QTYPREPACKING</td>
        <td>LPP_RPHPREPACKING</td>
        <td>LPP_QTYHILANG</td>
        <td>LPP_RPHHILANG</td>
        <td>LPP_QTYLAINOUT</td>
        <td>LPP_RPHLAINOUT</td>
        <td>LPP_QTYINTRANSIT</td>
        <td>LPP_RPHINTRANSIT</td>
        <td>LPP_QTYADJ</td>
        <td>LPP_RPHADJ</td>
        <td>LPP_SOADJ</td>
        <td>LPP_QTY_SELISIH_SO</td>
        <td>LPP_RPH_SELISIH_SO</td>
        <td>LPP_QTY_SELISIH_SOIC</td>
        <td>LPP_RPH_SELISIH_SOIC</td>
        <td>LPP_QTYAKHIR</td>
        <td>LPP_RPHAKHIR</td>
        <td>LPP_AVGCOST</td>
        <td>KOREKSI</td>
    </tr>
    @foreach ($lpp_rusak as $item)
    <tr>
        <td>{{ $item->lpp_kodeigr }}</td>
        <td>{{ $item->lpp_tgl1 }}</td>
        <td>{{ $item->lpp_tgl2 }}</td>
        <td>{{ $item->prd_kodedivisi }}</td>
        <td>{{ $item->prd_kodedepartement }}</td>
        <td>{{ $item->prd_kodekategoribarang }}</td>
        <td>{{ $item->prd_prdcd }}</td>
        <td>{{ $item->prd_deskripsipanjang }}</td>
        <td>{{ $item->prd_unit . '/' . $item->prd_frac }}</td>
        <td>{{ $item->lpp_qtybegbal }}</td>
        <td>{{ $item->lpp_rphbegbal }}</td>
        <td>{{ $item->lpp_qtybeli }}</td>
        <td>{{ $item->lpp_rphbeli }}</td>
        <td>{{ $item->lpp_qtybonus }}</td>
        <td>{{ $item->lpp_rphbonus }}</td>
        <td>{{ $item->lpp_qtytrmcb }}</td>
        <td>{{ $item->lpp_rphtrmcb }}</td>
        <td>{{ $item->lpp_qtyretursales }}</td>
        <td>{{ $item->lpp_rphretursales }}</td>
        <td>{{ $item->lpp_rphrafak }}</td>
        <td>{{ $item->lpp_qtyrepack }}</td>
        <td>{{ $item->lpp_rphrepack }}</td>
        <td>{{ $item->lpp_qtylainin }}</td>
        <td>{{ $item->lpp_rphlainin }}</td>
        <td>{{ $item->lpp_qtysales }}</td>
        <td>{{ $item->lpp_rphsales }}</td>
        <td>{{ $item->lpp_qtykirim }}</td>
        <td>{{ $item->lpp_rphkirim }}</td>
        <td>{{ $item->lpp_qtyprepacking }}</td>
        <td>{{ $item->lpp_rphprepacking }}</td>
        <td>{{ $item->lpp_qtyhilang }}</td>
        <td>{{ $item->lpp_rphhilang }}</td>
        <td>{{ $item->lpp_qtylainout }}</td>
        <td>{{ $item->lpp_rphlainout }}</td>
        <td>{{ $item->lpp_qtyintransit }}</td>
        <td>{{ $item->lpp_rphintransit }}</td>
        <td>{{ $item->lpp_qtyadj }}</td>
        <td>{{ $item->lpp_rphadj }}</td>
        <td>{{ $item->lpp_soadj }}</td>
        <td>{{ $item->lpp_qtyakhir }}</td>
        <td>{{ $item->lpp_rphakhir }}</td>
        <td>{{ $item->lpp_avgcost }}</td>
        <td>{{ $item->lpp_qty_selisih_so }}</td>
        <td>{{ $item->lpp_rph_selisih_so }}</td>
        <td>{{ $item->lpp_qty_selisih_soic }}</td>
        <td>{{ $item->lpp_rph_selisih_soic }}</td>
        <td>{{ $item->koreksi }}</td>
    </tr>
    @endforeach
    <tr><td></td></tr>
    <tr><td></td></tr>
    @endif

</table>
