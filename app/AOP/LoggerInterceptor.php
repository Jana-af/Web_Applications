<?php
namespace App\AOP;

use Exception;
use Ray\Aop\MethodInvocation;
use Ray\Aop\MethodInterceptor;
use App\Models\FileActionsLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LoggerInterceptor implements MethodInterceptor
{
    public function invoke(MethodInvocation $invocation)
    {
        $methodName = $invocation->getMethod()->getName();
        $arguments = $invocation->getArguments();

        $validatedData = $arguments[0] ?? [];
        $modelId = isset($arguments[1]) ? [$arguments[1]] : $validatedData['file_ids'];

        $fileLogs = [];

        try {
            foreach ($modelId as $id) {

                $fileLogs[$id] = FileActionsLog::create([
                    'file_id' => $id,
                    'user_id' => Auth::id(),
                    'action' => $methodName,
                    'status' => 'STARTING',
                ]);
            }

            $result = $invocation->proceed();

            foreach ($fileLogs as $fileLog) {
                $fileLog->update(['status' => 'SUCCESS']);
            }

            return $result;

        } catch (Exception $e) {
            DB::rollBack();
            foreach ($fileLogs as $fileLog) {
                $fileLog->update([
                    'status' => 'FAILED',
                    'exception' => $e->getMessage(),
                ]);
            }
            throw $e;
        }
    }

}
