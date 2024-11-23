<?php

namespace App\Http\Middleware;

use App\Services\FileService;
use Closure;
use Exception;
use Illuminate\Http\Request;

class CheckFileOriginalName
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
        $oldFile = $this->fileService->findById($request->id);
        $newFile = $request->file;

        if ($oldFile->file_name != $newFile->getClientOriginalName()) {
            throw new Exception(__('messages.fileModificationFailed'), 409);
        }
        return $next($request);
    }
}
