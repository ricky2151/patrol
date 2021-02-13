<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoom extends FormRequest
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
            'name' => 'string',
            'floor_id' => 'integer|exists:floors,id',
            'building_id' => 'integer|exists:buildings,id',
            'gateway_id' => 'integer|exists:gateways,id'
        ];
    }
}
