<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $method = $this->route()->getActionMethod();
        return $this->{$method . 'Validator'}();
    }

    private function loginValidator()
    {
        return [
            'username'         => 'required|string|max:14',
            'password'         => 'required|string|min:6|max:50'
        ];
    }

    private function registerValidator()
    {
        return [
            'name'           =>  'required|string|max:100',
            'username'          =>  'required|string|unique:users,username',
            'password'       =>  'required|string|confirmed|min:6|max:100',
        ];
    }

}
