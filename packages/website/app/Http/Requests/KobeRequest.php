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
            'content' => 'required|string|max:1500',
            'color' => [
                'bail',
                'required',
                'regex:/^[a-fA-F0-9]{6}$/',
            ],
            'image' => 'image|max:3072',
            'g-recaptcha-response' => 'bail|required|recaptcha',
            'accept-license' => 'required|accepted',
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
            'color.regex' => 'Invalid color value.',
        ];
    }
}
