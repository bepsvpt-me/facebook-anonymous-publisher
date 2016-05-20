<?php

namespace App\Http\Requests\Install;

use App\Http\Requests\Request;

class FacebookRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'app_id' => 'required|string',
            'app_secret' => 'required|string',
            'default_graph_version' => 'required|string',
            'default_access_token' => 'required|string',
        ];
    }
}
