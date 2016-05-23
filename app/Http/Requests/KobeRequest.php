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
        $rules = [
            'content' => 'required|string|max:500',
            'image' => 'image|max:3072',
            'g-recaptcha-response' => 'bail|required|recaptcha',
            'accept-license' => 'required|boolean',
        ];

        if ('127.0.0.1' === $this->ip()) {
            unset($rules['g-recaptcha-response']);
        }

        return $rules;
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
