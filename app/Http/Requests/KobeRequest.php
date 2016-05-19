<?php

namespace App\Http\Requests;

class KobeRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'required|string|max:1000',
            'g-recaptcha-response' => 'required|recaptcha',
//            'accept-license' => 'required|boolean',
        ];
    }

    /**
     * Set custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'g-recaptcha-response.required' => 'Please ensure that you are a human!',
        ];
    }
}
