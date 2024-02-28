<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class InputKksoActionUpdateRequest extends FormRequest
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
            'txtKodeRak' => ['required'],
            'txtKodeSubRak' => ['required','min:2'],
            'txtTipeRak' => ['required'],
            'txtShelvingRak' => ['required','min:2'],
            'datatables' => ['required','array'],
            'datatables.*.lso_nourut' => ['required'],
            'datatables.*.lso_jenisrak' => ['required'],
            'datatables.*.prd_prdcd' => ['required'],
            'datatables.*.prd_deskripsipanjang' => ['required'],
            'datatables.*.prd_unit' => ['required'],
            'datatables.*.lso_tmp_qtypcs' => ['required'],
            'datatables.*.lso_tmp_qtyctn' => ['required'],
            'datatables.*.prd_frac' => ['required'],
            'datatables.*.st_avgcost' => ['required'],
            'datatables.*.lso_qty' => ['required'],
            'datatables.*.row4' => ['required'],
            'datatables.*.row5' => ['required'],
        ];
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
