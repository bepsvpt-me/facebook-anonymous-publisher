<?php

namespace App\Http\Requests\Install;

use App\Http\Requests\Request;

class GoogleRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ga' => 'string',
            'ad-client' => 'required_with:ad-slot|string',
            'ad-slot' => 'required_with:ad-client|string',
        ];
    }
}
