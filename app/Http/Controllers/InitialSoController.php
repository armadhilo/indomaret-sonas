<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\initialSoRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class InitialSoController extends Controller
{

    private $flagOk;
    public function __construct(Request $request){
        DatabaseConnection::setConnection(session('KODECABANG'), "PRODUCTION");
    }

    public function index(){
        return view('initial-so');
    }

    public function actionStartPersiapanDataSo(initialSoRequest $request){

        //* Apakah anda yakin ingin memulai SO, Dan Sudah Yakin untuk Tanggal SO yang dipilih ?

        if(session('userlevel') != 1){
            return ApiFormatter::error(400, 'Anda tidak berhak menjalankan menu ini');
        }

        //! START SO
        try{

            $kodeigr = session('KODECABANG');
            $userid = session('userid');
            $procedure = DB::select("call sp_initial_so('$kodeigr','$userid', $request->tanggal_start_so, NULL)");
            $procedure = $procedure[0]->sukses;

            if (str_contains($procedure, 'Initial Sukses')) {

                // Str = "UPDATE TBMASTER_SETTING_SO SET MSO_FLAGTAHAP = '00' "
                // Str &= "WHERE MSO_TGLSO >= TO_DATE('" & Format(dtTgl_SO.Value, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY')"

                DB::table('tbmaster_setting_so')
                ->where('mso_tglso','>=', Carbon::parse($request->tanggal_start_so)->format('Y-m-d H:i:s'))
                ->update([
                    'mso_flagtahap' => '00'
                ]);

                return ApiFormatter::success(200, $procedure);
            }

            return ApiFormatter::error(400, $procedure);
        }

        catch(\Exception $e){

            DB::rollBack();

            $message = "Oops! Something wrong ( $e )";
            return ApiFormatter::error(400, $message);
        }
    }

    public function actionCopyMasterLokasi(initialSoRequest $request){

        // dtCekkkso = QueryOra("SELECT * FROM TBTR_LOKASI_SO WHERE DATE_TRUNC('DAY',LSO_TGLSO) = TO_DATE('" & Format(dtTgl_SO.Value, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY')  AND LSO_FLAGKKSO IS NULL")
        // If dtCekkkso.Rows.Count > 0 Then
        //     MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "Silahkan Cetak KKSO terlebih dahulu", Me.Text)
        //     Exit Sub
        // End If

        // dtCekLokasi = QueryOra("SELECT 1 FROM TBMASTER_LOKASI WHERE LKS_JENISRAK IS NULL and lks_prdcd is not null")
        // If dtCekLokasi.Rows.Count > 0 Then
        //     MessageDialog.Show(EnumMessageType.ErrorMessage, EnumCommonButtonMessage.Ok, "Terdapat PLU di Master Lokasi yang tidak ada Jenis Rak", Me.Text)
        //     Exit Sub
        // End If

        $check = DB::table('tbtr_lokasi_so')
            ->whereDate(DB::raw("DATE_TRUNC('DAY',lso_tglso)"), Carbon::parse($request->tanggal_start_so)->format('Y-m-d H:i:s'))
            ->whereNull('lso_flagkkso')
            ->count();

        if($check > 0) return ApiFormatter::error(400, 'Silahkan Cetak KKSO terlebih dahulu');

        $check = DB::table('tbmaster_lokasi')
            ->whereNull('lks_jenisrak')
            ->whereNotNull('lks_prdcd')
            ->count();

        if($check > 0) return ApiFormatter::error(400, 'Terdapat PLU di Master Lokasi yang tidak ada Jenis Rak');


        //! GET -> dtCek
        $dtCek = DB::select("
            select * from (
                select
                a.st_prdcd plu,
                a.st_lokasi lokasi,
                a.prd_deskripsipanjang deskripsi,
                a.prd_kodedivisi div,
                a.prd_kodedepartement dept,
                a.prd_kodekategoribarang katb,
                b.st_saldoakhir stock_qty,
                c.lks_koderak rak,
                c.lks_kodesubrak subrak,
                c.lks_tiperak tiperak,
                c.lks_shelvingrak shelvingrak,
                c.lks_qty plano_qty
            from
                (
                    select st_prdcd, st_lokasi, prd_deskripsipanjang, prd_kodedivisi, prd_kodedepartement, prd_kodekategoribarang from tbmaster_stock, tbmaster_prodmast where st_prdcd = prd_prdcd and st_saldoakhir <> 0
                    and st_prdcd || '-' || st_lokasi not in
                    (
                        select distinct lks_prdcd || '-' || '01' from tbmaster_lokasi where lks_prdcd is not null
                        union all
                        select distinct lso_prdcd || '-' || lso_lokasi from tbtr_lokasi_so where date_trunc('day',LSO_TGLSO) >= TO_DATE('" . Carbon::parse($request->tanggal_start_so)->format('Y-m-d H:i:s') . "', 'DD-MM-YYYY')
                    )
                order by st_lokasi, prd_kodedivisi, prd_kodedepartement, prd_kodekategoribarang
                ) a
                left join (
                    select st_prdcd, st_saldoakhir
                    from tbmaster_stock
                    where coalesce(st_saldoakhir,0) < 0
                ) b
                on a.st_prdcd = b.st_prdcd
                left join
                (
                    select lks_prdcd, lks_koderak, lks_kodesubrak, lks_tiperak, lks_shelvingrak, lks_qty
                    from tbmaster_lokasi
                    where coalesce(lks_qty, 0) < 0
                    and lks_prdcd is not null
                ) c
                on a.st_prdcd = c.lks_prdcd
            ) t
        ");

        if(count($dtCek)){
            $this->flagOk = false;

            $directoryName = 'initial-so';
            $fileName = 'PLU dengan LPP -'.Carbon::now()->format('Ymd').'.txt';

            // Define the base path
            Storage::makeDirectory('initial-so/');

            //CREATE FILE
            $content = '';
            $label_status = "--------- PLU dengan LPP <> 0 | " . Carbon::now()->format('H:i:s') . " ---------";

            //APPEND DATA ON TXT
            $content .= $label_status . chr(13) . chr(10);

            foreach($dtCek as $item){
                $label_status .= $item->plu . ' - ' . $item->lokasi . ' - ' . $item->deskripsi . ' - ' . $item->div . ' - ' . $item->dept . ' - ' . $item->katb . ' - ';
            }

            $label_status .= "====================================================================================";

            $directory = "$directoryName/$fileName";
            Storage::put($directory, $content);

            if (Storage::exists($directory)) {
                $fileContent = Storage::get($directory);

                $headers = [
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => 'attachment; filename="' . basename($directory) . '"',
                ];

                // Send the filename in the response headers
                $response = Response::make($fileContent, 200, $headers);
                $response->header('filename', basename($directory));

                // Delete the file after sending it
                Storage::delete($directory);

                // Send the file as a response
                //* Terdapat PLU dengan LPP <> 0 yang belum didaftarkan lokasi SO
                return $response;

            }else {
                $message = 'File gagal di download';
                return ApiFormatter::error(400, $message);
            }
        }

        //! GET -> dtCek
        // str = "select lks_prdcd plu, prd_deskripsipanjang deskripsi, lks_koderak rak, lks_kodesubrak subrak, lks_tiperak tiperak, lks_shelvingrak shelvingrak, lks_qty plano_qty "
        // str &= "from tbmaster_lokasi "
        // str &= "join tbmaster_prodmast "
        // str &= "on lks_prdcd = prd_prdcd "
        // str &= "where coalesce(lks_qty,0) < 0 "
        // str &= "and lks_prdcd is not null"

        $dtCek = DB::table('tbmaster_lokasi')
            ->selectRaw('lks_prdcd plu, prd_deskripsipanjang deskripsi, lks_koderak rak, lks_kodesubrak subrak, lks_tiperak tiperak, lks_shelvingrak shelvingrak, lks_qty plano_qty')
            ->join('tbmaster_prodmast',function($join){
                $join->on('lks_prdcd','=','prd_prdcd');
            })
            ->whereRaw("coalesce(lks_qty,0) < 0")
            ->whereNotNull('lks_prdcd')
            ->get();

        if(count($dtCek)){
            $this->flagOk = false;

            $directoryName = 'initial-so';
            $fileName = 'PLU dengan Plano -'.Carbon::now()->format('Ymd').'.txt';

            // Define the base path
            Storage::makeDirectory('initial-so/');

            //CREATE FILE
            $content = '';
            $label_status = "--------- Terdapat PLU dengan Plano < 0 | " . Carbon::now()->format('H:i:s') . " ---------";

            //APPEND DATA ON TXT
            $content .= $label_status . chr(13) . chr(10);

            foreach($dtCek as $item){

                $count = DB::table('tbtr_lokasi_so')
                    ->where('lso_koderak', 'like', 'FDZAN%')
                    ->where([
                        'lso_tiperak' => 'Z',
                        'lso_flagsarana' => 'K',
                        'lso_prdcd' => $item->plu
                    ])
                    ->count();

                if($count > 0){
                    continue;
                }

                $label_status .= $item->plu . ' - ' . $item->deskripsi . ' - ' . $item->rak . ' - ' . $item->subrak . ' - ' . $item->tiperak . ' - ' . $item->shelvingrak . ' - ' . $item->plano_qty;
            }

            $label_status .= "====================================================================================";

            $directory = "$directoryName/$fileName";
            Storage::put($directory, $content);

            if (Storage::exists($directory)) {
                $fileContent = Storage::get($directory);

                $headers = [
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => 'attachment; filename="' . basename($directory) . '"',
                ];

                // Send the filename in the response headers
                $response = Response::make($fileContent, 200, $headers);
                $response->header('filename', basename($directory));

                // Delete the file after sending it
                Storage::delete($directory);

                // Send the file as a response
                //* Terdapat PLU dengan Plano < 0
                return $response;

            }else {
                $message = 'File gagal di download';
                return ApiFormatter::error(400, $message);
            }
        }

        //! KALO BISA FILE NYA DIDOWNLOAD BERSAMAAN
        //! jika flagOK = false download .txt nya

        //? kemudian lanjut step

        //* Apakah anda yakin ingin meng-copy Master Lokasi ke Lokasi SO?

        if(session('userlevel') != 1){
            return ApiFormatter::error(400, 'Anda tidak berhak menjalankan menu ini');
        }

        //! COPY LOKASI
        try{

            $kodeigr = session('KODECABANG');
            $userid = session('userid');
            $procedure = DB::select("call sp_create_lokasiso_plano('$kodeigr','$userid', NULL)");
            $procedure = $procedure[0]->sukses;

            if (str_contains($procedure, 'Create Lokasi SO Sukses!')) {

                DB::update("
                    UPDATE tbtr_lokasi_so
                    set lso_tmp_qtyctn = lso_qty / (select prd_frac from tbmaster_prodmast where prd_prdcd = lso_prdcd),
                    lso_tmp_qtypcs = mod(lso_qty, (select prd_frac from tbmaster_prodmast where prd_prdcd = lso_prdcd)) where
                    lso_tglso = TO_DATE('" . Carbon::parse($request->tanggal_start_so)->format('Y-m-d H:i:s') . "', 'DD-MM-YYYY') and coalesce(lso_jenisrak, 'N') <> 'T'
                ");

                return ApiFormatter::success(200, $procedure);
            }

            return ApiFormatter::error(400, $procedure);
        }

        catch(\Exception $e){

            DB::rollBack();

            $message = "Oops! Something wrong ( $e )";
            return ApiFormatter::error(400, $message);
        }
    }
}
