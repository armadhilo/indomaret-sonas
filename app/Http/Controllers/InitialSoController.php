<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\initialSoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InitialSoController extends Controller
{

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
            $procedure = DB::select("call SP_INITIAL_SO('$kodeigr','$userid', $request->tanggal_start_so, NULL)");
            $procedure = $procedure[0]->sukses;

            if (str_contains($procedure, 'Initial Sukses')) {

                // Str = "UPDATE TBMASTER_SETTING_SO SET MSO_FLAGTAHAP = '00' "
                // Str &= "WHERE MSO_TGLSO >= TO_DATE('" & Format(dtTgl_SO.Value, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY')"

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

        //! GET -> dtCek
        // str &= "select * from "
        // str &= "("
        // str &= "select "
        // str &= "a.st_prdcd plu, "
        // str &= "a.st_lokasi lokasi, "
        // str &= "a.prd_deskripsipanjang deskripsi, "
        // str &= "a.prd_kodedivisi div, "
        // str &= "a.prd_kodedepartement dept, "
        // str &= "a.prd_kodekategoribarang katb, "
        // str &= "b.st_saldoakhir stock_qty, "
        // str &= "c.lks_koderak rak, "
        // str &= "c.lks_kodesubrak subrak, "
        // str &= "c.lks_tiperak tiperak, "
        // str &= "c.lks_shelvingrak shelvingrak, "
        // str &= "c.lks_qty plano_qty "
        // str &= "from "
        // str &= "( "
        // str &= "select st_prdcd, st_lokasi, prd_deskripsipanjang, prd_kodedivisi, prd_kodedepartement, prd_kodekategoribarang from tbmaster_stock, tbmaster_prodmast where st_prdcd = prd_prdcd and st_saldoakhir <> 0 "
        // str &= "and st_prdcd || '-' || st_lokasi not in "
        // str &= "(select distinct lks_prdcd || '-' || '01' from tbmaster_lokasi where lks_prdcd is not null "
        // str &= "union all "
        // str &= "select distinct lso_prdcd || '-' || lso_lokasi from tbtr_lokasi_so where date_trunc('day',LSO_TGLSO) >= TO_DATE('" & Format(dtTgl_SO.Value, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY')"
        // str &= ") "
        // str &= "order by st_lokasi, prd_kodedivisi, prd_kodedepartement, prd_kodekategoribarang "
        // str &= ") a "
        // str &= "left join "
        // str &= "( "
        // str &= "select st_prdcd, st_saldoakhir "
        // str &= "from tbmaster_stock "
        // str &= "where coalesce(st_saldoakhir,0) < 0 "
        // str &= ") b "
        // str &= "on a.st_prdcd = b.st_prdcd "
        // str &= "left join "
        // str &= "( "
        // str &= "select lks_prdcd, lks_koderak, lks_kodesubrak, lks_tiperak, lks_shelvingrak, lks_qty "
        // str &= "from tbmaster_lokasi "
        // str &= "where coalesce(lks_qty, 0) < 0 "
        // str &= "and lks_prdcd is not null "
        // str &= ") c "
        // str &= "on a.st_prdcd = c.lks_prdcd "
        // str &= ") t "

        //! CREATE TXT
        // If dtCek.Rows.Count > 0 Then
        //     flagOk = False
        //     notifLokasi = "Terdapat PLU dengan LPP <> 0 yang belum didaftarkan lokasi SO / "
        //     'TXT LOKASI
        //     sLokasi &= Format(GetCurrentDate, "dd-MM-yyyy HH:mm:ss") & vbCrLf
        //     For i As Integer = 0 To dtCek.Rows.Count - 1
        //         sLokasi &= dtCek.Rows(i).Item("plu").ToString & " - " & _
        //             dtCek.Rows(i).Item("lokasi").ToString & " - " & _
        //             dtCek.Rows(i).Item("deskripsi").ToString & " - " & _
        //             dtCek.Rows(i).Item("div").ToString & " - " & _
        //             dtCek.Rows(i).Item("dept").ToString & " - " & _
        //             dtCek.Rows(i).Item("katb").ToString & vbCrLf
        //     Next
        //     sLokasi &= vbCrLf
        //     sLokasi &= "===================================================================================="
        //     sLokasi &= vbCrLf
        // End If

        //! GET -> dtCek
        // str = "select st_prdcd plu, st_lokasi lokasi, st_saldoakhir stock_qty, prd_deskripsipanjang deskripsi "
        // str &= "from tbmaster_stock "
        // str &= "join tbmaster_prodmast "
        // str &= "on st_prdcd = prd_prdcd "
        // str &= "where coalesce(st_saldoakhir,0) < 0"

        // If dtCek.Rows.Count > 0 Then
        //     flagOk = False
        //     notifStock = "Terdapat PLU dengan Stock < 0 / "
        //     'TXT STOCK
        //     sStock &= Format(GetCurrentDate, "dd-MM-yyyy HH:mm:ss") & vbCrLf
        //     For i As Integer = 0 To dtCek.Rows.Count - 1

        //         str = "SELECT COUNT(1) CEK_FDZAN FROM TBTR_LOKASI_SO "
        //         str &= "WHERE LSO_KODERAK LIKE 'FDZAN%' "
        //         str &= "AND LSO_TIPERAK = 'Z' "
        //         str &= "AND LSO_FLAGSARANA = 'K' "
        //         str &= "AND LSO_PRDCD = '" & dtCek.Rows(i).Item("plu").ToString & "'"
        //         dtCekFdzan = QueryOra(str)

        //         If Val(dtCekFdzan.Rows(0).Item("CEK_FDZAN").ToString) > 0 Then
        //             Continue For
        //         End If

        //         sStock &= dtCek.Rows(i).Item("plu").ToString & " - " & _
        //             dtCek.Rows(i).Item("lokasi").ToString & " - " & _
        //             dtCek.Rows(i).Item("stock_qty").ToString & " - " & _
        //             dtCek.Rows(i).Item("deskripsi").ToString & vbCrLf
        //     Next
        //     sStock &= vbCrLf
        //     sStock &= "===================================================================================="
        //     sStock &= vbCrLf
        // End If

        //! GET -> dtCek
        // str = "select lks_prdcd plu, prd_deskripsipanjang deskripsi, lks_koderak rak, lks_kodesubrak subrak, lks_tiperak tiperak, lks_shelvingrak shelvingrak, lks_qty plano_qty "
        // str &= "from tbmaster_lokasi "
        // str &= "join tbmaster_prodmast "
        // str &= "on lks_prdcd = prd_prdcd "
        // str &= "where coalesce(lks_qty,0) < 0 "
        // str &= "and lks_prdcd is not null"

        // If dtCek.Rows.Count > 0 Then
        //     flagOk = False
        //     notifPlano = "Terdapat PLU dengan Plano < 0 / "
        //     'TXT PLANO
        //     sPlano &= Format(GetCurrentDate, "dd-MM-yyyy HH:mm:ss") & vbCrLf
        //     For i As Integer = 0 To dtCek.Rows.Count - 1

        //         str = "SELECT COUNT(1) CEK_FDZAN FROM TBTR_LOKASI_SO "
        //         str &= "WHERE LSO_KODERAK LIKE 'FDZAN%' "
        //         str &= "AND LSO_TIPERAK = 'Z' "
        //         str &= "AND LSO_FLAGSARANA = 'K' "
        //         str &= "AND LSO_PRDCD = '" & dtCek.Rows(i).Item("plu").ToString & "'"
        //         dtCekFdzan = QueryOra(str)

        //         If Val(dtCekFdzan.Rows(0).Item("CEK_FDZAN").ToString) > 0 Then
        //             Continue For
        //         End If

        //         sPlano &= dtCek.Rows(i).Item("plu").ToString & " - " & _
        //             dtCek.Rows(i).Item("deskripsi").ToString & " - " & _
        //             dtCek.Rows(i).Item("rak").ToString & " - " & _
        //             dtCek.Rows(i).Item("subrak").ToString & " - " & _
        //             dtCek.Rows(i).Item("tiperak").ToString & " - " & _
        //             dtCek.Rows(i).Item("shelvingrak").ToString & " - " & _
        //             dtCek.Rows(i).Item("plano_qty").ToString & vbCrLf
        //     Next
        //     sPlano &= vbCrLf
        //     sPlano &= "===================================================================================="
        //     sPlano &= vbCrLf
        // End If

        //? jika flagOK = false download .txt nya

        //? kemudian lanjut step

        //* Apakah anda yakin ingin meng-copy Master Lokasi ke Lokasi SO?

        if(session('userlevel') != 1){
            return ApiFormatter::error(400, 'Anda tidak berhak menjalankan menu ini');
        }

        //! COPY LOKASI
        try{

            $kodeigr = session('KODECABANG');
            $userid = session('userid');
            $procedure = DB::select("call SP_CREATE_LOKASISO_PLANO('$kodeigr','$userid', NULL)");
            $procedure = $procedure[0]->sukses;

            if (str_contains($procedure, 'Create Lokasi SO Sukses!')) {

                // str = "update tbtr_lokasi_so "
                // str &= "set LSO_TMP_QTYCTN = lso_qty / (select prd_frac from tbmaster_prodmast where prd_prdcd = lso_prdcd), "
                // str &= "LSO_TMP_QTYPCS = mod(lso_qty, (select prd_frac from tbmaster_prodmast where prd_prdcd = lso_prdcd)) where "
                // str &= "LSO_TGLSO = TO_DATE('" & Format(dtTgl_SO.Value, "dd-MM-yyyy").ToString & "', 'DD-MM-YYYY') and coalesce(lso_jenisrak, 'N') <> 'T' "

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
