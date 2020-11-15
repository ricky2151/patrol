<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUser extends FormRequest
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
             'name' => 'nullable|string',
            'age' => 'nullable|integer',
            'role_id' => 'nullable|integer',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'phone' => 'nullable|string',
            'master_key' => 'nullable|string',
            'email' => 'nullable|email',
            'shifts.*.id' => 'nullable|integer',
            'shifts.*.room_id' => 'nullable|integer',
            'shifts.*.time_id' => 'nullable|integer',
            'shifts.*.date' => 'nullable|string',
            'shifts.*.type' => 'nullable|string',

        ];
    }
}
