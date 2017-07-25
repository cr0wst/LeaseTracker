<?php

namespace LeaseTracker\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest for the Mileage entry form.
 * @package LeaseTracker\Http\Requests
 */
class CreateMileageFormRequest extends FormRequest
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
            'date' => 'required|date_format:Y-m-d|before_or_equal:now',
            // Should probably actually check to make sure it's the highest mileage entry.
            'currentMileage' => 'required|numeric|min:1',
        ];
    }
}
