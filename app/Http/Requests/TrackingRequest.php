<?php

namespace App\Http\Requests;

use App\Models\Device;
use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;


class TrackingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'device_id' => [
                'required', 'exists:'.(new Device)->getTable().',id'
            ],
            'location_id' => [
                'required', 'exists:'.(new Location)->getTable().',id'
            ],
            'status' => [
                'required','in:Mantenimiento,Activo,Activo (SL),Inactivo'
            ],
            /*'image_device' => [
                'required'
            ],
            'image_indoor' => [
                'required'
            ],
            'image_outdoor' => [
                'required'
            ]*/
        ];
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required' => 'Necesario',
        ];
    }
}
