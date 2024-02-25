<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\SettingJalurRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    public function __construct(Request $request){
        DatabaseConnection::setConnection(session('KODECABANG'), "PRODUCTION");
    }

    public function index(){

        $dtSO = DB::table('tbmaster_setting_so')
            ->whereNull('mso_flagreset')
            ->get();

        if(count($dtSO) == 0){
            return ApiFormatter::error(400, 'SO belum diinitial');
        }elseif($dtSO[0]->mso_flagsum <> ''){
            return ApiFormatter::error(400, 'SO sudah diproses BA');
        }

        if($dtSO[0]->mso_flagtahap > 0 && $dtSO[0]->mso_flaglimit == ""){
            return ApiFormatter::error(400, 'Setting limit item untuk tahap ini belum disetting');
        }

        // dtSO = QueryOra("SELECT * FROM TBMASTER_SETTING_SO WHERE MSO_FLAGRESET IS NULL")
        // If dtSO.Rows.Count = 0 Then
        //     MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "SO belum diinitial", "Warning")
        //     Me.DialogResult = Windows.Forms.DialogResult.No
        //     Me.Close()
        //     Exit Sub
        // Else
        //     If dtSO.Rows(0).Item("MSO_FLAGSUM").ToString <> "" Then
        //         MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "SO sudah diproses BA", "Warning")
        //         Me.DialogResult = Windows.Forms.DialogResult.No
        //         Me.Close()
        //         Exit Sub
        //     End If

        //     If Val(dtSO.Rows(0).Item("MSO_FLAGTAHAP").ToString) > 0 And dtSO.Rows(0).Item("MSO_FLAGLIMIT").ToString = "" Then
        //         MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "Setting limit item untuk tahap ini belum disetting", "Warning")
        //         Me.DialogResult = Windows.Forms.DialogResult.No
        //         Me.Close()
        //         Exit Sub
        //     End If
        // End If
        return view('home');
    }

    public function actionUpdate(SettingJalurRequest $request){
        $query = '';
        $query .= "UPDATE TBTR_LOKASI_SO SET LSO_FLAGSARANA = '" . $request->jalur_kertas . "' ";
        $query .= "WHERE LSO_KODEIGR = '" . session('KODECABANG') . "' ";
        $query .= "AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y' ";
        $query .= "AND LSO_TGLSO = TO_DATE('" . Carbon::parse($request->tanggal_start_so)->format('Y-m-d H:i:s') . "', 'DD-MM-YYYY') ";
        $query .= "AND LSO_RECID IS NULL ";
        $query .= "AND LSO_KODERAK = '" . $request->kode_rak . "' ";
        if(isset($request->kode_sub_rak)){
            $query .= "AND LSO_KODESUBRAK = '" . $request->kode_sub_rak . "' ";
        }elseif(isset($request->tipe_rak)){
            $query .= "AND LSO_TIPERAK = '" . $request->tipe_rak . "' ";
        }elseif(isset($request->shelving_rak)){
            $query .= "AND LSO_SHELVINGRAK = '" . $request->shelving_rak . "' ";
        }elseif(isset($request->no_urut)){
            $query .= "AND LSO_NOURUT = '" . $request->no_urut . "' ";
        }

        DB::update($query);

        //? cek apakah function updatenya berhasil melakukan save
        //* Records Updated
        //* Tidak ada lokasi yang terupdate
    }
}
