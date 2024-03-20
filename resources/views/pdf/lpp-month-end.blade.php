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
            <td>Tglal</td>
            <td>Tglir</td>
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
            <td>Tgl</td>
            <td>Tgl</td>
            <td>Divisi</td>
            <td>Dept</td>
            <td>Kategori</td>
            <td>PLU</td>
            <td>Deskripsi</td>
            <td>Unit</td>
            <td>LRT_QTYBEGBAL</td>
            <td>LRT_RPHBEGBAL</td>
            <td>LRT_QTYBAIK</td>
            <td>LRT_RPHBAIK</td>
            <td>LRT_QTYRUSAK</td>
            <td>LRT_RPHRUSAK</td>
            <td>LRT_QTYSUPPLIER</td>
            <td>LRT_RPHSUPPLIER</td>
            <td>LRT_QTYHILANG</td>
            <td>LRT_RPHHILANG</td>
            <td>LRT_QTYLBAIK</td>
            <td>LRT_RPHLBAIK</td>
            <td>LRT_QTYLRUSAK</td>
            <td>LRT_RPHLRUSAK</td>
            <td>LRT_QTYADJ</td>
            <td>LRT_RPHADJ</td>
            <td>LRT_SOADJ</td>
            <td>LRT_QTY_SELISIH_SO</td>
            <td>LRT_RPH_SELISIH_SO</td>
            <td>LRT_QTY_SELISIH_SOIC</td>
            <td>LRT_RPH_SELISIH_SOIC</td>
            <td>LRT_QTYAKHIR</td>
            <td>LRT_RPHAKHIR</td>
            <td>LRT_AVGCOST1</td>
            <td>LRT_AVGCOST</td>
            <td>KOREKSI</td>
        </tr>
        @foreach ($lpp_retur as $item)
        <tr>
            <td>{{ $item->lrt_kodeigr }}</td>
            <td>{{ $item->lrt_tgl1 }}</td>
            <td>{{ $item->lrt_tgl2 }}</td>
            <td>{{ $item->prd_kodedivisi }}</td>
            <td>{{ $item->prd_kodedepartement }}</td>
            <td>{{ $item->prd_kodekategoribarang }}</td>
            <td>{{ $item->prd_prdcd }}</td>
            <td>{{ $item->prd_deskripsipanjang }}</td>
            <td>{{ $item->prd_unit . '/' . $item->prd_frac }}</td>
            <td>{{ $item->lrt_qtybegbal }}</td>
            <td>{{ $item->lrt_rphbegbal }}</td>
            <td>{{ $item->lrt_qtybaik }}</td>
            <td>{{ $item->lrt_rphbaik }}</td>
            <td>{{ $item->lrt_qtyrusak }}</td>
            <td>{{ $item->lrt_rphrusak }}</td>
            <td>{{ $item->lrt_qtysupplier }}</td>
            <td>{{ $item->lrt_rphsupplier }}</td>
            <td>{{ $item->lrt_qtyhilang }}</td>
            <td>{{ $item->lrt_rphhilang }}</td>
            <td>{{ $item->lrt_qtylbaik }}</td>
            <td>{{ $item->lrt_rphlbaik }}</td>
            <td>{{ $item->lrt_qtylrusak }}</td>
            <td>{{ $item->lrt_rphlrusak }}</td>
            <td>{{ $item->lrt_qtyadj }}</td>
            <td>{{ $item->lrt_rphadj }}</td>
            <td>{{ $item->lrt_soadj }}</td>
            <td>{{ $item->lrt_qty_selisih_so }}</td>
            <td>{{ $item->lrt_rph_selisih_so }}</td>
            <td>{{ $item->lrt_qty_selisih_soic }}</td>
            <td>{{ $item->lrt_rph_selisih_soic }}</td>
            <td>{{ $item->lrt_qtyakhir }}</td>
            <td>{{ $item->lrt_rphakhir }}</td>
            <td>{{ $item->lrt_avgcost1 }}</td>
            <td>{{ $item->lrt_avgcost }}</td>
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
            <td>Tgl_Awal</td>
            <td>Tgl_Akhir</td>
            <td>Divisi</td>
            <td>Dept</td>
            <td>Kategori</td>
            <td>PLU</td>
            <td>Deskripsi</td>
            <td>Unit</td>
            <td>LRS_QTYBEGBAL</td>
            <td>LRS_RPHBEGBAL</td>
            <td>LRS_QTYBAIK</td>
            <td>LRS_RPHBAIK</td>
            <td>LRS_QTYRETUR</td>
            <td>LRS_RPHRETUR</td>
            <td>LRS_QTYMUSNAH</td>
            <td>LRS_RPHMUSNAH</td>
            <td>LRS_QTYHILANG</td>
            <td>LRS_RPHHILANG</td>
            <td>LRS_QTYLBAIK</td>
            <td>LRS_RPHLBAIK</td>
            <td>LRS_QTYLRETUR</td>
            <td>LRS_RPHLRETUR</td>
            <td>LRS_QTYADJ</td>
            <td>LRS_RPHADJ</td>
            <td>LRS_SOADJ</td>
            <td>LRS_QTY_SELISIH_SO</td>
            <td>LRS_RPH_SELISIH_SO</td>
            <td>LRS_QTY_SELISIH_SOIC</td>
            <td>LRS_RPH_SELISIH_SOIC</td>
            <td>LRS_QTYAKHIR</td>
            <td>LRS_RPHAKHIR</td>
            <td>LRS_AVGCOST1</td>
            <td>LRS_AVGCOST</td>
            <td>KOREKSI</td>
        </tr>
        @foreach ($lpp_rusak as $item)
        <tr>
            <td>{{ $item->lrs_kodeigr }}</td>
            <td>{{ $item->lrs_tgl1 }}</td>
            <td>{{ $item->lrs_tgl2 }}</td>
            <td>{{ $item->prd_kodedivisi }}</td>
            <td>{{ $item->prd_kodedepartement }}</td>
            <td>{{ $item->prd_kodekategoribarang }}</td>
            <td>{{ $item->prd_prdcd }}</td>
            <td>{{ $item->prd_deskripsipanjang }}</td>
            <td>{{ $item->prd_unit . '/' . $item->prd_frac }}</td>
            <td>{{ $item->lrs_qtybegbal }}</td>
            <td>{{ $item->lrs_rphbegbal }}</td>
            <td>{{ $item->lrs_qtybaik }}</td>
            <td>{{ $item->lrs_rphbaik }}</td>
            <td>{{ $item->lrs_qtyretur }}</td>
            <td>{{ $item->lrs_rphretur }}</td>
            <td>{{ $item->lrs_qtymusnah }}</td>
            <td>{{ $item->lrs_rphmusnah }}</td>
            <td>{{ $item->lrs_qtyhilang }}</td>
            <td>{{ $item->lrs_rphhilang }}</td>
            <td>{{ $item->lrs_qtylbaik }}</td>
            <td>{{ $item->lrs_rphlbaik }}</td>
            <td>{{ $item->lrs_qtylretur }}</td>
            <td>{{ $item->lrs_rphlretur }}</td>
            <td>{{ $item->lrs_qtyadj }}</td>
            <td>{{ $item->lrs_rphadj }}</td>
            <td>{{ $item->lrs_soadj }}</td>
            <td>{{ $item->lrs_qty_selisih_so }}</td>
            <td>{{ $item->lrs_rph_selisih_so }}</td>
            <td>{{ $item->lrs_qty_selisih_soic }}</td>
            <td>{{ $item->lrs_rph_selisih_soic }}</td>
            <td>{{ $item->lrs_qtyakhir }}</td>
            <td>{{ $item->lrs_rphakhir }}</td>
            <td>{{ $item->lrs_avgcost1 }}</td>
            <td>{{ $item->lrs_avgcost }}</td>
            <td>{{ $item->koreksi }}</td>
        </tr>
        @endforeach
        <tr><td></td></tr>
        <tr><td></td></tr>
    @endif

</table>
