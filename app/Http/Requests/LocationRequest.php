<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\{Location,State,Municipality,Town,Tag};
class LocationRequest extends FormRequest
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
            'tag' => [
                'required', 'min:3', Rule::unique((new Location)->getTable())->ignore($this->route()->location->id ?? null)
            ],
            'cluster_id' => [
                'required', 'exists:'.(new Tag)->getTable().',id'
            ],
            'town_id' => [
                'required', 'exists:'.(new Town)->getTable().',id'
            ],
            'municipality_id' => [
                'required', 'exists:'.(new Municipality)->getTable().',id'
            ],
            'state_id' => [
                'required', 'exists:'.(new State)->getTable().',id'
            ],
            'latitude' => [
                'required','regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'
            ],
            'longitude' => [
                'required','regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'
            ],
            'description' => [
                'nullable', 'min:5' 
            ]
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
            'latitude' => 'Formato inválido, se espera formato decimal eg. 123.232',
        ];
    }
}
