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
use ZipArchive;
use Illuminate\Support\Facades\File;

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

        $data['tgl_so'] = Carbon::parse($dtCek[0]->mso_tglso)->format('Y-m-d');

        if(count($dtCek) == 0){
            $data['check_error'] = "SO belum diinitial";
            return view('monitoring-so', $data);
        }else{
            if($dtCek[0]->mso_flaglimit == ''){
                $data['check_error'] = "Setting limit item untuk tahap ini belum disetting";
                return view('monitoring-so', $data);
            }
        }


        return view('monitoring-so', $data);
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
        if($request->has('KodeRak') && $request->KodeRak !== null){
            $query .= "AND LSO_KODERAK = '" . $request->KodeRak . "' ";
        }
        if($request->has('KodeSubrak') && $request->KodeRak !== null){
            $query .= "AND LSO_KODESUBRAK = '" . $request->KodeSubrak . "' ";
        }
        if($request->has('Tiperak') && $request->KodeRak !== null){
            $query .= "AND LSO_TIPERAK = '" . $request->Tiperak . "' ";
        }
        if($request->has('Shelvingrak') && $request->KodeRak !== null){
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
        return $data;
        // return ApiFormatter::success(200, 'Level 4 berhasil ditampilkan', $data);
    }

    public function printStrukSO($tanggal_start_so, $KodeRak, $KodeSubRak, $TipeRak = null, $ShelvingRak = null){
        $tempDir = storage_path('temp_txt');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir);
        }

        $files = [];
        if ($TipeRak === null) {
            $data = $this->privateShowLevel($KodeRak, $KodeSubRak);
            foreach ($data as $item) {
                $data2 = $this->privateShowLevel($KodeRak, $KodeSubRak, $item->lso_tiperak);
                foreach ($data2 as $item2) {
                    $files[] = $this->generateTxt($tanggal_start_so, $KodeRak, $KodeSubRak, $item->lso_tiperak, $item2->lso_shelvingrak, $tempDir);
                }
            }
        } elseif ($ShelvingRak === null) {
            $data2 = $this->privateShowLevel($KodeRak, $KodeSubRak, $TipeRak);
            foreach ($data2 as $item2) {
                $files[] = $this->generateTxt($tanggal_start_so, $KodeRak, $KodeSubRak, $TipeRak, $item2->lso_shelvingrak, $tempDir);
            }
        } else {
            $files[] = $this->generateTxt($tanggal_start_so, $KodeRak, $KodeSubRak, $TipeRak, $ShelvingRak, $tempDir);
        }

        foreach ($files as $value) {
            if ($value['status'] === 0 && $value['status'] !== 1) {
                return ApiFormatter::error(400, $value['file']);
            }
        }


        $zipFile = storage_path('MONITORING SO.zip');
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                $zip->addFile($file['file'], basename($file['file']));
            }
            $zip->close();
        }

        File::deleteDirectory($tempDir);

        return response()->download($zipFile, 'MONITORING_SO.zip')->deleteFileAfterSend();
        
        // if($TipeRak == null){
        //     $data = $this->privateShowLevel($KodeRak, $KodeSubRak);
        //     foreach($data as $item){
        //         $data2 = $this->privateShowLevel($KodeRak, $KodeSubRak, $item->lso_tiperak);
        //         foreach($data2 as $item2){
        //             $this->generateTxt($tanggal_start_so, $KodeRak, $KodeSubRak, $item->lso_tiperak, $item2->lso_shelvingrak);
        //         }
        //     }
        // }elseif($ShelvingRak == null){
        //     $data2 = $this->privateShowLevel($KodeRak, $KodeSubRak, $TipeRak);
        //     foreach($data2 as $item2){
        //         $this->generateTxt($tanggal_start_so, $KodeRak, $KodeSubRak, $TipeRak, $item2->lso_shelvingrak);
        //     }
        // }else{
        //     $this->generateTxt($tanggal_start_so, $KodeRak, $KodeSubRak, $TipeRak, $ShelvingRak);
        // }
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

            if(count($data) == 0){
                return [
                    "status" => 0,
                    "file" => "Tidak ada data yang dicetak [ $KodeRak | $KodeSubRak | $TipeRak | $ShelvingRak ]"
                ];
            }

            //! dummy
            $check = collect($data)->where('LSO_MODIFY_BY', '');
            if(count($check)){
                return [
                    "status" => 0,
                    "file" => "Ada item yang belum di SO! [ $KodeRak | $KodeSubRak | $TipeRak | $ShelvingRak ]"
                ];
            }

            $lokasi = 'Rusak';
            if($data[0]->lso_lokasi == '01'){
                $lokasi = 'Baik';
            }elseif($data[0]->lso_lokasi == '02'){
                $lokasi = 'Retur';
            }

            $s = "========================================" . PHP_EOL;
            $s .= str_pad("LISTING ITEM SO", 40, " ", STR_PAD_BOTH) . PHP_EOL;
            $s .= "========================================" . PHP_EOL;
            $s .= str_pad("Lokasi      : " . $lokasi, 40, " ", STR_PAD_RIGHT) . PHP_EOL;
            $s .= str_pad("User ID     : " . session('userid'), 40, " ", STR_PAD_RIGHT) . PHP_EOL;
            $s .= str_pad("Kode Rak    : " . $KodeRak, 40, " ", STR_PAD_RIGHT) . PHP_EOL;
            $s .= str_pad("Kode SubRak : " . $KodeSubRak, 40, " ", STR_PAD_RIGHT) . PHP_EOL;
            $s .= str_pad("Tipe Rak    : " . $TipeRak, 40, " ", STR_PAD_RIGHT) . PHP_EOL;
            $s .= str_pad("Shelv. Rak  : " . $ShelvingRak, 40, " ", STR_PAD_RIGHT) . PHP_EOL;
            $s .= str_pad("Waktu Cetak : " . date("d-m-Y H:i:s"), 40, " ", STR_PAD_RIGHT) . PHP_EOL;

            $s .= "----------------------------------------" . PHP_EOL;
            $s .= "NO   NAMA BARANG / PLU                  " . PHP_EOL;
            $s .= "          UNIT / FRAC      CTN     PCS  " . PHP_EOL;
            $s .= "========================================" . PHP_EOL;

            foreach ($data as $row) {
                $QtyCTN = floor($row->lso_qty / $row->prd_frac);
                $QtyPCS = $row->lso_qty % $row->prd_frac;

                $s .= str_pad($row->lso_nourut, 3, " ", STR_PAD_LEFT) . "   " .
                    str_pad(substr($row->prd_deskripsipendek, 0, 22) . " (" . $row->prd_prdcd . ")", 40, " ", STR_PAD_RIGHT) . PHP_EOL;
                $s .= str_pad($row->prd_unit . " / " . number_format($row->prd_frac, 0), 14, " ", STR_PAD_LEFT) . " / " .
                    str_pad(number_format($QtyCTN, 0), 5, " ", STR_PAD_LEFT) .
                    str_pad(number_format($QtyPCS, 0), 8, " ", STR_PAD_LEFT) . "  " . PHP_EOL;
            }

            $s .= "========================================" . PHP_EOL;
            $s .= "                                        " . PHP_EOL;
            $s .= "                                        " . PHP_EOL;
            $s .= "                                        " . PHP_EOL;
            $s .= "                                        " . PHP_EOL;

            // Set the file name
            $filename = 'PRINT_MONITORING_SO_' . $KodeRak . '-' . $KodeSubRak . '-' . $TipeRak . '-' . $ShelvingRak .'.txt';

            // Set the file path
            $filePath = storage_path('temp_txt/' . $filename);

            // Write the content to the file
            file_put_contents($filePath, $s);

            // Return the file path

            return [
                "status" => 1,
                "file" => $filePath
            ];

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
