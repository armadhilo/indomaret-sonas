<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', '0');

use App\Exports\InquiryPlanoExport;
use App\Exports\LppMonthEndExport;
use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\reportCetakDraftLhsoRequest;
use App\Http\Requests\ReportCetakDraftReturSebelumLhsoRequest;
use App\Http\Requests\ReportDaftarItemAdjustRequest;
use App\Http\Requests\ReportDaftarItemBelumAdaDiMasterRequest;
use App\Http\Requests\ReportInqueryPlanoSonasExcelRequest;
use App\Http\Requests\ReportInqueryPlanoSonasRequest;
use App\Http\Requests\ReportLokasiRakBelumDiSoRequest;
use App\Http\Requests\ReportLokasiSoRequest;
use App\Http\Requests\ReportLppMonthEndExcelActionCetakRequest;
use App\Http\Requests\ReportMasterLokasiSoRequest;
use App\Http\Requests\ReportPerincianBasoRequest;
use App\Http\Requests\ReportRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    private $FlagTahap;
    private $flagTransferLokasi;
    public function __construct(Request $request){
        DatabaseConnection::setConnection(session('KODECABANG'), "PRODUCTION");
    }

    private function processRoutePrefix($route){
        if (strpos($route, 'report/') === 0) {
            $route = substr($route, 7);
        }

        if ($route === "/report") {
            $route = "index";
        }

        return $route;
    }

    public function dummyData(Request $request){
        $currentView = $this->processRoutePrefix($request->route()->getPrefix());
        $data['data'] = '';

        $pdf = PDF::loadView('pdf.' . $currentView, $data);
        if($currentView == "perincian-baso"){
            $customPaper = array(0,0,1000, 500);
            $pdf->setPaper($customPaper);
        } else if($currentView == "cetak-draft-lhso"){
            $customPaper = array(0, 0, 795, 620);
            $pdf->setPaper($customPaper);
        } else if ($currentView == "cetak-draft-sebelum-lhso"){
            $customPaper = array(0, 0, 796, 620);
            $pdf->setPaper($customPaper);
        }
        return $pdf->stream();
    }

    public function index(Request $request){
        $dtCek = DB::select("SELECT DATE_TRUNC('DAY',MSO_TGLSO) MSO_TGLSO, MSO_FLAG_CREATELSO FROM TBMASTER_SETTING_SO ORDER BY MSO_TGLSO DESC");
        if(count($dtCek) == 0){
            return ApiFormatter::error(400, 'Data SO tidak ditemukan');
        }

        $data['TanggalSO'] = Carbon::parse($dtCek[0]->mso_tglso)->format('Y-m-d');
        $data['flagTransferLokasi'] = $dtCek[0]->mso_flag_createlso;

        $currentView = $this->processRoutePrefix($request->route()->getPrefix()) == 'report' ? 'index' : $this->processRoutePrefix($request->route()->getPrefix());
        return view('report.' . $currentView, $data);
    }

    //! DONE
    public function reportListFormKkso(ReportRequest $request){
        $query = '';
        $query .= "SELECT lso_tglso, lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lso_nourut, ";
        $query .= "lso_prdcd, CASE WHEN lso_lokasi = '01' THEN '01 - BARANG BAIK' ELSE CASE WHEN lso_lokasi = '02' THEN '02 - BARANG RETUR' ELSE '03 - BARANG RUSAK' END END lokasi, prd_deskripsipanjang, prd_unit || '/' || prd_frac AS satuan, prd_kodetag, lso_tmp_qtyctn, lso_tmp_qtypcs ";
        $query .= "FROM tbtr_lokasi_so, tbmaster_prodmast ";
        $query .= "WHERE COALESCE(lso_recid,'0') <> '1' AND lso_koderak BETWEEN '" . $request->koderak1 . "' AND '" . $request->koderak2 . "' ";
        $query .= "AND LSO_TGLSO = TO_DATE('" . $request->tanggal_start_so . "','YYYY-MM-DD') ";
        $query .= "AND LSO_LOKASI = '" . $request->jenis_barang . "'  ";
        $query .= "AND lso_kodesubrak BETWEEN '" . $request->subrak1 . "' AND '" . $request->subrak2 . "' ";
        $query .= "AND lso_tiperak BETWEEN '" . $request->tipe1 . "' AND '" . $request->tipe2 . "' ";
        $query .= "AND lso_shelvingrak BETWEEN '" . $request->shelving1 . "' AND '" . $request->shelving2 . "' ";
        $query .= "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR ";
        $query .= "AND COALESCE(lso_flagsarana, 'K') = 'K' ";
        if($this->flagTransferLokasi == 'Y'){
            $query .= "AND LSO_FLAGLIMIT='Y' ";
        }
        $query .= "ORDER BY lso_lokasi, lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lso_nourut ";
        $data['data'] = DB::select($query);

        //! UPDATE
        foreach($data['data'] as $item){
            DB::table('tbtr_lokasi_so')
                ->whereDate('lso_tglso', $request->tanggal_start_so)
                ->where('lso_prdcd', $item->lso_prdcd)
                ->update(['lso_flagkkso' => 'Y']);
        }

        $pdf = PDF::loadView('pdf.list-form-kkso', $data);
        if ($request->method() === 'GET') {
            return $pdf->stream('REGISTER KKSO 1.pdf');
        }
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);
    }


    //! DONE
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
        $query .= "AND LSO_TGLSO = TO_DATE('" . $request->tanggal_start_so . "','YYYY-MM-DD') ";
        $query .= "AND lso_kodesubrak between '" . $request->subrak1 . "' and '" . $request->subrak2 . "' ";
        $query .= "AND lso_tiperak between '" . $request->tipe1 . "' and '" . $request->tipe2 . "' ";
        $query .= "AND lso_shelvingrak between '" . $request->shelving1 . "' and '" . $request->shelving2 . "' ";
        $query .= "AND lso_lokasi = '" . $request->jenis_barang . "' ";
        $query .= "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR ";
        if($this->flagTransferLokasi == 'Y'){
            $query .= "AND LSO_FLAGLIMIT='Y' ";
        }
        $query .= " ) A ";
        $query .= "GROUP BY rak1, rak2, sub1, sub2, shel1, shel2, tipe1, tipe2, LSO_Koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lokasi ";
        $query .= "ORDER BY lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lokasi ";
        $data['data'] = DB::select($query);

        //! GET NAMA PERUSAHAAN
        $perusahaan = DB::table('tbmaster_perusahaan')
            ->select('prs_kodeigr as kode_igr', 'prs_namacabang')
            ->first();

        $data['perusahaan'] = $perusahaan;
        $data['request'] = $request->all();

        $pdf = PDF::loadView('pdf.register-kkso', $data);
        if ($request->method() === 'GET') {
            return $pdf->stream('REGISTER KKSO 1.pdf');
        }
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);
    }

    //! DONE
    public function reportEditListKkso(ReportRequest $request){

        $FlagReset = 'N';
        $dtCek = DB::select("SELECT coalesce(MSO_FLAGSUM, 'N') MSO_FLAGSUM FROM TBMASTER_SETTING_SO WHERE MSO_TGLSO =  TO_DATE('" . $request->tanggal_start_so . "','YYYY-MM-DD')");
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
        // $query .= "AND LSO_TGLSO = TO_DATE('" . $request->tanggal_start_so . "','YYYY-MM-DD') ";
        $query .= "AND LSO_KODERAK BETWEEN '" . $request->koderak1 . "' and '" . $request->koderak2 . "' ";
        $query .= "AND LSO_KODESUBRAK BETWEEN '" . $request->subrak1 . "' and '" . $request->subrak2 . "' ";
        $query .= "AND LSO_TIPERAK BETWEEN '" . $request->tipe1 . "' and '" . $request->tipe2 . "' ";
        $query .= "AND LSO_SHELVINGRAK BETWEEN '" . $request->shelving1 . "' and '" . $request->shelving2 . "' ";
        $query .= "AND LSO_LOKASI = '" . $request->jenis_barang . "' ";
        $query .= "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR ";
        $query .= "AND ST_PRDCD = LSO_PRDCD AND ST_LOKASI = LSO_LOKASI ";
        // $query .= "AND sop_tglso = TO_DATE('" . $request->tanggal_start_so . "','YYYY-MM-DD') ";
        $query .= "AND sop_lokasi = st_lokasi and sop_prdcd = st_prdcd ";
        $query .= "ORDER BY LSO_TGLSO, LOKASI, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT LIMIT 100";
        $data['data'] = DB::select($query);

        //! GET NAMA PERUSAHAAN
        $perusahaan = DB::table('tbmaster_perusahaan')
            ->select('prs_kodeigr as kode_igr', 'prs_namacabang')
            ->first();

        $data['perusahaan'] = $perusahaan;

        $pdf = PDF::loadView('pdf.edit-list-kkso', $data);
        if ($request->method() === 'GET') {
            return $pdf->stream('EDIT LIST KKSO 1.pdf');
        }
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);
    }

    //! DONE
    public function reportRegisterKkso2(ReportRequest $request){
        $query = '';
        $query .= "SELECT   RAK1, RAK2, SUB1, SUB2, SHEL1, SHEL2, TIPE1, TIPE2, LSO_KODERAK, LSO_TGLSO, LSO_KODESUBRAK, ";
        $query .= "LSO_TIPERAK, LSO_SHELVINGRAK, LOKASI, SUM (ITEM) ITEM, SUM (SO) SO, (SUM(ITEM) - SUM(SO)) SELISIH, ";
        $query .= "FLOOR (  ((SUM (ITEM) + 10 + (1 * 2)) / 25) + CASE WHEN MOD ((SUM (ITEM) + 10 + (1 * 2)), 25) <> 0 THEN 1 ELSE 0 END) LBR ";
        $query .= "FROM (SELECT '" . $request->koderak1 . "' AS rak1, '" . $request->koderak2 . "' AS rak2, '" . $request->subrak1 . "' AS sub1, '" . $request->subrak2 . "' AS sub2, ";
        $query .= "'" . $request->tipe1 . "' AS tipe1, '" . $request->tipe2 . "' AS tipe2, '" . $request->shelving1 . "' AS shel1, '" . $request->shelving2 . "' AS shel2, ";
        $query .= "TO_CHAR(LSO_TGLSO, 'dd-MM-yyyy') LSO_TGLSO, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT, LSO_PRDCD, 1 ITEM, ";
        $query .= "CASE WHEN LSO_QTY <> 0 THEN 1 ELSE 0 END SO, ";
        $query .= "CASE WHEN LSO_LOKASI = '01' THEN '01 - BARANG BAIK' ELSE CASE WHEN LSO_LOKASI = '02' THEN '02 - BARANG RETUR' ELSE '03 - BARANG RUSAK' END END LOKASI, ";
        $query .= "PRD_DESKRIPSIPANJANG, PRD_UNIT || '/' || PRD_FRAC SATUAN, PRD_KODETAG FROM TBTR_LOKASI_SO, TBMASTER_PRODMAST ";
        $query .= "WHERE COALESCE(LSO_RECID, '0') <> '1' AND LSO_KODERAK BETWEEN '" . $request->koderak1 . "' AND '" . $request->koderak2 . "' ";
        $query .= "AND LSO_TGLSO = TO_DATE('" . $request->tanggal_start_so . "','YYYY-MM-DD') ";
        $query .= "AND LSO_KODESUBRAK BETWEEN '" . $request->subrak1 . "' AND '" . $request->subrak2 . "' AND LSO_TIPERAK BETWEEN '" . $request->tipe1 . "' AND '" . $request->tipe2 . "' ";
        $query .= "AND LSO_SHELVINGRAK BETWEEN '" . $request->shelving1 . "' AND '" . $request->shelving2 . "'  AND LSO_LOKASI = '" . $request->jenis_barang . "' ";
        if($this->flagTransferLokasi == 'Y'){
            $query .= "AND LSO_FLAGLIMIT='Y' ";
        }
        $query .= "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR ) A ";
        $query .= "group by rak1, rak2, sub1, sub2, shel1, shel2, tipe1, tipe2, lso_koderak, lso_tglso, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lokasi ";
        $query .= "order by lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lokasi ";

        $data['data'] = DB::select($query);
        $data['request'] = $request->all();

        $pdf = PDF::loadView('pdf.register-kkso-2', $data);
        if ($request->method() === 'GET') {
            return $pdf->stream('REGISTER KKSO 2.pdf');
        }
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);
    }

    //! MINUS
    //? styling pdf
    //? footer pdf
    // * check
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
        $query .= "      AND SOP_LOKASI = '" . $request->jenis_barang . "'";
        // $query .= "      AND SOP_TGLSO = TO_DATE('" . $request->tanggal_start_so . "', 'YYYY-MM-DD')";
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
        // $query .= "WHERE PRD_KODEDIVISI BETWEEN '" . $request->div1 . "' AND '" . $request->div2 . "' ";
        // $query .= "AND PRD_KODEDEPARTEMENT BETWEEN '" . $request->dept1 . "' AND '" . $request->dept2 . "' ";
        // $query .= "AND PRD_KODEKATEGORIBARANG BETWEEN '" . $request->kat1 . "' AND '" . $request->kat2 . "' ";
        // $query .= "AND PRD_PRDCD BETWEEN '" . $request->plu1 . "' AND '" . $request->plu2 . "' ";
        // $query .= "AND PRD_PRDCD LIKE '%0' ";
        $query .= "Order by PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, LOKASI, PRD_PRDCD ) A ";

        if($request->selisih_so == '2'){
            $query .= "WHERE RPHSO - RPHLPP < -1000000 ";
        }elseif($request->selisih_so == '3'){
            $query .= "WHERE RPHSO - RPHLPP > 1000000 ";
        }

        if($request->check_rpt_audit == 1){
            $dtCek = DB::select("SELECT DISTINCT LSI_PRDCD FROM TBTR_LOKASI_SO_EY WHERE DATE_TRUNC('DAY',LSI_TGLSO) = TO_DATE('" . $request->tanggal_start_so . "', 'YYYY-MM-DD')");
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

        //! dummy
        $query .= 'LIMIT  100';

        $data['data'] = collect(DB::select($query))->groupBy(['dep_namadepartement','kat_namakategori'])->toArray();
        $data['request'] = $request->all();

        $pdf = PDF::loadView('pdf.perincian-baso', $data);
        $pdf->getDomPDF()->set_option("enable_php", true);
        $customPaper = array(0,0,1000, 500);
        $pdf->setPaper($customPaper);
        if ($request->method() === 'GET') {
            return $pdf->stream('PERINCIAN BASO.pdf');
        }
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);
    }

    //!  MINUS
    //? footer pdf
    // * check
    public function reportRingkasanBaso(ReportPerincianBasoRequest $request){
        $query = '';
        $query .= "SELECT PRD_KODEDIVISI, PRD_KODEDEPARTEMENT || ' - ' || DEP_NAMADEPARTEMENT as DEP_NAMADEPARTEMENT, PRD_KODEKATEGORIBARANG || ' - ' || KAT_NAMAKATEGORI as KAT_NAMAKATEGORI, ";
        $query .= "SOP_TGLSO, LOKASI, SUM (RPHLPP) RPHLPP, SUM (RPHSO) RPHSO, ";
        $query .= "SUM (RPHADJ) RPHADJ, SUM (RPHSEL) RPHSEL ";
        $query .= "FROM (SELECT PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, DEP_NAMADEPARTEMENT, PRD_KODEKATEGORIBARANG, ";
        $query .= "KAT_NAMAKATEGORI, PRD_PRDCD, TO_CHAR (SOP_TGLSO, 'dd-MM-yyyy') SOP_TGLSO, ";
        $query .= "CASE WHEN SOP_LOKASI = '01' THEN '01 - Barang Baik' ELSE CASE WHEN SOP_LOKASI = '02' THEN '02 - Barang Retur' ";
        $query .= "ELSE '03 = Barang Rusak' END END LOKASI, (case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * SOP_QTYLPP) RPHLPP, (case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * SOP_QTYSO) RPHSO, ";
        $query .= "(case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * coalesce (QTY_ADJ, 0)) RPHADJ, (case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end * (SOP_QTYSO + coalesce (QTY_ADJ, 0) - SOP_QTYLPP)) RPHSEL ";
        $query .= "FROM TBMASTER_PRODMAST ";
        $query .= "JOIN TBTR_BA_STOCKOPNAME ";
        $query .= "   ON SOP_PRDCD = PRD_PRDCD  ";
        // $query .= "   AND sop_tglso = TO_DATE('" . $request->tanggal_start_so . "', 'YYYY-MM-DD') ";
        // $query .= "   AND SOP_LOKASI = '" . $request->jenis_barang . "' ";
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
        // $query .= " WHERE PRD_KODEDIVISI BETWEEN '" . $request->div1 . "' AND '" . $request->div2 . "' AND PRD_KODEDEPARTEMENT BETWEEN '" . $request->dept1 . "' AND '" . $request->dept2 . "' ";
        // $query .= " AND PRD_KODEKATEGORIBARANG BETWEEN '" . $request->kat1 . "' AND '" . $request->kat2 . "' AND PRD_PRDCD LIKE '%0'";
        $query .= ") A ";
        $query .= "GROUP BY PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, DEP_NAMADEPARTEMENT, PRD_KODEKATEGORIBARANG, KAT_NAMAKATEGORI, SOP_TGLSO, LOKASI ";
        $query .= "ORDER BY LOKASI, PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG ";
        $data['data'] = collect(DB::select($query))->groupBy(['dep_namadepartement','kat_namakategori'])->toArray();

        switch ($request->jenis_barang) {
            case '01':
                $textJenisBarang = 'BAIK';
                break;
            case '02':
                $textJenisBarang = 'RETUR';
                break;
            default:
                $textJenisBarang = 'RUSAK';
                break;
        }

        $data['textJenisBarang'] = $textJenisBarang;

        $pdf = PDF::loadView('pdf.ringkasan-baso', $data);
        $pdf->getDomPDF()->set_option("enable_php", true);
        if ($request->method() === 'GET') {
            return $pdf->stream('BASO RINGKASAN ' . $textJenisBarang . ' - RESET.pdf');
        }
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);
    }

    //! MINUS
    //? kurang disesuaikan header
    public function reportDaftarItemYangSudahAdjust(ReportDaftarItemAdjustRequest $request){

        $query = '';
        $query .= "SELECT adj_prdcd ";
        $query .= "FROM tbtr_adjustso join tbtr_ba_stockopname on sop_prdcd = adj_prdcd join tbmaster_prodmast on prd_prdcd = adj_prdcd ";
        $query .= "WHERE adj_lokasi = '" . $request->jenis_barang . "' ";
        // $query .= "AND adj_tglso = TO_DATE('" . $request->tanggal_start_so . "','YYYY-MM-DD') ";
        // $query .= "AND DATE_TRUNC('DAY',adj_create_dt) between TO_DATE('" . $request->tanggal_adjust_start . "','YYYY-MM-DD') AND TO_DATE('" . $request->tanggal_adjust_end . "','YYYY-MM-DD') ";
        // $query .= "AND adj_prdcd BETWEEN '" . $request->plu1 . "' AND '" . $request->plu2 . "' AND sop_tglso = adj_tglso and sop_prdcd = adj_prdcd AND sop_lokasi = adj_lokasi AND prd_Prdcd = adj_prdcd ";
        $query .= "ORDER BY adj_create_dt ";
        //! dummy
        $query .= "LIMIT 10";
        $dt1 = DB::select($query);

        $query = '';
        $query .= "SELECT adj_prdcd, adj_create_dt, prd_deskripsipanjang, adj_qty, adj_keterangan, sop_newavgcost sop_lastavgcost, case when prd_unit = 'KG' then (adj_qty * sop_newavgcost) / 1000 else (adj_qty * sop_newavgcost) end total, ";
        $query .= "CASE WHEN ADJ_LOKASI = '01' THEN '01 - BARANG BAIK' ELSE CASE WHEN ADJ_LOKASI = '02' THEN '02 - BARANG RETUR' ELSE '03 - BARANG RUSAK' End END LOKASI ";
        $query .= "FROM tbtr_adjustso join tbtr_ba_stockopname on sop_prdcd = adj_prdcd join tbmaster_prodmast on prd_prdcd = adj_prdcd ";
        $query .= "WHERE adj_lokasi = '" . $request->jenis_barang . "' ";
        // $query .= "AND adj_tglso = TO_DATE('" . $request->tanggal_start_so . "','YYYY-MM-DD') ";
        // $query .= "AND DATE_TRUNC('DAY',adj_create_dt) between TO_DATE('" . $request->tanggal_adjust_start . "','YYYY-MM-DD') AND TO_DATE('" . $request->tanggal_adjust_end . "','YYYY-MM-DD') ";
        // $query .= "AND adj_prdcd BETWEEN '" . $request->plu1 . "' AND '" . $request->plu2 . "' AND sop_tglso = adj_tglso and sop_prdcd = adj_prdcd AND sop_lokasi = adj_lokasi AND prd_Prdcd = adj_prdcd ";
        $query .= "ORDER BY adj_create_dt ";
        //! dummy
        $query .= "LIMIT 10";
        $dtDATA = DB::select($query);
        $array = [];
        foreach($dt1 as $item){
            $dtCek = collect($dtDATA)->where('adj_prdcd', $item->adj_prdcd)->first();

            if(!empty($dtCek)){
                $query = '';
                $query .= "SELECT adj_prdcd, adj_create_dt, prd_deskripsipanjang, adj_qty, adj_keterangan, sop_newavgcost sop_lastavgcost, case when prd_unit = 'KG' then (adj_qty * sop_newavgcost) / 1000 else (adj_qty * sop_newavgcost) end total, ";
                $query .= "CASE WHEN ADJ_LOKASI = '01' THEN '01 - BARANG BAIK' ELSE CASE WHEN ADJ_LOKASI = '02' THEN '02 - BARANG RETUR' ELSE '03 - BARANG RUSAK' End END LOKASI ";
                $query .= "FROM tbtr_adjustso join tbtr_ba_stockopname on sop_prdcd = adj_prdcd join tbmaster_prodmast on prd_prdcd = adj_prdcd ";
                $query .= "WHERE adj_lokasi = '" . $request->jenis_barang . "' ";
                // $query .= "AND adj_tglso = TO_DATE('" . $request->tanggal_start_so . "','YYYY-MM-DD') ";
                // $query .= "AND DATE_TRUNC('DAY',adj_create_dt) between TO_DATE('" . $request->tanggal_adjust_start . "','YYYY-MM-DD') AND TO_DATE('" . $request->tanggal_adjust_end . "','YYYY-MM-DD') ";
                // $query .= "AND adj_prdcd = '" . $item->adj_prdcd . "' AND sop_tglso = adj_tglso and sop_prdcd = adj_prdcd AND sop_lokasi = adj_lokasi AND prd_Prdcd = adj_prdcd ";
                $query .= "ORDER BY adj_create_dt ";
                //! dummy
                $query .= "LIMIT 10";
                $data_detail = DB::select($query);

                foreach($data_detail as $item_detail){
                    $array[] = $item_detail;
                }
            }
        }

        $data['data'] = $array;

        $pdf = PDF::loadView('pdf.daftar-item-adjustment', $data);
        if ($request->method() === 'GET') {
            return $pdf->stream('DAFTAR ITEM YANG SUDAH DIADJUSTMENT SETELAH RESET.pdf');
        }
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);
    }

    //! DONE
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
        // $query .= "AND LSO_KODERAK BETWEEN '" . $request->koderak1 . "' AND '" . $request->koderak2 . "' ";
        // $query .= "AND LSO_KODESUBRAK BETWEEN '" . $request->subrak1 . "' AND '" . $request->subrak2 . "' ";
        // $query .= "AND LSO_TIPERAK BETWEEN '" . $request->tipe1 . "' AND '" . $request->tipe2 . "' ";
        // $query .= "AND LSO_SHELVINGRAK BETWEEN '" . $request->shelving1 . "' AND '" . $request->shelving2 . "' ";
        // $query .= "AND LSO_TGLSO = TO_DATE('" . $request->tanggal_start_so . "','YYYY-MM-DD') ";
        $query .= "AND LSO_LOKASI = '" . $request->jenis_barang . "' ";
        $query .= "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR ";
        $query .= "AND ST_PRDCD = LSO_PRDCD ";
        $query .= "AND ST_LOKASI = LSO_LOKASI ";
        $query .= "AND (coalesce(ST_AVGCOST,0) = 0 OR coalesce(ST_AVGCOST,0) < 0) ";
        $query .= "ORDER BY LSO_TGLSO, LOKASI, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT ";
        $data['data'] = DB::select($query);

        $pdf = PDF::loadView('pdf.daftar-kkso-acost-0', $data);
        if ($request->method() === 'GET') {
            return $pdf->stream('DAFTAR KKSO DENGAN ACOST.pdf');
        }
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);
    }

    //! DONE
    public function reportDaftarMasterLokasiSo(ReportMasterLokasiSoRequest $request){

        $query = '';
        $query .= "SELECT   LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, ";
        $query .= "LSO_SHELVINGRAK, LSO_PRDCD, LSO_LOKASI, ";
        $query .= "PRD_DESKRIPSIPANJANG || ' - ' || PRD_UNIT || '/' || PRD_FRAC PRD_DESKRIPSIPANJANG, PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, ";
        $query .= "FLOOR (LSO_QTY / PRD_FRAC) CTN, MOD (LSO_QTY, PRD_FRAC) PCS, LSO_QTY ";
        $query .= "FROM TBTR_LOKASI_SO, TBMASTER_PRODMAST ";
        $query .= "WHERE coalesce (LSO_RECID, '0') <> '1' ";
        $query .= "AND LSO_TGLSO = TO_DATE('" . $request->tanggal_start_so . "','YYYY-MM-DD') ";
        // $query .= "AND LSO_PRDCD = '" . $request->plu1 . "' ";
        $query .= "AND LSO_LOKASI = '" . $request->jenis_barang . "' ";
        $query .= "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR ";
        $query .= "ORDER BY PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_LOKASI  ";
        $data['data'] = DB::select($query);

        $pdf = PDF::loadView('pdf.daftar-master-lokasi-so', $data);
        if ($request->method() === 'GET') {
            return $pdf->stream('DAFTAR MASTER LOKASI SO.pdf');
        }
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);
    }

    //! DONE
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
        $query .= "(ST_SALDOAKHIR * ST_AVGCOST) TOTAL ";
        $query .= "FROM TBMASTER_PRODMAST ";
        $query .= "LEFT JOIN TBMASTER_DIVISI ";
        $query .= "ON DIV_KODEDIVISI = PRD_KODEDIVISI ";
        $query .= "LEFT JOIN TBMASTER_DEPARTEMENT ";
        $query .= "ON DEP_KODEDEPARTEMENT = PRD_KODEDEPARTEMENT ";
        $query .= "LEFT JOIN TBMASTER_KATEGORI ";
        $query .= "ON KAT_KODEDEPARTEMENT = PRD_KODEDEPARTEMENT ";
        $query .= "AND KAT_KODEKATEGORI= PRD_KODEKATEGORIBARANG ";
        $query .= "LEFT JOIN TBMASTER_STOCK ";
        $query .= "ON ST_PRDCD = PRD_PRDCD ";
        $query .= "AND ST_LOKASI = '" . $request->jenis_barang . "' ";
        $query .= "LEFT JOIN ";
        $query .= "(select TBTR_LOKASI_SO.* ";
        $query .= "from( ";
        $query .= "SELECT MAX(MSO_TGLSO) TGL_SO_SETTING ";
        $query .= "FROM tbmaster_Setting_so ";
        $query .= ") C ";
        $query .= "join TBTR_LOKASI_SO on LSO_TGLSO = TGL_SO_SETTING ";
        $query .= ") TBTR_LOKASI_SO ";
        $query .= "ON LSO_PRDCD = ST_PRDCD ";
        $query .= "LEFT JOIN ";
        $query .= "(SELECT MAX(MSO_TGLSO) TGL_SO_SETTING ";
        $query .= "FROM tbmaster_Setting_so) SO_SETTING ";
        $query .= "ON LSO_LOKASI  = ST_LOKASI ";
        $query .= "AND DATE_TRUNC('DAY',LSO_TGLSO) = TO_DATE('" . $request->tanggal_start_so . "','YYYY-MM-DD') ";
        $query .= ") A ";
        $query .= "WHERE LSO_PRDCD = '0000000' ";
        $query .= "AND PRD_KODEDIVISI BETWEEN '" . $request->div1 . "' AND '" . $request->div2 . "' ";
        $query .= "AND PRD_KODEDEPARTEMENT BETWEEN '" . $request->dept1 . "' AND '" . $request->dept2 . "' ";
        $query .= "AND PRD_KODEKATEGORIBARANG BETWEEN '" . $request->kat1 . "' AND '" . $request->kat2 . "' ";
        $query .= "AND PRD_PRDCD LIKE '%0' ";
        $query .= "ORDER BY PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, LSO_PRDCD ";

        //! dummy
        $query .= "LIMIT 100 ";

        $data['data'] = collect(DB::select($query))->groupBy(['div_namadivisi','dep_namadepartement','kat_namakategori']);

        $pdf = PDF::loadView('pdf.daftar-item-tidak-di-master', $data);
        if ($request->method() === 'GET') {
            return $pdf->stream('DAFTAR ITEM BELUM ADA DI MASTER.pdf');
        }
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);
    }

    //! DONE
    public function reportRakBelumSo(ReportLokasiRakBelumDiSoRequest $request){
        $query = '';
        $query .= "SELECT lso_koderak || '.' || lso_kodesubrak || '.' || lso_tiperak || '.' || lso_shelvingrak as lokasi, lso_nourut, ";
        $query .= "CASE WHEN lso_lokasi = '01' THEN 'BAIK' ELSE CASE WHEN lso_lokasi = '02' THEN 'RETUR' ELSE 'RUSAK' END END jenisbrg, ";
        $query .= "lso_prdcd, prd_deskripsipanjang, prd_unit || '/' || prd_frac UNIT, ";
        $query .= "CASE WHEN lso_flagsarana = 'H' THEN 'HandHeld' ELSE 'Kertas' END Sarana, lso_qty ";
        $query .= "FROM tbtr_lokasi_so, tbmaster_prodmast ";
        $query .= "WHERE lso_tglso = TO_DATE('" . $request->tanggal_start_so . "','YYYY-MM-DD') AND lso_modify_by IS NULL ";

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
        $data['data'] = collect(DB::select($query))->groupBy('lokasi')->toArray();

        $pdf = PDF::loadView('pdf.lokasi-rak-belum-di-so', $data);
        if ($request->method() === 'GET') {
            return $pdf->stream('LOKASI RAK BELUM DI SO.pdf');
        }
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);
    }

    public function getPlu(){
        $data = DB::table('tbmaster_prodmast')->select('prd_prdcd','prd_deskripsipanjang')->limit(100)->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function getPluDesc($prd_prdcd){
        $data = DB::table('tbmaster_prodmast')->where('prd_prdcd', $prd_prdcd)->select('prd_deskripsipanjang')->first();

        return ApiFormatter::success(200, 'Data detail berhasil ditampilkan', $data);
    }


    //! DONE
    //? CONTOH DATA -> 2017821
    public function reportInqueryPlanoSonasExcel(ReportInqueryPlanoSonasExcelRequest $request){

        if($request->jenis_barang == null){

            $query = '';
            $query .= "SELECT *, case when lso_lokasi = '01' then '01 - BARANG BAIK' else case when lso_lokasi = '02' then '02 - BARANG RETUR' else '03 - BARANG RUSAK' end end lokasi ";
            $query .= "FROM TBTR_LOKASI_SO, TBMASTER_PRODMAST ";
            $query .= "WHERE LSO_PRDCD = PRD_PRDCD ";
            // $query .= "AND LSO_PRDCD = '" . $request->plu . "' ";
            // $query .= "AND DATE_TRUNC('DAY',LSO_TGLSO) = TO_DATE('" . $request->tanggal_start_so . "', 'YYYY-MM-DD') ";

        }else{
            $query = '';
            $query .= "SELECT *, case when lso_lokasi = '01' then '01 - BARANG BAIK' else case when lso_lokasi = '02' then '02 - BARANG RETUR' else '03 - BARANG RUSAK' end end lokasi ";
            $query .= "FROM TBTR_LOKASI_SO, TBMASTER_PRODMAST ";
            $query .= "WHERE LSO_PRDCD = PRD_PRDCD ";
            // $query .= "AND LSO_LOKASI = '" . $request->jenis_barang . "' ";
            // $query .= "AND LSO_PRDCD = '" . $request->plu . "' ";
            // $query .= "AND DATE_TRUNC('DAY',LSO_TGLSO) = TO_DATE('" . $request->tanggal_start_so . "', 'YYYY-MM-DD') ";
        }

        if(!count(DB::select($query))){
            return ApiFormatter::error(400, 'PLU tidak terdaftar di master lokasi SONAS');
        }

        // $data['data'] = DB::select($query);
        // $data['data1'] = DB::select($query . " AND (LSO_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%' OR LSO_KODERAK LIKE 'L%') AND LSO_LOKASI = '01' ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT ASC limit 10");
        // $data['data2'] = DB::select($query . " AND (LSO_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%' AND LSO_KODERAK NOT LIKE 'L%') AND LSO_LOKASI = '01' ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT ASC limit 10");
        // $data['data3'] = DB::select($query . " AND (LSO_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%' OR LSO_KODERAK LIKE 'L%') AND LSO_LOKASI = '02' ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT ASC limit 10");
        // $data['data4'] = DB::select($query . " AND (LSO_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%' AND LSO_KODERAK NOT LIKE 'L%') AND LSO_LOKASI = '02' ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT ASC limit 10");
        // $data['data5'] = DB::select($query . " AND (LSO_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%' OR LSO_KODERAK LIKE 'L%') AND LSO_LOKASI = '03' ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT ASC limit 10");
        // $data['data6'] = DB::select($query . " AND (LSO_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%' AND LSO_KODERAK NOT LIKE 'L%') AND LSO_LOKASI = '03' ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT ASC limit 10");

        //! dummy
        $data['data'] = DB::select($query);
        $data['data1'] = DB::select($query . " ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT ASC limit 10");
        $data['data2'] = DB::select($query . " ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT ASC limit 10");
        $data['data3'] = DB::select($query . " ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT ASC limit 10");
        $data['data4'] = DB::select($query . " ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT ASC limit 10");
        $data['data5'] = DB::select($query . " ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT ASC limit 10");
        $data['data6'] = DB::select($query . " ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT ASC limit 10");

        $data['request'] = $request->all();

        return Excel::download(new InquiryPlanoExport($data), 'INQUIRY PLANO SONAS.xls');
    }

    public function reportLppMonthEndExcelActionCetak(ReportLppMonthEndExcelActionCetakRequest $request){

        $date = Carbon::parse($request->periode);

        $data['periode'] = $date->format('F Y');

        if($request->jenis_barang == 'B' || $request->jenis_barang == 'A'){
            $query = '';
            $query .= "SELECT LPP_KODEIGR, LPP_TGL1, LPP_TGL2, PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, PRD_PRDCD, PRD_DESKRIPSIPANJANG, PRD_UNIT, PRD_FRAC,  ";
            $query .= "LPP_QTYBEGBAL, LPP_RPHBEGBAL, LPP_QTYBELI, LPP_RPHBELI, LPP_QTYBONUS, LPP_RPHBONUS, LPP_QTYTRMCB, LPP_RPHTRMCB, LPP_QTYRETURSALES, ";
            $query .= "LPP_RPHRETURSALES, LPP_RPHRAFAK, LPP_QTYREPACK, LPP_RPHREPACK, LPP_QTYLAININ, LPP_RPHLAININ, LPP_QTYSALES, LPP_RPHSALES, ";
            $query .= "LPP_QTYKIRIM, LPP_RPHKIRIM, LPP_QTYPREPACKING, LPP_RPHPREPACKING, LPP_QTYHILANG, LPP_RPHHILANG, LPP_QTYLAINOUT, LPP_RPHLAINOUT, ";
            $query .= "LPP_QTYINTRANSIT, LPP_RPHINTRANSIT, LPP_QTYADJ, LPP_RPHADJ, LPP_SOADJ, LPP_QTYAKHIR, LPP_RPHAKHIR, LPP_AVGCOST, LPP_QTY_SELISIH_SO, LPP_RPH_SELISIH_SO, LPP_QTY_SELISIH_SOIC, LPP_RPH_SELISIH_SOIC, ";
            $query .= "coalesce(lpp_rphakhir,0) - (coalesce(lpp_rphbegbal,0) + coalesce(lpp_rphbeli,0) + coalesce(lpp_rphbonus,0) + coalesce(lpp_rphtrmcb,0) + ";
            $query .= "coalesce(lpp_rphretursales,0) + coalesce(lpp_rph_selisih_so, 0) + coalesce(lpp_rphrepack,0) + coalesce(lpp_rphlainin,0) - coalesce(lpp_rphrafak,0) - ";
            $query .= "coalesce(lpp_rphsales,0) - coalesce(lpp_rphkirim,0) - coalesce(lpp_rphprepacking,0) - ";
            $query .= "coalesce(lpp_rphhilang,0) - coalesce(lpp_rphlainout,0) + coalesce(lpp_rphintransit,0) + coalesce(lpp_rphadj, 0) + coalesce(lpp_soadj, 0)) koreksi ";
            $query .= "FROM TBTR_LPP ";
            $query .= "JOIN TBMASTER_PRODMAST ON LPP_PRDCD = PRD_PRDCD AND LPP_KODEIGR = PRD_KODEIGR ";
            //$query .= "WHERE TO_CHAR(LPP_TGL1, 'YYYY-MM') = '" . $request->periode . "' ";
            if($request->all_plu == 1){
                $query .= "";
            }else{
                $string_prdcdc = '';
                foreach($request->plu as $item){
                    $string_prdcdc .=  "'" . $item . "',";
                }

                $string_prdcdc = rtrim($string_prdcdc, ",");

                $query .= "AND LPP_PRDCD in ( " . $string_prdcdc . " ) ";
            }
            $query .= "ORDER BY LPP_PRDCD ";

            //! dummy
            $query .= 'LIMIT 10';
            $data['lpp_baik'] = DB::select($query);
        }

        if($request->jenis_barang == 'T' || $request->jenis_barang == 'A'){
            $query = '';
            $query .= "select LRT_KODEIGR ,LRT_TGL1, LRT_TGL2, PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, PRD_PRDCD, PRD_DESKRIPSIPANJANG, PRD_UNIT, PRD_FRAC, ";
            $query .= "LRT_QTYBEGBAL, LRT_RPHBEGBAL, LRT_QTYBAIK, LRT_RPHBAIK, LRT_QTYRUSAK, LRT_RPHRUSAK, LRT_QTYSUPPLIER, LRT_RPHSUPPLIER, ";
            $query .= "LRT_QTYHILANG, LRT_RPHHILANG, LRT_QTYLBAIK, LRT_RPHLBAIK, LRT_QTYLRUSAK, LRT_RPHLRUSAK, LRT_QTYADJ, LRT_RPHADJ, ";
            $query .= "LRT_SOADJ, LRT_QTYAKHIR, LRT_RPHAKHIR, LRT_AVGCOST1, LRT_AVGCOST, LRT_QTY_SELISIH_SO, LRT_RPH_SELISIH_SO, LRT_QTY_SELISIH_SOIC, LRT_RPH_SELISIH_SOIC, ";
            $query .= "coalesce(lrt_rphakhir,0) - (coalesce(lrt_rphbegbal,0) + coalesce(lrt_rphbaik,0) + coalesce(lrt_rphrusak,0) + coalesce(lrt_rphadj,0) + coalesce(lrt_rph_selisih_so, 0) ";
            $query .= "+ coalesce(lrt_soadj, 0) - coalesce(lrt_rphsupplier,0) - coalesce(lrt_rphhilang,0) - coalesce(lrt_rphlbaik,0) - coalesce(lrt_rphlrusak,0)) koreksi ";
            $query .= "FROM TBTR_LPPRT ";
            $query .= "JOIN TBMASTER_PRODMAST ON LRT_PRDCD = PRD_PRDCD AND LRT_KODEIGR = PRD_KODEIGR ";
            // $query .= "WHERE TO_CHAR(LRT_TGL1, 'YYYY-MM') = '" . $request->periode . "' ";
            if($request->all_plu == 1){
                $query .= "";
            }else{
                $string_prdcdc = '';
                foreach($request->plu as $item){
                    $string_prdcdc .=  "'" . $item . "',";
                }

                $string_prdcdc = rtrim($string_prdcdc, ",");

                $query .= "AND LRT_PRDCD in ( " . $string_prdcdc . " ) ";
            }
            $query .= "ORDER BY LRT_PRDCD ";
            //! dummy
            $query .= 'LIMIT 10';
            $data['lpp_retur'] = DB::select($query);
        }

        if($request->jenis_barang == 'R' || $request->jenis_barang == 'A'){
            $query = '';
            $query .= "select LRS_KODEIGR, LRS_TGL1, LRS_TGL2, PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, PRD_PRDCD, PRD_DESKRIPSIPANJANG, PRD_UNIT, PRD_FRAC, ";
            $query .= "LRS_QTYBEGBAL, LRS_RPHBEGBAL, LRS_QTYBAIK, LRS_RPHBAIK, LRS_QTYRETUR, LRS_RPHRETUR, LRS_QTYMUSNAH, LRS_RPHMUSNAH, ";
            $query .= "LRS_QTYHILANG, LRS_RPHHILANG, LRS_QTYLBAIK, LRS_RPHLBAIK, LRS_QTYLRETUR, LRS_RPHLRETUR, LRS_QTYADJ, LRS_RPHADJ, ";
            $query .= "LRS_SOADJ, LRS_QTYAKHIR, LRS_RPHAKHIR, LRS_AVGCOST1, LRS_AVGCOST, LRS_QTY_SELISIH_SO, LRS_RPH_SELISIH_SO, LRS_QTY_SELISIH_SOIC, LRS_RPH_SELISIH_SOIC, ";
            $query .= "coalesce(lrs_rphakhir,0) - (coalesce(lrs_rphbegbal,0) + coalesce(lrs_rphbaik,0) + coalesce(lrs_rphretur,0) + coalesce(lrs_rphadj,0) + coalesce(lrs_rph_selisih_so, 0) ";
            $query .= "+ coalesce(lrs_soadj, 0) - coalesce(lrs_rphmusnah,0) - coalesce(lrs_rphhilang,0) - coalesce(lrs_rphlbaik,0) - coalesce(lrs_rphlretur,0)) koreksi ";
            $query .= "FROM TBTR_LPPRS ";
            $query .= "JOIN TBMASTER_PRODMAST ON LRS_PRDCD = PRD_PRDCD AND LRS_KODEIGR = PRD_KODEIGR ";
            // $query .= "WHERE TO_CHAR(LRS_TGL1, 'YYYY-MM') = '" . $request->periode . "' ";
            if($request->all_plu == 1){
                $query .= "";
            }else{

                $string_prdcdc = '';
                foreach($request->plu as $item){
                    $string_prdcdc .=  "'" . $item . "',";
                }

                $string_prdcdc = rtrim($string_prdcdc, ",");

                $query .= "AND LRS_PRDCD in ( " . $string_prdcdc . " ) ";
            }
            $query .= "ORDER BY LRS_PRDCD ";
            //! dummy
            $query .= 'LIMIT 10';
            $data['lpp_rusak'] = DB::select($query);
        }

        $txtJenisBarang = 'ALL';
        if($request->jenis_barang == 'B'){
            $txtJenisBarang = 'BAIK';
        }elseif($request->jenis_barang == 'T'){
            $txtJenisBarang = 'RETUR';
        }elseif($request->jenis_barang == 'R'){
            $txtJenisBarang = 'RUSAK';
        }

        // Generate Excel file content
        $fileContent = Excel::raw(new LppMonthEndExport($data), \Maatwebsite\Excel\Excel::XLSX);

        $excelFileName = "LPP_MONTH_END - $txtJenisBarang.xlsx"; // Set your desired filename here
        $encodedFileName = rawurlencode($excelFileName);

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $encodedFileName . '"',
        ];


        // Return Excel file content as response=
        return response($fileContent, 200, $headers);
    }

    public function reportLppMonthEndExcelDatatables(){
        $data = DB::table('tbmaster_plu_sonas')
            ->orderBy('prd_prdcd')
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    //! new bikin action baru aja
    public function reportLppMonthEndExcelActionResetData(){
        //* Yakin akan membuat Data PLU Sonas Baru ?

        //! Delete TBMASTER_PLU_SONAS
        DB::table('tbmaster_plu_sonas')->truncate();
    }

    public function reportLppMonthEndExcelActionSimpanDataPlu(Request $request){
        DB::table('tbmaster_plu_sonas')
            ->insert([
                'prd_kodeigr' => session('KODECABANG'),
                'prd_prdcd' => $request->prd_prdcd,
                'prd_deskripsipanjang' => $request->prd_deskripsipanjang,
                'prd_create_by' => session('userid'),
                'prd_create_dt' => Carbon::now(),
            ]);
    }

    public function reportCetakDraftLhso(reportCetakDraftLhsoRequest $request){

        //! GET NAMA PERUSAHAAN
        $data['perusahaan'] = DB::table('tbmaster_perusahaan')
            ->select('prs_kodeigr as kode_igr', 'prs_namacabang')
            ->first();

        $dtCek = DB::table('tbmaster_setting_so')
            ->whereNull('mso_flagreset')
            ->get();

        if(count($dtCek) == 0){
            return ApiFormatter::error(400, '-Draf Lhso Tahap ' . $request->tahap . '  belum di Proses-');
        }

        $FlagTahap = $dtCek[0]->mso_flagtahap;
        $request->merge(['TglSO' => $dtCek[0]->mso_tglso]);

        // if($request->tahap != 1){

        //     if($FlagTahap != $request->tahap){
        //         return ApiFormatter::error(400, 'Saat ini Proses Tahap ke ' . $request->tahap . ' !');
        //     }

        // }

        if(isset($request->tahap)){
            $request->merge(['tahap' => str_pad($request->tahap, 2, "0", STR_PAD_LEFT)]);
        }

        if($request->type == 'draft_lhso'){
            $data['data'] = $this->loadReport($request);
        }else{
            $data['data'] = $this->LoadReport2($request);
        }

        $data['request'] = $request->all();

        $type = 'BAIK';
        if($request->jenis_barang == '02'){
            $type = 'RETUR';
        }elseif($request->jenis_barang == '03'){
            $type = 'RUSAK';
        }

        $pdf = PDF::loadView('pdf.cetak-draft-lhso', $data);
        $customPaper = array(0, 0, 795, 620);
        $pdf->setPaper($customPaper);
        if ($request->method() === 'GET') {
            return $pdf->stream('DRAFT LHSO - '.$type.'.pdf');
        }
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);
    }

    private function loadReport($request){

        $query = '';
        $query .= "SELECT * FROM (";
        $query .= "SELECT DISTINCT PLU, DESKRIPSI, AREAGUDANG, AREATOKO, (AREAGUDANG + AREATOKO) AS TOTAL, LPP, ((AREAGUDANG + AREATOKO  ) - LPP) AS SELISIH, ";
        $query .= "(((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) AS NILAI_SELISIH, LSO_FLAGTAHAP, LSO_CREATE_BY, ";
        $query .= "PRD_KODEDIVISI || '-' || div_namadivisi as PRD_KODEDIVISI, PRD_KODEDEPARTEMENT || '-' || dep_namadepartement as PRD_KODEDEPARTEMENT , PRD_KODEKATEGORIBARANG || '-' || kat_namakategori as PRD_KODEKATEGORIBARANG ";
        $query .= "FROM (SELECT PRD_AVGCOST, PRD_PRDCD AS PLU, PRD_DESKRIPSIPANJANG AS DESKRIPSI,  ";

        // $query .= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" . $request->tahap . "' AND LSO_TGLSO = TO_DATE('" . $request->TglSO . "','YYYY-MM-DD') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%') AND LSO_LOKASI = '" . $request->jenis_barang . "') AS AREAGUDANG, ";
        // $query .= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" . $request->tahap . "' AND LSO_TGLSO = TO_DATE('" . $request->TglSO . "','YYYY-MM-DD') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%') AND LSO_LOKASI = '" . $request->jenis_barang . "') AS AREATOKO,  ";
        //! dummy
        $query .= "0 AS AREAGUDANG, ";
        $query .= "0 AS AREATOKO,  ";

        $query .= "(LSO_ST_SALDOAKHIR) AS LPP, LSO_FLAGTAHAP, LSO_CREATE_BY, LSO_AVGCOST, ";
        $query .= "PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, ";
        $query .= "(select div_namadivisi from tbmaster_divisi where div_kodedivisi = PRD_KODEDIVISI) as div_namadivisi, ";
        $query .= "(select dep_namadepartement from tbmaster_departement where dep_kodedepartement = PRD_KODEDEPARTEMENT and dep_kodedivisi = PRD_KODEDIVISI) as dep_namadepartement, ";
        $query .= "(select kat_namakategori from tbmaster_kategori where kat_kodekategori = PRD_KODEKATEGORIBARANG and kat_kodedepartement = PRD_KODEDEPARTEMENT ) as kat_namakategori ";
        $query .= "FROM TBMASTER_PRODMAST, tbhistory_lhso_sonas, ";
        $query .= "tbmaster_divisi, tbmaster_departement, tbmaster_kategori ";
        $query .= "WHERE LSO_PRDCD = PRD_PRDCD ";
        // $query .= "AND LSO_TGLSO = TO_DATE('" . $request->TglSO . "','YYYY-MM-DD') ";
        $query .= "AND PRD_KODEDIVISI = div_kodedivisi ";
        $query .= "AND PRD_KODEDEPARTEMENT = dep_kodedepartement ";
        $query .= "AND PRD_KODEKATEGORIBARANG = kat_kodekategori ";
        $query .= "AND div_kodedivisi = dep_kodedivisi ";
        $query .= "AND dep_kodedepartement = kat_kodedepartement ";
        // $query .= "AND LSO_FLAGTAHAP = '" . $request->tahap . "' ";

        if(!isset($request->div1)){
            $query .= "";
        }elseif(!isset($request->dept1)){
            $query .= "AND PRD_KODEDIVISI BETWEEN '" . $request->div1 . "' and '" . $request->div2 . "'  ";
        }elseif(!isset($request->kat1)){

            $query .= "AND PRD_KODEDIVISI BETWEEN '" . $request->div1 . "' and '" . $request->div2 . "'  ";
            $query .= "AND PRD_KODEDEPARTEMENT BETWEEN '" . $request->dept1 . "' and '" . $request->dept2 . "' ";
        }else{
            $query .= "AND PRD_KODEDIVISI BETWEEN '" . $request->div1 . "' and '" . $request->div2 . "'  ";
            $query .= "AND PRD_KODEDEPARTEMENT BETWEEN '" . $request->dept1 . "' and '" . $request->dept2 . "' ";
            $query .= "AND PRD_KODEKATEGORIBARANG BETWEEN  '" . $request->kat1 . "' and '" . $request->kat2 . "' ";
        }

        if(!isset($request->plu)){
            $query .= "";
        }else{
            $query .= "AND LSO_PRDCD BETWEEN '" . $request->plu1 . "' and '" . $request->plu2 . "' ";
        }

        $query .= "AND LSO_LOKASI = '" . $request->jenis_barang . "' ";
        $query .= ") q ";
        // $query .= "WHERE (((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) <> 0 ";
        $query .= ") p ORDER BY ABS(NILAI_SELISIH) DESC ";
        if(isset($request->limit)){
            $query .= "LIMIT " . $request->limit . " ";
        }
        $data = collect(DB::select($query))->groupBy(['prd_kodedivisi','prd_kodedepartement','prd_kodekategoribarang']);

        return $data;
    }

    private function LoadReport2($request){

        $query = '';
        $query .= "SELECT * FROM (";
        $query .= "SELECT DISTINCT PLU, DESKRIPSI, AREAGUDANG, AREATOKO, (AREAGUDANG + AREATOKO) AS TOTAL, LPP, ((AREAGUDANG + AREATOKO  ) - LPP) AS SELISIH, ";
        $query .= "(((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) AS NILAI_SELISIH, LSO_FLAGTAHAP, LSO_CREATE_BY, ";
        $query .= "PRD_KODEDIVISI || '-' || div_namadivisi as PRD_KODEDIVISI, PRD_KODEDEPARTEMENT || '-' || dep_namadepartement as PRD_KODEDEPARTEMENT , PRD_KODEKATEGORIBARANG || '-' || kat_namakategori as PRD_KODEKATEGORIBARANG ";
        $query .= "FROM (SELECT PRD_AVGCOST, PRD_PRDCD AS PLU, PRD_DESKRIPSIPANJANG AS DESKRIPSI,  ";
        $query .= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" . $request->tahap . "' AND LSO_TGLSO = TO_DATE('" . $request->TglSO . "','YYYY-MM-DD') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%') AND LSO_LOKASI = '" . $request->jenisbrg . "') AS AREAGUDANG, ";
        $query .= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" . $request->tahap . "' AND LSO_TGLSO = TO_DATE('" . $request->TglSO . "','YYYY-MM-DD') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%') AND LSO_LOKASI = '" . $request->jenisbrg . "') AS AREATOKO,  ";
        $query .= "(LSO_ST_SALDOAKHIR) AS LPP, LSO_FLAGTAHAP, LSO_CREATE_BY, LSO_AVGCOST, ";
        $query .= "PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, ";
        $query .= "(select div_namadivisi from tbmaster_divisi where div_kodedivisi = PRD_KODEDIVISI) as div_namadivisi, ";
        $query .= "(select dep_namadepartement from tbmaster_departement where dep_kodedepartement = PRD_KODEDEPARTEMENT and dep_kodedivisi = PRD_KODEDIVISI) as dep_namadepartement, ";
        $query .= "(select kat_namakategori from tbmaster_kategori where kat_kodekategori = PRD_KODEKATEGORIBARANG and kat_kodedepartement = PRD_KODEDEPARTEMENT ) as kat_namakategori ";
        $query .= "FROM TBMASTER_PRODMAST, tbhistory_lhso_sonas, ";
        $query .= "tbmaster_divisi, tbmaster_departement, tbmaster_kategori ";
        $query .= "WHERE LSO_TGLSO = TO_DATE('" . $request->TglSO . "','YYYY-MM-DD') ";
        $query .= "AND LSO_PRDCD = PRD_PRDCD ";
        $query .= "AND PRD_KODEDIVISI = div_kodedivisi ";
        $query .= "AND PRD_KODEDEPARTEMENT = dep_kodedepartement ";
        $query .= "AND PRD_KODEKATEGORIBARANG = kat_kodekategori ";
        $query .= "AND div_kodedivisi = dep_kodedivisi ";
        $query .= "AND dep_kodedepartement = kat_kodedepartement ";
        $query .= "AND LSO_FLAGTAHAP = '" . $request->tahap . "' ";
        if(!isset($request->div1)){
            $query .= "";
        }elseif(!isset($request->dept1)){
            $query .= "AND PRD_KODEDIVISI BETWEEN '" . $request->div1 . "' and '" . $request->div2 . "'  ";
        }elseif(!isset($request->kat1)){
            $query .= "AND PRD_KODEDIVISI BETWEEN '" . $request->div1 . "' and '" . $request->div2 . "'  ";
            $query .= "AND PRD_KODEDEPARTEMENT BETWEEN '" . $request->dept1 . "' and '" . $request->dept2 . "' ";
        }else{
            $query .= "AND PRD_KODEDIVISI BETWEEN '" . $request->div1 . "' and '" . $request->div2 . "'  ";
            $query .= "AND PRD_KODEDEPARTEMENT BETWEEN '" . $request->dept1 . "' and '" . $request->dept2 . "' ";
            $query .= "AND PRD_KODEKATEGORIBARANG BETWEEN  '" . $request->kat1 . "' and '" . $request->kat2 . "' ";
        }

        if(!isset($request->plu)){
            $query .= "";
        }else{
            $query .= "AND LSO_PRDCD BETWEEN '" . $request->plu1 . "' and '" . $request->plu2 . "' ";
        }

        $query .= "AND LSO_LOKASI = '" . $request->jenisbrg . "' ";
        $query .= ") q ";
        $query .= "WHERE (((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) <> 0 ";
        $query .= ") p ORDER BY ABS(NILAI_SELISIH) DESC limit " . $request->limit . "";
        $data = collect(DB::select($query))->groupBy(['prd_kodedivisi','prd_kodedepartement','prd_kodekategoribarang']);

        return $data;
    }

    public function reportCetakDraftReturSebelumLhso(ReportCetakDraftReturSebelumLhsoRequest $request){

        //! GET NAMA PERUSAHAAN
        $data['perusahaan'] = DB::table('tbmaster_perusahaan')
            ->select('prs_kodeigr as kode_igr', 'prs_namacabang')
            ->first();

        $dtCek = DB::table('tbmaster_setting_so')
            ->whereNull('mso_flagreset')
            ->get();

            if(count($dtCek) == 0){
            return ApiFormatter::error(400, '-Draf Lhso belum di Proses- ');
        }

        $request->merge(['tahap' => $dtCek[0]->mso_flagtahap]);
        $request->merge(['tanggal_start_so' => $dtCek[0]->mso_tglso]);
        $TglSO = $dtCek[0]->mso_tglso;

        $query = '';
        $query .= "SELECT * FROM (";
        $query .= "SELECT DISTINCT PLU, DESKRIPSI, AREAGUDANG, AREATOKO, (AREAGUDANG + AREATOKO) AS TOTAL, LPP, ((AREAGUDANG + AREATOKO  ) - LPP) AS SELISIH, ";
        $query .= "(((AREAGUDANG + AREATOKO ) - LPP ) * ACOST) AS NILAI_SELISIH,  LSO_CREATE_BY, ";
        $query .= "PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, div_namadivisi, dep_namadepartement, kat_namakategori ";
        $query .= "FROM (SELECT PRD_AVGCOST, PRD_PRDCD AS PLU, PRD_DESKRIPSIPANJANG AS DESKRIPSI,  ";

        // $query .= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM TBTR_LOKASI_SO WHERE   LSO_TGLSO = TO_DATE('" . $TglSO . "','YYYY-MM-DD') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%') AND LSO_LOKASI = '02') AS AREAGUDANG, ";
        // $query .= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM TBTR_LOKASI_SO WHERE   LSO_TGLSO = TO_DATE('" . $TglSO . "','YYYY-MM-DD') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%') AND LSO_LOKASI = '02') AS AREATOKO,  ";
        //! dummy
        $query .= "0 AS AREAGUDANG, ";
        $query .= "0 AS AREATOKO,  ";

        $query .= "(SELECT (case when prd_unit='KG' then st_avgcost/1000  else st_avgcost  end) ST_AVGCOST FROM TBMASTER_STOCK, TBMASTER_PRODMAST WHERE ST_PRDCD = PRD_PRDCD AND ST_LOKASI = LSO_LOKASI AND ST_PRDCD = LSO_PRDCD and ST_AVGCOST IS NOT NULL LIMIT 1) AS ACOST, ";
        $query .= "(SELECT coalesce (ST_SALDOAKHIR, 0) FROM TBMASTER_STOCK WHERE ST_LOKASI = LSO_LOKASI AND ST_PRDCD = LSO_PRDCD and ST_SALDOAKHIR IS NOT NULL LIMIT 1 ) AS LPP, LSO_CREATE_BY,  ";
        $query .= "PRD_KODEDIVISI, PRD_KODEDEPARTEMENT, PRD_KODEKATEGORIBARANG, ";
        $query .= "(select div_namadivisi from tbmaster_divisi where div_kodedivisi = PRD_KODEDIVISI) as div_namadivisi, ";
        $query .= "(select dep_namadepartement from tbmaster_departement where dep_kodedepartement = PRD_KODEDEPARTEMENT and dep_kodedivisi = PRD_KODEDIVISI) as dep_namadepartement, ";
        $query .= "(select kat_namakategori from tbmaster_kategori where kat_kodekategori = PRD_KODEKATEGORIBARANG and kat_kodedepartement = PRD_KODEDEPARTEMENT ) as kat_namakategori ";
        $query .= "FROM TBMASTER_PRODMAST, TBTR_LOKASI_SO, ";
        $query .= "tbmaster_divisi, tbmaster_departement, tbmaster_kategori ";
        $query .= "WHERE LSO_PRDCD = PRD_PRDCD ";
        // $query .= "AND LSO_TGLSO = TO_DATE('" . $TglSO . "','YYYY-MM-DD') ";
        $query .= "AND PRD_KODEDIVISI = div_kodedivisi ";
        $query .= "AND PRD_KODEDEPARTEMENT = dep_kodedepartement ";
        $query .= "AND PRD_KODEKATEGORIBARANG = kat_kodekategori ";
        $query .= "AND div_kodedivisi = dep_kodedivisi ";
        $query .= "AND dep_kodedepartement = kat_kodedepartement ";
        if(!isset($request->div1)){
            $query .= "";
        }elseif(!isset($request->dept1)){
            $query .= "AND PRD_KODEDIVISI BETWEEN '" . $request->div1 . "' and '" . $request->div2 . "'  ";
        }elseif(!isset($request->kat1)){
            $query .= "AND PRD_KODEDIVISI BETWEEN '" . $request->div1 . "' and '" . $request->div2 . "'  ";
            $query .= "AND PRD_KODEDEPARTEMENT BETWEEN '" . $request->dept1 . "' and '" . $request->dept2 . "' ";
        }else{
            $query .= "AND PRD_KODEDIVISI BETWEEN '" . $request->div1 . "' and '" . $request->div2 . "'  ";
            $query .= "AND PRD_KODEDEPARTEMENT BETWEEN '" . $request->dept1 . "' and '" . $request->dept2 . "' ";
            $query .= "AND PRD_KODEKATEGORIBARANG BETWEEN  '" . $request->kat1 . "' and '" . $request->kat2 . "' ";
        }

        if(!isset($request->plu)){
            $query .= "";
        }else{
            $query .= "AND LSO_PRDCD BETWEEN '" . $request->plu1 . "' and '" . $request->plu2 . "' ";
        }
        $query .= "AND LSO_LOKASI = '02' ) t ";
        // $query .= "WHERE (((AREAGUDANG + AREATOKO ) - LPP ) * ACOST) <> 0 ";
        $query .= ") p ORDER BY ABS(NILAI_SELISIH) DESC ";
        //! dummy
        $query .= 'LIMIT 10';
        $data['data'] = DB::select($query);

        switch ($request->jenis_barang) {
            case '01':
                $textJenisBarang = 'BAIK';
                break;
            case '02':
                $textJenisBarang = 'RETUR';
                break;
            default:
                $textJenisBarang = 'RUSAK';
                break;
        }

        $request->merge(['textJenisBarang' => $textJenisBarang]);

        $data['request'] = $request->all();

        $pdf = PDF::loadView('pdf.cetak-draft-sebelum-lhso', $data);
        $customPaper = array(0, 0, 795, 620);
        $pdf->setPaper($customPaper);
        if ($request->method() === 'GET') {
            return $pdf->stream('LAPORAN HASIL STOCKNAME IGR - CETAK DRAFT SEBELUM LHSO.pdf');
        }
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);
    }

    //! DONE
    public function reportLokasiSo(ReportLokasiSoRequest $request){

        //! GET NAMA PERUSAHAAN
        $data['perusahaan'] = DB::table('tbmaster_perusahaan')
            ->select('prs_kodeigr as kode_igr', 'prs_namacabang')
            ->first();

        $query = '';
        $query .= "SELECT lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lso_nourut, lso_lokasi, lso_qty, ";
        $query .= "lso_prdcd, prd_deskripsipanjang, lso_tglso, lso_flagsarana, prd_kodedivisi, prd_prdcd, prd_kodedepartement, prd_kodekategoribarang, FLOOR (LSO_QTY / PRD_FRAC) CTN, MOD (LSO_QTY, PRD_FRAC) PCS  ";
        $query .= "FROM tbtr_lokasi_so, tbmaster_prodmast ";
        $query .= "WHERE lso_prdcd = prd_prdcd ";
        if(isset($request->raksubrak)){
            $koderak = explode('.', $request->raksubrak)[0];
            $kodesubrak = explode('.', $request->raksubrak)[1];
            $query .= "AND lso_koderak = '" . $koderak . "' ";
            $query .= "AND lso_kodesubrak= '" . $kodesubrak . "' ";
        }else{
            $query .= "";
        }
        // $query .= "AND lso_flagsarana= '" . $request->sarana . "' ";
        $query .= "AND lso_flaglimit= 'Y' ";
        // $query .= "AND DATE_trunc('DAY',LSO_TGLSO) >= TO_DATE('" . $request->tanggal_start_so . "','YYYY-MM-DD') ";
        $query .= "ORDER BY lso_koderak, lso_kodesubrak, lso_tiperak, lso_shelvingrak, lso_nourut ";
        $data['data'] = collect(DB::select($query))->groupBy(['lso_prdcd'])->slice(0, 10); //! DUMMY SLICE NYA

        $pdf = PDF::loadView('pdf.lokasi-so', $data);
        if ($request->method() === 'GET') {
            return $pdf->stream('LOKASI SO.pdf');
        }
        $pdfContent = $pdf->output();
        return response()->json(['pdf' => base64_encode($pdfContent)]);

    }
}
