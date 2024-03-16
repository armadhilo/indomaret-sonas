<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\MonitoringRequest;
use App\Http\Requests\ProsesBaSoRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

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
        // $query .= "      AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y' AND LSO_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%' AND (LSO_KODERAK NOT LIKE 'L%' OR LSO_TIPERAK NOT LIKE 'Z%')";
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
        // $query .= "      AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y' AND Lso_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%' AND (LSO_KODERAK  NOT LIKE 'L%' OR LSO_TIPERAK  NOT LIKE 'Z%') ";
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
        // $query .= "      AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y' AND (Lso_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%' OR (LSO_KODERAK  LIKE 'L%' AND LSO_TIPERAK  LIKE 'Z%'))";
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
        // $query .= "      AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y' AND (Lso_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%' OR (LSO_KODERAK  LIKE 'L%' AND LSO_TIPERAK  LIKE 'Z%')) ";
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

    public function datatables(MonitoringRequest $request){
        $query = '';
        $query .= "SELECT LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT, LSO_FLAGSARANA, PRD_PRDCD, PRD_DESKRIPSIPANJANG, PRD_UNIT, PRD_FRAC, LSO_QTY, LSO_MODIFY_BY, coalesce(ST_AVGCOST, 0) AS ST_AVGCOST ";
        $query .= "FROM TBTR_LOKASI_SO LEFT JOIN TBMASTER_STOCK ON LSO_PRDCD = ST_PRDCD AND LSO_LOKASI = ST_LOKASI, TBMASTER_PRODMAST ";
        $query .= "WHERE coalesce(LSO_FLAGLIMIT, 'N') = 'Y' ";
        // $query .= "AND DATE_TRUNC('DAY',LSO_TGLSO) = TO_DATE('" . $request->tanggal_start_so . "', 'YYYY-MM-DD') AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR AND PRD_PRDCD LIKE '%0' ";
        if($request->has('KodeRak')){
            $query .= "AND LSO_KODERAK = '" . $request->KodeRak . "' ";
        }
        if($request->has('KodeSubrak')){
            $query .= "AND LSO_KODESUBRAK = '" . $request->KodeSubrak . "' ";
        }
        if($request->has('Tiperak')){
            $query .= "AND LSO_TIPERAK = '" . $request->Tiperak . "' ";
        }
        if($request->has('Shelvingrak')){
            $query .= "AND LSO_SHELVINGRAK = '" . $request->Shelvingrak . "' ";
        }
        $query .= "ORDER BY LSO_KODERAK, LSO_KODESUBRAK, LSO_TIPERAK, LSO_SHELVINGRAK, LSO_NOURUT ASC ";
        //! dummy
        $query .= "LIMIT 10";
        $data = DB::select($query);

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function printStrukSO($KodeRak, $KodeSubRak, $TipeRak, $ShelvingRak, $tanggal_start_so){
            $query = '';
            $query .= "SELECT LSO_LOKASI, LSO_NOURUT, PRD_PRDCD, PRD_DESKRIPSIPENDEK, PRD_UNIT, PRD_FRAC, LSO_QTY, LSO_MODIFY_BY, coalesce(ST_AVGCOST, 0) AS ST_AVGCOST ";
            $query .= "FROM TBTR_LOKASI_SO LEFT JOIN TBMASTER_STOCK ON LSO_PRDCD = ST_PRDCD AND LSO_LOKASI = ST_LOKASI, TBMASTER_PRODMAST ";
            $query .= "WHERE DATE_TRUNC('DAY',LSO_TGLSO) = TO_DATE('" . $tanggal_start_so . "', 'YYYY-MM-DD') ";
            $query .= "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR AND PRD_PRDCD LIKE '%0' ";
            $query .= "AND LSO_KODERAK = '" . $KodeRak . "' ";
            $query .= "AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y' ";
            $query .= "AND LSO_KODESUBRAK = '" . $KodeSubRak . "' ";
            $query .= "AND LSO_TIPERAK = '" . $TipeRak . "' ";
            $query .= "AND LSO_SHELVINGRAK = '" . $ShelvingRak . "' ";
            $query .= "ORDER BY LSO_NOURUT ASC";
            $data = DB::select($query);

            if(count($data) == 0){
                return ApiFormatter::error(400, 'Tidak ada data yang dicetak');
            }

            $check = collect($data)->where('LSO_MODIFY_BY', '');
            if(count($check)){
                return ApiFormatter::error(400, 'Ada item yang belum di SO!');
            }

            $lokasi = 'Rusak';
            if($data[0]->lso_lokasi == '01'){
                $lokasi = 'Baik';
            }elseif($data[0]->lso_lokasi == '02'){
                $lokasi = 'Retur';
            }

            //! CETAK  .TXT
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

            //         s &= "----------------------------------------" & vbCrLf
            //         s &= "NO   NAMA BARANG / PLU                  " & vbCrLf
            //         s &= "          UNIT / FRAC      CTN     PCS  " & vbCrLf
            //         s &= "========================================" & vbCrLf

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
    }
}
