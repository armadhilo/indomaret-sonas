<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\ProsesBaSoRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ResetSoController extends Controller
{

    private $FlagTahap;
    public function __construct(Request $request){
        DatabaseConnection::setConnection(session('KODECABANG'), "PRODUCTION");
    }

    public function index(){

        $dtCek = DB::table('tbmaster_setting_so')
            ->whereNull('mso_flagreset')
            ->get();

        if(count($dtCek) == 0){
            $data['btnResetText'] = 'SO BELUM DI-INITIAL';
            $data['btnResetEnabled'] = false;
            $data['btnReprintEnable'] = true;
        }else{
            if($dtCek[0]->mso_flagsum == ''){
                $data['btnResetText'] = 'SO BELUM DI PROSES BA';
                $data['btnResetEnabled'] = false;
                $data['btnReprintEnable'] = false;
                $data['check_error'] = "SO belum di Proses BA";
                return view('reset-so', $data);
            }else{
                $data['tgl_so'] = $dtCek[0]->mso_tglso;

                $data['btnResetText'] = 'RESET SO ' . Carbon::parse($data['tgl_so'])->format('d/m/Y');
                $data['btnResetEnabled'] = true;
                $data['btnReprintEnable'] = false;
            }
        }

        return view('reset-so', $data);
    }

    public function actionReset(){
        //* Apakah anda yakin melakukan Reset Stock Opname?


        if(session('userlevel') != 1){
            return ApiFormatter::error(400, 'Anda tidak berhak menjalankan menu ini');
        }

        try{

            $kodeigr = session('KODECABANG');
            $userid = session('userid');
            $procedure = DB::select("call sp_reset_so('$kodeigr','$userid', NULL)");
            $procedure = $procedure[0]->sukses;

            if($procedure == 'Sukses Reset SO!'){
                return ApiFormatter::success(200, $procedure);
            }

            return ApiFormatter::error(400, $procedure);
        }

        catch(\Exception $e){

            DB::rollBack();

            $message = "Oops! Something wrong ( $e )";
            return ApiFormatter::error(400, $message);
        }


    }

    public function actionCheckReprint(){
        $dtCek = DB::table('tbmaster_setting_so')
            ->select('mso_tglso')
            ->whereNotNull('mso_flagreset')
            ->orderBy('mso_tglso','desc')
            ->get();

        if(count($dtCek) == 0){
            return ApiFormatter::error(400, 'Data Reset SO terakhir tidak ditemukan');
        }

        return ApiFormatter::success(200, "success");
    }
    
    public function actionReprint(){
        // dt = QueryOra("SELECT * FROM TBMASTER_SETTING_SO WHERE MSO_MODIFY_DT in (select max(MSO_MODIFY_DT) from TBMASTER_SETTING_SO)")
        // TglSO = Format(dt.Rows(0).Item("mso_tglso"), "dd-MM-yyyy").ToString
        // tglreset = Format(dt.Rows(0).Item("MSO_CREATE_DT"), "dd-MM-yyyy").ToString

        $dt = DB::select("SELECT * FROM TBMASTER_SETTING_SO WHERE MSO_MODIFY_DT in (select max(MSO_MODIFY_DT) from TBMASTER_SETTING_SO)");
        $TglSO = Carbon::parse($dt[0]->mso_tglso)->format('Y-m-d');
        $TglReset = $dt[0]->mso_create_dt;

        $query = '';
        $query .= "select sop_lokasi, sum(total)  as Total ";
        $query .= " FROM ( ";
        $query .= "      select ";
        $query .= "      sop_prdcd, ";
        $query .= "      sop_lokasi, ";
        $query .= "      sop_qtyso + coalesce(qty_adj, 0) - sop_qtylpp as qty, ";
        $query .= "      case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end as acost, ";
        $query .= "      sop_qtyso + coalesce(qty_adj, 0) - sop_qtylpp * case when prd_unit = 'KG' then sop_newavgcost / 1000 else sop_newavgcost end as total ";
        $query .= "    FROM ";
        $query .= "    ( ";
        $query .= "      SELECT ";
        $query .= "      sop_prdcd,sop_lokasi, ";
        $query .= "      sop_qtyso,sop_qtylpp, ";
        $query .= "      sop_newavgcost, ";
        $query .= "      sop_kodeigr, ";
        $query .= "      sop_tglso ";
        $query .= "     FROM tbtr_ba_stockopname ";
        $query .= "    ) as p";
        $query .= "    LEFT JOIN ";
        $query .= "    (";
        $query .= "     SELECT adj_kodeigr, adj_tglso, adj_prdcd, adj_lokasi, ";
        $query .= "     SUM(coalesce(adj_qty, 0)) qty_adj ";
        $query .= "     FROM tbtr_adjustso ";
        $query .= "     GROUP BY adj_kodeigr, adj_tglso, adj_prdcd, adj_lokasi ";
        $query .= "     ) as q ON ";
        $query .= "         sop_kodeigr = adj_kodeigr ";
        $query .= "         AND sop_tglso = adj_tglso ";
        $query .= "         AND sop_prdcd = adj_prdcd ";
        $query .= "         AND sop_lokasi = adj_lokasi ";
        $query .= "         AND DATE_TRUNC('DAY',lso_tglso) = '" . $TglSO . "' ";
        $query .= "     LEFT JOIN ";
        $query .= "     (select prd_unit, prd_prdcd ";
        $query .= "      from tbmaster_prodmast ";
        $query .= "      ) as x ON p.sop_prdcd = x.prd_prdcd";
        $query .= ") as datas ";
        $query .= "group by sop_lokasi";

        $brgBaik = $dt[0]->Total;
        if(count($dt) < 3){
            $brgRetur = 0;
            $brgRusak = 0;
        }else{
            $brgRetur = $dt[1]->total;
            $brgRusak = $dt[2]->total;
        }

        $data['perusahaan'] = DB::table('tbmaster_perusahaan')->select('prs_namaperusahaan', 'prs_namacabang')->first();
        $data['TglSO'] = $TglSO;
        $data['TglReset'] = $TglReset;
        $data['brgBaik'] = $brgBaik;
        $data['brgRetur'] = $brgRetur;
        $data['brgRusak'] = $brgRusak;
        $data['total'] = (int)$brgBaik + (int)$brgRetur + (int)$brgRusak;

        //     oRpt.SetParameterValue("tgl_SO", TglSO)
        //     oRpt.SetParameterValue("Tgl_ref", tglreset)
        //     oRpt.SetParameterValue("Brg_Baik", brgbaik)
        //     oRpt.SetParameterValue("Brg_Retur", brgretur)
        //     oRpt.SetParameterValue("Brg_Rusak", brgrusak)
        //     oRpt.SetParameterValue("Total", Total)
        $pdf = PDF::loadView('pdf.reset-so', $data);
        return $pdf->stream();
    }
}
