<?php

namespace App\Http\Requests\Install;

use App\Http\Requests\Request;

class RecaptchaRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'public_key' => 'required|string',
            'private_key' => 'required|string',
        ];
    }
}
