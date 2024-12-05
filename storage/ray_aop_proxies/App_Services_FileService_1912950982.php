<?php

namespace App\Services;

use App\AOP\Logger;
use App\Models\File;
use App\Models\FileBackup;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use App\Traits\FileTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;

class FileService_1912950982 extends FileService implements \Ray\Aop\WeavedInterface  {
    use \Ray\Aop\InterceptTrait;
        #[\App\AOP\Logger()]
      public function checkIn($validatedData)
    {
        return $this->_intercept(__FUNCTION__, func_get_args());
    }

        #[\App\AOP\Logger()]
      public function checkOut($validatedData)
    {
        return $this->_intercept(__FUNCTION__, func_get_args());
    }

        #[\App\AOP\Logger()]
      public function update($validatedData, $modelId)
    {
        return $this->_intercept(__FUNCTION__, func_get_args());
    }
}