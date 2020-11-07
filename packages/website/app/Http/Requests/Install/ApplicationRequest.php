<?php

namespace App\Http\Requests\Install;

use App\Http\Requests\Request;

class ApplicationRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|string|max:24',
            'password' => 'required|string|min:6|different:username',
            'page_name' => 'required|string',
            'extra_content' => 'string',
            'license' => 'string',
        ];
    }
}
