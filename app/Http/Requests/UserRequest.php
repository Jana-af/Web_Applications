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

    private function inviteUserToGroupValidator(){
        return [
            'user_id' => 'required|integer|exists:users,id',
            'group_id' => 'required|integer|exists:groups,id'
        ];
    }

    private function getMyInvitesValidator(){
        return[
            'send' => 'required|boolean'
        ];
    }

    private function acceptOrRejectOrCancelInviteValidator(){
        return[
            'id' => 'required|exists:group_user,id',
            'action' => 'required|string|max:50|in:reject,accept,cancel'
        ];
    }

    private function getUsersInGroupValidator(){
        return[
            'id' => 'required|exists:groups,id',
        ];
    }
}
