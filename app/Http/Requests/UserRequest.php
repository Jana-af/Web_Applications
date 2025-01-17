<?php

namespace App\Http\Requests;

class UserRequest extends GenericRequest
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

    private function inviteUserToGroupValidator()
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'group_id' => 'required|integer|exists:groups,id'
        ];
    }

    private function getMyInvitesValidator()
    {
        return [
            'send' => 'required_without:group_id|boolean',
            'group_id'  => 'nullable'
        ];
    }

    private function acceptOrRejectOrCancelInviteValidator()
    {
        return [
            'id' => 'required|exists:group_user,id',
            'action' => 'required|string|max:50|in:reject,accept,cancel'
        ];
    }

    private function getUsersInGroupValidator()
    {
        return [
            'id' => 'required|exists:groups,id',
        ];
    }

    private function getAllUsersValidator()
    {
        return [
            'group_id'    => 'nullable|exists:groups,id'
        ];
    }

    private function storeValidator()
    {
        return [
            'name'           =>  'required|string|max:100',
            'username'          =>  'required|string|unique:users,username',
            'password'       =>  'required|string|confirmed|min:6|max:100',
            'role'       =>  'required|in:ADMIN,USER',
        ];
    }

    private function removeUserFromGroupValidator()
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'group_id' => 'required|integer|exists:groups,id'
        ];
    }
    private function updateDeviceTokenValidator()
    {
        return [
            'device_token' => 'required|string'
        ];
    }
}
