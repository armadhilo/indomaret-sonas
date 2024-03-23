<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\MonitoringRequest;
use App\Http\Requests\ProsesBaSoRequest;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
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

        // if(count($dtCek) == 0){
        //     return ApiFormatter::error(400, 'SO BELUM DI-INITIAL');
        // }else{
        //     if($dtCek[0]->mso_flaglimit == ''){
        //         return ApiFormatter::error(400, 'Setting limit item untuk tahap ini belum disetting');
        //     }
        // }


        return view('monitoring-so');
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
        $data['toko'] = collect(DB::select($query))->last();
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
        // $data['detail_toko'] = DB::select($query);
        $detail_toko = DB::select($query);

        $array = [];
        foreach($detail_toko as $item){
           $array[] = [
               'lso_koderak' => $item->lso_koderak,
               'progress' => $item->progress,
               'koderak' => $item->lso_koderak,
               'data_subrak' => $this->recursive(1, $item->lso_koderak)
           ];
        }

        $data['detail_toko'] = $array;


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
        $data['gudang'] = collect(DB::select($query))->last();

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
        // $data['detail_gudang'] = DB::select($query);

        $detail_gudang = DB::select($query);

        $array = [];
        foreach($detail_gudang as $item){
           $array[] = [
               'lso_koderak' => $item->lso_koderak,
               'progress' => $item->progress,
               'koderak' => $item->lso_koderak,
               'data_subrak' => $this->recursive(1, $item->lso_koderak)
           ];
        }

        $data['detail_gudang'] = $array;

        // nod.Name = dr.Item("LSO_KODERAK").ToString
        // nod.Text = dr.Item("LSO_KODERAK").ToString.PadRight(7) & _
        //            " " & dr.Item("PROGRESS").ToString
        // TreeView1.Nodes("GUDANG").Nodes.Add(nod)
        // TreeView1.Nodes("GUDANG").Nodes(dr.Item("LSO_KODERAK").ToString).Nodes.Add("1")

        return $data;
    }

    public function showLevel($lso_koderak, $lso_kodesubrak = null, $lso_tiperak = null){
        $query = '';
        if($lso_tiperak != null) $query .= "select distinct lso_shelvingrak ";
        elseif($lso_kodesubrak != null) $query .= "select distinct lso_tiperak ";
        else $query .= "select distinct lso_kodesubrak ";

        $query .= "FROM tbtr_lokasi_so ";
        $query .= "JOIN tbmaster_setting_so on lso_tglso = mso_tglso ";
        // $query .= "WHERE mso_flagreset is null ";
        // $query .= "AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y' ";
        $query .= "WHERE LSO_KODERAK = '" . $lso_koderak . "' ";
        if($lso_kodesubrak != null) $query .= "AND LSO_KODESUBRAK = '" . $lso_kodesubrak . "' ";
        if($lso_tiperak != null)  $query .= "AND LSO_TIPERAK = '" . $lso_tiperak . "' ";

        if($lso_tiperak != null) $query .= "ORDER BY lso_shelvingrak ";
        elseif($lso_kodesubrak != null) $query .= "ORDER BY lso_tiperak ";
        else $query .= "ORDER BY lso_kodesubrak ";

        $data = DB::select($query);

        return $data;

        return ApiFormatter::success(200, 'Show level berhasil ditampilkan', $data);
    }

    private function recursive($level, $lso_koderak = null, $lso_kodesubrak = null, $lso_tiperak = null){
        $array = [];
        if($level == 1){
            $data = $this->showLevel($lso_koderak);
            foreach($data as $item){
                $array[] = [
                    'subrak' => $item->lso_kodesubrak,
                    'data_tiperak' => $this->recursive(2, $lso_koderak, $item->lso_kodesubrak)
                ];
            }
        }elseif($level == 2){
            $data = $this->showLevel($lso_koderak, $lso_kodesubrak);
            foreach($data as $item){
                $array[] = [
                    'tiperak' => $item->lso_tiperak,
                    'data_shelving' => $this->recursive(3, $lso_koderak, $lso_kodesubrak, $item->lso_tiperak)
                ];
            }
        }elseif($level == 3){
            $data = $this->showLevel($lso_koderak, $lso_kodesubrak, $lso_tiperak);
            foreach($data as $item){
                $array[] = [
                    'shelving' => $item->lso_shelvingrak,
                ];
            }
        }

        return $array;
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

    private function privateShowLevel($lso_koderak, $lso_kodesubrak = null, $lso_tiperak = null){
        $query = '';
        if($lso_tiperak != null) $query .= "select distinct lso_shelvingrak ";
        elseif($lso_kodesubrak != null) $query .= "select distinct lso_tiperak ";
        else $query .= "select distinct lso_kodesubrak ";

        $query .= "FROM tbtr_lokasi_so, tbmaster_setting_so ";
        // $query .= "WHERE lso_tglso = mso_tglso and mso_flagreset is null ";
        // $query .= "AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y' ";
        $query .= "WHERE LSO_KODERAK = '" . $lso_koderak . "' ";
        if($lso_kodesubrak != null) $query .= "AND LSO_KODESUBRAK = '" . $lso_kodesubrak . "' ";
        if($lso_tiperak != null)  $query .= "AND LSO_TIPERAK = '" . $lso_tiperak . "' ";

        if($lso_tiperak != null) $query .= "ORDER BY lso_shelvingrak ";
        elseif($lso_kodesubrak != null) $query .= "ORDER BY lso_tiperak ";
        else $query .= "ORDER BY lso_kodesubrak ";

        $data = DB::select($query);

        return ApiFormatter::success(200, 'Level 4 berhasil ditampilkan', $data);
    }

    public function printStrukSO($tanggal_start_so, $KodeRak, $KodeSubRak, $TipeRak = null, $ShelvingRak = null){
        if($TipeRak == null){
            $data = $this->privateShowLevel($KodeRak, $KodeSubRak);
            foreach($data as $item){
                $data2 = $this->privateShowLevel($KodeRak, $KodeSubRak, $item->lso_tiperak);
                foreach($data2 as $item2){
                    $this->generateTxt($tanggal_start_so, $KodeRak, $KodeSubRak, $item->lso_tiperak, $item2->lso_shelvingrak);
                }
            }
        }elseif($ShelvingRak == null){
            $data2 = $this->privateShowLevel($KodeRak, $KodeSubRak, $TipeRak);
            foreach($data2 as $item2){
                $this->generateTxt($tanggal_start_so, $KodeRak, $KodeSubRak, $TipeRak, $item2->lso_shelvingrak);
            }
        }else{
            $this->generateTxt($tanggal_start_so, $KodeRak, $KodeSubRak, $TipeRak, $ShelvingRak);
        }
    }

    private function generateTxt($tanggal_start_so, $KodeRak, $KodeSubRak, $TipeRak, $ShelvingRak){
            $query = '';
            $query .= "SELECT LSO_LOKASI, LSO_NOURUT, PRD_PRDCD, PRD_DESKRIPSIPENDEK, PRD_UNIT, PRD_FRAC, LSO_QTY, LSO_MODIFY_BY, coalesce(ST_AVGCOST, 0) AS ST_AVGCOST ";
            $query .= "FROM TBTR_LOKASI_SO LEFT JOIN TBMASTER_STOCK ON LSO_PRDCD = ST_PRDCD AND LSO_LOKASI = ST_LOKASI, TBMASTER_PRODMAST ";
            // $query .= "WHERE DATE_TRUNC('DAY',LSO_TGLSO) = TO_DATE('" . $tanggal_start_so . "', 'YYYY-MM-DD') ";
            // $query .= "AND LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR AND PRD_PRDCD LIKE '%0' ";
            // $query .= "AND LSO_KODERAK = '" . $KodeRak . "' ";
            // $query .= "AND coalesce(LSO_FLAGLIMIT, 'N') = 'Y' ";
            // $query .= "AND LSO_KODESUBRAK = '" . $KodeSubRak . "' ";
            // $query .= "AND LSO_TIPERAK = '" . $TipeRak . "' ";
            // $query .= "AND LSO_SHELVINGRAK = '" . $ShelvingRak . "' ";
            $query .= "ORDER BY LSO_NOURUT ASC ";
            //! dummy
            $query .= 'LIMIT 10';
            $data = DB::select($query);

            return $data;

            if(count($data) == 0){
                throw new HttpResponseException(ApiFormatter::error(404, "Tidak ada data yang dicetak [ $KodeRak | $KodeSubRak | $TipeRak | $ShelvingRak ]"));
            }

            //! dummy
            $check = collect($data)->where('LSO_MODIFY_BY', '');
            if(count($check)){
                throw new HttpResponseException(ApiFormatter::error(404, "Ada item yang belum di SO! [ $KodeRak | $KodeSubRak | $TipeRak | $ShelvingRak ]"));
            }

            $lokasi = 'Rusak';
            if($data[0]->lso_lokasi == '01'){
                $lokasi = 'Baik';
            }elseif($data[0]->lso_lokasi == '02'){
                $lokasi = 'Retur';
            }

            //! CETAK  .TXT
            //! INI NEK BISA DI JADIKAN .ZIP AJA
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
