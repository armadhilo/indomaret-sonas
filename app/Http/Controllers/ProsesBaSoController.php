<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use Illuminate\Http\Request;

class ProsesBaSoController extends Controller
{

    private $FlagTahap;
    public function __construct(Request $request){
        DatabaseConnection::setConnection(session('KODECABANG'), "PRODUCTION");
    }

    public function index(){

        // dt = QueryOra("SELECT * FROM TBMASTER_SETTING_SO WHERE MSO_FLAGRESET IS NULL")
        // If dt.Rows.Count = 0 Then
        //     MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "SO belum diinitial", Me.Name)
        //     Me.DialogResult = Windows.Forms.DialogResult.No
        //     Me.Close()
        //     Exit Sub
        // ElseIf dt.Rows(0).Item("MSO_FLAGTAHAP").ToString = "" Then
        //     MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "Belum Copy Master Lokasi ke lokasi SO ", Me.Name)
        //     Me.DialogResult = Windows.Forms.DialogResult.No
        //     Me.Close()
        //     Exit Sub

        //? membawa isi $data query dibawah ini
        // $data['tgl_so'] = dt.Rows(0).Item("MSO_TGLSO");
        // If dt.Rows(0).Item("MSO_FLAGSUM").ToString = "" Then
        //     BtnDraftLHSO.Enabled = True
        //     BtnProsesBASO.Enabled = True
        //     BtnProsesBASO.Text = "START PROSES BA SO "

        //     FlagTahap = Val(dt.Rows(0).Item("MSO_FLAGTAHAP").ToString)
        //     BtnDraftLHSO.Text = "PROSES DRAFT LHSO TAHAP " & FlagTahap + 1 & "  "

        //     If FlagTahap < 1 Then
        //         BtnProsesBASO.Enabled = False
        //     Else
        //         BtnProsesBASO.Enabled = True
        //     End If

        //     'If FlagTahap < 6 Then
        //     BtnDraftLHSO.Enabled = True
        //     'Else
        //     '    BtnDraftLHSO.Enabled = False
        //     'End If
        // Else
        //     MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "SO sudah di Proses BA", Me.Name)
        //     Me.DialogResult = Windows.Forms.DialogResult.No
        //     Me.Close()
        //     Exit Sub
        // End If

        return view('home', $data);
    }

    public function actionProsesDraft(){
        // If FlagTahap > 0 Then
        //     dtCheck = QueryOra("SELECT 1 FROM TBTR_LOKASI_SO WHERE DATE_TRUNC('DAY',LSO_TGLSO) >= TO_DATE('" & Format(TglSO, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY') AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y'")
        //     If dtCheck.Rows.Count = 0 Then
        //         MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "Belum setting limit item SO pada tahap ini", Me.Name)
        //         Exit Sub
        //     End If
        // End If

        // dtCheck = QueryOra("SELECT 1 FROM TBTR_LOKASI_SO WHERE DATE_TRUNC('DAY',LSO_TGLSO) >= TO_DATE('" & Format(TglSO, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY') AND LSO_MODIFY_BY IS NULL AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y'")
        // If dtCheck.Rows.Count > 0 Then
        //     MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "Ada Lokasi yang belum di SO pada tahap ini", Me.Name)
        //     Exit Sub
        // End If

        // dtCheck = QueryOra("SELECT * FROM TBMASTER_SETTING_SO WHERE DATE_TRUNC('DAY',MSO_TGLSO) >= TO_DATE('" & Format(TglSO, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY') ")
        // If dtCheck.Rows.Count > 0 Then
        //     If dtCheck.Rows(0).Item("MSO_FLAG_CREATELSO").ToString <> "Y" Then
        //         MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "Lokasi Plano belum dicopy ke Lokasi SO", Me.Name)
        //         Exit Sub
        //     End If
        // End If

        if(session('userlevel') != 1){
            return ApiFormatter::error(400, 'Anda tidak berhak menjalankan menu ini');
        }

        // Dim Tahap As String = (FlagTahap + 1).ToString.PadLeft(2, "0")

        // str = "UPDATE TBMASTER_SETTING_SO SET MSO_FLAGTAHAP = '" & Tahap & "', MSO_FLAGTRFLOKASI = 'Y', MSO_FLAGLIMIT = NULL "
        // str &= "WHERE DATE_TRUNC('DAY',MSO_TGLSO) >= TO_DATE('" & Format(TglSO, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY')"

        // NonQueryOraTransaction(str, OraConn, OraTrans)

        // str = "INSERT INTO TBHISTORY_LHSO_SONAS "
        // str &= "(LSO_KODEIGR, "
        // str &= "LSO_TGLSO, "
        // str &= "LSO_KODERAK, "
        // str &= "LSO_KODESUBRAK, "
        // str &= "LSO_TIPERAK, "
        // str &= "LSO_SHELVINGRAK, "
        // str &= "LSO_NOURUT, "
        // str &= "LSO_PRDCD, "
        // str &= "LSO_LOKASI, "
        // str &= "LSO_QTY, "
        // str &= "LSO_TMP_QTYCTN, "
        // str &= "LSO_TMP_QTYPCS, "
        // str &= "LSO_FLAGSARANA, "
        // str &= "LSO_FLAGTAHAP, "
        // str &= "LSO_AVGCOST, "
        // str &= "LSO_CREATE_BY, "
        // str &= "LSO_CREATE_DT, "
        // str &= "LSO_JENISRAK, "
        // str &= "LSO_ST_SALDOAKHIR) "
        // str &= "SELECT "
        // str &= "LSO_KODEIGR, "
        // str &= "TO_DATE('" & Format(TglSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY'), "
        // str &= "LSO_KODERAK, "
        // str &= "LSO_KODESUBRAK, "
        // str &= "LSO_TIPERAK, "
        // str &= "LSO_SHELVINGRAK, "
        // str &= "LSO_NOURUT, "
        // str &= "LSO_PRDCD, "
        // str &= "LSO_LOKASI, "
        // str &= "LSO_QTY, "
        // str &= "LSO_TMP_QTYCTN, "
        // str &= "LSO_TMP_QTYPCS, "
        // str &= "LSO_FLAGSARANA, "
        // str &= "'" & Tahap & "', "
        // str &= "(SELECT (case when prd_unit='KG' then st_avgcost/1000  else st_avgcost  end) ST_AVGCOST FROM TBMASTER_STOCK, TBMASTER_PRODMAST WHERE ST_PRDCD = PRD_PRDCD AND ST_LOKASI = LSO_LOKASI AND ST_PRDCD = LSO_PRDCD AND ST_AVGCOST IS NOT NULL LIMIT 1) AS ACOST, "
        // str &= "'" & UserMODUL & "', "
        // str &= "CURRENT_TIMESTAMP, "
        // str &= "LSO_JENISRAK, "
        // str &= "(SELECT coalesce (ST_SALDOAKHIR, 0) FROM TBMASTER_STOCK WHERE ST_LOKASI = LSO_LOKASI AND ST_PRDCD = LSO_PRDCD AND ST_SALDOAKHIR IS NOT NULL LIMIT 1) AS SALDO_AKHIR "
        // str &= "FROM TBTR_LOKASI_SO WHERE DATE_TRUNC('DAY',LSO_TGLSO) >= TO_DATE('" & Format(TglSO, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY') "
        // str &= " AND LSO_PRDCD = LSO_PRDCD"

        // NonQueryOraTransaction(str, OraConn, OraTrans)

        // str = "UPDATE TBTR_LOKASI_SO SET LSO_MODIFY_BY = NULL, LSO_MODIFY_DT = NULL, LSO_FLAGLIMIT = NULL "
        // str &= "WHERE DATE_TRUNC('DAY',LSO_TGLSO) >= TO_DATE('" & Format(TglSO, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY')"
    }

    public function prosesBaSo(){
        // dtCheck = QueryOra("SELECT 1 FROM TBTR_LOKASI_SO WHERE DATE_TRUNC('DAY',LSO_TGLSO) >= TO_DATE('" & Format(TglSO, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY') AND LSO_MODIFY_BY IS NULL AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y'")
        // If dtCheck.Rows.Count > 0 Then
        //     MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "Ada Lokasi yang belum di SO pada tahap ini", Me.Name)
        //     Exit Sub
        // End If

        //* Apakah anda yakin ingin proses BA SO?

        if(session('userlevel') != 1){
            return ApiFormatter::error(400, 'Anda tidak berhak menjalankan menu ini');
        }

        // Try
        //     If oConn.State = ConnectionState.Closed Then oConn.Open()
        //     cmdd = New OdbcCommand("CALL sp_summary_so_plano ('" & KodeIGR.Trim & "', '" & UserMODUL.Trim & "', NULL)", oConn)
        //     cmdd.CommandTimeout = 3000
        //     rdd = cmdd.ExecuteReader
        //     rdd.Read()
        //     If rdd.HasRows Then
        //         output = rdd.Item("sukses").ToString.Trim
        //     End If
        //     If oConn.State = ConnectionState.Open Then oConn.Close()
        //     rdd.Close()
        //     cmdd.Dispose()
        // Catch ex As Exception
        //     MsgBox("ERROR PANGGIL STORE PROCEDURE SP_SUMMARY_SO_PLANO" & vbCr & ex.ToString)
        // End Try
    }


}
