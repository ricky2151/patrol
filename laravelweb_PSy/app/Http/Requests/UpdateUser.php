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
            'name' => 'string',
            'age' => 'integer',
            'role_id' => 'integer|exists:roles,id',
            'username' => 'string',
            'password' => 'string',
            'phone' => 'string',
            'master_key' => 'string',
            'email' => 'email',
            'shifts.*.id' => 'integer|exists:shifts,id',
            'shifts.*.room_id' => 'integer|exists:rooms,id',
            'shifts.*.time_id' => 'integer|exists:times,id',
            'shifts.*.date' => 'string',
            'shifts.*.type' => 'integer',

        ];
    }
}
