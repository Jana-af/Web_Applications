<?php

namespace App\Http\Middleware;

use App\Services\GroupUserService;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIfUserExistInGroup
{
    public function __construct(private GroupUserService $groupUserService) {}
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
       if (isset($request->group_id) ) {
        $userId = isset($request->user_id) ? $request->user_id : Auth::id();
            if (!$this->groupUserService->checkUserInGroup($userId, $request->group_id)) {
                throw new Exception(__('messages.userDoesNotHavePermissionOnGroup'), 401);
            }
        }
        return $next($request);
    }
}
