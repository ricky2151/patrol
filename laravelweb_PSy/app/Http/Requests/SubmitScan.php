<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitScan extends FormRequest
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
            'id' => 'required|integer|exists:shifts,id',
            'status_node_id' => 'required|integer|exists:status_nodes,id',
            'message' => 'nullable|string',
            'photos.*.file' => 'required_with:photos.*.photo_time,true|file',
            'photos.*.photo_time' => 'required_with:photos.*.file, true|regex:"(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})"',
        ];
    }

    
}
