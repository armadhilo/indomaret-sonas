<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\ReportDaftarItemAdjustRequest;
use App\Http\Requests\ReportDaftarItemBelumAdaDiMasterRequest;
use App\Http\Requests\ReportInqueryPlanoSonasRequest;
use App\Http\Requests\ReportLokasiRakBelumDiSoRequest;
use App\Http\Requests\ReportMasterLokasiSoRequest;
use App\Http\Requests\ReportPerincianBasoRequest;
use App\Http\Requests\ReportRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetLimitSoController extends Controller
{
    private $FlagTahap;
    private $flagTransferLokasi;
    public function __construct(Request $request){
        DatabaseConnection::setConnection(session('KODECABANG'), "PRODUCTION");
    }

    public function index(){

        $dtCek = DB::select("SELECT DATE_TRUNC('DAY',MSO_TGLSO) MSO_TGLSO, MSO_FLAG_CREATELSO FROM TBMASTER_SETTING_SO ORDER BY MSO_TGLSO DESC");
        if(count($dtCek) == 0){
            return ApiFormatter::error(400, 'Data SO tidak ditemukan');
        }

        $data['TanggalSO'] = $dtCek[0]->mso_tglso;
        $this->flagTransferLokasi = $dtCek[0]->mso_flag_createlso;

        return view('proses-ba-so', $data);
    }

    public function reportListFormKkso(ReportRequest $request){

        $query = '';
        $query .= "SELECT lso_tglso, lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lso_nourut, ";
        $query .= "lso_prdcd, case when lso_lokasi = '01' then '01 - BARANG BAIK' else case when lso_lokasi = '02' then '02 - BARANG RETUR' else '03 - BARANG RUSAK' end end lokasi, prd_deskripsipanjang, prd_unit || '/' || prd_frac satuan, prd_kodetag, lso_tmp_qtyctn, lso_tmp_qtypcs ";
        $query .= "FROM tbtr_lokasi_so, tbmaster_prodmast ";
        $query .= "WHERE coalesce(lso_recid,'0') <> '1' and lso_koderak between '" & $request->koderak1 & "' and '" & $request->koderak2 & "' ";
        $query .= "AND LSO_TGLSO = TO_DATE('" & $request->tanggal_start_so & "','DD-MM-YYYY') ";
        $query .= "AND LSO_LOKASI = '" & $request->jenisbrg & "'  ";
        $query .= "AND lso_kodesubrak between '" & $request->subrak1 & "' and '" & $request->subrak2 & "' ";
        $query .= "AND lso_tiperak between '" & $request->tipe1 & "' and '" & $request->tipe2 & "' ";
        $query .= "AND lso_shelvingrak between '" & $request->shelving1 & "' and '" & $request->shelving2 & "' ";
        $query .= "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR ";
        $query .= "AND coalesce(lso_flagsarana, 'K') = 'K' ";
        if($this->flagTransferLokasi == 'Y'){
            $query .= "AND LSO_FLAGLIMIT='Y' ";
        }
        $query .= "Order By lso_lokasi, lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lso_nourut  ";
        $data = DB::select($query);

        //! UPDATE
        foreach($data as $item){
            DB::table('tbtr_lokasi_so')
                ->whereDate('lso_tglso', $request->tanggal_start_so)
                ->where('lso_prdcd', $item->lso_prdcd)
                ->update(['lso_flagkkso' => 'Y']);
        }

        $data['data'] = $data;

        return $data;
    }

    public function reportRegisterKkso1(ReportRequest $request){

        $query = '';
        $query .= "SELECT rak1, rak2, sub1, sub2, shel1, shel2, tipe1, tipe2, lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lokasi, SUM(item) item, ";
        $query .= "floor(( (sum(ITEM)+10+(1*2)) / 25) + CASE WHEN  MOD((sum(ITEM)+10+(1*2)), 25) <> 0 THEN 1 ELSE 0 END) lbr FROM ( ";
        $query .= "SELECT '" . $request->koderak1 . "' rak1, '" . $request->koderak2 . "' rak2, '" . $request->subrak1 . "' sub1, '" . $request->subrak2 . "' sub2, ";
        $query .= "'" . $request->tipe1 . "' tipe1, '" . $request->tipe2 . "' tipe2, '" . $request->shelving1 . "' shel1, '" . $request->shelving2 . "' shel2, ";
        $query .= "lso_tglso, lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lso_nourut, ";
        $query .= "lso_prdcd, 1 item, case when lso_lokasi = '01' then '01 - BARANG BAIK' else case when lso_lokasi = '02' then '02 - BARANG RETUR' else '03 - BARANG RUSAK' end end lokasi, prd_deskripsipanjang, prd_unit || '/' || prd_frac satuan, prd_kodetag ";
        $query .= "FROM tbtr_lokasi_so, tbmaster_prodmast ";
        $query .= "WHERE coalesce(lso_recid,'0') <> '1' and lso_koderak between '" . $request->koderak1 . "' and '" . $request->koderak2 . "' ";
        $query .= "AND LSO_TGLSO = TO_DATE('" . $request->tanggal_start_so . "','DD-MM-YYYY') ";
        $query .= "AND lso_kodesubrak between '" . $request->subrak1 . "' and '" . $request->subrak2 . "' ";
        $query .= "AND lso_tiperak between '" . $request->tipe1 . "' and '" . $request->tipe2 . "' ";
        $query .= "AND lso_shelvingrak between '" . $request->shelving1 . "' and '" . $request->shelving2 . "' ";
        $query .= "AND lso_lokasi = '" . $request->jenisbrg . "' ";
        $query .= "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR ";
        if($this->flagTransferLokasi == 'Y'){
            $query .= "AND LSO_FLAGLIMIT='Y' ";
        }
        $query .= " ) A ";
        $query .= "GROUP BY rak1, rak2, sub1, sub2, shel1, shel2, tipe1, tipe2, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LOKASI ";
        $query .= "ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LOKASI ";
        $data = DB::select($query);

        //! GET NAMA PERUSAHAAN
        $perusahaan = DB::table('tbmaster_perusahaan')
            ->select('prs_kodeigr as kode_igr', 'prs_namacabang')
            ->first();

        $data['data'] = $data;
        $data['perusahaan'] = $perusahaan;

        return $data;
    }

    public function reportEditListKkso(ReportRequest $request){

        $FlagReset = 'N';
        $dtCek = DB::select("SELECT coalesce(MSO_FLAGSUM, 'N') MSO_FLAGSUM FROM TBMASTER_SETTING_SO WHERE MSO_TGLSO =  TO_DATE('" . $request->tanggal_start_so . "','DD-MM-YYYY')");
        if(count($dtCek) <> 0){
            $FlagReset = $dtCek[0]->mso_flagsum;
        }

        $query = '';
        $query .= "SELECT to_char(LSO_TGLSO, 'dd-MM-yyyy') LSO_TGLSO, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT, LSO_PRDCD, ";
        $query .= "CASE WHEN LSO_LOKASI = '01' THEN '01 - BARANG BAIK' ELSE CASE WHEN LSO_LOKASI = '02' THEN '02 - BARANG RETUR' ELSE '03 - BARANG RUSAK' End END LOKASI, ";
        $query .= "PRD_DESKRIPSIPANJANG, PRD_UNIT || '/' || PRD_FRAC SATUAN, PRD_KODETAG, ";
        $query .= "CASE WHEN LSO_FLAGSARANA = 'K' THEN FLOOR (LSO_QTY / PRD_FRAC) ELSE LSO_TMP_QTYCTN END CTN, CASE WHEN LSO_FLAGSARANA = 'K' THEN MOD (LSO_QTY, PRD_FRAC) ELSE LSO_TMP_QTYPCS END PCS, LSO_QTY, ";

        if($FlagReset == 'Y'){
            $query .= "(LSO_QTY * CASE WHEN PRD_UNIT = 'KG' THEN (SOP_NEWAVGCOST / 1000) ELSE SOP_NEWAVGCOST End ) TOTAL, ";
            $query .= "SOP_NEWAVGCOST ST_AVGCOSTMONTHEND, ";
        }else{
            $query .= "ST_AVGCOST ST_AVGCOSTMONTHEND, ";
            $query .= "(LSO_QTY * CASE WHEN PRD_UNIT = 'KG' THEN (ST_AVGCOST / 1000) ELSE ST_AVGCOST End ) TOTAL, ";
        }

        $query .= "LSO_MODIFY_BY, sop_prdcd, sop_newavgcost, sop_tglso ";
        $query .= "FROM TBTR_LOKASI_SO, TBMASTER_PRODMAST, TBMASTER_STOCK, tbtr_ba_stockopname ";
        $query .= "WHERE coalesce (LSO_RECID, '0') <> '1' ";
        $query .= "AND LSO_TGLSO = TO_DATE('" . $request->tanggal_start_so . "','DD-MM-YYYY') ";
        $query .= "AND LSO_KODERAK BETWEEN '" . $request->koderak1 . "' and '" . $request->koderak2 . "' ";
        $query .= "AND LSO_KODESUBRAK BETWEEN '" . $request->subrak1 . "' and '" . $request->subrak2 . "' ";
        $query .= "AND LSO_TIPERAK BETWEEN '" . $request->tipe1 . "' and '" . $request->tipe2 . "' ";
        $query .= "AND LSO_SHELVINGRAK BETWEEN '" . $request->shelving1 . "' and '" . $request->shelving2 . "' ";
        $query .= "AND LSO_LOKASI = '" . $request->jenisbrg . "' ";
        $query .= "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR ";
        $query .= "AND ST_PRDCD = LSO_PRDCD AND ST_LOKASI = LSO_LOKASI ";
        $query .= "and sop_tglso = TO_DATE('" . $request->tanggal_start_so . "','DD-MM-YYYY') ";
        $query .= "AND sop_lokasi = st_lokasi and sop_prdcd = st_prdcd ";
        $query .= "ORDER BY LSO_TGLSO, LOKASI, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT ";
        $data = DB::select($query);

        //! GET NAMA PERUSAHAAN
        $perusahaan = DB::table('tbmaster_perusahaan')
            ->select('prs_kodeigr as kode_igr', 'prs_namacabang')
            ->first();

        $data['data'] = $data;
        $data['perusahaan'] = $perusahaan;

        return $data;
    }

    public function reportRegisterKkso2(ReportRequest $request){
        $query = '';
        $query .= "SELECT   RAK1, RAK2, SUB1, SUB2, SHEL1, SHEL2, TIPE1, TIPE2, LSO_KODERAK, LSO_TGLSO, LSO_KODESUBRAK, ";
        $query .= "LSO_TIPERAK, LSO_SHELVINGRAK, LOKASI, SUM (ITEM) ITEM, SUM (SO) SO, (SUM(ITEM) - SUM(SO)) SELISIH, ";
        $query .= "FLOOR (  ((SUM (ITEM) + 10 + (1 * 2)) / 25) + CASE WHEN MOD ((SUM (ITEM) + 10 + (1 * 2)), 25) <> 0 THEN 1 ELSE 0 END) LBR ";
        $query .= "FROM (SELECT '" & $request->koderak1 & "' rak1, '" & $request->koderak2 & "' rak2, '" & $request->subrak1 & "' sub1, '" & $request->subrak2 & "' sub2, ";
        $query .= "'" & $request->tipe1 & "' tipe1, '" & $request->tipe2 & "' tipe2, '" & $request->shelving1 & "' shel1, '" & $request->shelving2 & "' shel2, ";
        $query .= "TO_CHAR(LSO_TGLSO, 'dd-MM-yyyy') LSO_TGLSO, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT, LSO_PRDCD, 1 ITEM, ";
        $query .= "CASE WHEN LSO_QTY <> 0 THEN 1 ELSE 0 END SO, ";
        $query .= "CASE WHEN LSO_LOKASI = '01' THEN '01 - BARANG BAIK' ELSE CASE WHEN LSO_LOKASI = '02' THEN '02 - BARANG RETUR' ELSE '03 - BARANG RUSAK' END END LOKASI, ";
        $query .= "PRD_DESKRIPSIPANJANG, PRD_UNIT || '/' || PRD_FRAC SATUAN, PRD_KODETAG FROM TBTR_LOKASI_SO, TBMASTER_PRODMAST ";
        $query .= "WHERE coalesce (LSO_RECID, '0') <> '1' AND LSO_KODERAK BETWEEN '" & $request->koderak1 & "' and '" & $request->koderak2 & "' ";
        $query .= "AND LSO_TGLSO = TO_DATE('" & $request->tanggal_start_so & "','DD-MM-YYYY') ";
        $query .= "AND LSO_KODESUBRAK BETWEEN '" & $request->subrak1 & "' and '" & $request->subrak2 & "' AND LSO_TIPERAK BETWEEN '" & $request->tipe1 & "' and '" & $request->tipe2 & "' ";
        $query .= "AND LSO_SHELVINGRAK BETWEEN '" & $request->shelving1 & "' and '" & $request->shelving2 & "'  AND LSO_LOKASI = '" & $request->jenisbrg & "' ";
        if($this->flagTransferLokasi == 'Y'){
            $query .= "AND LSO_FLAGLIMIT='Y' ";
        }
        $query .= "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR ) A ";
        $query .= "GROUP BY RAK1,  RAK2, SUB1, SUB2, SHEL1, SHEL2, TIPE1, TIPE2, LSO_KODERAK, LSO_TGLSO, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LOKASI ";
        $query .= "ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LOKASI ";
        $data = DB::select($query);

        $data['data'] = $data;

        return $data;
    }

    public function reportPerincianBaso(ReportPerincianBasoRequest $request){
        $query = '';
        $query .= "SELECT * FROM (SELECT PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, DEP_NAMADEPARTEMENT, PRD_KODEKATEGORIBARANG, ";
        $query .= "KAT_NAMAKATEGORI, PRD_PRDCD, PRD_DESKRIPSIPANJANG, PRD_UNIT || '/' || PRD_FRAC SATUAN, ";
        $query .= "TO_CHAR(SOP_TGLSO, 'dd-MM-yyyy') SOP_TGLSO, CASE WHEN '" . $request->selisih_so . "' = '1' THEN 'ALL' ELSE CASE WHEN '" . $request->selisih_so . "' = '2' THEN '< -1000000' ELSE '> 1000000' END END Lap, CASE WHEN SOP_LOKASI = '01' THEN '01 - Barang Baik' ELSE CASE WHEN SOP_LOKASI = '02' THEN '02 - Barang Retur' ";
        $query .= "ELSE '03 = Barang Rusak' END END LOKASI, (sop_newavgcost * CASE WHEN PRD_UNIT = 'KG' THEN 1000 ELSE 1 END) SOP_LASTAVGCOST, (sop_newavgcost * PRD_FRAC) HPP, ";
        $query .= "SOP_QTYLPP, FLOOR (SOP_QTYLPP / PRD_FRAC) CTNLPP, MOD (SOP_QTYLPP, PRD_FRAC) PCSLPP, (case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * SOP_QTYLPP) RPHLPP, SOP_QTYSO, ";
        $query .= "FLOOR (SOP_QTYSO / PRD_FRAC) CTNSO, MOD (SOP_QTYSO, PRD_FRAC) PCSSO, (case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * SOP_QTYSO) RPHSO, coalesce (QTY_ADJ, 0) QTY_ADJ, ";
        $query .= "case when FLOOR (coalesce (QTY_ADJ, 0) / PRD_FRAC) < 0 then CEIL (coalesce (QTY_ADJ, 0) / PRD_FRAC) else FLOOR (coalesce (QTY_ADJ, 0) / PRD_FRAC) end CTNADJ, MOD (coalesce (QTY_ADJ, 0), PRD_FRAC) PCSADJ, (case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * coalesce (QTY_ADJ, 0)) RPHADJ, ";
        $query .= "case when FLOOR ((SOP_QTYSO + coalesce (QTY_ADJ, 0) - SOP_QTYLPP) / PRD_FRAC) < 0 then CEIL ((SOP_QTYSO + coalesce (QTY_ADJ, 0) - SOP_QTYLPP) / PRD_FRAC) else FLOOR ((SOP_QTYSO + coalesce (QTY_ADJ, 0) - SOP_QTYLPP ) / PRD_FRAC) end CTNSEL, MOD ((SOP_QTYSO + coalesce (QTY_ADJ, 0) - SOP_QTYLPP ), PRD_FRAC) PCSSEL, ";
        $query .= "(case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * (SOP_QTYSO + coalesce (QTY_ADJ, 0) - SOP_QTYLPP )) RPHSEL ";
        $query .= " FROM TBMASTER_PRODMAST";
        $query .= " JOIN TBTR_BA_STOCKOPNAME";
        $query .= "      ON PRD_PRDCD = SOP_PRDCD ";
        $query .= "      AND SOP_LOKASI = '" . $request->jenisbrg . "'";
        $query .= "      AND SOP_TGLSO = TO_DATE('" . $request->tanggal_start_so . "', 'DD-MM-YYYY')";
        $query .= " JOIN TBMASTER_DEPARTEMENT ";
        $query .= "      ON DEP_KODEDEPARTEMENT = PRD_KODEDEPARTEMENT";
        $query .= " LEFT JOIN TBMASTER_KATEGORI ";
        $query .= "      ON KAT_KODEKATEGORI = PRD_KODEKATEGORIBARANG ";
        $query .= "      AND KAT_KODEDEPARTEMENT = PRD_KODEDEPARTEMENT ";
        $query .= " LEFT JOIN ";
        $query .= " (";
        $query .= "        Select ";
        $query .= "        ADJ_KODEIGR,";
        $query .= "        ADJ_TGLSO, ";
        $query .= "        ADJ_PRDCD, ";
        $query .= "        ADJ_LOKASI, ";
        $query .= "        SUM (coalesce (ADJ_QTY, 0)) QTY_ADJ ";
        $query .= "        FROM TBTR_ADJUSTSO ";
        $query .= "        GROUP BY ADJ_KODEIGR, ADJ_TGLSO, ADJ_PRDCD, ADJ_LOKASI";
        $query .= "  ) AS DATAS ";
        $query .= "    ON ADJ_KODEIGR = SOP_KODEIGR  ";
        $query .= "    AND ADJ_TGLSO = SOP_TGLSO ";
        $query .= "    AND ADJ_PRDCD = SOP_PRDCD ";
        $query .= "    AND ADJ_LOKASI = SOP_LOKASI ";
        $query .= "WHERE PRD_KODEDIVISI BETWEEN '" . $request->div1 . "' AND '" . $request->div2 . "' ";
        $query .= "AND PRD_KODEDEPARTEMENT BETWEEN '" . $request->dept1 . "' AND '" . $request->dept2 . "' ";
        $query .= "AND PRD_KODEKATEGORIBARANG BETWEEN '" . $request->kat1 . "' AND '" . $request->kat2 . "' ";
        $query .= "AND PRD_PRDCD BETWEEN '" . $request->plu1 . "' AND '" . $request->plu2 . "' ";
        $query .= "AND PRD_PRDCD LIKE '%0' ";
        $query .= "Order by PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, LOKASI, PRD_PRDCD ) A ";

        if($request->selisih_so == '2'){
            $query .= "WHERE RPHSO - RPHLPP < -1000000 ";
        }elseif($request->selisih_so == '3'){
            $query .= "WHERE RPHSO - RPHLPP > 1000000 ";
        }

        if($request->check_rpt_audit == 1){
            $dtCek = DB::select("SELECT DISTINCT LSI_PRDCD FROM TBTR_LOKASI_SO_EY WHERE DATE_TRUNC('DAY',LSI_TGLSO) = TO_DATE('" & $request->tanggal_start_so & "', 'DD-MM-YYYY')");
            if(count($dtCek) > 0){
                if($request->selisih_so != '1'){
                    $query .= "AND ";
                }else{
                    $query .= "WHERE ";
                }

                $query .= "PRD_PRDCD IN (";

                foreach($dtCek as $key => $item){

                    if($key == count($dtCek) - 1){
                        $query .= "'" . $item->lsi_prdcd . "'";
                    }else{
                        $query .= "'" . $item->lsi_prdcd . "',";
                    }
                }

                $query .= ")";
            }
        }

        $data = DB::select($query);

        $data['data'] = $data;

        return $data;
    }

    public function reportRingkasanBaso(ReportPerincianBasoRequest $request){
        $query = '';
        $query .= "SELECT PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, DEP_NAMADEPARTEMENT, PRD_KODEKATEGORIBARANG, ";
        $query .= "KAT_NAMAKATEGORI, SOP_TGLSO, LOKASI, SUM (RPHLPP) RPHLPP, SUM (RPHSO) RPHSO, ";
        $query .= "SUM (RPHADJ) RPHADJ, SUM (RPHSEL) RPHSEL ";
        $query .= "FROM (SELECT PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, DEP_NAMADEPARTEMENT, PRD_KODEKATEGORIBARANG, ";
        $query .= "KAT_NAMAKATEGORI, PRD_PRDCD, TO_CHAR (SOP_TGLSO, 'dd-MM-yyyy') SOP_TGLSO, ";
        $query .= "CASE WHEN SOP_LOKASI = '01' THEN '01 - Barang Baik' ELSE CASE WHEN SOP_LOKASI = '02' THEN '02 - Barang Retur' ";
        $query .= "ELSE '03 = Barang Rusak' END END LOKASI, (case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * SOP_QTYLPP) RPHLPP, (case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * SOP_QTYSO) RPHSO, ";
        $query .= "(case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * coalesce (QTY_ADJ, 0)) RPHADJ, (case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * (SOP_QTYSO + coalesce (QTY_ADJ, 0) - SOP_QTYLPP)) RPHSEL ";
        $query .= "FROM TBMASTER_PRODMAST ";
        $query .= "JOIN TBTR_BA_STOCKOPNAME ";
        $query .= "   ON SOP_PRDCD = PRD_PRDCD  ";
        $query .= "   AND sop_tglso = TO_DATE('" & $request->tanggal_start_so & "', 'DD-MM-YYYY') ";
        $query .= "   AND SOP_LOKASI = '" & $request->jenisbrg & "' ";
        $query .= "LEFT JOIN TBMASTER_DEPARTEMENT ";
        $query .= "   ON DEP_KODEDEPARTEMENT = PRD_KODEDEPARTEMENT   ";
        $query .= "LEFT JOIN  TBMASTER_KATEGORI ";
        $query .= "   ON KAT_KODEKATEGORI = PRD_KODEKATEGORIBARANG ";
        $query .= "   AND KAT_KODEDEPARTEMENT = PRD_KODEDEPARTEMENT ";
        $query .= "LEFT JOIN ";
        $query .= "  (SELECT   ADJ_KODEIGR, ADJ_TGLSO, ADJ_PRDCD, ADJ_LOKASI, SUM (coalesce (ADJ_QTY, 0)) QTY_ADJ ";
        $query .= "   FROM TBTR_ADJUSTSO GROUP BY ADJ_KODEIGR, ADJ_TGLSO, ADJ_PRDCD, ADJ_LOKASI";
        $query .= "   )AS DATS ";
        $query .= " ON ADJ_KODEIGR = SOP_KODEIGR ";
        $query .= "    AND ADJ_TGLSO= SOP_TGLSO ";
        $query .= "    AND ADJ_PRDCD = SOP_PRDCD ";
        $query .= "    AND ADJ_LOKASI = SOP_LOKASI";
        $query .= " WHERE PRD_KODEDIVISI BETWEEN '" & $request->div1 & "' AND '" & $request->div2 & "' AND PRD_KODEDEPARTEMENT BETWEEN '" & $request->dept1 & "' AND '" & $request->dept2 & "' ";
        $query .= " AND PRD_KODEKATEGORIBARANG BETWEEN '" & $request->kat1 & "' AND '" & $request->kat2 & "' AND PRD_PRDCD LIKE '%0'";
        $query .= ") A ";
        $query .= "GROUP BY PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, DEP_NAMADEPARTEMENT, PRD_KODEKATEGORIBARANG, KAT_NAMAKATEGORI, SOP_TGLSO, LOKASI ";
        $query .= "ORDER BY LOKASI, PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG ";
        $data = DB::select($query);

        $data['data'] = $data;

        return $data;
    }

    public function reportDaftarItemYangSudahAdjust(ReportDaftarItemAdjustRequest $request){

        $query = '';
        $query .= "SELECT adj_prdcd ";
        $query .= "FROM tbtr_adjustso, tbtr_ba_stockopname, tbmaster_prodmast ";
        $query .= "WHERE adj_tglso = TO_DATE('" . $request->tanggal_start_so . "','DD-MM-YYYY') AND adj_lokasi = '" . $request->jenisbrg . "' ";
        $query .= "AND DATE_TRUNC('DAY',adj_create_dt) between TO_DATE('" . $request->tanggal_adjust_start . "','DD-MM-YYYY') AND TO_DATE('" . $request->tanggal_adjust_end . "','DD-MM-YYYY') ";
        $query .= "AND adj_prdcd BETWEEN '" . $request->plu1 . "' AND '" . $request->plu2 . "' AND sop_tglso = adj_tglso and sop_prdcd = adj_prdcd AND sop_lokasi = adj_lokasi AND prd_Prdcd = adj_prdcd ";
        $query .= "ORDER BY adj_create_dt ";
        $dt1 = DB::select($query);

        $query = '';
        $query .= "SELECT adj_prdcd, adj_create_dt, prd_deskripsipanjang, adj_qty, adj_keterangan, sop_newavgcost sop_lastavgcost, case when prd_unit = 'KG' then (adj_qty * sop_newavgcost) / 1000 else (adj_qty * sop_newavgcost) end total, ";
        $query .= "CASE WHEN ADJ_LOKASI = '01' THEN '01 - BARANG BAIK' ELSE CASE WHEN ADJ_LOKASI = '02' THEN '02 - BARANG RETUR' ELSE '03 - BARANG RUSAK' End END LOKASI ";
        $query .= "FROM tbtr_adjustso, tbtr_ba_stockopname, tbmaster_prodmast ";
        $query .= "WHERE adj_tglso = TO_DATE('" . $request->tanggal_start_so . "','DD-MM-YYYY') AND adj_lokasi = '" . $request->jenisbrg . "' ";
        $query .= "AND DATE_TRUNC('DAY',adj_create_dt) between TO_DATE('" . $request->tanggal_adjust_start . "','DD-MM-YYYY') AND TO_DATE('" . $request->tanggal_adjust_end . "','DD-MM-YYYY') ";
        $query .= "AND adj_prdcd BETWEEN '" . $request->plu1 . "' AND '" . $request->plu2 . "' AND sop_tglso = adj_tglso and sop_prdcd = adj_prdcd AND sop_lokasi = adj_lokasi AND prd_Prdcd = adj_prdcd ";
        $query .= "ORDER BY adj_create_dt ";
        $dtDATA = DB::select($query);

        $array = [];
        foreach($dt1 as $item){
            $dtCek = collect($dtDATA)->where('adj_prdcd', $item->adj_prdcd)->first();

            if(!empty($dtCek)){
                $query = '';
                $query .= "SELECT adj_prdcd, adj_create_dt, prd_deskripsipanjang, adj_qty, adj_keterangan, sop_newavgcost sop_lastavgcost, case when prd_unit = 'KG' then (adj_qty * sop_newavgcost) / 1000 else (adj_qty * sop_newavgcost) end total, ";
                $query .= "CASE WHEN ADJ_LOKASI = '01' THEN '01 - BARANG BAIK' ELSE CASE WHEN ADJ_LOKASI = '02' THEN '02 - BARANG RETUR' ELSE '03 - BARANG RUSAK' End END LOKASI ";
                $query .= "FROM tbtr_adjustso, tbtr_ba_stockopname, tbmaster_prodmast ";
                $query .= "WHERE adj_tglso = TO_DATE('" . $request->tanggal_start_so . "','DD-MM-YYYY') AND adj_lokasi = '" . $request->jenisbrg . "' ";
                $query .= "AND DATE_TRUNC('DAY',adj_create_dt) between TO_DATE('" . $request->tanggal_adjust_start . "','DD-MM-YYYY') AND TO_DATE('" . $request->tanggal_adjust_end . "','DD-MM-YYYY') ";
                $query .= "AND adj_prdcd = '" . $item->adj_prdcd . "' AND sop_tglso = adj_tglso and sop_prdcd = adj_prdcd AND sop_lokasi = adj_lokasi AND prd_Prdcd = adj_prdcd ";
                $query .= "ORDER BY adj_create_dt ";
                $data_detail = DB::select($query);

                foreach($data_detail as $item_detail){
                    $array[] = $item_detail;
                }
            }
        }

        return $array;
    }

    public function reportDafterKksoAcost(ReportRequest $request){
        $query = '';
        $query .= "SELECT TO_CHAR (LSO_TGLSO, 'dd-MM-yyyy') LSO_TGLSO, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, ";
        $query .= "LSO_SHELVINGRAK, LSO_NOURUT, LSO_PRDCD, ";
        $query .= "CASE WHEN LSO_LOKASI = '01' THEN '01 - BARANG BAIK' ELSE CASE WHEN LSO_LOKASI = '02' THEN '02 - BARANG RETUR' ";
        $query .= "ELSE '03 - BARANG RUSAK' END END LOKASI, ";
        $query .= "PRD_DESKRIPSIPANJANG, PRD_UNIT || '/' || PRD_FRAC SATUAN, PRD_KODETAG, ";
        $query .= "FLOOR (LSO_QTY / PRD_FRAC) CTN, MOD (LSO_QTY, PRD_FRAC) PCS, LSO_QTY, ST_AVGCOST ";
        $query .= "FROM TBTR_LOKASI_SO, TBMASTER_PRODMAST, TBMASTER_STOCK ";
        $query .= "WHERE coalesce (LSO_RECID, '0') <> '1' ";
        $query .= "AND LSO_KODERAK BETWEEN '" . $request->koderak1 . "' AND '" . $request->koderak2 . "' ";
        $query .= "AND LSO_KODESUBRAK BETWEEN '" . $request->subrak1 . "' AND '" . $request->subrak2 . "' ";
        $query .= "AND LSO_TIPERAK BETWEEN '" . $request->tipe1 . "' AND '" . $request->tipe2 . "' ";
        $query .= "AND LSO_SHELVINGRAK BETWEEN '" . $request->shelving1 . "' AND '" . $request->shelving2 . "' ";
        $query .= "AND LSO_TGLSO = TO_DATE('" . $request->tanggal_start_so . "','DD-MM-YYYY') ";
        $query .= "AND LSO_LOKASI = '" . $request->jenisbrg . "' ";
        $query .= "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR ";
        $query .= "AND ST_PRDCD = LSO_PRDCD ";
        $query .= "AND ST_LOKASI = LSO_LOKASI ";
        $query .= "AND (coalesce(ST_AVGCOST,0) = 0 OR coalesce(ST_AVGCOST,0) < 0) ";
        $query .= "ORDER BY LSO_TGLSO, LOKASI, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT ";
        $data = DB::select($query);

        $data['data'] = $data;

        return $data;
    }

    public function reportDaftarMasterLokasiSo(ReportMasterLokasiSoRequest $request){

        $query = '';
        $query .= "SELECT   LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, ";
        $query .= "LSO_SHELVINGRAK, LSO_PRDCD, LSO_LOKASI, ";
        $query .= "PRD_DESKRIPSIPANJANG || ' - ' || PRD_UNIT || '/' || PRD_FRAC PRD_DESKRIPSIPANJANG, PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, ";
        $query .= "FLOOR (LSO_QTY / PRD_FRAC) CTN, MOD (LSO_QTY, PRD_FRAC) PCS, LSO_QTY ";
        $query .= "FROM TBTR_LOKASI_SO, TBMASTER_PRODMAST ";
        $query .= "WHERE coalesce (LSO_RECID, '0') <> '1' ";
        $query .= "AND LSO_TGLSO = TO_DATE('" . $request->tanggal_start_so . "','DD-MM-YYYY') ";
        $query .= "AND LSO_PRDCD = '" . $request->plu1 . "' ";
        $query .= "AND LSO_LOKASI = '" . $request->jenisbrg . "' ";
        $query .= "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR ";
        $query .= "ORDER BY PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_LOKASI  ";
        $data = DB::select($query);

        $data['data'] = $data;

        return $data;
    }

    public function reportDaftarItemBelumAdaDiMaster(ReportDaftarItemBelumAdaDiMasterRequest $request){

        $query = '';
        $query .= "SELECT * ";
        $query .= "FROM (SELECT PRD_KODEDIVISI, ";
        $query .= "DIV_NAMADIVISI, ";
        $query .= "PRD_KODEDEPARTEMENT, ";
        $query .= "DEP_NAMADEPARTEMENT, ";
        $query .= "PRD_KODEKATEGORIBARANG, ";
        $query .= "KAT_NAMAKATEGORI, ";
        $query .= "PRD_DESKRIPSIPANJANG, ";
        $query .= "PRD_UNIT || '/' || PRD_FRAC SATUAN, ";
        $query .= "PRD_KODETAG, ";
        $query .= "coalesce (LSO_PRDCD, '0000000') LSO_PRDCD, ";
        $query .= "TGL_SO_SETTING LSO_TGLSO, ";
        $query .= "ST_SALDOAKHIR, ";
        $query .= "prd_prdcd, ";
        $query .= "ST_AVGCOST, ";
        $query .= " ( ST_SALDOAKHIR * ST_AVGCOST) TOTAL ";
        $query .= "   FROM TBMASTER_PRODMAST ";
        $query .= "   LEFT JOIN TBMASTER_DIVISI ";
        $query .= "   ON DIV_KODEDIVISI = PRD_KODEDIVISI";
        $query .= "   LEFT JOIN TBMASTER_DEPARTEMENT ";
        $query .= "   ON DEP_KODEDEPARTEMENT = PRD_KODEDEPARTEMENT ";
        $query .= "   LEFT JOIN TBMASTER_KATEGORI ";
        $query .= "   ON KAT_KODEDEPARTEMENT = PRD_KODEDEPARTEMENT ";
        $query .= "   AND KAT_KODEKATEGORI= PRD_KODEKATEGORIBARANG";
        $query .= "   LEFT JOIN TBMASTER_STOCK ";
        $query .= "   ON ST_PRDCD = PRD_PRDCD ";
        $query .= "   AND ST_LOKASI = '" & $request->jenisbrg & "' ";
        $query .= "   LEFT JOIN";
        $query .= " ( select TBTR_LOKASI_SO.* ";
        $query .= "   from( ";
        $query .= "        SELECT MAX(MSO_TGLSO) TGL_SO_SETTING ";
        $query .= "        FROM tbmaster_Setting_so";
        $query .= "       ) C ";
        $query .= "       join TBTR_LOKASI_SO on LSO_TGLSO = TGL_SO_SETTING";
        $query .= "       ) TBTR_LOKASI_SO ";
        $query .= "    ON LSO_PRDCD = ST_PRDCD ";
        $query .= "LEFT JOIN";
        $query .= "   (SELECT MAX(MSO_TGLSO) TGL_SO_SETTING ";
        $query .= "     FROM tbmaster_Setting_so) SO_SETTING           ";
        $query .= "   ON LSO_LOKASI  = ST_LOKASI ";
        $query .= "   AND DATE_TRUNC('DAY',LSO_TGLSO) = TO_DATE('" & $request->tanggal_start_so & "','DD-MM-YYYY')";
        $query .= ") A";
        $query .= " WHERE LSO_PRDCD = '0000000' ";
        $query .= " AND PRD_KODEDIVISI BETWEEN '" & $request->div1 & "' AND '" & $request->div2 & "' ";
        $query .= " AND PRD_KODEDEPARTEMENT BETWEEN '" & $request->dept1 & "' AND '" & $request->dept2 & "' ";
        $query .= " AND PRD_KODEKATEGORIBARANG BETWEEN '" & $request->kat1 & "' AND '" & $request->kat2 & "' ";
        $query .= " AND PRD_PRDCD LIKE '%0' ";
        $query .= " ORDER BY PRD_KODEDIVISI, PRD_KODEDEPARTEMENT,PRD_KODEKATEGORIBARANG,LSO_PRDCD ";
        $data = DB::select($query);

        $data['data'] = $data;

        return $data;
    }

    public function reportRakBelumSo(ReportLokasiRakBelumDiSoRequest $request){

        $query = '';
        $query .= "SELECT lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lso_nourut, ";
        $query .= "CASE WHEN lso_lokasi = '01' THEN 'BAIK' ELSE CASE WHEN lso_lokasi = '02' THEN 'RETUR' ELSE 'RUSAK' END END jenisbrg, ";
        $query .= "lso_prdcd, prd_deskripsipanjang, prd_unit || '/' || prd_frac UNIT, ";
        $query .= "CASE WHEN lso_flagsarana = 'H' THEN 'HandHeld' ELSE 'Kertas' END Sarana, lso_qty ";
        $query .= "FROM tbtr_lokasi_so, tbmaster_prodmast ";
        $query .= "WHERE lso_tglso = TO_DATE('" . $request->tanggal_start_so . "','DD-MM-YYYY') AND lso_modify_by IS NULL ";

        if($request->koderak1 <> '0'){
            $query .= " AND lso_koderak = '" . $request->koderak1 . "' ";
        }

        if($request->subrak1 <> '0'){
            $query .= " AND lso_kodesubrak = '" . $request->subrak1 . "' ";
        }

        if($request->tipe1 <> '0'){
            $query .= " AND lso_tiperak = '" . $request->tipe1 . "' ";
        }

        if($request->shelving1 <> '0'){
            $query .= " AND lso_shelvingrak = '" . $request->shelving1 . "' ";
        }

        $query .= " AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR ";

        if($this->flagTransferLokasi == 'Y'){
            $query .= " AND LSO_FLAGLIMIT='Y' ";
        }

        $query .= "Order By lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lso_nourut, lso_lokasi, lso_prdcd ";
        $data = DB::select($query);

        $data['data'] = $data;

        return $data;
    }

    public function reportInqueryPlanoSonasExcel(ReportInqueryPlanoSonasRequest $request){

        if($request->jenis_barang == 'A'){
            $data = DB::select("SELECT * FROM TBTR_LOKASI_SO, TBMASTER_PRODMAST WHERE LSO_PRDCD = PRD_PRDCD AND LSO_PRDCD = '" . $request->plu . "' AND DATE_TRUNC('DAY',LSO_TGLSO) = TO_DATE('" . $request->tanggal_start_so . "', 'DD-MM-YYYY')");
        }else{
            $data = DB::select("SELECT * FROM TBTR_LOKASI_SO, TBMASTER_PRODMAST WHERE LSO_PRDCD = PRD_PRDCD AND LSO_LOKASI = '" . $request->jenis_barang . "' AND LSO_PRDCD = '" . $request->plu . "' AND DATE_TRUNC('DAY',LSO_TGLSO) = TO_DATE('" . $request->tanggal_start_so . "', 'DD-MM-YYYY')");
        }

        $data['data'] = $data;

        return $data;
    }

    public function reportLppMonthEndExcel(){
        // If inp.Flag = "N" Then
        //     Exit Sub
        // Else
        //     PLU1 = inp.PluId1
        //     PLU2 = inp.PluId2
        //     Lokasi = inp.JenisBrgId
        //     Tanggal = inp.Tanggal
        //     cek = inp.cekpluId
        // End If

        //! BELUM BERES
    }

    public function reportCetakDraftLhso(){
        // If InputRpt13.Flag = "Y" Then
        //     If InputRpt13.RB1.Checked Then
        //         LoadReport()
        //     Else
        //         LoadReport2()
        //     End If
        // End If


    }

    private function loadReport(){
        // dtPer = QueryOra("select prs_kodeigr kode_igr, PRS_NAMACABANG from tbmaster_perusahaan ")
        // NamaCabang = dtPer.Rows(0).Item("PRS_NAMACABANG")

        // If tahap = 2 Or 3 Or 4 Or 5 Or 6 Then
        //     dt = QueryOra("SELECT * FROM TBMASTER_SETTING_SO WHERE MSO_FLAGRESET IS NULL")
        //     If dt.Rows.Count = 0 Then
        //         MsgBox("-Draf Lhso Tahap '" & tahap & "'  belum di Proses-  ")
        //         Exit Sub
        //     Else
        //         FlagTahap = Val(dt.Rows(0).Item("MSO_FLAGTAHAP").ToString)

        //         If FlagTahap <> tahap Then
        //             MsgBox(" Saat ini Proses Tahap ke '" & FlagTahap & "' !  ")
        //             Exit Sub
        //         Else
        //             FlagTahap = Val(dt.Rows(0).Item("MSO_FLAGTAHAP").ToString)
        //         End If

        //     End If

        //     If tahap <> FlagTahap Then
        //         MsgBox("-Draf Lhso Tahap '" & tahap & "'  belum di Proses-  ")
        //         Exit Sub
        //     Else

        //     End If

        // End If

        // dt = QueryOra("SELECT * FROM TBMASTER_SETTING_SO WHERE MSO_FLAGRESET IS NULL")
        // If dt.Rows.Count = 0 Then
        //     MsgBox("-Draf Lhso Tahap '" & tahap & "'  belum di Proses- ")
        //     Exit Sub
        // Else
        //     FlagTahap = dt.Rows(0).Item("MSO_FLAGTAHAP")
        //     TglSO = dt.Rows(0).Item("MSO_TGLSO")
        // End If


        // If jenisbrg = "B" Then
        //     jenisbrg = "01"
        // ElseIf jenisbrg = "T" Then
        //     jenisbrg = "02"
        // ElseIf jenisbrg = "R" Then
        //     jenisbrg = "03"
        // End If

        // If tahap > 0 Then
        //     tahap = tahap.ToString.PadLeft(2, "0")
        // End If

        // If limit = "" Then
        //     limit = "100000"
        // End If

        // If jenisbrg = "01" Then
        //     ketlokasi = "Lokasi Barang Baik - 01"
        // ElseIf jenisbrg = "02" Then
        //     ketlokasi = "Lokasi Barang Retur - 02"
        // Else
        //     ketlokasi = "Lokasi Barang Rusak - 03"
        // End If

        // If div1 = "" Then
        //     d1 = "1 - FOOD"
        //     d2 = "6 - ELECTRONICS"
        //     d3 = "01 - BREAKFAST FOOD"
        //     d4 = "58 - SERVICE"
        //     d5 = "01 - TEH CELUP"
        //     d6 = "C1 - COUNTER UMUM"
        // Else
        //     d1 = ""
        //     d2 = ""
        //     d3 = ""
        //     d4 = ""
        //     d5 = ""
        //     d6 = ""
        // End If

        // Str = "SELECT * FROM ("
        // Str &= "SELECT DISTINCT PLU, DESKRIPSI, AREAGUDANG, AREATOKO, (AREAGUDANG + AREATOKO) AS TOTAL, LPP, ((AREAGUDANG + AREATOKO  ) - LPP) AS SELISIH, "
        // Str &= "(((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) AS NILAI_SELISIH, LSO_FLAGTAHAP, LSO_CREATE_BY, "
        // Str &= "PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, div_namadivisi, dep_namadepartement, kat_namakategori "
        // Str &= "FROM (SELECT PRD_AVGCOST, PRD_PRDCD AS PLU, PRD_DESKRIPSIPANJANG AS DESKRIPSI,  "
        // Str &= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" & tahap & "' AND LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%') AND LSO_LOKASI = '" & jenisbrg & "') AS AREAGUDANG, "
        // Str &= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" & tahap & "' AND LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%') AND LSO_LOKASI = '" & jenisbrg & "') AS AREATOKO,  "
        // Str &= "(LSO_ST_SALDOAKHIR) AS LPP, LSO_FLAGTAHAP, LSO_CREATE_BY, LSO_AVGCOST, "
        // Str &= "PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, "
        // Str &= "(select div_namadivisi from tbmaster_divisi where div_kodedivisi = PRD_KODEDIVISI) as div_namadivisi, "
        // Str &= "(select dep_namadepartement from tbmaster_departement where dep_kodedepartement = PRD_KODEDEPARTEMENT and dep_kodedivisi = PRD_KODEDIVISI) as dep_namadepartement, "
        // Str &= "(select kat_namakategori from tbmaster_kategori where kat_kodekategori = PRD_KODEKATEGORIBARANG and kat_kodedepartement = PRD_KODEDEPARTEMENT ) as kat_namakategori "
        // Str &= "FROM TBMASTER_PRODMAST, tbhistory_lhso_sonas, "
        // Str &= "tbmaster_divisi, tbmaster_departement, tbmaster_kategori "
        // Str &= "WHERE LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') "
        // Str &= "AND LSO_PRDCD = PRD_PRDCD "
        // Str &= "AND PRD_KODEDIVISI = div_kodedivisi "
        // Str &= "AND PRD_KODEDEPARTEMENT = dep_kodedepartement "
        // Str &= "AND PRD_KODEKATEGORIBARANG = kat_kodekategori "
        // Str &= "AND div_kodedivisi = dep_kodedivisi "
        // Str &= "AND dep_kodedepartement = kat_kodedepartement "
        // Str &= "AND LSO_FLAGTAHAP = '" & tahap & "' "
        // If div1 = "" Then
        //     Str &= ""
        // ElseIf dept1 = "" Then
        //     Str &= "AND PRD_KODEDIVISI BETWEEN '" & div1 & "' and '" & div2 & "'  "
        // ElseIf kat1 = "" Then
        //     Str &= "AND PRD_KODEDIVISI BETWEEN '" & div1 & "' and '" & div2 & "'  "
        //     Str &= "AND PRD_KODEDEPARTEMENT BETWEEN '" & dept1 & "' and '" & dept2 & "' "
        // Else
        //     Str &= "AND PRD_KODEDIVISI BETWEEN '" & div1 & "' and '" & div2 & "'  "
        //     Str &= "AND PRD_KODEDEPARTEMENT BETWEEN '" & dept1 & "' and '" & dept2 & "' "
        //     Str &= "AND PRD_KODEKATEGORIBARANG BETWEEN  '" & kat1 & "' and '" & kat2 & "' "
        // End If
        // If plu1 = "" Then
        //     Str &= ""
        // Else
        //     Str &= "AND LSO_PRDCD BETWEEN '" & plu1 & "' and '" & plu2 & "' "
        // End If
        // Str &= "AND LSO_LOKASI = '" & jenisbrg & "' "
        // Str &= ") q "
        // Str &= "WHERE (((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) <> 0 "
        // Str &= ") p ORDER BY ABS(NILAI_SELISIH) DESC LIMIT " & limit & ""
    }

    private function LoadReport2(){
        // If tahap > 1 Then
        //     dt = QueryOra("SELECT * FROM TBMASTER_SETTING_SO WHERE MSO_FLAGRESET IS NULL")
        //     If dt.Rows.Count = 0 Then
        //         MsgBox("-Draf Lhso Tahap '" & tahap & "'  belum di Proses-  ")
        //         Exit Sub
        //     Else
        //         FlagTahap = Val(dt.Rows(0).Item("MSO_FLAGTAHAP").ToString)

        //         If FlagTahap <> tahap Then
        //             MsgBox(" Saat ini Proses Tahap ke '" & FlagTahap & "' !  ")
        //             Exit Sub
        //         Else
        //             FlagTahap = Val(dt.Rows(0).Item("MSO_FLAGTAHAP").ToString)
        //         End If

        //     End If

        //     If tahap <> FlagTahap Then
        //         MsgBox("-Draf Lhso Tahap '" & tahap & "'  belum di Proses-  ")
        //         Exit Sub
        //     Else

        //     End If

        // End If

        // dt = QueryOra("SELECT * FROM TBMASTER_SETTING_SO WHERE MSO_FLAGRESET IS NULL")
        // If dt.Rows.Count = 0 Then
        //     MsgBox("-Draf Lhso Tahap '" & tahap & "'  belum di Proses- ")
        //     Exit Sub
        // Else
        //     FlagTahap = dt.Rows(0).Item("MSO_FLAGTAHAP")
        //     TglSO = dt.Rows(0).Item("MSO_TGLSO")
        // End If


        // If jenisbrg = "B" Then
        //     jenisbrg = "01"
        // ElseIf jenisbrg = "T" Then
        //     jenisbrg = "02"
        // ElseIf jenisbrg = "R" Then
        //     jenisbrg = "03"
        // End If

        // If tahap = 1 Then
        //     tahap = "01"
        // ElseIf tahap = 2 Then
        //     tahap = "02"
        // ElseIf tahap = 3 Then
        //     tahap = "03"
        // ElseIf tahap = 4 Then
        //     tahap = "04"
        // ElseIf tahap = 5 Then
        //     tahap = "05"
        // ElseIf tahap = 6 Then
        //     tahap = "06"
        // End If

        // If limit = "" Then
        //     limit = "100000"
        // End If

        // Str = "SELECT * FROM ("
        // Str &= "SELECT DISTINCT PLU, DESKRIPSI, AREAGUDANG, AREATOKO, (AREAGUDANG + AREATOKO) AS TOTAL, LPP, ((AREAGUDANG + AREATOKO  ) - LPP) AS SELISIH, "
        // Str &= "(((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) AS NILAI_SELISIH, LSO_FLAGTAHAP, LSO_CREATE_BY, "
        // Str &= "PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, div_namadivisi, dep_namadepartement, kat_namakategori "
        // Str &= "FROM (SELECT PRD_AVGCOST, PRD_PRDCD AS PLU, PRD_DESKRIPSIPANJANG AS DESKRIPSI,  "
        // Str &= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" & tahap & "' AND LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%') AND LSO_LOKASI = '" & jenisbrg & "') AS AREAGUDANG, "
        // Str &= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" & tahap & "' AND LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%') AND LSO_LOKASI = '" & jenisbrg & "') AS AREATOKO,  "
        // Str &= "(LSO_ST_SALDOAKHIR) AS LPP, LSO_FLAGTAHAP, LSO_CREATE_BY, LSO_AVGCOST, "
        // Str &= "PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, "
        // Str &= "(select div_namadivisi from tbmaster_divisi where div_kodedivisi = PRD_KODEDIVISI) as div_namadivisi, "
        // Str &= "(select dep_namadepartement from tbmaster_departement where dep_kodedepartement = PRD_KODEDEPARTEMENT and dep_kodedivisi = PRD_KODEDIVISI) as dep_namadepartement, "
        // Str &= "(select kat_namakategori from tbmaster_kategori where kat_kodekategori = PRD_KODEKATEGORIBARANG and kat_kodedepartement = PRD_KODEDEPARTEMENT ) as kat_namakategori "
        // Str &= "FROM TBMASTER_PRODMAST, tbhistory_lhso_sonas, "
        // Str &= "tbmaster_divisi, tbmaster_departement, tbmaster_kategori "
        // Str &= "WHERE LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') "
        // Str &= "AND LSO_PRDCD = PRD_PRDCD "
        // Str &= "AND PRD_KODEDIVISI = div_kodedivisi "
        // Str &= "AND PRD_KODEDEPARTEMENT = dep_kodedepartement "
        // Str &= "AND PRD_KODEKATEGORIBARANG = kat_kodekategori "
        // Str &= "AND div_kodedivisi = dep_kodedivisi "
        // Str &= "AND dep_kodedepartement = kat_kodedepartement "
        // Str &= "AND LSO_FLAGTAHAP = '" & tahap & "' "
        // If div1 = "" Then
        //     Str &= ""
        // ElseIf dept1 = "" Then
        //     Str &= "AND PRD_KODEDIVISI BETWEEN '" & div1 & "' and '" & div2 & "'  "
        // ElseIf kat1 = "" Then
        //     Str &= "AND PRD_KODEDIVISI BETWEEN '" & div1 & "' and '" & div2 & "'  "
        //     Str &= "AND PRD_KODEDEPARTEMENT BETWEEN '" & dept1 & "' and '" & dept2 & "' "
        // Else
        //     Str &= "AND PRD_KODEDIVISI BETWEEN '" & div1 & "' and '" & div2 & "'  "
        //     Str &= "AND PRD_KODEDEPARTEMENT BETWEEN '" & dept1 & "' and '" & dept2 & "' "
        //     Str &= "AND PRD_KODEKATEGORIBARANG BETWEEN  '" & kat1 & "' and '" & kat2 & "' "
        // End If
        // If plu1 = "" Then
        //     Str &= ""
        // Else
        //     Str &= "AND LSO_PRDCD BETWEEN '" & plu1 & "' and '" & plu2 & "' "
        // End If
        // Str &= "AND LSO_LOKASI = '" & jenisbrg & "' "
        // Str &= ") q "
        // Str &= "WHERE (((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) <> 0 "
        // Str &= ") p ORDER BY ABS(NILAI_SELISIH) DESC limit " & limit & ""
    }

    public function reportCetakDraftReturSebelumLhso(){
        // divdeptkat = "-"

        // dtPer = QueryOra("select prs_kodeigr kode_igr, PRS_NAMACABANG from tbmaster_perusahaan ")
        // NamaCabang = dtPer.Rows(0).Item("PRS_NAMACABANG")

        // dt = QueryOra("SELECT * FROM TBMASTER_SETTING_SO WHERE MSO_FLAGRESET IS NULL")
        // If dt.Rows.Count = 0 Then
        //     MsgBox("-Draf Lhso belum di Proses- ")
        //     Exit Sub
        // Else
        //     FlagTahap = dt.Rows(0).Item("MSO_FLAGTAHAP")
        //     TglSO = dt.Rows(0).Item("MSO_TGLSO")
        // End If

        // If div1 = "" Then
        //     d1 = "1 - FOOD"
        //     d2 = "6 - ELECTRONICS"
        //     d3 = "01 - BREAKFAST FOOD"
        //     d4 = "58 - SERVICE"
        //     d5 = "01 - TEH CELUP"
        //     d6 = "C1 - COUNTER UMUM"
        // Else
        //     d1 = ""
        //     d2 = ""
        //     d3 = ""
        //     d4 = ""
        //     d5 = ""
        //     d6 = ""
        // End If

        // Str = "SELECT * FROM ("
        // Str &= "SELECT DISTINCT PLU, DESKRIPSI, AREAGUDANG, AREATOKO, (AREAGUDANG + AREATOKO) AS TOTAL, LPP, ((AREAGUDANG + AREATOKO  ) - LPP) AS SELISIH, "
        // Str &= "(((AREAGUDANG + AREATOKO ) - LPP ) * ACOST) AS NILAI_SELISIH,  LSO_CREATE_BY, "
        // Str &= "PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, div_namadivisi, dep_namadepartement, kat_namakategori "
        // Str &= "FROM (SELECT PRD_AVGCOST, PRD_PRDCD AS PLU, PRD_DESKRIPSIPANJANG AS DESKRIPSI,  "
        // Str &= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM TBTR_LOKASI_SO WHERE   LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%') AND LSO_LOKASI = '02') AS AREAGUDANG, "
        // Str &= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM TBTR_LOKASI_SO WHERE   LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%') AND LSO_LOKASI = '02') AS AREATOKO,  "
        // Str &= "(SELECT (case when prd_unit='KG' then st_avgcost/1000  else st_avgcost  end) ST_AVGCOST FROM TBMASTER_STOCK, TBMASTER_PRODMAST WHERE ST_PRDCD = PRD_PRDCD AND ST_LOKASI = LSO_LOKASI AND ST_PRDCD = LSO_PRDCD and ST_AVGCOST IS NOT NULL LIMIT 1) AS ACOST, "
        // Str &= "(SELECT coalesce (ST_SALDOAKHIR, 0) FROM TBMASTER_STOCK WHERE ST_LOKASI = LSO_LOKASI AND ST_PRDCD = LSO_PRDCD and ST_SALDOAKHIR IS NOT NULL LIMIT 1 ) AS LPP, LSO_CREATE_BY,  "
        // Str &= "PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, "
        // Str &= "(select div_namadivisi from tbmaster_divisi where div_kodedivisi = PRD_KODEDIVISI) as div_namadivisi, "
        // Str &= "(select dep_namadepartement from tbmaster_departement where dep_kodedepartement = PRD_KODEDEPARTEMENT and dep_kodedivisi = PRD_KODEDIVISI) as dep_namadepartement, "
        // Str &= "(select kat_namakategori from tbmaster_kategori where kat_kodekategori = PRD_KODEKATEGORIBARANG and kat_kodedepartement = PRD_KODEDEPARTEMENT ) as kat_namakategori "
        // Str &= "FROM TBMASTER_PRODMAST, TBTR_LOKASI_SO, "
        // Str &= "tbmaster_divisi, tbmaster_departement, tbmaster_kategori "
        // Str &= "WHERE LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') "
        // Str &= "AND LSO_PRDCD = PRD_PRDCD "
        // Str &= "AND PRD_KODEDIVISI = div_kodedivisi "
        // Str &= "AND PRD_KODEDEPARTEMENT = dep_kodedepartement "
        // Str &= "AND PRD_KODEKATEGORIBARANG = kat_kodekategori "
        // Str &= "AND div_kodedivisi = dep_kodedivisi "
        // Str &= "AND dep_kodedepartement = kat_kodedepartement "
        // If div1 = "" Then
        //     Str &= ""
        // ElseIf dept1 = "" Then
        //     Str &= "AND PRD_KODEDIVISI BETWEEN '" & div1 & "' and '" & div2 & "'  "
        // ElseIf kat1 = "" Then
        //     Str &= "AND PRD_KODEDIVISI BETWEEN '" & div1 & "' and '" & div2 & "'  "
        //     Str &= "AND PRD_KODEDEPARTEMENT BETWEEN '" & dept1 & "' and '" & dept2 & "' "
        // Else
        //     Str &= "AND PRD_KODEDIVISI BETWEEN '" & div1 & "' and '" & div2 & "'  "
        //     Str &= "AND PRD_KODEDEPARTEMENT BETWEEN '" & dept1 & "' and '" & dept2 & "' "
        //     Str &= "AND PRD_KODEKATEGORIBARANG BETWEEN  '" & kat1 & "' and '" & kat2 & "' "
        // End If
        // If plu1 = "" Then
        //     Str &= ""
        // Else
        //     Str &= "AND LSO_PRDCD BETWEEN '" & plu1 & "' and '" & plu2 & "' "
        // End If
        // Str &= "AND LSO_LOKASI = '02' )t "
        // Str &= "WHERE (((AREAGUDANG + AREATOKO ) - LPP ) * ACOST) <> 0 "
        // Str &= ") p ORDER BY ABS(NILAI_SELISIH) DESC "
    }

    public function reportLokasiSo(){
        // If sarana = "H" Then
        //     flagsarana = "Handheld"
        // Else
        //     flagsarana = "Kertas"
        // End If

        // dtPer = QueryOra("select prs_kodeigr kode_igr, PRS_NAMACABANG from tbmaster_perusahaan ")
        // NamaCabang = dtPer.Rows(0).Item("PRS_NAMACABANG")

        // Str = "select lso_koderak, lso_kodesubrak, lso_tiperak || '.' || lso_shelvingrak || '.' || lso_nourut as lokasi, "
        // Str &= "lso_prdcd, prd_deskripsipanjang, lso_tglso, lso_flagsarana "
        // Str &= "from TBTR_LOKASI_SO, TBMASTER_PRODMAST "
        // Str &= "where LSO_PRDCD = PRD_PRDCD "
        // If subrak <> "" Then
        //     koderak = Split(subrak, ".")(0)
        //     kodesubrak = Split(subrak, ".")(1)
        //     Str &= "and lso_koderak = '" & koderak & "' "
        //     Str &= "and lso_kodesubrak= '" & kodesubrak & "' "
        // Else
        //     Str &= ""
        // End If
        // Str &= "and lso_flagsarana= '" & sarana & "' "
        // Str &= "and lso_flaglimit= 'Y' "
        // Str &= "and DATE_trunc('DAY',LSO_TGLSO) >= TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') "
        // Str &= "order by lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lso_nourut "
    }
}
