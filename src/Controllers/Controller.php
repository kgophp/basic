<?php

namespace YK\Basic\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use YK\Basic\Controllers\HasControllerResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, HasControllerResponse;

    protected function getPageSize($quest){
        $pagesize=$quest->get('pagesize');
        if (empty($pagesize)&& $pagesize>200)
            $pagesize=15;
        return $pagesize;
    }
}
