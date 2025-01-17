<?php

namespace App\Http\Middleware;

use App\Services\FileService;
use App\Services\GroupUserService;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccessToFile
{

    public function __construct(private FileService $fileService, private GroupUserService $groupUserService) {}
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     *///TODO check
    public function handle(Request $request, Closure $next, $type = null)
    {
        if (isset($type)) {
            if(isset($request->group_id)){
                $groupId = $request->group_id;
            }else if (isset($request->id)) {
                $file = $this->fileService->findById($request->id);
                $groupId = $file->group_id;
            } else {
                $result = $this->fileService->getFilesGroupId($request->file_ids);
                if (!$result) {
                    throw new Exception(__('messages.bulkCheckInMultiGoup'), 409);
                }
                $groupId = $result;
            }

            $exception = false;
            if ($type == 'delete') {
                if (!$this->groupUserService->checkIfAuthUserOwnTheGroup($groupId, Auth::id())  && Auth::user()->role == 'USER') {
                    $exception = true;
                }
            } elseif ($type == 'findById') {
                if (!$this->groupUserService->checkUserInGroup(Auth::id(), $groupId) && Auth::user()->role == 'USER') {
                    $exception = true;
                }
            }
            if ($exception) {
                throw new Exception(__('messages.userDoesNotHavePermissionOnGroup'), 401);
            }
        }
        return $next($request);
    }
}
