<?php
namespace App\AOP\Interceptors;

use Exception;
use Ray\Aop\MethodInvocation;
use Ray\Aop\MethodInterceptor;
use Illuminate\Support\Facades\DB;

class TransactionalInterceptor implements MethodInterceptor
{
    public function invoke(MethodInvocation $invocation)
    {
        try {
            DB::beginTransaction();

            $result = $invocation->proceed();

            DB::commit();

            return $result;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
