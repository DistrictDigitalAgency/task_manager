<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class AccountUpdateRequest extends FormRequest
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
            'hostname' => 'required',
            'mysql_username' => 'required',
            'mysql_database' => 'required',
            'purchase_code' => 'required',
        ];
    }
}
