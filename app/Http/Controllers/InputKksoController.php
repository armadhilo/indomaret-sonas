<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use Illuminate\Http\Request;

class InputKksoController extends Controller
{

    private $flagCopyLokasi;
    private $cboJenisBarang;

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
        // End If

        // flagCopyLokasi = dtSO.Rows(0).Item("mso_flag_createlso").ToString.ToUpper
        // cboJenisBarang.Text = "Baik"

        return view('home');
    }

    public function getData(){
        // strSQL = "SELECT LSO_NOURUT, PRD_PRDCD, PRD_DESKRIPSIPANJANG, PRD_UNIT, PRD_FRAC, LSO_QTY, LSO_TMP_QTYCTN, LSO_TMP_QTYPCS, coalesce(ST_AVGCOST, 0) AS ST_AVGCOST, LSO_MODIFY_BY "
        // strSQL &= "FROM TBTR_LOKASI_SO LEFT JOIN TBMASTER_STOCK ON LSO_PRDCD = ST_PRDCD AND LSO_LOKASI = ST_LOKASI AND LSO_KODEIGR = ST_KODEIGR, TBMASTER_PRODMAST "
        // strSQL &= "WHERE TO_CHAR(LSO_TGLSO, 'DD-MM-YYYY') = '" & Format(dtSO.Rows(0).Item("MSO_TGLSO"), "dd-MM-yyyy").ToString & "' "
        // strSQL &= "WHERE LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR AND PRD_PRDCD LIKE '%0' "
        // If flagCopyLokasi = "Y" Then
        //     strSQL &= "AND (coalesce(LSO_FLAGLIMIT, 'N') = 'Y' OR LSO_LOKASI = '02' OR LSO_LOKASI = '03') "
        // End If
        // strSQL &= "AND LSO_KODERAK = '" & txtKodeRak.Text & "' "
        // strSQL &= "AND LSO_KODESUBRAK = '" & txtKodeSubRak.Text & "' "
        // strSQL &= "AND LSO_TIPERAK = '" & txtTipeRak.Text & "' "
        // strSQL &= "AND LSO_SHELVINGRAK = '" & txtShelvingRak.Text & "' "
        // strSQL &= "AND LSO_FLAGSARANA = 'K' "
        // If cboJenisBarang.Text = "Baik" Then
        //     strSQL &= "AND LSO_LOKASI = '01' "
        // ElseIf cboJenisBarang.Text = "Retur" Then
        //     strSQL &= "AND LSO_LOKASI = '02' "
        // ElseIf cboJenisBarang.Text = "Rusak" Then
        //     strSQL &= "AND LSO_LOKASI = '03' "
        // End If
        // strSQL &= "ORDER BY LSO_NOURUT ASC"
    }

    public function actionUpdate(){
        // For i As Integer = 0 To DGV.Rows.Count - 1
        //     strSQL = ""
        //     strSQL &= "INSERT INTO TBHISTORY_SONAS "
        //     strSQL &= "(HSO_KODERAK, "
        //     strSQL &= "HSO_KODESUBRAK, "
        //     strSQL &= "HSO_TIPERAK, "
        //     strSQL &= "HSO_SHELVINGRAK, "
        //     strSQL &= "HSO_NOURUT, "
        //     strSQL &= "HSO_PRDCD, "
        //     strSQL &= "HSO_TGLSO, "
        //     strSQL &= "HSO_QTYLAMA, "
        //     strSQL &= "HSO_QTYBARU, "
        //     strSQL &= "HSO_CREATE_BY, "
        //     strSQL &= "HSO_CREATE_DT) "
        //     strSQL &= "VALUES "
        //     strSQL &= "( "
        //     strSQL &= "'" & txtKodeRak.Text.Replace("'", "") & "', "
        //     strSQL &= "'" & txtKodeSubRak.Text.Replace("'", "") & "', "
        //     strSQL &= "'" & txtTipeRak.Text.Replace("'", "") & "', "
        //     strSQL &= "'" & txtShelvingRak.Text.Replace("'", "") & "', "
        //     strSQL &= "'" & DGV.Item(0, i).Value.ToString.Replace("'", "") & "', "
        //     strSQL &= "'" & DGV.Item(1, i).Value.ToString.Replace("'", "").ToString & "', "
        //     strSQL &= "TO_DATE('" & Format(dtSO.Rows(0).Item("MSO_TGLSO"), "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY'), "
        //     strSQL &= "'" & Val(DGV.Item(9, i).Value.ToString.Replace("'", "")) & "', "
        //     strSQL &= "'" & Val(DGV.Item(7, i).Value.ToString.Replace(".", "")) & "', "
        //     strSQL &= "'" & UserMODUL & "', "
        //     strSQL &= "CURRENT_TIMESTAMP) "
        //     NonQueryOraTransaction(strSQL, OraConn, OraTrans)

        //     strSQL = "UPDATE TBTR_LOKASI_SO SET "
        //     strSQL &= "LSO_QTY = '" & Val(DGV.Item(7, i).Value.ToString.Replace(".", "")) & "', "
        //     strSQL &= "LSO_TMP_QTYCTN = '" & Val(DGV.Item(4, i).Value.ToString.Replace(".", "")) & "', "
        //     strSQL &= "LSO_TMP_QTYPCS = '" & Val(DGV.Item(5, i).Value.ToString.Replace(".", "")) & "', "
        //     strSQL &= "LSO_MODIFY_BY = '" & UserMODUL & "', "
        //     strSQL &= "LSO_MODIFY_DT = CURRENT_TIMESTAMP "
        //     strSQL &= "WHERE LSO_TGLSO = TO_DATE('" & Format(dtSO.Rows(0).Item("MSO_TGLSO"), "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY') "
        //     strSQL &= "AND LSO_KODERAK = '" & txtKodeRak.Text.Replace("'", "") & "' "
        //     strSQL &= "AND LSO_KODESUBRAK = '" & txtKodeSubRak.Text.Replace("'", "") & "' "
        //     strSQL &= "AND LSO_TIPERAK = '" & txtTipeRak.Text.Replace("'", "") & "' "
        //     strSQL &= "AND LSO_SHELVINGRAK = '" & txtShelvingRak.Text.Replace("'", "") & "' "
        //     strSQL &= "AND LSO_NOURUT = '" & DGV.Item(0, i).Value.ToString.Replace("'", "") & "' "
        //     strSQL &= "AND LSO_PRDCD = '" & DGV.Item(1, i).Value.ToString.Replace("'", "") & "' "
        //     strSQL &= "AND LSO_FLAGSARANA = 'K'"

        //     NonQueryOraTransaction(strSQL, OraConn, OraTrans)
        // Next

        //! LANGSUNG UPLOAD PLANOPROGRAM
        // For i As Integer = 0 To DGV.Rows.Count - 1
        //     Dim KodeRak As String = txtKodeRak.Text.Replace("'", "")
        //     Dim KodeSubRak As String = txtKodeSubRak.Text.Replace("'", "")
        //     Dim TipeRak As String = txtTipeRak.Text.Replace("'", "")
        //     Dim Shelving As String = txtShelvingRak.Text.Replace("'", "")
        //     Dim NoUrut As String = DGV.Item(0, i).Value.ToString.Replace("'", "")
        //     Dim KodePLU As String = DGV.Item(1, i).Value.ToString.Replace("'", "")
        //     Dim Qty As Double = Val(DGV.Item(7, i).Value.ToString.Replace(".", ""))

        //     strSQL = "SELECT * FROM TBTR_LOKASI_SO "
        //     strSQL &= "WHERE LSO_KODERAK = '" & KodeRak & "' AND "
        //     strSQL &= "LSO_KODESUBRAK = '" & KodeSubRak & "' AND "
        //     strSQL &= "LSO_TIPERAK = '" & TipeRak & "' AND "
        //     strSQL &= "LSO_SHELVINGRAK = '" & Shelving & "' AND "
        //     strSQL &= "LSO_NOURUT = '" & NoUrut & "' AND "
        //     strSQL &= "TO_CHAR(LSO_TGLSO, 'DD-MM-YYYY') = '" & Format(dtSO.Rows(0).Item("MSO_TGLSO"), "dd-MM-yyyy").ToString & "' "

        //     Dim dtCek As DataTable
        //     dtCek = QueryOra(strSQL)
        //     If dtCek.Rows.Count > 0 Then
        //         If Strings.Left(KodeRak, 1).ToUpper = "D" Or Strings.Left(KodeRak, 1).ToUpper = "G" Then
        //             If dtCek.Rows(0).Item("LSO_JENISRAK").ToString = "D" Or dtCek.Rows(0).Item("LSO_JENISRAK").ToString = "N" Then
        //                 'Jika rak cadangan Gudang (L) maka update ke Display Gudang

        //                 strSQL = ""
        //                 strSQL &= "UPDATE tbmaster_lokasi "
        //                 strSQL &= "SET lks_qty = coalesce ( ( SELECT SUM (lso_qty) qty "
        //                 strSQL &= "             FROM tbtr_lokasi_so "
        //                 strSQL &= "             WHERE (lso_lokasi = '01' "
        //                 strSQL &= "                 AND lso_jenisrak = 'L' "
        //                 strSQL &= "                 AND lso_tglso = TO_DATE('" & Format(dtSO.Rows(0).Item("MSO_TGLSO"), "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY') "
        //                 strSQL &= "                 AND lso_prdcd = '" & KodePLU.ToString & "' "
        //                 strSQL &= "                 AND lso_koderak LIKE 'L%') "
        //                 strSQL &= "             OR (lso_koderak LIKE 'D%'"
        //                 strSQL &= "                 AND lso_jenisrak IN ('D', 'N')"
        //                 strSQL &= "                 AND lso_tglso = TO_DATE('" & Format(dtSO.Rows(0).Item("MSO_TGLSO"), "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY') "
        //                 strSQL &= "                 AND lso_prdcd     = '" & KodePLU.ToString & "')"
        //                 strSQL &= "             GROUP BY lso_prdcd), 0), "
        //                 strSQL &= "LKS_MODIFY_BY = '" & UserMODUL & "', "
        //                 strSQL &= "LKS_MODIFY_DT = CURRENT_TIMESTAMP "
        //                 strSQL &= "WHERE lks_koderak LIKE 'D%' AND lks_jenisrak IN ('D', 'N') and lks_prdcd = '" & KodePLU.ToString & "' "

        //                 NonQueryOraTransaction(strSQL, OraConn, OraTrans)
        //             Else
        //                 'Jika Gudang update plano apa adanya

        //                 strSQL = ""
        //                 strSQL &= "UPDATE TBMASTER_LOKASI "
        //                 strSQL &= "SET "
        //                 strSQL &= "LKS_QTY = '" & Qty & "', "
        //                 strSQL &= "LKS_PRDCD = CASE WHEN LKS_JENISRAK = 'S' THEN '" & KodePLU.ToString & "' ELSE LKS_PRDCD END, "
        //                 strSQL &= "LKS_MODIFY_BY = '" & UserMODUL & "', "
        //                 strSQL &= "LKS_MODIFY_DT = CURRENT_TIMESTAMP "
        //                 strSQL &= "WHERE LKS_KODERAK = '" & KodeRak & "' AND "
        //                 strSQL &= "LKS_KODESUBRAK = '" & KodeSubRak & "' AND "
        //                 strSQL &= "LKS_TIPERAK = '" & TipeRak & "' AND "
        //                 strSQL &= "LKS_SHELVINGRAK = '" & Shelving & "' AND "
        //                 strSQL &= "LKS_NOURUT = '" & NoUrut & "' "

        //                 NonQueryOraTransaction(strSQL, OraConn, OraTrans)
        //             End If
        //         ElseIf Strings.Left(KodeRak, 1).ToUpper = "L" Then
        //             'Jika rak cadangan Gudang (L) maka update ke Display Gudang

        //             strSQL = ""
        //             strSQL &= "UPDATE tbmaster_lokasi "
        //             strSQL &= "SET lks_qty = coalesce ( ( SELECT SUM (lso_qty) qty "
        //             strSQL &= "             FROM tbtr_lokasi_so "
        //             strSQL &= "             WHERE (lso_lokasi = '01' "
        //             strSQL &= "                 AND lso_jenisrak = 'L' "
        //             strSQL &= "                 AND lso_tglso = TO_DATE('" & Format(dtSO.Rows(0).Item("MSO_TGLSO"), "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY') "
        //             strSQL &= "                 AND lso_prdcd = '" & KodePLU.ToString & "' "
        //             strSQL &= "                 AND lso_koderak LIKE 'L%') "
        //             strSQL &= "             OR (lso_koderak LIKE 'D%'"
        //             strSQL &= "                 AND lso_jenisrak IN ('D', 'N')"
        //             strSQL &= "                 AND lso_tglso = TO_DATE('" & Format(dtSO.Rows(0).Item("MSO_TGLSO"), "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY') "
        //             strSQL &= "                 AND lso_prdcd     = '" & KodePLU.ToString & "')"
        //             strSQL &= "             GROUP BY lso_prdcd), 0), "
        //             strSQL &= "LKS_MODIFY_BY = '" & UserMODUL & "', "
        //             strSQL &= "LKS_MODIFY_DT = CURRENT_TIMESTAMP "
        //             strSQL &= "WHERE lks_koderak LIKE 'D%' AND lks_jenisrak IN ('D', 'N') and lks_prdcd = '" & KodePLU.ToString & "' "

        //             NonQueryOraTransaction(strSQL, OraConn, OraTrans)
        //         Else
        //             If dtCek.Rows(0).Item("LSO_JENISRAK").ToString = "S" Then
        //                 'Jika Toko Storage update apa adanya
        //                 strSQL = ""
        //                 strSQL &= "UPDATE TBMASTER_LOKASI "
        //                 strSQL &= "SET "
        //                 strSQL &= "LKS_QTY = '" & Qty & "', "
        //                 strSQL &= "LKS_PRDCD = CASE WHEN LKS_JENISRAK = 'S' THEN '" & KodePLU.ToString & "' ELSE LKS_PRDCD END, "
        //                 strSQL &= "LKS_MODIFY_BY = '" & UserMODUL & "', "
        //                 strSQL &= "LKS_MODIFY_DT = CURRENT_TIMESTAMP "
        //                 strSQL &= "WHERE LKS_KODERAK = '" & KodeRak & "' AND "
        //                 strSQL &= "LKS_KODESUBRAK = '" & KodeSubRak & "' AND "
        //                 strSQL &= "LKS_TIPERAK = '" & TipeRak & "' AND "
        //                 strSQL &= "LKS_SHELVINGRAK = '" & Shelving & "' AND "
        //                 strSQL &= "LKS_NOURUT = '" & NoUrut & "' "

        //                 NonQueryOraTransaction(strSQL, OraConn, OraTrans)
        //             Else
        //                 'Jika Toko selain Storage update plano SUM ke lokasi display utama
        //                 strSQL = ""
        //                 strSQL &= "UPDATE TBMASTER_LOKASI "
        //                 strSQL &= "SET "
        //                 strSQL &= "LKS_QTY = "
        //                 strSQL &= "         coalesce((SELECT SUM(LSO_QTY) FROM TBTR_LOKASI_SO "
        //                 strSQL &= "         WHERE LSO_TGLSO = TO_DATE('" & Format(dtSO.Rows(0).Item("MSO_TGLSO"), "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY') "
        //                 strSQL &= "         AND (SUBSTR(LSO_KODERAK,1,1) <> 'D' AND SUBSTR(LSO_KODERAK,1,1) <> 'G' AND SUBSTR(LSO_KODERAK,1,3) <> 'HDH') AND "
        //                 strSQL &= "         LSO_PRDCD = '" & KodePLU.ToString & "' AND (LSO_JENISRAK = 'D' OR LSO_JENISRAK = 'N' OR LSO_JENISRAK = 'L')), 0), "
        //                 strSQL &= "LKS_MODIFY_BY = '" & UserMODUL & "', "
        //                 strSQL &= "LKS_MODIFY_DT = CURRENT_TIMESTAMP "
        //                 strSQL &= "WHERE "
        //                 strSQL &= "(SUBSTR(LKS_KODERAK,1,1) <> 'D' AND SUBSTR(LKS_KODERAK,1,1) <> 'G') AND "
        //                 strSQL &= "(LKS_JENISRAK = 'D' OR LKS_JENISRAK = 'N') AND "
        //                 strSQL &= "LKS_PRDCD = '" & KodePLU.ToString & "'"

        //                 NonQueryOraTransaction(strSQL, OraConn, OraTrans)
        //             End If
        //         End If
        //     End If
        // Next


    }


}
