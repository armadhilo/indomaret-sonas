<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ReportRequest extends FormRequest
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
            'koderak1' => ['nullable'],
            'subrak1' => ['nullable','min:2'],
            'tiperak1' => ['nullable'],
            'shelving1' => ['nullable'],
            'koderak2' => ['nullable'],
            'subrak2' => ['nullable','min:2'],
            'tiperak2' => ['nullable'],
            'shelving2' => ['nullable'],
            'jenis_barang' => ['required','in:B,T,R'],
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

        $koderak1 = isset($koderak1) ? $koderak1 : "0";
        $subrak1 = isset($subrak1) ? $subrak1 : "0";
        $tipe1 = isset($tipe1) ? $tipe1 : "0";
        $shelving1 = isset($shelving1) ? $shelving1 : "0";
        $koderak2 = isset($koderak2) ? $koderak2 : "ZZZZZZZ";
        $subrak2 = isset($subrak2) ? $subrak2 : "ZZZ";
        $tipe2 = isset($tipe2) ? $tipe2 : "ZZZ";
        $shelving2 = isset($shelving2) ? $shelving2 : "ZZ";

        $this->merge([
            'koderak1' => $koderak1,
            'subrak1' => $subrak1,
            'tipe1' => $tipe1,
            'shelving1' => $shelving1,
            'koderak2' => $koderak2,
            'subrak2' => $subrak2,
            'tipe2' => $tipe2,
            'shelving2' => $shelving2,
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
            'message' => 'Input validation error'
        ], 400));
    }
}
