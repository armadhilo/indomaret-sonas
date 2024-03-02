<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\ProsesBaSoRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            return ApiFormatter::error(400, 'SO BELUM DI-INITIAL');
        }else{
            if($dtCek[0]->mso_flagsum <> ''){
                return ApiFormatter::error(400, 'SO sudah diproses BA');
            }

            if($dtCek[0]->mso_flaglimit <> ''){
                return ApiFormatter::error(400, 'Setting Limit Item untuk tahap ini sudah dilakukan');
            }
        }

        $data['tglSo'] = $dtCek[0]->MSO_TGLSO;

        return view('proses-ba-so');
    }

    public function loadDatatables(){
        // Str = "SELECT * FROM ("
        // Str &= "SELECT DISTINCT PLU, DESKRIPSI, LSO_LOKASI, DIVISI, DEPARTEMENT, KATEGORI, AREAGUDANG, AREATOKO, (AREAGUDANG + AREATOKO) AS TOTAL, LPP, ((AREAGUDANG + AREATOKO  ) - LPP) AS SELISIH, "
        // Str &= "ABS(((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) AS NILAI_SELISIH_ABS, "
        // Str &= "(((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) AS NILAI_SELISIH, LSO_FLAGTAHAP, LSO_CREATE_BY "
        // Str &= "FROM (SELECT PRD_AVGCOST, PRD_PRDCD AS PLU, PRD_DESKRIPSIPANJANG AS DESKRIPSI,  "
        // Str &= "PRD_KODEDIVISI || ' - ' || DIV_NAMADIVISI AS DIVISI, "
        // Str &= "PRD_KODEDEPARTEMENT || ' - ' || DEP_NAMADEPARTEMENT AS DEPARTEMENT, "
        // Str &= "PRD_KODEKATEGORIBARANG || ' - ' || KAT_NAMAKATEGORI AS KATEGORI, "
        // Str &= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" & tahap & "' AND LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%') AND LSO_LOKASI = SO.LSO_LOKASI) AS AREAGUDANG, "
        // Str &= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" & tahap & "' AND LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%') AND LSO_LOKASI = SO.LSO_LOKASI) AS AREATOKO,  "
        // Str &= "(LSO_ST_SALDOAKHIR) AS LPP, LSO_LOKASI, LSO_FLAGTAHAP, LSO_CREATE_BY, LSO_AVGCOST "
        // Str &= "FROM TBMASTER_PRODMAST, tbhistory_lhso_sonas SO, tbmaster_divisi, tbmaster_departement, tbmaster_kategori "
        // Str &= "WHERE LSO_TGLSO = TO_DATE('" & Format(tglSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') "
        // Str &= "AND LSO_LOKASI = '01' "
        // Str &= "AND LSO_PRDCD = PRD_PRDCD "
        // Str &= "AND LSO_FLAGTAHAP = '" & tahap & "' "
        // Str &= "and prd_kodedivisi = div_kodedivisi "
        // Str &= "and prd_kodedivisi = dep_kodedivisi "
        // Str &= "and prd_kodedepartement = dep_kodedepartement "
        // Str &= "and prd_kodedepartement = kat_kodedepartement "
        // Str &= "and prd_kodekategoribarang = kat_kodekategori "
        // Str &= ") DATAS"
        // Str &= " WHERE (((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) <> 0 ORDER BY LSO_LOKASI ASC, NILAI_SELISIH_ABS DESC "
        // Str &= ") DATAS1"
    }

    public function loadDataExcel(){
        // Str = "SELECT * FROM ("
        // Str &= "SELECT DISTINCT PLU, DESKRIPSI, LSO_LOKASI, DIVISI, DEPARTEMENT, KATEGORI, AREAGUDANG, AREATOKO, (AREAGUDANG + AREATOKO) AS TOTAL, LPP, ((AREAGUDANG + AREATOKO  ) - LPP) AS SELISIH, "
        // Str &= "ABS(((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) AS NILAI_SELISIH_ABS, "
        // Str &= "(((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) AS NILAI_SELISIH, LSO_FLAGTAHAP, LSO_CREATE_BY "
        // Str &= "FROM (SELECT PRD_AVGCOST, PRD_PRDCD AS PLU, PRD_DESKRIPSIPANJANG AS DESKRIPSI,  "
        // Str &= "PRD_KODEDIVISI || ' - ' || DIV_NAMADIVISI AS DIVISI, "
        // Str &= "PRD_KODEDEPARTEMENT || ' - ' || DEP_NAMADEPARTEMENT AS DEPARTEMENT, "
        // Str &= "PRD_KODEKATEGORIBARANG || ' - ' || KAT_NAMAKATEGORI AS KATEGORI, "
        // Str &= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" & tahap & "' AND LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK LIKE 'D%' OR LSO_KODERAK LIKE 'G%') AND LSO_LOKASI = SO.LSO_LOKASI) AS AREAGUDANG, "
        // Str &= "(SELECT coalesce(SUM(LSO_QTY), 0) FROM tbhistory_lhso_sonas WHERE LSO_FLAGTAHAP = '" & tahap & "' AND LSO_TGLSO = TO_DATE('" & Format(TanggalSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') AND LSO_PRDCD = PRD_PRDCD AND (LSO_KODERAK NOT LIKE 'D%' AND LSO_KODERAK NOT LIKE 'G%') AND LSO_LOKASI = SO.LSO_LOKASI) AS AREATOKO,  "
        // Str &= "(LSO_ST_SALDOAKHIR) AS LPP, LSO_LOKASI, LSO_FLAGTAHAP, LSO_CREATE_BY, LSO_AVGCOST "
        // Str &= "FROM TBMASTER_PRODMAST, tbhistory_lhso_sonas SO, tbmaster_divisi, tbmaster_departement, tbmaster_kategori "
        // Str &= "WHERE LSO_TGLSO = TO_DATE('" & Format(tglSO, "dd-MM-yyyy").ToString & "','DD-MM-YYYY') "
        // Str &= "AND LSO_LOKASI = '01' "
        // Str &= "AND LSO_PRDCD = PRD_PRDCD "
        // Str &= "AND LSO_FLAGTAHAP = '" & tahap & "' "
        // Str &= "and prd_kodedivisi = div_kodedivisi "
        // Str &= "and prd_kodedivisi = dep_kodedivisi "
        // Str &= "and prd_kodedepartement = dep_kodedepartement "
        // Str &= "and prd_kodedepartement = kat_kodedepartement "
        // Str &= "and prd_kodekategoribarang = kat_kodekategori "
        // Str &= ")AS DATAS "
        // Str &= " WHERE (((AREAGUDANG + AREATOKO ) - LPP ) * LSO_AVGCOST) <> 0 ORDER BY LSO_LOKASI, DIVISI ASC, NILAI_SELISIH_ABS DESC "
        // Str &= ")AS DATAS1"
    }
}
