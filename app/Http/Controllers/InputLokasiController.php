<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\InputLokasiActionSaveRequest;
use App\Http\Requests\InputLokasiRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class InputLokasiController extends Controller
{

    private $FlagTahap;

    public function __construct(Request $request){
        $this->FlagTahap = 0;
        DatabaseConnection::setConnection(session('KODECABANG'), "PRODUCTION");
    }

    public function index(){
        $dtSO = DB::table('tbmaster_setting_so')
            ->whereNull('mso_flagreset')
            ->get();

        if(count($dtSO) == 0){
            $check_error = "SO belum diinitial";
            return view('input-lokasi', compact('check_error'));
        }

        if($dtSO[0]->mso_flagsum <> ''){
            $check_error = "SO sudah diproses BA";
            return view('input-lokasi', compact('check_error'));

            $this->FlagTahap = (int)$dtSO[0]->mso_flagtahap;
        }

        $tgl_so = Carbon::parse($dtSO[0]->mso_tglso)->format('Y-m-d');

        return view('input-lokasi', compact('tgl_so'));
    }

    public function actionAddLokasi(InputLokasiRequest $request){

        $dtCek = DB::table('tbmaster_lokasi')
            ->where([
                'lks_koderak' => $request->kode_rak,
                'lks_kodesubrak' => $request->kode_sub_rak,
                'lks_tiperak' => $request->tipe_rak,
                'lks_shelvingrak' => $request->shelving_rak,
            ])
            ->get();

        if(count($dtCek) > 0){
            return ApiFormatter::error(400, 'Lokasi sudah terdaftar di Master Lokasi');
        }

        $dtCek = DB::table('tbtr_lokasi_so')
            ->select('lso_lokasi')
            ->where('lso_koderak', $request->kode_rak)
            ->whereRaw("to_char(lso_tglso, 'DD-MM-YYYY') = '" . Carbon::parse($request->tanggal_start_so)->format('d-m-Y') . "'")
            ->first();
    
        if(!empty($dtCek)){
            if($dtCek->lso_lokasi == '01' &&  $request->jenis_barang == 'Baik'){
                return ApiFormatter::error(400, 'Kode Rak sudah terisi barang Baik');
            }elseif($dtCek->lso_lokasi == '02' &&  $request->jenis_barang == 'Retur'){
                return ApiFormatter::error(400, 'Kode Rak sudah terisi barang Retur');
            }elseif($dtCek->lso_lokasi == '03' &&  $request->jenis_barang == 'Rusak'){
                return ApiFormatter::error(400, 'Kode Rak sudah terisi barang Rusak');
            }
        }
        
        //? akan buka halaman baru untuk menambah produk dan daftar lokas
        return ApiFormatter::success(200, 'Lokasi SO Berhasil Ditambahkan..!', $request);
    }

    public function detail(){
        return view('detail-input-lokasi');
    }

    //? jadi nanti no urut berdasarkan function ini
    public function getLastNumber(InputLokasiRequest $request){

        $data = DB::table('tbtr_lokasi_so')
            ->selectRaw('MAX(lso_nourut) AS nourut')
            ->where([
                'lso_koderak' => $request->kode_rak,
                'lso_kodesubrak' => $request->kode_sub_rak,
                'lso_tiperak' => $request->tipe_rak,
                'lso_shelvingrak' => $request->shelving_rak,
            ])
            ->whereRaw("TO_CHAR(lso_tglso, 'DD-MM-YYYY') = '" . Carbon::parse($request->tanggal_start_so)->format('Y-m-d') . "'")
            ->first();

        return ApiFormatter::success(200, 'Berhasil menampilkan last number', $data);
    }

    //? klik enter nanti harusnya otomatis dapet desc nya
    public function getDescPlu($prdcd){

        $data = DB::table('tbmaster_prodmast')
            ->select('prd_deskripsipanjang','prd_unit','prd_frac')
            ->where('prd_prdcd', $prdcd)
            ->first();

        if(empty($data)){
            return ApiFormatter::error(400, 'PLU tidak ditemukan');
        }

        if($this->FlagTahap != 0){
            $dtCek = DB::table('tbtr_lokasi_so')
                ->where('lso_prdcd', $prdcd)
                ->whereRaw("coalesce(lso_flaglimit, 'N') = 'Y'")
                ->count();

            if($dtCek == 0){
                return ApiFormatter::error(400, 'PLU tidak termasuk dalam limit item SO');
            }
        }

        return ApiFormatter::success(200, 'Plu berhasil ditampilkan', $data);
    }

    public function datatablesDetailLokasi(InputLokasiRequest $request){
        $data = DB::table('tbtr_lokasi_so')
            ->where([
                'lso_koderak' => $request->kode_rak,
                'lso_kodesubrak' => $request->kode_sub_rak,
                'lso_tiperak' => $request->tipe_rak,
                'lso_shelvingrak' => $request->shelving_rak,
            ])
            ->whereDate('lso_tglso', Carbon::parse($request->tanggal_start_so)->format('Y-m-d'))
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function actionSave(InputLokasiActionSaveRequest $request){

        DB::beginTransaction();
	    try{

            $jenis_barang = '03';
            if($request->jenis_barang == 'Baik'){
                $jenis_barang = '01';
            }elseif($request->jenis_barang == 'Retur'){
                $jenis_barang = '02';
            }

            $flag_limit = 'Y';
            if($this->FlagTahap == 0) $flag_limit = null;

            $jenis_rak = 'T';
            if(strtoupper($request->kode_rak[0]) == 'D' || strtoupper($request->kode_rak[0]) == 'G' || strtoupper($request->kode_rak[0]) == 'Z'){
                $jenis_rak = 'L';
            }

            foreach($request->plu as $key => $item){
                DB::table('tbtr_lokasi_so')->updateOrInsert([
                    'lso_tglso' => Carbon::parse($request->tanggal_start_so)->format('Y-m-d H:i:s'),
                    'lso_koderak' => $request->kode_rak,
                    'lso_kodesubrak' => $request->kode_sub_rak,
                    'lso_tiperak' => $request->tipe_rak,
                    'lso_shelvingrak' => $request->shelving_rak,
                    'lso_nourut' => $request->no_urut[$key],
                    'lso_prdcd' => $item,
                ],[
                    'lso_kodeigr' => session('KODECABANG'),
                    'lso_tglso' => Carbon::parse($request->tanggal_start_so)->format('Y-m-d H:i:s'),
                    'lso_koderak' => $request->kode_rak,
                    'lso_kodesubrak' => $request->kode_sub_rak,
                    'lso_tiperak' => $request->tipe_rak,
                    'lso_shelvingrak' => $request->shelving_rak,
                    'lso_nourut' => $request->no_urut[$key],
                    'lso_prdcd' => $item,
                    'lso_lokasi' => $jenis_barang,
                    'lso_qty' => 0,
                    'lso_flagsarana' => 'K',
                    'lso_create_by' => session('user_id'),
                    'lso_create_dt' => Carbon::now(),
                    'lso_flaglimit' => $flag_limit,
                    'lso_jenisrak' => $jenis_rak,
                ]);
            }

            DB::commit();

            return ApiFormatter::success(200, 'Action Save Berhasil');
        }

        catch(\Exception $e){

            DB::rollBack();

            $message = "Oops! Something wrong ( $e )";
            return ApiFormatter::error(400, $message);
        }
    }
}
