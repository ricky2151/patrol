<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoom extends FormRequest
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
            'name' => 'required|string',
            'floor_id' => 'required|integer|exists:floors,id',
            'building_id' => 'required|integer|exists:buildings,id',
            'gateway_id' => 'required|integer|exists:gateways,id'
        ];
    }
}
