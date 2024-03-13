<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ReportPerincianBasoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tanggal_start_so' => ['required', 'date_format:Y-m-d'],
            'div1' => ['nullable'],
            'div2' => ['nullable'],
            'dept1' => ['nullable'],
            'dept2' => ['nullable'],
            'kat1' => ['nullable'],
            'kat2' => ['nullable'],
            'plu1' => ['nullable'],
            'plu2' => ['nullable'],
            'jenis_barang' => ['required','in:B,T,R'],
            'selisih_so' => ['nullable','in:1,2,3'],
            'check_rpt_audit' => ['nullable','in:0,1'],
        ];
    }

    protected function passedValidation()
    {
        if($this->jenis_barang == 'B'){
            $this->merge(['jenis_barang' => '01']);
        }elseif($this->jenis_barang == 'T'){
            $this->merge(['jenis_barang' => '02']);
        }else{
            $this->merge(['jenis_barang' => '03']);
        }

        $div1 = isset($div1) ? $div1 : "0";
        $div2 = isset($div2) ? $div2 : "0";
        $dept1 = isset($dept1) ? $dept1 : "0";
        $dept2 = isset($dept2) ? $dept2 : "Z";
        $kat1 = isset($kat1) ? $kat1 : "ZZ";
        $kat2 = isset($kat2) ? $kat2 : "ZZ";

        $this->merge([
            'div1' => $div1,
            'div2' => $div2,
            'dept1' => $dept1,
            'dept2' => $dept2,
            'kat1' => $kat1,
            'kat2' => $kat2,
        ]);
    }

    protected function failedValidation(Validator $validator) {
        $errors = json_decode($validator->errors());
        $array = [];
        //format error validation message laravel to Wowrack RESTAPI format
        foreach($errors as $key => $item){
            foreach($item as $error){
                $array[] = [
                    'message' => $error,
                    'field' => $key,
                ];
            }
        }
        throw new HttpResponseException(response()->json([
            'code' => 400,
            'errors' => $array,
            'message' => 'Terdapat Input yang belum diisi',
        ], 400));
    }
}
