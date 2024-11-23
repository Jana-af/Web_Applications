<?php

namespace App\Http\Middleware;

use App\Services\FileService;
use Closure;
use Exception;
use Illuminate\Http\Request;

class CheckInAuthority
{
    public function __construct(private FileService $fileService) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $fileIds = isset($request->file_ids) ? $request->file_ids : [$request->id];
        $isOwner = $this->fileService->isCheckInOwner($fileIds);

        if (!$isOwner) {
            throw new Exception(__('messages.checkOutFailed'), 401);
        }

        return $next($request);
    }
}
