<?php

namespace App\Http\Middleware;

use App\Services\GroupUserService;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckGroupAuthority
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
        if (isset($request->group_id)) {
            if (!$this->groupUserService->checkIfAuthUserOwnTheGroup(Auth::id(), $request->group_id)) {
                throw new Exception(__('messages.userDoesNotHavePermissionOnGroup'), 401);
            }
        }
        return $next($request);
    }
}
