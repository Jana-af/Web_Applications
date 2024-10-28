<?php

namespace App\Http\Requests;

class GroupUserRequest extends GenericRequest
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
}