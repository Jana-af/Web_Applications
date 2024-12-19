<?php

namespace App\Http\Requests;

use Exception;

class FileActionsLogRequest extends GenericRequest
{
    /**
     * Dynamically Get the the validation rules based on the request's action method.
     *
     * @return array
     */
    public function rules()
    {
        $method = request()->route()->getActionMethod();

        try {
            return $this->{$method . 'Validator'}();
        } catch (Exception $e) {
            return $this->defaultValidator();
        }
    }
    private function defaultValidator()
    {
        return [
            'pdf'   => 'nullable|boolean'
        ];
    }
}
