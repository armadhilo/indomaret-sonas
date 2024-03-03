<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ReportDaftarItemAdjustRequest extends FormRequest
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
            'tanggal_adjust_start' => ['required', 'date_format:Y-m-d'],
            'tanggal_adjust_end' => ['required', 'date_format:Y-m-d'],
            'plu1' => ['nullable'],
            'plu2' => ['nullable'],
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

        $plu1 = isset($plu1) ? $plu1 : "0";
        $plu2 = isset($plu2) ? $plu2 : "ZZZZZZZ";

        $this->merge([
            'plu1' => $plu1,
            'plu2' => $plu2,
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
