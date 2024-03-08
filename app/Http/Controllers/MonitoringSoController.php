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
        $query .= "select ROUND((total_so / total_lokasi) * 100, 2) ";
        $query .= "|| '%' || '  (' || total_so || '/' || total_lokasi || ')' progress ";
        $query .= "FROM (SELECT   lso_koderak, ";
        $query .= "COUNT(1) total_lokasi, ";
        $query .= "SUM(CASE WHEN lso_modify_by IS NULL THEN 0 ELSE 1 END) total_so ";
        $query .= "FROM tbtr_lokasi_so, tbmaster_setting_so ";
        $query .= "WHERE lso_tglso = mso_tglso    ";
        $query .= "AND Lso_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%') ";
        $data['toko'] = DB::select($query);
        //* _toko = dr.Item("PROGRESS").ToString

        //! DETAIL TOKO
        $query = '';
        $query .= "select lso_koderak, ROUND((total_so / total_lokasi) * 100, 2) ";
        $query .= "|| '%' || '  (' || nvl(total_so,0) || '/' || total_lokasi || ')' progress ";
        $query .= "FROM (SELECT   lso_koderak, ";
        $query .= "COUNT(1) total_lokasi, ";
        $query .= "SUM(CASE WHEN lso_modify_by IS NULL THEN 0 ELSE 1 END) total_so ";
        $query .= "FROM tbtr_lokasi_so, tbmaster_setting_so ";
        $query .= "WHERE lso_tglso = mso_tglso    ";
        $query .= "AND Lso_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%' ";
        $query .= "GROUP BY lso_koderak ";
        $query .= "ORDER BY lso_koderak) ";
        $data['detail_toko'] = DB::select($query);
        //* nod.Name = dr.Item("LSO_KODERAK").ToString
        //*         nod.Text = dr.Item("LSO_KODERAK").ToString.PadRight(7) & _
        //*                     " " & dr.Item("PROGRESS").ToString

        //! GUDANG
        $query = '';
        $query .= "select ROUND((total_so / total_lokasi) * 100, 2) ";
        $query .= "|| '%' || '  (' || total_so || '/' || total_lokasi || ')' progress ";
        $query .= "FROM (SELECT   lso_koderak, ";
        $query .= "COUNT(1) total_lokasi, ";
        $query .= "SUM(CASE WHEN lso_modify_by IS NULL THEN 0 ELSE 1 END) total_so ";
        $query .= "FROM tbtr_lokasi_so, tbmaster_setting_so ";
        $query .= "WHERE lso_tglso = mso_tglso    ";
        $query .= "AND Lso_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%') ";
        $data['gudang'] = DB::select($query);

        //* _toko = dr.Item("PROGRESS").ToString


        //! DETAIL GUDANG
        $query = '';
        $query .= "select lso_koderak, ROUND((total_so / total_lokasi) * 100, 2) ";
        $query .= "|| '%' || '  (' || total_so || '/' || total_lokasi || ')' progress ";
        $query .= "FROM (SELECT   lso_koderak, ";
        $query .= "COUNT(1) total_lokasi, ";
        $query .= "SUM(CASE WHEN lso_modify_by IS NULL THEN 0 ELSE 1 END) total_so ";
        $query .= "FROM tbtr_lokasi_so, tbmaster_setting_so ";
        $query .= "WHERE lso_tglso = mso_tglso   ";
        $query .= "AND Lso_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%' ";
        $query .= "GROUP BY lso_koderak ";
        $query .= "ORDER BY lso_koderak) ";
        $data['detail_gudang'] = DB::select($query);

        //* nod.Name = dr.Item("LSO_KODERAK").ToString
        //* nod.Text = dr.Item("LSO_KODERAK").ToString.PadRight(7) & _
        //*            " " & dr.Item("PROGRESS").ToString

        return $data;
    }
}
