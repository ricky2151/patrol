<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUser extends FormRequest
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
            'age' => 'required|integer',
            'role_id' => 'required|integer',
            'username' => 'required|string',
            'password' => 'required|string',
            'phone' => 'required|string',
            'master_key' => 'nullable|string',
            'email' => 'required|email',
            'shifts.*.room_id' => 'nullable|integer',
            'shifts.*.time_id' => 'nullable|integer',
            'shifts.*.date' => 'nullable|string',
            'shifts.*.status_node_id' => 'nullable|string',
            'shifts.*.message' => 'nullable|string',
            'shifts.*.token_shift' => 'nullable|string',

            
        ];
    }
}
