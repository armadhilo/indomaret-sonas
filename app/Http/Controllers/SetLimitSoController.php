<?php

namespace App\Http\Controllers;

use App\Exports\SetLimitSoExport;
use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\ProsesBaSoRequest;
use App\Imports\SetLimitSoImport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SetLimitSoController extends Controller
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
            $check_error = "SO belum diinitial";
            return view('set-limit-so', compact('check_error'));
        }else{
            if($dtCek[0]->mso_flagsum <> ''){
                $check_error = "SO sudah diproses BA";
                return view('set-limit-so', compact('check_error'));
            }

            if($dtCek[0]->mso_flaglimit <> ''){
                $check_error = "Setting Limit Item untuk tahap ini sudah dilakukan";
                return view('set-limit-so', compact('check_error'));
            }
        }

        $data['tahap'] = $dtCek[0]->mso_flagtahap;
        $data['tglSO'] = Carbon::parse($dtCek[0]->mso_tglso)->format('Y-m-d');

        return view('set-limit-so', $data);
    }

    // public function loadDatatables($tahap, $tglSO){
    //     $query = '';
    //     $query .= "SELECT * FROM ( ";
    //     $query .= "SELECT DISTINCT PLU, DESKRIPSI, LSO_LOKASI, DIVISI, DEPARTEMENT, KATEGORI, AREAGUDANG, AREATOKO, (AREAGUDANG + AREATOKO) AS TOTAL, LPP, ((AREAGUDANG + AREATOKO  ) - LPP) AS SELISIH, ";
    //     $query .= "ABS(((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) AS NILAI_SELISIH_ABS, ";
    //     $query .= "(((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) AS NILAI_SELISIH, LSO_FLAGTAHAP, LSO_CREATE_BY ";
    //     $query .= "FROM (SELECT PRD_AVGCOST, PRD_PRDCD AS PLU, PRD_DESKRIPSIPANJANG AS DESKRIPSI,  ";
    //     $query .= "PRD_KODEDIVISI || ' - ' || DIV_NAMADIVISI AS DIVISI, ";
    //     $query .= "PRD_KODEDEPARTEMENT || ' - ' || DEP_NAMADEPARTEMENT AS DEPARTEMENT, ";
    //     $query .= "PRD_KODEKATEGORIBARANG || ' - ' || KAT_NAMAKATEGORI AS KATEGORI, ";
    //     $query .= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" . $tahap . "' AND LSO_TGLSO = TO_DATE('" . $tglSO . "','YYYY-MM-DD') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%') AND LSO_LOKASI = SO.LSO_LOKASI) AS AREAGUDANG, ";
    //     $query .= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" . $tahap . "' AND LSO_TGLSO = TO_DATE('" . $tglSO . "','YYYY-MM-DD') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%') AND LSO_LOKASI = SO.LSO_LOKASI) AS AREATOKO,  ";
    //     $query .= "(LSO_ST_SALDOAKHIR) AS LPP, LSO_LOKASI, LSO_FLAGTAHAP, LSO_CREATE_BY, LSO_AVGCOST ";
    //     $query .= "FROM TBMASTER_PRODMAST, tbhistory_lhso_sonas SO, tbmaster_divisi, tbmaster_departement, tbmaster_kategori ";
    //     $query .= "WHERE LSO_TGLSO = TO_DATE('" . $tglSO . "','YYYY-MM-DD') ";
    //     $query .= "AND LSO_LOKASI = '01' ";
    //     $query .= "AND LSO_PRDCD = PRD_PRDCD ";
    //     $query .= "AND LSO_FLAGTAHAP = '" . $tahap . "' ";
    //     $query .= "and prd_kodedivisi = div_kodedivisi ";
    //     $query .= "and prd_kodedivisi = dep_kodedivisi ";
    //     $query .= "and prd_kodedepartement = dep_kodedepartement ";
    //     $query .= "and prd_kodedepartement = kat_kodedepartement ";
    //     $query .= "and prd_kodekategoribarang = kat_kodekategori ";
    //     $query .= ") DATAS";
    //     $query .= " WHERE (((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) <> 0 ORDER BY LSO_LOKASI ASC, NILAI_SELISIH_ABS DESC ";
    //     $query .= ") DATAS1";
    //     $data = DB::select($query);

    //     return DataTables::of($data)
    //         ->addIndexColumn()
    //         ->make(true);
    // }

    //!! DUMMY DATA
    public function loadDatatables($tahap, $tglSO){

        $query = '';
        $query .= "SELECT * FROM ( ";
        $query .= "SELECT DISTINCT PLU, DESKRIPSI, LSO_LOKASI, DIVISI, DEPARTEMENT, KATEGORI, AREAGUDANG, AREATOKO, (AREAGUDANG + AREATOKO) AS TOTAL, LPP, ((AREAGUDANG + AREATOKO  ) - LPP) AS SELISIH, ";
        $query .= "ABS(((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) AS NILAI_SELISIH_ABS, ";
        $query .= "(((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) AS NILAI_SELISIH, LSO_FLAGTAHAP, LSO_CREATE_BY ";
        $query .= "FROM (SELECT PRD_AVGCOST, PRD_PRDCD AS PLU, PRD_DESKRIPSIPANJANG AS DESKRIPSI,  ";
        $query .= "PRD_KODEDIVISI || ' - ' || DIV_NAMADIVISI AS DIVISI, ";
        $query .= "PRD_KODEDEPARTEMENT || ' - ' || DEP_NAMADEPARTEMENT AS DEPARTEMENT, ";
        $query .= "PRD_KODEKATEGORIBARANG || ' - ' || KAT_NAMAKATEGORI AS KATEGORI, ";
        // $query .= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" . $tahap . "' AND LSO_TGLSO = TO_DATE('" . $tglSO . "','YYYY-MM-DD') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%') AND LSO_LOKASI = SO.LSO_LOKASI) AS AREAGUDANG, ";
        // $query .= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" . $tahap . "' AND LSO_TGLSO = TO_DATE('" . $tglSO . "','YYYY-MM-DD') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%') AND LSO_LOKASI = SO.LSO_LOKASI) AS AREATOKO,  ";
        //! dummy
        $query .= "0 AS AREAGUDANG, ";
        $query .= "0 AS AREATOKO,  ";

        $query .= "(LSO_ST_SALDOAKHIR) AS LPP, LSO_LOKASI, LSO_FLAGTAHAP, LSO_CREATE_BY, LSO_AVGCOST ";
        $query .= "FROM TBMASTER_PRODMAST, tbhistory_lhso_sonas SO, tbmaster_divisi, tbmaster_departement, tbmaster_kategori ";
        $query .= "WHERE LSO_LOKASI = '01' ";
        // $query .= "AND LSO_TGLSO = TO_DATE('" . $tglSO . "','YYYY-MM-DD') ";
        $query .= "AND LSO_PRDCD = PRD_PRDCD ";
        // $query .= "AND LSO_FLAGTAHAP = '" . $tahap . "' ";
        $query .= "and prd_kodedivisi = div_kodedivisi ";
        $query .= "and prd_kodedivisi = dep_kodedivisi ";
        $query .= "and prd_kodedepartement = dep_kodedepartement ";
        $query .= "and prd_kodedepartement = kat_kodedepartement ";
        $query .= "and prd_kodekategoribarang = kat_kodekategori ";
        $query .= ") DATAS";
        // $query .= " WHERE (((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) <> 0 ORDER BY LSO_LOKASI ASC, NILAI_SELISIH_ABS DESC ";
        $query .= ") DATAS1 ";
        //! dummy
        $query .= 'LIMIT 10';
        $data = DB::select($query);

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    //! CREATE EXCEL
    public function downloadDataExcel($tahap, $tglSO){
        $query = '';
        $query .= "SELECT * FROM ( ";
        $query .= "SELECT DISTINCT PLU, DESKRIPSI, LSO_LOKASI, DIVISI, DEPARTEMENT, KATEGORI, AREAGUDANG, AREATOKO, (AREAGUDANG + AREATOKO) AS TOTAL, LPP, ((AREAGUDANG + AREATOKO  ) - LPP) AS SELISIH, ";
        $query .= "ABS(((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) AS NILAI_SELISIH_ABS, ";
        $query .= "(((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) AS NILAI_SELISIH, LSO_FLAGTAHAP, LSO_CREATE_BY ";
        $query .= "FROM (SELECT PRD_AVGCOST, PRD_PRDCD AS PLU, PRD_DESKRIPSIPANJANG AS DESKRIPSI,  ";
        $query .= "PRD_KODEDIVISI || ' - ' || DIV_NAMADIVISI AS DIVISI, ";
        $query .= "PRD_KODEDEPARTEMENT || ' - ' || DEP_NAMADEPARTEMENT AS DEPARTEMENT, ";
        $query .= "PRD_KODEKATEGORIBARANG || ' - ' || KAT_NAMAKATEGORI AS KATEGORI, ";
        // $query .= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" . $tahap . "' AND LSO_TGLSO = TO_DATE('" . $tglSO . "','YYYY-MM-DD') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%') AND LSO_LOKASI = SO.LSO_LOKASI) AS AREAGUDANG, ";
        // $query .= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" . $tahap . "' AND LSO_TGLSO = TO_DATE('" . $tglSO . "','YYYY-MM-DD') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%') AND LSO_LOKASI = SO.LSO_LOKASI) AS AREATOKO,  ";
        //! dummy
        $query .= "0 AS AREAGUDANG, ";
        $query .= "0 AS AREATOKO,  ";

        $query .= "(LSO_ST_SALDOAKHIR) AS LPP, LSO_LOKASI, LSO_FLAGTAHAP, LSO_CREATE_BY, LSO_AVGCOST ";
        $query .= "FROM TBMASTER_PRODMAST, tbhistory_lhso_sonas SO, tbmaster_divisi, tbmaster_departement, tbmaster_kategori ";
        $query .= "WHERE LSO_LOKASI = '01' ";
        // $query .= "AND LSO_TGLSO = TO_DATE('" . $tglSO . "','YYYY-MM-DD') ";
        $query .= "AND LSO_PRDCD = PRD_PRDCD ";
        // $query .= "AND LSO_FLAGTAHAP = '" . $tahap . "' ";
        $query .= "and prd_kodedivisi = div_kodedivisi ";
        $query .= "and prd_kodedivisi = dep_kodedivisi ";
        $query .= "and prd_kodedepartement = dep_kodedepartement ";
        $query .= "and prd_kodedepartement = kat_kodedepartement ";
        $query .= "and prd_kodekategoribarang = kat_kodekategori ";
        $query .= ") DATAS";
        // $query .= " WHERE (((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) <> 0 ORDER BY LSO_LOKASI ASC, NILAI_SELISIH_ABS DESC ";
        $query .= ") DATAS1 ";
        //! dummy
        $query .= 'LIMIT 10';

        $data['data'] = DB::select($query);

        $fileContent = Excel::raw(new SetLimitSoExport($data), \Maatwebsite\Excel\Excel::XLSX);

        $excelFileName = 'SET_LIMIT_SO.xlsx'; // Set your desired filename here
        $encodedFileName = rawurlencode($excelFileName);

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $encodedFileName . '"',
        ];

        return response($fileContent, 200, $headers);
    }

    public function uploadDataExcel(Request $request){
        $file = $request->file('excel_file');

        $import = new SetLimitSoImport();
        Excel::import($import, $file);
        $data = $import->data;

        //! LOOP DATA DATA DARI EXCEL
        $array = [];
        foreach($data as $item){
            if($item["plu"] != ''){
                $array[] = $item["plu"] .'-'. $item["lokasi"];
            }
        }
        //! END LOOP DATA

        DB::table('tbtr_lokasi_so')
            ->whereIn(DB::raw("LSO_PRDCD || '-' || LSO_LOKASI"), $array)
            ->where('lso_lokasi', '01')
            ->whereRaw("DATE_TRUNC('DAY',lso_tglso) = TO_DATE('" . $request->tglSO . "','YYYY-MM-DD')")
            ->update([
                'LSO_FLAGLIMIT' => 'Y',
            ]);

        return ApiFormatter::success(200, "Upload Excel Berhasil");
    }
}
