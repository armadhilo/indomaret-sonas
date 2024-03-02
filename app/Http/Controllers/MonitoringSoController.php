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
        // sql = "select ROUND((total_so / total_lokasi) * 100, 2) "
        // sql += "|| '%' || '  (' || total_so || '/' || total_lokasi || ')' progress "
        // sql += "FROM (SELECT   lso_koderak, "
        // sql += "COUNT(1) total_lokasi, "
        // sql += "SUM(CASE WHEN lso_modify_by IS NULL THEN 0 ELSE 1 END) total_so "
        // sql += "FROM tbtr_lokasi_so, tbmaster_setting_so "
        // sql += "WHERE lso_tglso = mso_tglso    "
        // sql += "AND Lso_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%') "
        //* _toko = dr.Item("PROGRESS").ToString

        //! DETAIL TOKO
        // sql = "select lso_koderak, ROUND((total_so / total_lokasi) * 100, 2) "
        // sql += "|| '%' || '  (' || nvl(total_so,0) || '/' || total_lokasi || ')' progress "
        // sql += "FROM (SELECT   lso_koderak, "
        // sql += "COUNT(1) total_lokasi, "
        // sql += "SUM(CASE WHEN lso_modify_by IS NULL THEN 0 ELSE 1 END) total_so "
        // sql += "FROM tbtr_lokasi_so, tbmaster_setting_so "
        // sql += "WHERE lso_tglso = mso_tglso    "
        // sql += "AND Lso_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%' "
        // sql += "GROUP BY lso_koderak "
        // sql += "ORDER BY lso_koderak) "
        //* nod.Name = dr.Item("LSO_KODERAK").ToString
        //*         nod.Text = dr.Item("LSO_KODERAK").ToString.PadRight(7) & _
        //*                     " " & dr.Item("PROGRESS").ToString

        //! GUDANG
        // sql = "select ROUND((total_so / total_lokasi) * 100, 2) "
        // sql += "|| '%' || '  (' || total_so || '/' || total_lokasi || ')' progress "
        // sql += "FROM (SELECT   lso_koderak, "
        // sql += "COUNT(1) total_lokasi, "
        // sql += "SUM(CASE WHEN lso_modify_by IS NULL THEN 0 ELSE 1 END) total_so "
        // sql += "FROM tbtr_lokasi_so, tbmaster_setting_so "
        // sql += "WHERE lso_tglso = mso_tglso    "
        // sql += "AND Lso_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%') "
        //* _toko = dr.Item("PROGRESS").ToString


        //! DETAIL GUDANG
        // sql = "select lso_koderak, ROUND((total_so / total_lokasi) * 100, 2) "
        // sql += "|| '%' || '  (' || total_so || '/' || total_lokasi || ')' progress "
        // sql += "FROM (SELECT   lso_koderak, "
        // sql += "COUNT(1) total_lokasi, "
        // sql += "SUM(CASE WHEN lso_modify_by IS NULL THEN 0 ELSE 1 END) total_so "
        // sql += "FROM tbtr_lokasi_so, tbmaster_setting_so "
        // sql += "WHERE lso_tglso = mso_tglso   "
        // sql += "AND Lso_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%' "
        // sql += "GROUP BY lso_koderak "
        // sql += "ORDER BY lso_koderak) "
        //* nod.Name = dr.Item("LSO_KODERAK").ToString
        //* nod.Text = dr.Item("LSO_KODERAK").ToString.PadRight(7) & _
        //*            " " & dr.Item("PROGRESS").ToString
    }
}
