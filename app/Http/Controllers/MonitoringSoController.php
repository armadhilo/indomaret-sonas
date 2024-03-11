<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\ProsesBaSoRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringSoController extends Controller
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
            return ApiFormatter::error(400, 'SO BELUM DI-INITIAL');
        }else{
            if($dtCek[0]->mso_flaglimit == ''){
                return ApiFormatter::error(400, 'Setting limit item untuk tahap ini belum disetting');
            }
        }


        return view('proses-ba-so');
    }

    public function getMonitoring(){
        //! TOKO
        $query = '';
        $query .= "select to_char(ROUND((total_so / total_lokasi) * 100, 2), '990D99') ";
        $query .= "|| '%' || '  (' || total_so || '/' || total_lokasi || ')' progress ";
        $query .= "FROM (SELECT   lso_koderak, ";
        $query .= "      COUNT(1) total_lokasi, ";
        $query .= "      SUM(CASE WHEN lso_modify_by IS NULL THEN 0 ELSE 1 END) total_so ";
        $query .= "      FROM tbtr_lokasi_so, tbmaster_setting_so ";
        $query .= "      WHERE lso_tglso = mso_tglso and mso_flagreset is null ";
        $query .= "      AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y' AND LSO_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%' AND (LSO_KODERAK NOT LIKE 'L%' OR LSO_TIPERAK NOT LIKE 'Z%')";
        $query .= "      GROUP BY lso_koderak ";
        $query .= " ) AS DATAS";
        $data['toko'] = DB::select($query);
        //* _toko = dr.Item("PROGRESS").ToString

        //! DETAIL TOKO
        $query = '';
        $query .= "select lso_koderak, to_char(ROUND((total_so / total_lokasi) * 100, 2), '990D99') ";
        $query .= "|| '%' || '  (' || coalesce(total_so,0) || '/' || total_lokasi || ')' progress ";
        $query .= "FROM (SELECT   lso_koderak, ";
        $query .= "      COUNT(1) total_lokasi, ";
        $query .= "      SUM(CASE WHEN lso_modify_by IS NULL THEN 0 ELSE 1 END) total_so ";
        $query .= "      FROM tbtr_lokasi_so, tbmaster_setting_so ";
        $query .= "      WHERE lso_tglso = mso_tglso and mso_flagreset is null ";
        $query .= "      AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y' AND Lso_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%' AND (LSO_KODERAK  NOT LIKE 'L%' OR LSO_TIPERAK  NOT LIKE 'Z%') ";
        $query .= "      GROUP BY lso_koderak";
        $query .= ")AS DATAS";
        $query .= " ORDER BY lso_koderak ";
        $data['detail_toko'] = DB::select($query);
        // nod.Name = dr.Item("LSO_KODERAK").ToString
        // nod.Text = dr.Item("LSO_KODERAK").ToString.PadRight(7) & _
        //             " " & dr.Item("PROGRESS").ToString
        // TreeView1.Nodes("TOKO").Nodes.Add(nod)
        // TreeView1.Nodes("TOKO").Nodes(dr.Item("LSO_KODERAK").ToString).Nodes.Add("1")

        //! GUDANG
        $query = '';
        $query .= "select to_char(ROUND((total_so / total_lokasi) * 100, 2), '990D99') ";
        $query .= "|| '%' || '  (' || total_so || '/' || total_lokasi || ')' progress ";
        $query .= "FROM (SELECT   lso_koderak, ";
        $query .= "      COUNT(1) total_lokasi, ";
        $query .= "      SUM(CASE WHEN lso_modify_by IS NULL THEN 0 ELSE 1 END) total_so ";
        $query .= "      FROM tbtr_lokasi_so, tbmaster_setting_so ";
        $query .= "      WHERE lso_tglso = mso_tglso and mso_flagreset is null ";
        $query .= "      AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y' AND (Lso_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%' OR (LSO_KODERAK  LIKE 'L%' AND LSO_TIPERAK  LIKE 'Z%'))";
        $query .= "      GROUP BY lso_koderak ";
        $query .= ")AS DATAS ";
        $data['gudang'] = DB::select($query);

        //* _toko = dr.Item("PROGRESS").ToString


        //! DETAIL GUDANG
        $query = '';
        $query .= "select lso_koderak, to_char(ROUND((total_so / total_lokasi) * 100, 2), '990D99') ";
        $query .= "|| '%' || '  (' || total_so || '/' || total_lokasi || ')' progress ";
        $query .= "FROM (SELECT   lso_koderak, ";
        $query .= "      COUNT(1) total_lokasi, ";
        $query .= "      SUM(CASE WHEN lso_modify_by IS NULL THEN 0 ELSE 1 END) total_so ";
        $query .= "      FROM tbtr_lokasi_so, tbmaster_setting_so ";
        $query .= "      WHERE lso_tglso = mso_tglso and mso_flagreset is null ";
        $query .= "      AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y' AND (Lso_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%' OR (LSO_KODERAK  LIKE 'L%' AND LSO_TIPERAK  LIKE 'Z%')) ";
        $query .= "      GROUP BY lso_koderak";
        $query .= ")AS DATAS";
        $query .= " ORDER BY lso_koderak ";
        $data['detail_gudang'] = DB::select($query);

        // nod.Name = dr.Item("LSO_KODERAK").ToString
        // nod.Text = dr.Item("LSO_KODERAK").ToString.PadRight(7) & _
        //            " " & dr.Item("PROGRESS").ToString
        // TreeView1.Nodes("GUDANG").Nodes.Add(nod)
        // TreeView1.Nodes("GUDANG").Nodes(dr.Item("LSO_KODERAK").ToString).Nodes.Add("1")

        return $data;
    }

    public function datatables(){
        // strSQL = "SELECT LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT, LSO_FLAGSARANA, PRD_PRDCD, PRD_DESKRIPSIPANJANG, PRD_UNIT, PRD_FRAC, LSO_QTY, LSO_MODIFY_BY, coalesce(ST_AVGCOST, 0) AS ST_AVGCOST "
        // strSQL &= "FROM TBTR_LOKASI_SO LEFT JOIN TBMASTER_STOCK ON LSO_PRDCD = ST_PRDCD AND LSO_LOKASI = ST_LOKASI, TBMASTER_PRODMAST "
        // strSQL &= "WHERE TO_CHAR(LSO_TGLSO, 'DD-MM-YYYY') = '" & Format(dtSO.Rows(0).Item("MSO_TGLSO"), "dd-MM-yyyy").ToString & "' "
        // strSQL &= "AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y' AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR AND PRD_PRDCD LIKE '%0' "
        // If KodeRak <> "" Then
        //     strSQL &= "AND LSO_KODERAK = '" & KodeRak & "' "
        // End If
        // If KodeSubrak <> "" Then
        //     strSQL &= "AND LSO_KODESUBRAK = '" & KodeSubrak & "' "
        // End If
        // If Tiperak <> "" Then
        //     strSQL &= "AND LSO_TIPERAK = '" & Tiperak & "' "
        // End If
        // If Shelvingrak <> "" Then
        //     strSQL &= "AND LSO_SHELVINGRAK = '" & Shelvingrak & "' "
        // End If
        // strSQL &= "ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT ASC"
    }

    // Private Sub PrintStrukSO(ByVal KodeRak As String, ByVal KodeSubrak As String, ByVal Tiperak As String, ByVal Shelvingrak As String)
    //     Dim s As String = ""

    //     Dim strSQL As String
    //     strSQL = "SELECT LSO_LOKASI, LSO_NOURUT, PRD_PRDCD, PRD_DESKRIPSIPENDEK, PRD_UNIT, PRD_FRAC, LSO_QTY, LSO_MODIFY_BY, coalesce(ST_AVGCOST, 0) AS ST_AVGCOST "
    //     strSQL &= "FROM TBTR_LOKASI_SO LEFT JOIN TBMASTER_STOCK ON LSO_PRDCD = ST_PRDCD AND LSO_LOKASI = ST_LOKASI, TBMASTER_PRODMAST "
    //     strSQL &= "WHERE TO_CHAR(LSO_TGLSO, 'DD-MM-YYYY') = '" & Format(dtSO.Rows(0).Item("MSO_TGLSO"), "dd-MM-yyyy").ToString & "' "
    //     strSQL &= "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR AND PRD_PRDCD LIKE '%0' "
    //     strSQL &= "AND LSO_KODERAK = '" & KodeRak & "' "
    //     strSQL &= "AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y' "
    //     strSQL &= "AND LSO_KODESUBRAK = '" & KodeSubrak & "' "
    //     strSQL &= "AND LSO_TIPERAK = '" & Tiperak & "' "
    //     strSQL &= "AND LSO_SHELVINGRAK = '" & Shelvingrak & "' "
    //     strSQL &= "ORDER BY LSO_NOURUT ASC"

    //     Dim dt As New DataTable
    //     dt = QueryOra(strSQL)

    //     For i As Integer = 0 To dt.Rows.Count - 1
    //         If dt.Rows(i).Item("LSO_MODIFY_BY").ToString = "" Then
    //             MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "Ada item yang belum di SO!", "Warning")
    //             Exit Sub
    //         End If
    //     Next

    //     Dim Lokasi As String = ""
    //     If dt.Rows(0).Item("LSO_LOKASI").ToString = "01" Then
    //         Lokasi = "Baik"
    //     ElseIf dt.Rows(0).Item("LSO_LOKASI").ToString = "02" Then
    //         Lokasi = "Retur"
    //     ElseIf dt.Rows(0).Item("LSO_LOKASI").ToString = "03" Then
    //         Lokasi = "Rusak"
    //     End If

    //     If dt.Rows.Count > 0 Then
    //         s &= "========================================" & vbCrLf
    //         s &= StrCenter("LISTING ITEM SO", 40) & vbCrLf
    //         s &= "========================================" & vbCrLf
    //         s &= ("Lokasi      : " & Lokasi).PadRight(40, " ").ToString & vbCrLf
    //         s &= ("User ID     : " & dt.Rows(0).Item("LSO_MODIFY_BY").ToString).PadRight(40, " ").ToString & vbCrLf
    //         s &= ("Kode Rak    : " & KodeRak).PadRight(40, " ").ToString & vbCrLf
    //         s &= ("Kode SubRak : " & KodeSubrak).PadRight(40, " ").ToString & vbCrLf
    //         s &= ("Tipe Rak    : " & Tiperak).PadRight(40, " ").ToString & vbCrLf
    //         s &= ("Shelv. Rak  : " & Shelvingrak).PadRight(40, " ").ToString & vbCrLf
    //         s &= ("Waktu Cetak : " & Format(GetCurrentDate, "dd-MM-yyyy HH:mm:ss")).PadRight(40, " ").ToString & vbCrLf
    //     Else
    //         MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "Tidak ada data yang dicetak", "Warning")
    //         Exit Sub
    //     End If

    //     Try
    //         s &= "----------------------------------------" & vbCrLf
    //         s &= "NO   NAMA BARANG / PLU                  " & vbCrLf
    //         s &= "          UNIT / FRAC      CTN     PCS  " & vbCrLf
    //         s &= "========================================" & vbCrLf

    //         Dim QtyCTN As Double
    //         Dim QtyPCS As Double


    //         For j As Integer = 0 To dt.Rows.Count - 1
    //             QtyCTN = Math.Floor(Val(dt.Rows(j).Item("LSO_QTY").ToString) / Val(dt.Rows(j).Item("PRD_FRAC").ToString))
    //             QtyPCS = Val(dt.Rows(j).Item("LSO_QTY").ToString) Mod Val(dt.Rows(j).Item("PRD_FRAC").ToString)

    //             s &= dt.Rows(j).Item("LSO_NOURUT").ToString.PadLeft(3, " ") & "   " & dt.Rows(j).Item("PRD_DESKRIPSIPENDEK").ToString.PadRight(22, " ") & "(" & dt.Rows(j).Item("PRD_PRDCD").ToString.PadRight(7, " ") & ")   " & vbCrLf
    //             s &= dt.Rows(j).Item("PRD_UNIT").ToString.PadLeft(14, " ") & " / " & FormatNumber(Val(dt.Rows(j).Item("PRD_FRAC").ToString), 0).ToString.PadRight(8, " ") & _
    //                  FormatNumber(QtyCTN, 0).ToString.PadLeft(5, " ") & _
    //                  FormatNumber(QtyPCS, 0).ToString.PadLeft(8, " ") & "  " & vbCrLf
    //         Next

    //         s &= "========================================" & vbCrLf
    //         s &= "                                        " & vbCrLf
    //         s &= "                                        " & vbCrLf
    //         s &= "                                        " & vbCrLf
    //         s &= "                                        " & vbCrLf

    //         s = GetCharacterPrinting(s)

    //         RawPrinterHelper.SendStringToPrinter("PRINTER SONAS", s)
    //     Catch ex As Exception

    //     End Try
    // End Sub
}
