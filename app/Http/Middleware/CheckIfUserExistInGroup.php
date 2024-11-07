<?php

namespace App\Http\Middleware;

use App\Services\GroupUserService;
use Closure;
use Exception;
use Illuminate\Http\Request;

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
       if (isset($request->group_id) && isset($request->user_id)) {
            if ($this->groupUserService->checkUserInGroup($request->user_id, $request->group_id)) {
                throw new Exception(__('messages.userAlreadyInvitedToGroup'), 401);
            }
        }
        return $next($request);
    }
}
