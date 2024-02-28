<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\ProsesBaSoRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProsesBaSoController extends Controller
{

    private $FlagTahap;
    public function __construct(Request $request){
        DatabaseConnection::setConnection(session('KODECABANG'), "PRODUCTION");
    }

    public function index(){

        $dtCek = DB::table('tbmaster_setting_so')
        ->whereNull('mso_flagreset')
        ->get();
        $data['tgl_so'] = Carbon::parse($dtCek[0]->mso_tglso)->format('Y-m-d');

        if(count($dtCek)){
            $check_error = "SO belum diinitial";
            return view('proses-ba-so', compact('check_error'));
        }

        if($dtCek[0]->mso_flagtahap == ''){
            $check_error = "Belum Copy Master Lokasi ke lokasi SOZ";
            return view('proses-ba-so', compact('check_error'));
        }

        //? membawa isi $data query dibawah ini
        if($dtCek[0]->mso_flagsum == ''){
            $data['BtnDraftLHSO'] = True;
            $data['BtnProsesBASO'] = True;
            $data['BtnProsesBASOText'] = 'START PROSES BA SO';

            $this->FlagTahap = $dtCek[0]->mso_flagtahap;
            $data['FlagTahap'] = True;
            $data['BtnDraftLHSOText'] = "PROSES DRAFT LHSO TAHAP " . (int)$this->FlagTahap + 1;

            if($this->FlagTahap < 1){
                $data['BtnProsesBASO'] = True;
            }else{
                $data['BtnProsesBASO'] = False;
            }
        }else{
            $check_error = "SO sudah di Proses BA";
            return view('proses-ba-so', compact('check_error'));
        }

        return view('proses-ba-so', $data);
    }

    public function action(ProsesBaSoRequest $request){
        if($this->FlagTahap > 0){
            $dtCek = DB::table('tbtr_lokasi_so')
                ->where(DB::raw("DATE_TRUNC('DAY',lso_tglso)"),'>=', Carbon::parse($request->tanggal_start_so)->format('Y-m-d'))
                ->whereRaw("coalesce(lso_flaglimit, 'N') = 'Y'")
                ->count();

            if($dtCek == 0){
                return ApiFormatter::error(400, 'Belum setting limit item SO pada tahap ini');
            }
        }

        $dtCek = DB::table('tbtr_lokasi_so')
            ->where(DB::raw("DATE_TRUNC('DAY',lso_tglso)"),'>=', Carbon::parse($request->tanggal_start_so)->format('Y-m-d'))
            ->whereNull('lso_modify_by')
            ->whereRaw("coalesce(lso_flaglimit, 'N') = 'Y'")
            ->count();

        if($dtCek > 0){
            return ApiFormatter::error(400, 'Ada Lokasi yang belum di SO pada tahap ini');
        }

        $dtCek = DB::table('tbmaster_setting_so')
            ->where(DB::raw("DATE_TRUNC('DAY',mso_tglso)"),'>=', Carbon::parse($request->tanggal_start_so)->format('Y-m-d'))
            ->get();

        if(count($dtCek) > 0){
            if($dtCek[0]->mso_flag_createlso == 'Y'){
                return ApiFormatter::error(400, 'Lokasi Plano belum dicopy ke Lokasi SO');
            }
        }

        if(session('userlevel') != 1){
            return ApiFormatter::error(400, 'Anda tidak berhak menjalankan menu ini');
        }

        // Dim Tahap As String = (FlagTahap + 1).ToString.PadLeft(2, "0")
        $tahap = str_pad($this->FlagTahap + 1,2,"0",STR_PAD_LEFT);

        // str = "UPDATE TBMASTER_SETTING_SO SET MSO_FLAGTAHAP = '" & Tahap & "', MSO_FLAGTRFLOKASI = 'Y', MSO_FLAGLIMIT = NULL "
        // str &= "WHERE DATE_TRUNC('DAY',MSO_TGLSO) >= TO_DATE('" & Format(TglSO, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY')"

        DB::beginTransaction();
	    try{

            DB::table('tbmaster_setting_so')
                ->whereRaw("DATE_TRUNC('DAY',mso_tglso) >= '" . Carbon::parse($request->tanggal_start_so)->format('Y-m-d') ."'")
                ->update([
                    'mso_flagtahap' => $tahap,
                    'mso_flagtrflokasi' => 'Y',
                    'mso_flaglimit' => NULL
                ]);

            $query = '';
            $query .= "INSERT INTO TBHISTORY_LHSO_SONAS ";
            $query .= "(LSO_KODEIGR, ";
            $query .= "LSO_TGLSO, ";
            $query .= "LSO_KODERAK, ";
            $query .= "LSO_KODESUBRAK, ";
            $query .= "LSO_TIPERAK, ";
            $query .= "LSO_SHELVINGRAK, ";
            $query .= "LSO_NOURUT, ";
            $query .= "LSO_PRDCD, ";
            $query .= "LSO_LOKASI, ";
            $query .= "LSO_QTY, ";
            $query .= "LSO_TMP_QTYCTN, ";
            $query .= "LSO_TMP_QTYPCS, ";
            $query .= "LSO_FLAGSARANA, ";
            $query .= "LSO_FLAGTAHAP, ";
            $query .= "LSO_AVGCOST, ";
            $query .= "LSO_CREATE_BY, ";
            $query .= "LSO_CREATE_DT, ";
            $query .= "LSO_JENISRAK, ";
            $query .= "LSO_ST_SALDOAKHIR) ";
            $query .= "SELECT ";
            $query .= "LSO_KODEIGR, ";
            $query .= "'" . Carbon::parse($request->tanggal_start_so)->format('Y-m-d') . "', ";
            $query .= "LSO_KODERAK, ";
            $query .= "LSO_KODESUBRAK, ";
            $query .= "LSO_TIPERAK, ";
            $query .= "LSO_SHELVINGRAK, ";
            $query .= "LSO_NOURUT, ";
            $query .= "LSO_PRDCD, ";
            $query .= "LSO_LOKASI, ";
            $query .= "LSO_QTY, ";
            $query .= "LSO_TMP_QTYCTN, ";
            $query .= "LSO_TMP_QTYPCS, ";
            $query .= "LSO_FLAGSARANA, ";
            $query .= "'" . $tahap . "', ";
            $query .= "(SELECT (case when prd_unit='KG' then st_avgcost/1000  else st_avgcost  end) ST_AVGCOST FROM TBMASTER_STOCK, TBMASTER_PRODMAST WHERE ST_PRDCD = PRD_PRDCD AND ST_LOKASI = LSO_LOKASI AND ST_PRDCD = LSO_PRDCD AND ST_AVGCOST IS NOT NULL LIMIT 1) AS ACOST, ";
            $query .= "'" . session('user_id') . "', ";
            $query .= "CURRENT_TIMESTAMP, ";
            $query .= "LSO_JENISRAK, ";
            $query .= "(SELECT coalesce (ST_SALDOAKHIR, 0) FROM TBMASTER_STOCK WHERE ST_LOKASI = LSO_LOKASI AND ST_PRDCD = LSO_PRDCD AND ST_SALDOAKHIR IS NOT NULL LIMIT 1) AS SALDO_AKHIR ";
            $query .= "FROM TBTR_LOKASI_SO WHERE DATE_TRUNC('DAY',LSO_TGLSO) >= '".Carbon::parse($request->tanggal_start_so)->format('Y-m-d')."' ";
            $query .= " AND LSO_PRDCD = LSO_PRDCD";

            // str = "UPDATE TBTR_LOKASI_SO SET LSO_MODIFY_BY = NULL, LSO_MODIFY_DT = NULL, LSO_FLAGLIMIT = NULL "
            // str &= "WHERE DATE_TRUNC('DAY',LSO_TGLSO) >= TO_DATE('" & Format(TglSO, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY')"

            DB::table('tbtr_lokasi_so')
            ->whereRaw("DATE_TRUNC('DAY',lso_tglso) >= '".Carbon::parse($request->tanggal_start_so)->format('Y-m-d')."'")
            ->update([
                'lso_modify_by' => NULL,
                'lso_modify_dt' => NULL,
                'lso_flaglimit' => NULL
            ]);

            // DB::commit();

            return ApiFormatter::success(200, 'Update BASO berhasil dilakukan');

        }

        catch(\Exception $e){

            DB::rollBack();

            $message = "Oops! Something wrong ( $e )";
            return ApiFormatter::error(400, $message);
        }
    }

    public function prosesBaSo(ProsesBaSoRequest $request){

        // dtCheck = QueryOra("SELECT 1 FROM TBTR_LOKASI_SO WHERE DATE_TRUNC('DAY',LSO_TGLSO) >= TO_DATE('" & Format(TglSO, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY') AND LSO_MODIFY_BY IS NULL AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y'")
        // If dtCheck.Rows.Count > 0 Then
        //     MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "Ada Lokasi yang belum di SO pada tahap ini", Me.Name)
        //     Exit Sub
        // End If

        $dtCheck = DB::table('tbtr_lokasi_so')
            ->whereRaw("DATE_TRUNC('DAY',lso_tglso) >= '".Carbon::parse($request->tanggal_start_so)->format('Y-m-d')."'")
            ->whereRaw("coalesce(lso_flaglimit, 'N') = 'Y'")
            ->whereNull('lso_modify_by')
            ->count();

        if($dtCheck > 0){
            return ApiFormatter::error(400, 'Ada Lokasi yang belum di SO pada tahap ini');
        }

        //* Apakah anda yakin ingin proses BA SO?

        if(session('userlevel') != 1){
            return ApiFormatter::error(400, 'Anda tidak berhak menjalankan menu ini');
        }

        try{

            $kodeigr = session('KODECABANG');
            $userid = session('userid');
            $procedure = DB::select("call sp_summary_so_plano('$kodeigr','$userid', NULL)");
            $procedure = $procedure[0]->sukses;

            return ApiFormatter::success(200, $procedure);
        }

        catch(\Exception $e){

            DB::rollBack();

            $message = "Oops! Something wrong ( $e )";
            return ApiFormatter::error(400, $message);
        }
    }
}
