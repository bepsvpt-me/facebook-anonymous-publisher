<?php

namespace App\Http\Requests;

class BlockWordRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'value' => 'bail|required|string|max:48|unique:blocks,value,NULL,id,type,keyword',
        ];
    }
}
