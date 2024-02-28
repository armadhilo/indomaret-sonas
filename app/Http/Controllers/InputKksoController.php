<?php

namespace App\Http\Controllers;

use App\Helper\ApiFormatter;
use App\Helper\DatabaseConnection;
use App\Http\Requests\InputKksoActionUpdateRequest;
use App\Http\Requests\InputKksoGetDataRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InputKksoController extends Controller
{

    private $flagCopyLokasi;
    private $cboJenisBarang;

    public function __construct(Request $request){
        DatabaseConnection::setConnection(session('KODECABANG'), "PRODUCTION");
    }

    public function index(){

        $dtSO = DB::table('tbmaster_setting_so')
            ->whereNull('mso_flagreset')
            ->get();
        $data['tglSo'] = Carbon::parse($dtSO[0]->mso_tglso)->format('Y-m-d');

        if(count($dtSO)){
            $check_error = "SO belum diinitial";
            return view('input-kkso', compact('check_error'));
        }

        if($dtSO[0]->mso_flagsum <> ""){
            $check_error = "SO sudah diproses BA";
            return view('input-kkso', compact('check_error'));
        }

        $this->flagCopyLokasi = $dtSO[0]->mso_flag_createlso;
        $this->cboJenisBarang = "Baik";

        $data['flagCopyLokasi'] = $dtSO[0]->mso_flag_createlso;
        $data['cboJenisBarang'] = "Baik";

        return view('input-kkso', $data);
    }

    public function getData(InputKksoGetDataRequest $request){
        $query = '';
        $query .= "SELECT LSO_NOURUT, LSO_JENISRAK, PRD_PRDCD, PRD_DESKRIPSIPANJANG, PRD_UNIT, PRD_FRAC, LSO_QTY, LSO_TMP_QTYCTN, LSO_TMP_QTYPCS, coalesce(ST_AVGCOST, 0) AS ST_AVGCOST, LSO_MODIFY_BY ";
        $query .= "FROM TBTR_LOKASI_SO LEFT JOIN TBMASTER_STOCK ON LSO_PRDCD = ST_PRDCD AND LSO_LOKASI = ST_LOKASI AND LSO_KODEIGR = ST_KODEIGR, TBMASTER_PRODMAST ";
        // $query .= "WHERE TO_CHAR(LSO_TGLSO, 'DD-MM-YYYY') = '" . Carbon::parse($request->tanggal_start_so)->format('Y-m-d') . "' ";
        $query .= "WHERE LSO_PRDCD = PRD_PRDCD AND LSO_KODEIGR = PRD_KODEIGR AND PRD_PRDCD LIKE '%0' ";
        if($this->flagCopyLokasi == 'Y'){
            $query .= "AND (coalesce(LSO_FLAGLIMIT, 'N') = 'Y' OR LSO_LOKASI = '02' OR LSO_LOKASI = '03') ";
        }
        $query .= "AND LSO_KODERAK = '" . $request->txtKodeRak . "' ";
        $query .= "AND LSO_KODESUBRAK = '" . $request->txtKodeSubRak . "' ";
        $query .= "AND LSO_TIPERAK = '" . $request->txtTipeRak . "' ";
        $query .= "AND LSO_SHELVINGRAK = '" . $request->txtShelvingRak . "' ";
        $query .= "AND LSO_FLAGSARANA = 'K' ";
        if($this->cboJenisBarang == 'Baik'){
            $query .= "AND LSO_LOKASI = '01' ";
        }elseif($this->cboJenisBarang == 'Retur'){
            $query .= "AND LSO_LOKASI = '02' ";
        }elseif($this->cboJenisBarang == 'Rusak'){
            $query .= "AND LSO_LOKASI = '03' ";
        }
        $query .= "ORDER BY LSO_NOURUT ASC";

        $data = DB::select($query);

        return ApiFormatter::success(200, 'Data Berhasil Ditampilkan !', $data);

        // DGV.Item(0, i).Value = Val(dt.Rows(i).Item("LSO_NOURUT").ToString)
        // DGV.Item(1, i).Value = dt.Rows(i).Item("PRD_PRDCD").ToString
        // DGV.Item(2, i).Value = dt.Rows(i).Item("PRD_DESKRIPSIPANJANG").ToString
        // DGV.Item(3, i).Value = dt.Rows(i).Item("PRD_UNIT").ToString & "/" & dt.Rows(i).Item("PRD_FRAC").ToString
        // If Val(dt.Rows(i).Item("LSO_QTY").ToString) <> 0 And (Val(dt.Rows(i).Item("LSO_TMP_QTYCTN").ToString) = 0 And Val(dt.Rows(i).Item("LSO_TMP_QTYPCS").ToString) = 0) Then
        //     Dim CTN As Double = 0
        //     If Val(dt.Rows(i).Item("LSO_QTY").ToString) < 0 Then
        //         DGV.Item(4, i).Value = Math.Ceiling(Val(dt.Rows(i).Item("LSO_QTY").ToString) / IIf(Val(dt.Rows(i).Item("PRD_FRAC").ToString) = 0, 1, Val(dt.Rows(i).Item("PRD_FRAC").ToString)))
        //     Else
        //         DGV.Item(4, i).Value = Math.Floor(Val(dt.Rows(i).Item("LSO_QTY").ToString) / IIf(Val(dt.Rows(i).Item("PRD_FRAC").ToString) = 0, 1, Val(dt.Rows(i).Item("PRD_FRAC").ToString)))
        //     End If
        //     DGV.Item(5, i).Value = Val(dt.Rows(i).Item("LSO_QTY").ToString) Mod IIf(Val(dt.Rows(i).Item("PRD_FRAC").ToString) = 0, 1, Val(dt.Rows(i).Item("PRD_FRAC").ToString))
        // Else
        //     DGV.Item(4, i).Value = Val(dt.Rows(i).Item("LSO_TMP_QTYCTN").ToString)
        //     DGV.Item(5, i).Value = Val(dt.Rows(i).Item("LSO_TMP_QTYPCS").ToString)
        // End If
        // DGV.Item(6, i).Value = Val(dt.Rows(i).Item("PRD_FRAC").ToString)
        // DGV.Item(7, i).Value = Val(dt.Rows(i).Item("LSO_QTY").ToString)
        // DGV.Item(8, i).Value = Val(dt.Rows(i).Item("ST_AVGCOST").ToString)
        // DGV.Item(9, i).Value = Val(dt.Rows(i).Item("LSO_QTY").ToString)
        // DGV.Item(10, i).Value = dt.Rows(i).Item("LSO_MODIFY_BY").ToString
    }

    public function actionUpdate(InputKksoActionUpdateRequest $request){

        DB::beginTransaction();
	    try{

            foreach($request->datatables as $item){
                $query = "";
                $query .= "INSERT INTO TBHISTORY_SONAS ";
                $query .= "(HSO_KODERAK, ";
                $query .= "HSO_KODESUBRAK, ";
                $query .= "HSO_TIPERAK, ";
                $query .= "HSO_SHELVINGRAK, ";
                $query .= "HSO_NOURUT, ";
                $query .= "HSO_PRDCD, ";
                $query .= "HSO_TGLSO, ";
                $query .= "HSO_QTYLAMA, ";
                $query .= "HSO_QTYBARU, ";
                $query .= "HSO_CREATE_BY, ";
                $query .= "HSO_CREATE_DT) ";
                $query .= "VALUES ";
                $query .= "( ";
                $query .= "'" . $request->txtKodeRak . "', ";
                $query .= "'" . $request->txtKodeSubRak . "', ";
                $query .= "'" . $request->txtTipeRak . "', ";
                $query .= "'" . $request->txtShelvingRak . "', ";
                $query .= "'" . $item->lso_nourut . "', ";
                $query .= "'" . $item->prd_prdcd . "', ";
                $query .= "TO_DATE('" . $request->tanggal_start_so . "', 'DD-MM-YYYY'), ";
                $query .= "'" . $item->lso_qty . "', ";
                $query .= "'" . $item->lso_qty . "', ";
                $query .= "'" . session('user_id') . "', ";
                $query .= "CURRENT_TIMESTAMP) ";
                DB::insert($query);

                $query = '';
                $query .= "UPDATE TBTR_LOKASI_SO SET ";
                $query .= "LSO_QTY = '" . $item->lso_qty . "', ";
                $query .= "LSO_TMP_QTYCTN = '" . $item->row4 . "', ";
                $query .= "LSO_TMP_QTYPCS = '" . $item->row5 . "', ";
                $query .= "LSO_MODIFY_BY = '" . session('user_id') . "', ";
                $query .= "LSO_MODIFY_DT = CURRENT_TIMESTAMP ";
                $query .= "WHERE LSO_TGLSO = TO_DATE('" . $request->tanggal_start_so . "', 'DD-MM-YYYY') ";
                $query .= "AND LSO_KODERAK = '" . $request->txtKodeRak . "' ";
                $query .= "AND LSO_KODESUBRAK = '" . $request->txtKodeSubRak . "' ";
                $query .= "AND LSO_TIPERAK = '" . $request->txtTipeRak . "' ";
                $query .= "AND LSO_SHELVINGRAK = '" . $request->txtShelvingRak . "' ";
                $query .= "AND LSO_NOURUT = '" . $item->lso_nourut . "' ";
                $query .= "AND LSO_PRDCD = '" . $item->prdcd . "' ";
                $query .= "AND LSO_FLAGSARANA = 'K'";
                DB::update($query);
            }

            //! LANGSUNG UPLOAD PLANOPROGRAM
            $KodeRak = $request->txtKodeRak;
            $KodeSubRak = $request->txtKodeSubRak;
            $TipeRak = $request->txtTipeRak;
            $Shelving = $request->txtShelvingRak;
            $NoUrut = $item->lso_nourut;
            $KodePLU = $item->prdcd;
            $Qty = $item->lso_qty;

            foreach($request->datatables as $item){
                $query = '';
                $query .= "SELECT * FROM TBTR_LOKASI_SO ";
                $query .= "WHERE LSO_KODERAK = '" . $KodeRak . "' AND ";
                $query .= "LSO_KODESUBRAK = '" . $KodeSubRak . "' AND ";
                $query .= "LSO_TIPERAK = '" . $TipeRak . "' AND ";
                $query .= "LSO_SHELVINGRAK = '" . $Shelving . "' AND ";
                $query .= "LSO_NOURUT = '" . $NoUrut . "' AND ";
                $query .= "TO_CHAR(LSO_TGLSO, 'DD-MM-YYYY') = '" . $request->tanggal_start_so . "' ";

                $firstCharacter = strtoupper(substr($KodeRak, 0, 1));

                if($firstCharacter == 'D' || $firstCharacter == 'G'){
                    if($item->lso_jenisrak == 'D' || $item->lso_jenisrak == 'N'){
                        //! Jika rak cadangan Gudang (L) maka update ke Display Gudang
                        $query = "";
                        $query .= "UPDATE tbmaster_lokasi ";
                        $query .= "SET lks_qty = coalesce ( ( SELECT SUM (lso_qty) qty ";
                        $query .= "             FROM tbtr_lokasi_so ";
                        $query .= "             WHERE (lso_lokasi = '01' ";
                        $query .= "                 AND lso_jenisrak = 'L' ";
                        $query .= "                 AND lso_tglso = TO_DATE('" . $request->tanggal_start_so . "', 'DD-MM-YYYY') ";
                        $query .= "                 AND lso_prdcd = '" . $KodePLU . "' ";
                        $query .= "                 AND lso_koderak LIKE 'L%') ";
                        $query .= "             OR (lso_koderak LIKE 'D%'";
                        $query .= "                 AND lso_jenisrak IN ('D', 'N')";
                        $query .= "                 AND lso_tglso = TO_DATE('" . $request->tanggal_start_so . "', 'DD-MM-YYYY') ";
                        $query .= "                 AND lso_prdcd     = '" . $KodePLU . "')";
                        $query .= "             GROUP BY lso_prdcd), 0), ";
                        $query .= "LKS_MODIFY_BY = '" . session('user_id') . "', ";
                        $query .= "LKS_MODIFY_DT = CURRENT_TIMESTAMP ";
                        $query .= "WHERE lks_koderak LIKE 'D%' AND lks_jenisrak IN ('D', 'N') and lks_prdcd = '" . $KodePLU . "' ";
                        DB::update($query);
                    }else{
                        //! Jika Gudang update plano apa adanya
                        $query = "";
                        $query .= "UPDATE TBMASTER_LOKASI ";
                        $query .= "SET ";
                        $query .= "LKS_QTY = '" . $Qty . "', ";
                        $query .= "LKS_PRDCD = CASE WHEN LKS_JENISRAK = 'S' THEN '" . $KodePLU . "' ELSE LKS_PRDCD END, ";
                        $query .= "LKS_MODIFY_BY = '" . session('user_id') . "', ";
                        $query .= "LKS_MODIFY_DT = CURRENT_TIMESTAMP ";
                        $query .= "WHERE LKS_KODERAK = '" . $KodeRak . "' AND ";
                        $query .= "LKS_KODESUBRAK = '" . $KodeSubRak . "' AND ";
                        $query .= "LKS_TIPERAK = '" . $TipeRak . "' AND ";
                        $query .= "LKS_SHELVINGRAK = '" . $Shelving . "' AND ";
                        $query .= "LKS_NOURUT = '" . $NoUrut . "' ";
                        DB::update($query);
                    }
                }elseif($firstCharacter == 'L'){
                    //! Jika rak cadangan Gudang (L) maka update ke Display Gudang
                    $query = "";
                    $query .= "UPDATE tbmaster_lokasi ";
                    $query .= "SET lks_qty = coalesce ( ( SELECT SUM (lso_qty) qty ";
                    $query .= "             FROM tbtr_lokasi_so ";
                    $query .= "             WHERE (lso_lokasi = '01' ";
                    $query .= "                 AND lso_jenisrak = 'L' ";
                    $query .= "                 AND lso_tglso = TO_DATE('" . $request->tanggal_start_so . "', 'DD-MM-YYYY') ";
                    $query .= "                 AND lso_prdcd = '" . $KodePLU . "' ";
                    $query .= "                 AND lso_koderak LIKE 'L%') ";
                    $query .= "             OR (lso_koderak LIKE 'D%'";
                    $query .= "                 AND lso_jenisrak IN ('D', 'N')";
                    $query .= "                 AND lso_tglso = TO_DATE('" . $request->tanggal_start_so . "', 'DD-MM-YYYY') ";
                    $query .= "                 AND lso_prdcd     = '" . $KodePLU . "')";
                    $query .= "             GROUP BY lso_prdcd), 0), ";
                    $query .= "LKS_MODIFY_BY = '" . session('user_id') . "', ";
                    $query .= "LKS_MODIFY_DT = CURRENT_TIMESTAMP ";
                    $query .= "WHERE lks_koderak LIKE 'D%' AND lks_jenisrak IN ('D', 'N') and lks_prdcd = '" . $KodePLU . "' ";
                    DB::update($query);
                }else{
                    if($item->lso_jenisrak == "S"){
                        //! Jika Toko Storage update apa adanya
                        $query .= "";
                        $query .= "UPDATE TBMASTER_LOKASI ";
                        $query .= "SET ";
                        $query .= "LKS_QTY = '" . $Qty . "', ";
                        $query .= "LKS_PRDCD = CASE WHEN LKS_JENISRAK = 'S' THEN '" . $KodePLU . "' ELSE LKS_PRDCD END, ";
                        $query .= "LKS_MODIFY_BY = '" . session('user_id') . "', ";
                        $query .= "LKS_MODIFY_DT = CURRENT_TIMESTAMP ";
                        $query .= "WHERE LKS_KODERAK = '" . $KodeRak . "' AND ";
                        $query .= "LKS_KODESUBRAK = '" . $KodeSubRak . "' AND ";
                        $query .= "LKS_TIPERAK = '" . $TipeRak . "' AND ";
                        $query .= "LKS_SHELVINGRAK = '" . $Shelving . "' AND ";
                        $query .= "LKS_NOURUT = '" . $NoUrut . "' ";
                        DB::update($query);
                    }else{
                        //! Jika Toko selain Storage update plano SUM ke lokasi display utama
                        $query .= "";
                        $query .= "UPDATE TBMASTER_LOKASI ";
                        $query .= "SET ";
                        $query .= "LKS_QTY = ";
                        $query .= "         coalesce((SELECT SUM(LSO_QTY) FROM TBTR_LOKASI_SO ";
                        $query .= "         WHERE LSO_TGLSO = TO_DATE('" . $request->tanggal_start_so . "', 'DD-MM-YYYY') ";
                        $query .= "         AND (SUBSTR(LSO_KODERAK,1,1) <> 'D' AND SUBSTR(LSO_KODERAK,1,1) <> 'G' AND SUBSTR(LSO_KODERAK,1,3) <> 'HDH') AND ";
                        $query .= "         LSO_PRDCD = '" . $KodePLU . "' AND (LSO_JENISRAK = 'D' OR LSO_JENISRAK = 'N' OR LSO_JENISRAK = 'L')), 0), ";
                        $query .= "LKS_MODIFY_BY = '" . session('user_id') . "', ";
                        $query .= "LKS_MODIFY_DT = CURRENT_TIMESTAMP ";
                        $query .= "WHERE ";
                        $query .= "(SUBSTR(LKS_KODERAK,1,1) <> 'D' AND SUBSTR(LKS_KODERAK,1,1) <> 'G') AND ";
                        $query .= "(LKS_JENISRAK = 'D' OR LKS_JENISRAK = 'N') AND ";
                        $query .= "LKS_PRDCD = '" . $KodePLU . "'";
                        DB::update($query);
                    }
                }
            }

            dd('run');
            // DB::commit();

            $message = 'Data Berhasil disimpan !!';
            return ApiFormatter::success(200, $message);
        }

        catch(\Exception $e){

            DB::rollBack();

            $message = "Oops! Something wrong ( $e )";
            return ApiFormatter::error(400, $message);
        }
    }
}
