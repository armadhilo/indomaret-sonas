<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\InputLokasiRequest;
use Illuminate\Http\Request;

class InputLokasiController extends Controller
{

    private $FlagTahap = false;
    public function __construct(Request $request){
        DatabaseConnection::setConnection(session('KODECABANG'), "PRODUCTION");
    }

    public function index(){

        // $dtSO = QueryOra("SELECT * FROM TBMASTER_SETTING_SO WHERE MSO_FLAGRESET IS NULL");
        // if(count($dtSO) == 0){
        //     return ApiFormatter::error(400, 'SO belum diinitial');
        // }

        // if($dtSO[0]->MSO_FLAGSUM <> ''){
        //     return ApiFormatter::error(400, 'SO sudah diproses BA');

        //     $FlagTahap = Val(dtSO.Rows(0).Item("MSO_FLAGTAHAP").ToString);
        // }

        return view('input-lokasi');
    }

    public function actionAddLokasi(InputLokasiRequest $request){

        // strSQL = "SELECT * FROM TBMASTER_LOKASI "
        // strSQL &= "WHERE LKS_KODERAK = '" & txtKodeRak.Text & "' "
        // strSQL &= "AND LKS_KODESUBRAK = '" & txtKodeSubRak.Text & "' "
        // strSQL &= "AND LKS_TIPERAK = '" & txtTipeRak.Text & "' "
        // strSQL &= "AND LKS_SHELVINGRAK = '" & txtShelvingRak.Text & "' "

        // If dtCek.Rows.Count > 0 Then
        //     MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "Lokasi sudah terdaftar di Master Lokasi", "Warning")
        //     Exit Sub
        // End If

        // strSQL = "SELECT LSO_LOKASI FROM TBTR_LOKASI_SO "
        // strSQL &= "WHERE TO_CHAR(LSO_TGLSO, 'DD-MM-YYYY') = '" & Format(dtSO.Rows(0).Item("MSO_TGLSO"), "dd-MM-yyyy").ToString & "' "
        // strSQL &= "AND LSO_KODERAK = '" & txtKodeRak.Text & "' "

        // If dtCek.Rows.Count > 0 Then
        //     If dtCek.Rows(0).Item("LSO_LOKASI") = "01" And cboJenisBarang.Text <> "Baik" Then
        //         MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "Kode Rak sudah terisi barang Baik", "Warning")
        //         txtKodeRak.Focus()
        //         txtKodeRak.SelectionStart = 0
        //         txtKodeRak.SelectionLength = txtKodeRak.TextLength
        //         Exit Sub
        //     ElseIf dtCek.Rows(0).Item("LSO_LOKASI") = "02" And cboJenisBarang.Text <> "Retur" Then
        //         MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "Kode Rak sudah terisi barang Retur", "Warning")
        //         txtKodeRak.Focus()
        //         txtKodeRak.SelectionStart = 0
        //         txtKodeRak.SelectionLength = txtKodeRak.TextLength
        //         Exit Sub
        //     ElseIf dtCek.Rows(0).Item("LSO_LOKASI") = "03" And cboJenisBarang.Text <> "Rusak" Then
        //         MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "Kode Rak sudah terisi barang Rusak", "Warning")
        //         txtKodeRak.Focus()
        //         txtKodeRak.SelectionStart = 0
        //         txtKodeRak.SelectionLength = txtKodeRak.TextLength
        //         Exit Sub
        //     End If
        // End If

        //? akan buka halaman baru untuk menambah produk dan daftar lokasi
    }

    //? jadi nanti no urut berdasarkan function ini
    public function getLastNumber(){
        // strSQL = "SELECT MAX(LSO_NOURUT) AS NOURUT FROM TBTR_LOKASI_SO "
        // strSQL &= "WHERE TO_CHAR(LSO_TGLSO, 'DD-MM-YYYY') = '" & Format(dtSO.Rows(0).Item("MSO_TGLSO"), "dd-MM-yyyy").ToString & "' "
        // strSQL &= "AND LSO_KODERAK = '" & txtKodeRak.Text & "' "
        // strSQL &= "AND LSO_KODESUBRAK = '" & txtKodeSubRak.Text & "' "
        // strSQL &= "AND LSO_TIPERAK = '" & txtTipeRak.Text & "' "
        // strSQL &= "AND LSO_SHELVINGRAK = '" & txtShelvingRak.Text & "' "
    }

    //? klik enter nanti harusnya otomatis dapet desc nya
    public function getDescPlu($prdcd){
        // dtPrd = QueryOra("SELECT PRD_DESKRIPSIPANJANG, PRD_UNIT, PRD_FRAC FROM TBMASTER_PRODMAST WHERE PRD_PRDCD = '" & DGV.Item(1, DGV.CurrentCell.RowIndex).Value.ToString & "'")
        // If dtPrd.Rows.Count > 0 Then
        //     If FlagTahap = 0 Then
        //         DGV.Item(2, DGV.CurrentCell.RowIndex).Value = dtPrd.Rows(0).Item("PRD_DESKRIPSIPANJANG").ToString
        //         DGV.Item(3, DGV.CurrentCell.RowIndex).Value = dtPrd.Rows(0).Item("PRD_UNIT").ToString & "/" & dtPrd.Rows(0).Item("PRD_FRAC").ToString
        //     Else
        //         Dim dtCek As New DataTable
        //         dtCek = QueryOra("SELECT * FROM TBTR_LOKASI_SO WHERE LSO_PRDCD = '" & DGV.Item(1, DGV.CurrentCell.RowIndex).Value.ToString & "' AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y'")
        //         If dtCek.Rows.Count > 0 Then
        //             DGV.Item(2, DGV.CurrentCell.RowIndex).Value = dtPrd.Rows(0).Item("PRD_DESKRIPSIPANJANG").ToString
        //             DGV.Item(3, DGV.CurrentCell.RowIndex).Value = dtPrd.Rows(0).Item("PRD_UNIT").ToString & "/" & dtPrd.Rows(0).Item("PRD_FRAC").ToString
        //         Else
        //             MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "PLU tidak termasuk dalam limit item SO", "Warning")
        //             DGV.Item(1, DGV.CurrentCell.RowIndex).Value = ""
        //             DGV.CurrentCell = DGV.Item(1, DGV.CurrentCell.RowIndex)
        //         End If
        //     End If
        // Else
        //     MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "PLU tidak ditemukan", "Warning")
        //     DGV.Item(1, DGV.CurrentCell.RowIndex).Value = ""
        //     DGV.CurrentCell = DGV.Item(1, DGV.CurrentCell.RowIndex)
        // End If
    }

    public function actionSave(){
        // If DGV.Item(1, i).Value.ToString <> "" And DGV.Item(4, i).Value.ToString = "" Then
        //     strSQL = "SELECT 1 FROM TBTR_LOKASI_SO "
        //     strSQL &= "WHERE TO_CHAR(LSO_TGLSO, 'DD-MM-YYYY') = '" & Format(dtSO.Rows(0).Item("MSO_TGLSO"), "dd-MM-yyyy").ToString & "' "
        //     strSQL &= "AND LSO_KODERAK = '" & txtKodeRak.Text & "' "
        //     strSQL &= "AND LSO_KODESUBRAK = '" & txtKodeSubRak.Text & "' "
        //     strSQL &= "AND LSO_TIPERAK = '" & txtTipeRak.Text & "' "
        //     strSQL &= "AND LSO_SHELVINGRAK = '" & txtShelvingRak.Text & "' "
        //     strSQL &= "AND LSO_NOURUT = '" & DGV.Item(0, i).Value.ToString & "'"

        //     dtLks = QueryOra(strSQL)
        //     If dtLks.Rows.Count = 0 Then
        //         strSQL = "INSERT INTO TBTR_LOKASI_SO ( "
        //         strSQL &= "LSO_KODEIGR, "
        //         strSQL &= "LSO_TGLSO, "
        //         strSQL &= "LSO_KODERAK, "
        //         strSQL &= "LSO_KODESUBRAK, "
        //         strSQL &= "LSO_TIPERAK, "
        //         strSQL &= "LSO_SHELVINGRAK, "
        //         strSQL &= "LSO_NOURUT, "
        //         strSQL &= "LSO_PRDCD, "
        //         strSQL &= "LSO_LOKASI, "
        //         strSQL &= "LSO_QTY, "
        //         strSQL &= "LSO_FLAGSARANA, "
        //         strSQL &= "LSO_CREATE_BY, "
        //         strSQL &= "LSO_CREATE_DT, "
        //         strSQL &= "LSO_FLAGLIMIT, "
        //         strSQL &= "LSO_JENISRAK "
        //         strSQL &= ") VALUES ( "
        //         strSQL &= "'" & KodeIGR & "', "
        //         strSQL &= "TO_DATE('" & Format(dtSO.Rows(0).Item("MSO_TGLSO"), "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY'), "
        //         strSQL &= "'" & txtKodeRak.Text.Replace("'", "") & "', "
        //         strSQL &= "'" & txtKodeSubRak.Text.Replace("'", "") & "', "
        //         strSQL &= "'" & txtTipeRak.Text.Replace("'", "") & "', "
        //         strSQL &= "'" & txtShelvingRak.Text.Replace("'", "") & "', "
        //         strSQL &= "'" & DGV.Item(0, i).Value.ToString.Replace("'", "") & "', "
        //         strSQL &= "'" & DGV.Item(1, i).Value.ToString.Replace("'", "") & "', "
        //         If cboJenisBarang.Text = "Baik" Then
        //             strSQL &= "'01', "
        //         ElseIf cboJenisBarang.Text = "Retur" Then
        //             strSQL &= "'02', "
        //         ElseIf cboJenisBarang.Text = "Rusak" Then
        //             strSQL &= "'03', "
        //         End If
        //         strSQL &= "'0', "
        //         strSQL &= "'K', "
        //         strSQL &= "'" & UserMODUL & "', "
        //         strSQL &= "CURRENT_TIMESTAMP, "
        //         If FlagTahap = 0 Then
        //             strSQL &= "NULL, "
        //         Else
        //             strSQL &= "'Y', "
        //         End If

        //         Dim JenisRak As String = "T"
        //         If Strings.Left(txtKodeRak.Text.Replace("'", ""), 1).ToUpper <> "D" And _
        //             Strings.Left(txtKodeRak.Text.Replace("'", ""), 1).ToUpper <> "G" And _
        //             Strings.Left(txtTipeRak.Text.Replace("'", ""), 1).ToUpper = "Z" Then
        //             JenisRak = "L"
        //         End If
        //         strSQL &= "'" & JenisRak & "')"

        //         NonQueryOraTransaction(strSQL, OraConn, OraTrans)
        //     Else
        //         strSQL = "UPDATE TBTR_LOKASI_SO SET "
        //         strSQL &= "LSO_PRDCD = '" & DGV.Item(1, i).Value.ToString.Replace("'", "") & "' "
        //         strSQL &= "WHERE TO_CHAR(LSO_TGLSO, 'DD-MM-YYYY') = '" & Format(dtSO.Rows(0).Item("MSO_TGLSO"), "dd-MM-yyyy").ToString & "' "
        //         strSQL &= "AND LSO_KODERAK = '" & txtKodeRak.Text & "' "
        //         strSQL &= "AND LSO_KODESUBRAK = '" & txtKodeSubRak.Text & "' "
        //         strSQL &= "AND LSO_TIPERAK = '" & txtTipeRak.Text & "' "
        //         strSQL &= "AND LSO_SHELVINGRAK = '" & txtShelvingRak.Text & "' "
        //         strSQL &= "AND LSO_NOURUT = '" & DGV.Item(0, i).Value.ToString & "'"

        //         NonQueryOraTransaction(strSQL, OraConn, OraTrans)
        //     End If
        // End If
    }
}
