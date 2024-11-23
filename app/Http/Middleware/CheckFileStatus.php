<?php

namespace App\Http\Middleware;

use App\Services\FileService;
use Closure;
use Exception;
use Illuminate\Http\Request;

class CheckFileStatus
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
        $fileIds = isset($request->file_ids) ? $request->file_ids : array_values([$request->id]);

        $isFree = $this->fileService->checkIfFilesFree($fileIds);

        if (!$isFree) {
            if (count($fileIds) > 1) {
                $key = 'messages.bulkCheckInFailed';
            } else {
                $key = 'messages.checkInFailed';
            }
            throw new Exception(__($key),401);
        }

        return $next($request);
    }
}
