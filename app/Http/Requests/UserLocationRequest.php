<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserLocationRequest extends FormRequest
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
            'from_date' => 'required|date',
            'to_date' => 'date|after_equal:from_date',
            'location_id' => 'required'
        ];
    }

    public function attributes()
    {
        return[
            'location_id' => 'location',
        ];

    }
}
