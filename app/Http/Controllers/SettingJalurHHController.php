<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\SettingJalurRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingJalurHHController extends Controller
{

    public function __construct(Request $request){
        DatabaseConnection::setConnection(session('KODECABANG'), "PRODUCTION");
    }

    public function index(){

        $dtSO = DB::table('tbmaster_setting_so')
            ->whereNull('mso_flagreset')
            ->get();
        $data['tglSo'] = Carbon::parse($dtSO[0]->mso_tglso)->format('Y-m-d');

        if(count($dtSO) == 0){
            $check_error = "SO belum diinitial";
            return view('setting-jalur', compact('check_error'));
        }elseif($dtSO[0]->mso_flagsum <> ''){
            $check_error = "SO sudah diproses BA";
            return view('setting-jalur', compact('check_error'));
        }

        if($dtSO[0]->mso_flagtahap > 0 && $dtSO[0]->mso_flaglimit == ""){
            $check_error = "Setting limit item untuk tahap ini belum disetting";
            return view('setting-jalur', compact('check_error'));
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
        return view('setting-jalur', $data);
    }

    public function actionUpdate(SettingJalurRequest $request){
        $query = '';
        $query .= "UPDATE TBTR_LOKASI_SO SET LSO_FLAGSARANA = '" . $request->jalur_kertas . "' ";
        $query .= "WHERE LSO_KODEIGR = '" . session('KODECABANG') . "' ";
        $query .= "AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y' ";
        $query .= "AND LSO_TGLSO = '" . Carbon::parse($request->tanggal_start_so)->format('Y-m-d H:i:s') . "' ";
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

        $affectedRows = DB::update($query);

        //? cek apakah function updatenya berhasil melakukan save
        //* Records Updated
        //* Tidak ada lokasi yang terupdate
        if ($affectedRows !== false) {
            return ApiFormatter::success(200, 'Records Updated');
        } else {
            return ApiFormatter::error(400, 'Tidak ada lokasi yang terupdate');
        }
    }
}
