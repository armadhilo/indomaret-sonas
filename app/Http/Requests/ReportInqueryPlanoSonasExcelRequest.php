<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ReportLppMonthEndExcelActionSimpanDataPluRequest extends FormRequest
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
            'plu' => ['required'],
            'jenis_barang' => ['required','in:A,B,T,R'],
        ];
    }

    protected function passedValidation()
    {
        if($this->jenis_barang == 'B'){
            $this->merge(['jenis_barang' => '01']);
        }elseif($this->jenis_barang == 'T'){
            $this->merge(['jenis_barang' => '02']);
        }elseif($this->jenis_barang == 'R'){
            $this->merge(['jenis_barang' => '03']);
        }else{
            $this->merge(['jenis_barang' => null]);
        }
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
