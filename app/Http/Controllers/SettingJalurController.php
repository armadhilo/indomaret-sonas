<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\SettingJalurRequest;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function __construct(Request $request){
        DatabaseConnection::setConnection(session('KODECABANG'), "PRODUCTION");
    }

    public function index(){
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
        // strSQL = "UPDATE TBTR_LOKASI_SO SET LSO_FLAGSARANA = '" & Jalur & "' "
        // strSQL &= "WHERE LSO_KODEIGR = '" & KodeIGR & "' "
        // strSQL &= "AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y' "
        // strSQL &= "AND LSO_TGLSO = TO_DATE('" & Format(dtSO.Rows(0).Item("MSO_TGLSO"), "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY') "
        // strSQL &= "AND LSO_RECID IS NULL "
        // strSQL &= "AND LSO_KODERAK = '" & txtKodeRak.Text & "' "
        // If txtKodeSubRak.Text <> "" Then
        //     strSQL &= "AND LSO_KODESUBRAK = '" & txtKodeSubRak.Text & "' "
        // End If
        // If txtTipeRak.Text <> "" Then
        //     strSQL &= "AND LSO_TIPERAK = '" & txtTipeRak.Text & "' "
        // End If
        // If txtShelvingRak.Text <> "" Then
        //     strSQL &= "AND LSO_SHELVINGRAK = '" & txtShelvingRak.Text & "' "
        // End If
        // If txtNoUrut.Text <> "" Then
        //     strSQL &= "AND LSO_NOURUT = '" & txtNoUrut.Text & "' "
        // End If

        //? cek apakah function updatenya berhasil melakukan save
        //* Records Updated
        //* Tidak ada lokasi yang terupdate
    }
}
