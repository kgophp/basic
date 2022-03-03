<?php

namespace YK\Basic\Middleware;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Exceptions\UnauthorizedException;

class Authenticate
{
    /**
     * @author nash< >
     * @param $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $permission = Route::currentRouteName();
        $pos=strpos($permission,'public.');

        if ($pos===0 ||Auth::user()->can($permission)) {
            return $next($request);
        }

        throw UnauthorizedException::forPermissions([$permission]);
    }
}
