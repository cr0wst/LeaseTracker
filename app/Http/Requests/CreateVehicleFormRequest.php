<?php

namespace LeaseTracker\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for the Vehicle entry form.
 * @package LeaseTracker\Http\Requests
 */
class CreateVehicleFormRequest extends FormRequest
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
            // regex:/^[(a-zA-Z\s)]+$/u = Alpha+Space
            'make_model' => 'required|regex:/^[(a-zA-Z0-9\s)]+$/u',
            'name' => 'required|regex:/^[(a-zA-Z\s)]+$/u',
            'start_date' => 'required|date_format:Y-m-d|before_or_equal:now',
            'cost_per_mile' => 'required|numeric|between:0,5',
            'months' => 'required|numeric|min:1',
            'starting_mileage' => 'required|numeric|min:1',
            'total_allowable_mileage' => 'required|numeric|min:1'
        ];
    }
}
