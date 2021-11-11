<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use stdClass;

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
            'user_name' => 'max:20|min:4',
            'avatar' => 'required|dimensions:width=256,height=256',

        ];
    }
    public function messages()
    {
        return [
            'avatar.required' => 'A avatar is required',
            'avatar.dimensions' => 'Dimensions should under 256px',
        ];
    }
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = new JsonResponse([
            'data' => new stdClass,
            'message' => $validator->errors()->first(),
        ], Response::HTTP_BAD_REQUEST);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
