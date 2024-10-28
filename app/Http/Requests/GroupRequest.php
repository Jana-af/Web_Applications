<?php

namespace App\Http\Requests;

class GroupRequest extends GenericRequest
{
    /**
     * Dynamically Get the the validation rules based on the request's action method.
     *
     * @return array
     */
    public function rules()
    {
        $method = request()->route()->getActionMethod();
        return $this->{$method . 'Validator'}();
    }

    private function storeValidator()
    {
        return [
            'group_name'         => 'required|string|max:255',
            'group_type'         => 'required|string|max:50|in:PRIVATE,SHARED'
        ];
    }
}
