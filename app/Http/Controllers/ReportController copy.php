<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\ProsesBaSoRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetLimitSoController extends Controller
{
    private $FlagTahap;
    public function __construct(Request $request){
        DatabaseConnection::setConnection(session('KODECABANG'), "PRODUCTION");
    }

    public function index(){

        $dtCek = DB::select("SELECT DATE_TRUNC('DAY',MSO_TGLSO) MSO_TGLSO, MSO_FLAG_CREATELSO FROM TBMASTER_SETTING_SO ORDER BY MSO_TGLSO DESC");
        if(count($dtCek) == 0){
            return ApiFormatter::error(400, 'Data SO tidak ditemukan');
        }

        $data['TanggalSO'] = $dtCek[0]->mso_tglso;
        $data['flagTransferLokasi'] = $dtCek[0]->mso_flag_createlso;

        return view('proses-ba-so', $data);
    }

    public function reportListFormKkso(){

        $koderak1 = "0";
        $subrak1 = "0";
        $tipe1 = "0";
        $shelving1 = "0";
        $koderak2 = "ZZZZZZZ";
        $subrak2 = "ZZZ";
        $tipe2 = "ZZZ";
        $shelving2 = "ZZ";

        // jb = IIf(InputRpt1.JbID = "B", "01", IIf(InputRpt1.JbID = "T", "02", "03"))

        // Sql = "SELECT lso_tglso, lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lso_nourut, "
        // Sql += "lso_prdcd, case when lso_lokasi = '01' then '01 - BARANG BAIK' else case when lso_lokasi = '02' then '02 - BARANG RETUR' else '03 - BARANG RUSAK' end end lokasi, prd_deskripsipanjang, prd_unit || '/' || prd_frac satuan, prd_kodetag, lso_tmp_qtyctn, lso_tmp_qtypcs "
        // Sql += "FROM tbtr_lokasi_so, tbmaster_prodmast "
        // Sql += "WHERE coalesce(lso_recid,'0') <> '1' and lso_koderak between '" & koderak1 & "' and '" & koderak2 & "' "
        // Sql += "AND LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') "
        // Sql += "AND LSO_LOKASI = '" & jb & "'  "
        // Sql += "AND lso_kodesubrak between '" & subrak1 & "' and '" & subrak2 & "' "
        // Sql += "AND lso_tiperak between '" & tipe1 & "' and '" & tipe2 & "' "
        // Sql += "AND lso_shelvingrak between '" & shelving1 & "' and '" & shelving2 & "' "
        // Sql += "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR "
        // Sql += "AND coalesce(lso_flagsarana, 'K') = 'K' "
        // If _flagTransferLokasi = "Y" Then
        //     Sql += "AND LSO_FLAGLIMIT='Y' "
        // End If
        // Sql += "Order By lso_lokasi, lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lso_nourut  "

        //! UPDATE
        // For i As Integer = 0 To dtSO.Rows.Count - 1
        //     plu = dtSO.Rows(i).Item("lso_prdcd")
        //     Str = "UPDATE TBTR_LOKASI_SO SET LSO_FLAGKKSO='Y' "
        //     Str &= "WHERE LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND LSO_PRDCD = '" & plu & "' "
        //     NonQueryOraTransaction(Str, OraConn, OraTrans)
        // Next
    }

    public function reportRegisterKkso1(){
        $koderak1 = "0";
        $subrak1 = "0";
        $tipe1 = "0";
        $shelving1 = "0";
        $koderak2 = "ZZZZZZZ";
        $subrak2 = "ZZZ";
        $tipe2 = "ZZZ";
        $shelving2 = "ZZ";

        // jb = IIf(InputRpt1.JbID = "B", "01", IIf(InputRpt1.JbID = "T", "02", "03"))

        // Sql = "SELECT rak1, rak2, sub1, sub2, shel1, shel2, tipe1, tipe2, lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lokasi, SUM(item) item, "
        // Sql += "floor(( (sum(ITEM)+10+(1*2)) / 25) + CASE WHEN  MOD((sum(ITEM)+10+(1*2)), 25) <> 0 THEN 1 ELSE 0 END) lbr FROM ( "
        // Sql += "SELECT '" & koderak1 & "' rak1, '" & koderak2 & "' rak2, '" & subrak1 & "' sub1, '" & subrak2 & "' sub2, "
        // Sql += "'" & tipe1 & "' tipe1, '" & tipe2 & "' tipe2, '" & shelving1 & "' shel1, '" & shelving2 & "' shel2, "
        // Sql += "lso_tglso, lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lso_nourut, "
        // Sql += "lso_prdcd, 1 item, case when lso_lokasi = '01' then '01 - BARANG BAIK' else case when lso_lokasi = '02' then '02 - BARANG RETUR' else '03 - BARANG RUSAK' end end lokasi, prd_deskripsipanjang, prd_unit || '/' || prd_frac satuan, prd_kodetag "
        // Sql += "FROM tbtr_lokasi_so, tbmaster_prodmast "
        // Sql += "WHERE coalesce(lso_recid,'0') <> '1' and lso_koderak between '" & koderak1 & "' and '" & koderak2 & "' "
        // Sql += "AND LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') "
        // Sql += "AND lso_kodesubrak between '" & subrak1 & "' and '" & subrak2 & "' "
        // Sql += "AND lso_tiperak between '" & tipe1 & "' and '" & tipe2 & "' "
        // Sql += "AND lso_shelvingrak between '" & shelving1 & "' and '" & shelving2 & "' "
        // Sql += "AND lso_lokasi = '" & jenisbrg & "' "
        // Sql += "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR "
        // If _flagTransferLokasi = "Y" Then
        //     Sql += "AND LSO_FLAGLIMIT='Y' "
        // End If
        // Sql += " ) A "
        // Sql += "GROUP BY rak1, rak2, sub1, sub2, shel1, shel2, tipe1, tipe2, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LOKASI "
        // Sql += "ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LOKASI "

        //! GET NAMA PERUSAHAAN
        // "select prs_kodeigr kode_igr, PRS_NAMACABANG from tbmaster_perusahaan
    }

    public function reportEditListKkso(){
        // FlagReset = "N"
        // dtSO = QueryOra("SELECT coalesce(MSO_FLAGSUM, 'N') MSO_FLAGSUM FROM TBMASTER_SETTING_SO WHERE MSO_TGLSO =  TO_DATE('" & Format(tglSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY')")
        // If dtSO.Rows.Count <> 0 Then
        //     FlagReset = dtSO.Rows(0).Item("MSO_FLAGSUM")
        // End If

        // Sql = "SELECT to_char(LSO_TGLSO, 'dd-MM-yyyy') LSO_TGLSO, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT, LSO_PRDCD, "
        // Sql += "CASE WHEN LSO_LOKASI = '01' THEN '01 - BARANG BAIK' ELSE CASE WHEN LSO_LOKASI = '02' THEN '02 - BARANG RETUR' ELSE '03 - BARANG RUSAK' End END LOKASI, "
        // Sql += "PRD_DESKRIPSIPANJANG, PRD_UNIT || '/' || PRD_FRAC SATUAN, PRD_KODETAG, "
        // Sql += "CASE WHEN LSO_FLAGSARANA = 'K' THEN FLOOR (LSO_QTY / PRD_FRAC) ELSE LSO_TMP_QTYCTN END CTN, CASE WHEN LSO_FLAGSARANA = 'K' THEN MOD (LSO_QTY, PRD_FRAC) ELSE LSO_TMP_QTYPCS END PCS, LSO_QTY, "

        // If FlagReset = "Y" Then
        //     Sql += "SOP_NEWAVGCOST ST_AVGCOSTMONTHEND, "
        //     Sql += "(LSO_QTY * CASE WHEN PRD_UNIT = 'KG' THEN (SOP_NEWAVGCOST / 1000) ELSE SOP_NEWAVGCOST End ) TOTAL, "
        // Else
        //     Sql += "ST_AVGCOST ST_AVGCOSTMONTHEND, "
        //     Sql += "(LSO_QTY * CASE WHEN PRD_UNIT = 'KG' THEN (ST_AVGCOST / 1000) ELSE ST_AVGCOST End ) TOTAL, "
        // End If

        // Sql += "LSO_MODIFY_BY, sop_prdcd, sop_newavgcost, sop_tglso "
        // Sql += "FROM TBTR_LOKASI_SO, TBMASTER_PRODMAST, TBMASTER_STOCK, tbtr_ba_stockopname "
        // Sql += "WHERE coalesce (LSO_RECID, '0') <> '1' "
        // Sql += "AND LSO_TGLSO = TO_DATE('" & Format(tglSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') "
        // Sql += "AND LSO_KODERAK BETWEEN '" & koderak1 & "' and '" & koderak2 & "' "
        // Sql += "AND LSO_KODESUBRAK BETWEEN '" & subrak1 & "' and '" & subrak2 & "' "
        // Sql += "AND LSO_TIPERAK BETWEEN '" & tipe1 & "' and '" & tipe2 & "' "
        // Sql += "AND LSO_SHELVINGRAK BETWEEN '" & shelving1 & "' and '" & shelving2 & "' "
        // Sql += "AND LSO_LOKASI = '" & jenisbrg & "' "
        // Sql += "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR "
        // Sql += "AND ST_PRDCD = LSO_PRDCD AND ST_LOKASI = LSO_LOKASI "
        // Sql += "and sop_tglso = TO_DATE('" & Format(tglSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') "
        // Sql += "AND sop_lokasi = st_lokasi and sop_prdcd = st_prdcd "
        // Sql += "ORDER BY LSO_TGLSO, LOKASI, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT "

        //! GET NAMA PERUSAHAAN
        // "select prs_kodeigr kode_igr, PRS_NAMACABANG from tbmaster_perusahaan
    }

    public function reportRegisterKkso2(){
        // Sql = "SELECT   RAK1, RAK2, SUB1, SUB2, SHEL1, SHEL2, TIPE1, TIPE2, LSO_KODERAK, LSO_TGLSO, LSO_KODESUBRAK, "
        // Sql += "LSO_TIPERAK, LSO_SHELVINGRAK, LOKASI, SUM (ITEM) ITEM, SUM (SO) SO, (SUM(ITEM) - SUM(SO)) SELISIH, "
        // Sql += "FLOOR (  ((SUM (ITEM) + 10 + (1 * 2)) / 25) + CASE WHEN MOD ((SUM (ITEM) + 10 + (1 * 2)), 25) <> 0 THEN 1 ELSE 0 END) LBR "
        // Sql += "FROM (SELECT '" & koderak1 & "' rak1, '" & koderak2 & "' rak2, '" & subrak1 & "' sub1, '" & subrak2 & "' sub2, "
        // Sql += "'" & tipe1 & "' tipe1, '" & tipe2 & "' tipe2, '" & shelving1 & "' shel1, '" & shelving2 & "' shel2, "
        // Sql += "TO_CHAR(LSO_TGLSO, 'dd-MM-yyyy') LSO_TGLSO, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT, LSO_PRDCD, 1 ITEM, "
        // Sql += "CASE WHEN LSO_QTY <> 0 THEN 1 ELSE 0 END SO, "
        // Sql += "CASE WHEN LSO_LOKASI = '01' THEN '01 - BARANG BAIK' ELSE CASE WHEN LSO_LOKASI = '02' THEN '02 - BARANG RETUR' ELSE '03 - BARANG RUSAK' END END LOKASI, "
        // Sql += "PRD_DESKRIPSIPANJANG, PRD_UNIT || '/' || PRD_FRAC SATUAN, PRD_KODETAG FROM TBTR_LOKASI_SO, TBMASTER_PRODMAST "
        // Sql += "WHERE coalesce (LSO_RECID, '0') <> '1' AND LSO_KODERAK BETWEEN '" & koderak1 & "' and '" & koderak2 & "' "
        // Sql += "AND LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') "
        // Sql += "AND LSO_KODESUBRAK BETWEEN '" & subrak1 & "' and '" & subrak2 & "' AND LSO_TIPERAK BETWEEN '" & tipe1 & "' and '" & tipe2 & "' "
        // Sql += "AND LSO_SHELVINGRAK BETWEEN '" & shelving1 & "' and '" & shelving2 & "'  AND LSO_LOKASI = '" & jenisbrg & "' "
        // If _flagTransferLokasi = "Y " Then
        //     Sql += "AND LSO_FLAGLIMIT='Y' "
        // End If
        // Sql += "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR ) A "
        // Sql += "GROUP BY RAK1,  RAK2, SUB1, SUB2, SHEL1, SHEL2, TIPE1, TIPE2, LSO_KODERAK, LSO_TGLSO, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LOKASI "
        // Sql += "ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LOKASI "
    }

    public function reportPerincianBaso(){
        // Sql = "SELECT * FROM (SELECT PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, DEP_NAMADEPARTEMENT, PRD_KODEKATEGORIBARANG, "
        // Sql += "KAT_NAMAKATEGORI, PRD_PRDCD, PRD_DESKRIPSIPANJANG, PRD_UNIT || '/' || PRD_FRAC SATUAN, "
        // Sql += "TO_CHAR(SOP_TGLSO, 'dd-MM-yyyy') SOP_TGLSO, CASE WHEN '" & pilLap & "' = '1' THEN 'ALL' ELSE CASE WHEN '" & pilLap & "' = '2' THEN '< -1000000' ELSE '> 1000000' END END Lap, CASE WHEN SOP_LOKASI = '01' THEN '01 - Barang Baik' ELSE CASE WHEN SOP_LOKASI = '02' THEN '02 - Barang Retur' "
        // Sql += "ELSE '03 = Barang Rusak' END END LOKASI, (sop_newavgcost * CASE WHEN PRD_UNIT = 'KG' THEN 1000 ELSE 1 END) SOP_LASTAVGCOST, (sop_newavgcost * PRD_FRAC) HPP, "
        // Sql += "SOP_QTYLPP, FLOOR (SOP_QTYLPP / PRD_FRAC) CTNLPP, MOD (SOP_QTYLPP, PRD_FRAC) PCSLPP, (case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * SOP_QTYLPP) RPHLPP, SOP_QTYSO, "
        // Sql += "FLOOR (SOP_QTYSO / PRD_FRAC) CTNSO, MOD (SOP_QTYSO, PRD_FRAC) PCSSO, (case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * SOP_QTYSO) RPHSO, coalesce (QTY_ADJ, 0) QTY_ADJ, "
        // Sql += "case when FLOOR (coalesce (QTY_ADJ, 0) / PRD_FRAC) < 0 then CEIL (coalesce (QTY_ADJ, 0) / PRD_FRAC) else FLOOR (coalesce (QTY_ADJ, 0) / PRD_FRAC) end CTNADJ, MOD (coalesce (QTY_ADJ, 0), PRD_FRAC) PCSADJ, (case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * coalesce (QTY_ADJ, 0)) RPHADJ, "
        // Sql += "case when FLOOR ((SOP_QTYSO + coalesce (QTY_ADJ, 0) - SOP_QTYLPP) / PRD_FRAC) < 0 then CEIL ((SOP_QTYSO + coalesce (QTY_ADJ, 0) - SOP_QTYLPP) / PRD_FRAC) else FLOOR ((SOP_QTYSO + coalesce (QTY_ADJ, 0) - SOP_QTYLPP ) / PRD_FRAC) end CTNSEL, MOD ((SOP_QTYSO + coalesce (QTY_ADJ, 0) - SOP_QTYLPP ), PRD_FRAC) PCSSEL, "
        // Sql += "(case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * (SOP_QTYSO + coalesce (QTY_ADJ, 0) - SOP_QTYLPP )) RPHSEL "
        // Sql += " FROM TBMASTER_PRODMAST"
        // Sql += " JOIN TBTR_BA_STOCKOPNAME"
        // Sql += "      ON PRD_PRDCD = SOP_PRDCD "
        // Sql += "      AND SOP_LOKASI = '" & jenisbrg & "'"
        // Sql += "      AND SOP_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY')"
        // Sql += " JOIN TBMASTER_DEPARTEMENT "
        // Sql += "      ON DEP_KODEDEPARTEMENT = PRD_KODEDEPARTEMENT"
        // Sql += " LEFT JOIN TBMASTER_KATEGORI "
        // Sql += "      ON KAT_KODEKATEGORI = PRD_KODEKATEGORIBARANG "
        // Sql += "      AND KAT_KODEDEPARTEMENT = PRD_KODEDEPARTEMENT "
        // Sql += " LEFT JOIN "
        // Sql += " ("
        // Sql += "        Select "
        // Sql += "        ADJ_KODEIGR,"
        // Sql += "        ADJ_TGLSO, "
        // Sql += "        ADJ_PRDCD, "
        // Sql += "        ADJ_LOKASI, "
        // Sql += "        SUM (coalesce (ADJ_QTY, 0)) QTY_ADJ "
        // Sql += "        FROM TBTR_ADJUSTSO "
        // Sql += "        GROUP BY ADJ_KODEIGR, ADJ_TGLSO, ADJ_PRDCD, ADJ_LOKASI"
        // Sql += "  ) AS DATAS "
        // Sql += "    ON ADJ_KODEIGR = SOP_KODEIGR  "
        // Sql += "    AND ADJ_TGLSO = SOP_TGLSO "
        // Sql += "    AND ADJ_PRDCD = SOP_PRDCD "
        // Sql += "    AND ADJ_LOKASI = SOP_LOKASI "
        // Sql += "WHERE PRD_KODEDIVISI BETWEEN '" & div1 & "' AND '" & div2 & "' "
        // Sql += "AND PRD_KODEDEPARTEMENT BETWEEN '" & dept1 & "' AND '" & dept2 & "' "
        // Sql += "AND PRD_KODEKATEGORIBARANG BETWEEN '" & kat1 & "' AND '" & kat2 & "' "
        // Sql += "AND PRD_PRDCD BETWEEN '" & plu1 & "' AND '" & plu2 & "' "
        // Sql += "AND PRD_PRDCD LIKE '%0' "
        // Sql += "Order by PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, LOKASI, PRD_PRDCD ) A "

        // If pilLap = 2 Then
        //     Sql += "WHERE RPHSO - RPHLPP < -1000000 "
        // ElseIf pilLap = 3 Then
        //     Sql += "WHERE RPHSO - RPHLPP > 1000000 "
        // End If

        // If audit = True Then
        //     Dim dtAudit As DataTable
        //     dtAudit = QueryOra("SELECT DISTINCT LSI_PRDCD FROM TBTR_LOKASI_SO_EY WHERE DATE_TRUNC('DAY',LSI_TGLSO) = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY')")
        //     If dtAudit.Rows.Count > 0 Then
        //         If pilLap = 2 Or pilLap = 3 Then
        //             Sql += "AND "
        //         Else
        //             Sql += "WHERE "
        //         End If

        //         Sql += "PRD_PRDCD IN ("
        //         For i As Integer = 0 To dtAudit.Rows.Count - 1
        //             Sql += "'" & dtAudit.Rows(i).Item("LSI_PRDCD").ToString & "',"
        //         Next
        //         Sql = Strings.Left(Sql, Sql.Length - 1)
        //         Sql += ")"
        //     End If
        // End If
    }

    public function reportRingkasanBaso(){
        // Sql = "SELECT PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, DEP_NAMADEPARTEMENT, PRD_KODEKATEGORIBARANG, "
        // Sql += "KAT_NAMAKATEGORI, SOP_TGLSO, LOKASI, SUM (RPHLPP) RPHLPP, SUM (RPHSO) RPHSO, "
        // Sql += "SUM (RPHADJ) RPHADJ, SUM (RPHSEL) RPHSEL "
        // Sql += "FROM (SELECT PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, DEP_NAMADEPARTEMENT, PRD_KODEKATEGORIBARANG, "
        // Sql += "KAT_NAMAKATEGORI, PRD_PRDCD, TO_CHAR (SOP_TGLSO, 'dd-MM-yyyy') SOP_TGLSO, "
        // Sql += "CASE WHEN SOP_LOKASI = '01' THEN '01 - Barang Baik' ELSE CASE WHEN SOP_LOKASI = '02' THEN '02 - Barang Retur' "
        // Sql += "ELSE '03 = Barang Rusak' END END LOKASI, (case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * SOP_QTYLPP) RPHLPP, (case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * SOP_QTYSO) RPHSO, "
        // Sql += "(case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * coalesce (QTY_ADJ, 0)) RPHADJ, (case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * (SOP_QTYSO + coalesce (QTY_ADJ, 0) - SOP_QTYLPP)) RPHSEL "
        // Sql += "FROM TBMASTER_PRODMAST "
        // Sql += "JOIN TBTR_BA_STOCKOPNAME "
        // Sql += "   ON SOP_PRDCD = PRD_PRDCD  "
        // Sql += "   AND sop_tglso = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY') "
        // Sql += "   AND SOP_LOKASI = '" & jenisbrg & "' "
        // Sql += "LEFT JOIN TBMASTER_DEPARTEMENT "
        // Sql += "   ON DEP_KODEDEPARTEMENT = PRD_KODEDEPARTEMENT   "
        // Sql += "LEFT JOIN  TBMASTER_KATEGORI "
        // Sql += "   ON KAT_KODEKATEGORI = PRD_KODEKATEGORIBARANG "
        // Sql += "   AND KAT_KODEDEPARTEMENT = PRD_KODEDEPARTEMENT "
        // Sql += "LEFT JOIN "
        // Sql += "  (SELECT   ADJ_KODEIGR, ADJ_TGLSO, ADJ_PRDCD, ADJ_LOKASI, SUM (coalesce (ADJ_QTY, 0)) QTY_ADJ "
        // Sql += "   FROM TBTR_ADJUSTSO GROUP BY ADJ_KODEIGR, ADJ_TGLSO, ADJ_PRDCD, ADJ_LOKASI"
        // Sql += "   )AS DATS "
        // Sql += " ON ADJ_KODEIGR = SOP_KODEIGR "
        // Sql += "    AND ADJ_TGLSO= SOP_TGLSO "
        // Sql += "    AND ADJ_PRDCD = SOP_PRDCD "
        // Sql += "    AND ADJ_LOKASI = SOP_LOKASI"
        // Sql += " WHERE PRD_KODEDIVISI BETWEEN '" & div1 & "' AND '" & div2 & "' AND PRD_KODEDEPARTEMENT BETWEEN '" & dept1 & "' AND '" & dept2 & "' "
        // Sql += " AND PRD_KODEKATEGORIBARANG BETWEEN '" & kat1 & "' AND '" & kat2 & "' AND PRD_PRDCD LIKE '%0'"
        // Sql += ") A "
        // Sql += "GROUP BY PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, DEP_NAMADEPARTEMENT, PRD_KODEKATEGORIBARANG, KAT_NAMAKATEGORI, SOP_TGLSO, LOKASI "
        // Sql += "ORDER BY LOKASI, PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG "
    }

    public function reportDaftarItemYangSudahAdjust(){
        // plu1 = "0"
        // plu2 = "ZZZZZZZ"
        // tglSO = Now

        // Dim dt1 As New DataTable
        // Sql = "SELECT adj_prdcd "
        // Sql += "FROM tbtr_adjustso, tbtr_ba_stockopname, tbmaster_prodmast "
        // Sql += "WHERE adj_tglso = TO_DATE('" & Format(tglSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND adj_lokasi = '" & jenisbrg & "' "
        // Sql += "AND DATE_TRUNC('DAY',adj_create_dt) between TO_DATE('" & Format(TglAdj1, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND TO_DATE('" & Format(TglAdj2, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') "
        // Sql += "AND adj_prdcd BETWEEN '" & plu1 & "' AND '" & plu2 & "' AND sop_tglso = adj_tglso and sop_prdcd = adj_prdcd AND sop_lokasi = adj_lokasi AND prd_Prdcd = adj_prdcd "
        // Sql += "ORDER BY adj_create_dt "
        // dt1 = QueryOra(Sql)

        // Dim dtDATA As New DataTable()
        // Sql = "SELECT adj_prdcd, adj_create_dt, prd_deskripsipanjang, adj_qty, adj_keterangan, sop_newavgcost sop_lastavgcost, case when prd_unit = 'KG' then (adj_qty * sop_newavgcost) / 1000 else (adj_qty * sop_newavgcost) end total, "
        // Sql += "CASE WHEN ADJ_LOKASI = '01' THEN '01 - BARANG BAIK' ELSE CASE WHEN ADJ_LOKASI = '02' THEN '02 - BARANG RETUR' ELSE '03 - BARANG RUSAK' End END LOKASI "
        // Sql += "FROM tbtr_adjustso, tbtr_ba_stockopname, tbmaster_prodmast "
        // Sql += "WHERE adj_tglso = TO_DATE('" & Format(tglSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND adj_lokasi = '" & jenisbrg & "' "
        // Sql += "AND DATE_TRUNC('DAY',adj_create_dt) between TO_DATE('" & Format(TglAdj1, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND TO_DATE('" & Format(TglAdj2, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') "
        // Sql += "AND adj_prdcd BETWEEN '" & plu1 & "' AND '" & plu2 & "' AND sop_tglso = adj_tglso and sop_prdcd = adj_prdcd AND sop_lokasi = adj_lokasi AND prd_Prdcd = adj_prdcd "
        // Sql += "ORDER BY adj_create_dt "

        // dtDATA = QueryOra(Sql)
        // dtDATA.Rows.Clear()
        // dtDATA.TableName = "DATA"

        // Dim dt3 As New DataTable
        // For i As Integer = 0 To dt1.Rows.Count - 1
        //     Dim drcek As DataRow()
        //     drcek = dtDATA.Select("adj_prdcd = '" & dt1.Rows(i).Item("adj_prdcd").ToString & "'")
        //     If drcek.Length = 0 Then
        //         Sql = "SELECT adj_prdcd, adj_create_dt, prd_deskripsipanjang, adj_qty, adj_keterangan, sop_newavgcost sop_lastavgcost, case when prd_unit = 'KG' then (adj_qty * sop_newavgcost) / 1000 else (adj_qty * sop_newavgcost) end total, "
        //         Sql += "CASE WHEN ADJ_LOKASI = '01' THEN '01 - BARANG BAIK' ELSE CASE WHEN ADJ_LOKASI = '02' THEN '02 - BARANG RETUR' ELSE '03 - BARANG RUSAK' End END LOKASI "
        //         Sql += "FROM tbtr_adjustso, tbtr_ba_stockopname, tbmaster_prodmast "
        //         Sql += "WHERE adj_tglso = TO_DATE('" & Format(tglSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND adj_lokasi = '" & jenisbrg & "' "
        //         Sql += "AND DATE_TRUNC('DAY',adj_create_dt) between TO_DATE('" & Format(TglAdj1, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND TO_DATE('" & Format(TglAdj2, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') "
        //         Sql += "AND adj_prdcd = '" & dt1.Rows(i).Item("adj_prdcd").ToString & "' AND sop_tglso = adj_tglso and sop_prdcd = adj_prdcd AND sop_lokasi = adj_lokasi AND prd_Prdcd = adj_prdcd "
        //         Sql += "ORDER BY adj_create_dt "
        //         dt3 = QueryOra(Sql)
        //         For j As Integer = 0 To dt3.Rows.Count - 1
        //             Dim dr As DataRow
        //             dr = dtDATA.NewRow
        //             dr(0) = dt3.Rows(j).Item(0)
        //             dr(1) = dt3.Rows(j).Item(1)
        //             dr(2) = dt3.Rows(j).Item(2)
        //             dr(3) = dt3.Rows(j).Item(3)
        //             dr(4) = dt3.Rows(j).Item(4)
        //             dr(5) = dt3.Rows(j).Item(5)
        //             dr(6) = dt3.Rows(j).Item(6)
        //             dr(7) = dt3.Rows(j).Item(7)
        //             dtDATA.Rows.Add(dr)
        //         Next
        //     End If
        // Next
    }

    public function reportDafterKksoAcost(){
        // Sql = "SELECT TO_CHAR (LSO_TGLSO, 'dd-MM-yyyy') LSO_TGLSO, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, "
        // Sql += "LSO_SHELVINGRAK, LSO_NOURUT, LSO_PRDCD, "
        // Sql += "CASE WHEN LSO_LOKASI = '01' THEN '01 - BARANG BAIK' ELSE CASE WHEN LSO_LOKASI = '02' THEN '02 - BARANG RETUR' "
        // Sql += "ELSE '03 - BARANG RUSAK' END END LOKASI, "
        // Sql += "PRD_DESKRIPSIPANJANG, PRD_UNIT || '/' || PRD_FRAC SATUAN, PRD_KODETAG, "
        // Sql += "FLOOR (LSO_QTY / PRD_FRAC) CTN, MOD (LSO_QTY, PRD_FRAC) PCS, LSO_QTY, ST_AVGCOST "
        // Sql += "FROM TBTR_LOKASI_SO, TBMASTER_PRODMAST, TBMASTER_STOCK "
        // Sql += "WHERE coalesce (LSO_RECID, '0') <> '1' "
        // Sql += "AND LSO_KODERAK BETWEEN '" & koderak1 & "' AND '" & koderak2 & "' "
        // Sql += "AND LSO_KODESUBRAK BETWEEN '" & subrak1 & "' AND '" & subrak2 & "' "
        // Sql += "AND LSO_TIPERAK BETWEEN '" & tipe1 & "' AND '" & tipe2 & "' "
        // Sql += "AND LSO_SHELVINGRAK BETWEEN '" & shelving1 & "' AND '" & shelving2 & "' "
        // Sql += "AND LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') "
        // Sql += "AND LSO_LOKASI = '" & jenisbrg & "' "
        // Sql += "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR "
        // Sql += "AND ST_PRDCD = LSO_PRDCD "
        // Sql += "AND ST_LOKASI = LSO_LOKASI "
        // Sql += "AND (coalesce(ST_AVGCOST,0) = 0 OR coalesce(ST_AVGCOST,0) < 0) "
        // Sql += "ORDER BY LSO_TGLSO, LOKASI, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT "
    }

    public function reportDaftarMasterLokasiSo(){
        // plu1 = "0"
        // '------------------
        // If InputRpt8.PLUId1 <> "" Then
        //     plu1 = InputRpt8.PLUId1
        // End If

        // Sql = "SELECT   LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, "
        // Sql += "LSO_SHELVINGRAK, LSO_PRDCD, LSO_LOKASI, "
        // Sql += "PRD_DESKRIPSIPANJANG || ' - ' || PRD_UNIT || '/' || PRD_FRAC PRD_DESKRIPSIPANJANG, PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, "
        // Sql += "FLOOR (LSO_QTY / PRD_FRAC) CTN, MOD (LSO_QTY, PRD_FRAC) PCS, LSO_QTY "
        // Sql += "FROM TBTR_LOKASI_SO, TBMASTER_PRODMAST "
        // Sql += "WHERE coalesce (LSO_RECID, '0') <> '1' "
        // Sql += "AND LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') "
        // Sql += "AND LSO_PRDCD = '" & plu1 & "' "
        // Sql += "AND LSO_LOKASI = '" & jenisbrg & "' "
        // Sql += "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR "
        // Sql += "ORDER BY PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_LOKASI  "
    }

    public function reportDaftarItemSo(){
        // div1 = "0"
        // dept1 = "0"
        // kat1 = "0"
        // div2 = "Z"
        // dept2 = "ZZ"
        // kat2 = "ZZ"

        // Sql += "SELECT * "
        // Sql += "FROM (SELECT PRD_KODEDIVISI, "
        // Sql += "DIV_NAMADIVISI, "
        // Sql += "PRD_KODEDEPARTEMENT, "
        // Sql += "DEP_NAMADEPARTEMENT, "
        // Sql += "PRD_KODEKATEGORIBARANG, "
        // Sql += "KAT_NAMAKATEGORI, "
        // Sql += "PRD_DESKRIPSIPANJANG, "
        // Sql += "PRD_UNIT || '/' || PRD_FRAC SATUAN, "
        // Sql += "PRD_KODETAG, "
        // Sql += "coalesce (LSO_PRDCD, '0000000') LSO_PRDCD, "
        // Sql += "TGL_SO_SETTING LSO_TGLSO, "
        // Sql += "ST_SALDOAKHIR, "
        // Sql += "prd_prdcd, "
        // Sql += "ST_AVGCOST, "
        // Sql += " ( ST_SALDOAKHIR * ST_AVGCOST) TOTAL "
        // Sql += "   FROM TBMASTER_PRODMAST "
        // Sql += "   LEFT JOIN TBMASTER_DIVISI "
        // Sql += "   ON DIV_KODEDIVISI = PRD_KODEDIVISI"
        // Sql += "   LEFT JOIN TBMASTER_DEPARTEMENT "
        // Sql += "   ON DEP_KODEDEPARTEMENT = PRD_KODEDEPARTEMENT "
        // Sql += "   LEFT JOIN TBMASTER_KATEGORI "
        // Sql += "   ON KAT_KODEDEPARTEMENT = PRD_KODEDEPARTEMENT "
        // Sql += "   AND KAT_KODEKATEGORI= PRD_KODEKATEGORIBARANG"
        // Sql += "   LEFT JOIN TBMASTER_STOCK "
        // Sql += "   ON ST_PRDCD = PRD_PRDCD "
        // Sql += "   AND ST_LOKASI = '" & jenisbrg & "' "
        // Sql += "   LEFT JOIN"
        // Sql += " ( select TBTR_LOKASI_SO.* "
        // Sql += "   from( "
        // Sql += "        SELECT MAX(MSO_TGLSO) TGL_SO_SETTING "
        // Sql += "        FROM tbmaster_Setting_so"
        // Sql += "       ) C "
        // Sql += "       join TBTR_LOKASI_SO on LSO_TGLSO = TGL_SO_SETTING"
        // Sql += "       ) TBTR_LOKASI_SO "
        // Sql += "    ON LSO_PRDCD = ST_PRDCD "
        // Sql += "LEFT JOIN"
        // Sql += "   (SELECT MAX(MSO_TGLSO) TGL_SO_SETTING "
        // Sql += "     FROM tbmaster_Setting_so) SO_SETTING           "
        // Sql += "   ON LSO_LOKASI  = ST_LOKASI "
        // Sql += "   AND DATE_TRUNC('DAY',LSO_TGLSO) = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY')"
        // Sql += ") A"
        // Sql += " WHERE LSO_PRDCD = '0000000' "
        // Sql += " AND PRD_KODEDIVISI BETWEEN '" & div1 & "' AND '" & div2 & "' "
        // Sql += " AND PRD_KODEDEPARTEMENT BETWEEN '" & dept1 & "' AND '" & dept2 & "' "
        // Sql += " AND PRD_KODEKATEGORIBARANG BETWEEN '" & kat1 & "' AND '" & kat2 & "' "
        // Sql += " AND PRD_PRDCD LIKE '%0' "
        // Sql += " ORDER BY PRD_KODEDIVISI, PRD_KODEDEPARTEMENT,PRD_KODEKATEGORIBARANG,LSO_PRDCD "
    }

    public function reportRakBelumSo(){
        // koderak1 = "0"
        // subrak1 = "0"
        // tipe1 = "0"
        // shelving1 = "0"
        // tglSO = Now

        // Sql = "SELECT lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lso_nourut, "
        // Sql += "CASE WHEN lso_lokasi = '01' THEN 'BAIK' ELSE CASE WHEN lso_lokasi = '02' THEN 'RETUR' ELSE 'RUSAK' END END jenisbrg, "
        // Sql += "lso_prdcd, prd_deskripsipanjang, prd_unit || '/' || prd_frac UNIT, "
        // Sql += "CASE WHEN lso_flagsarana = 'H' THEN 'HandHeld' ELSE 'Kertas' END Sarana, lso_qty "
        // Sql += "FROM tbtr_lokasi_so, tbmaster_prodmast "
        // Sql += "WHERE lso_tglso = TO_DATE('" & Format(tglSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND lso_modify_by IS NULL "

        // If koderak1 <> "0" Then
        //     Sql += " AND lso_koderak = '" & koderak1 & "' "
        // End If

        // If subrak1 <> "0" Then
        //     Sql += " AND lso_kodesubrak = ' " & subrak1 & "' "
        // End If

        // If tipe1 <> "0" Then
        //     Sql += " AND lso_tiperak = ' " & tipe1 & "' "
        // End If

        // If shelving1 <> "0" Then
        //     Sql += " AND lso_shelvingrak = ' " & shelving1 & "'"
        // End If

        // Sql += " AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR "
        // If _flagTransferLokasi = "Y" Then
        //     Sql += " AND LSO_FLAGLIMIT='Y' "
        // End If
        // Sql += "Order By lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lso_nourut, lso_lokasi, lso_prdcd "
    }

    public function reportInqueryPlanoSonasExcel(){
        // If inp.Flag = "N" Then
        //     Exit Sub
        // Else
        //     PLU = inp.PLUId1
        //     Lokasi = inp.JenisBrgId
        //     If Lokasi.ToUpper = "R" Then
        //         Lokasi = "03"
        //     ElseIf Lokasi.ToUpper = "T" Then
        //         Lokasi = "02"
        //     ElseIf Lokasi.ToUpper = "B" Then
        //         Lokasi = "01"
        //     End If
        // End If

        // If Lokasi = "A" Then
        //     dt = QueryOra("SELECT * FROM TBTR_LOKASI_SO, TBMASTER_PRODMAST WHERE LSO_PRDCD = PRD_PRDCD AND LSO_PRDCD = '" & PLU & "' AND DATE_TRUNC('DAY',LSO_TGLSO) = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy") & "', 'DD-MM-YYYY')")
        // Else
        //     dt = QueryOra("SELECT * FROM TBTR_LOKASI_SO, TBMASTER_PRODMAST WHERE LSO_PRDCD = PRD_PRDCD AND LSO_LOKASI = '" & Lokasi & "' AND LSO_PRDCD = '" & PLU & "' AND DATE_TRUNC('DAY',LSO_TGLSO) = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy") & "', 'DD-MM-YYYY')")
        // End If
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
