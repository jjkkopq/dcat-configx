<?php

namespace Jjkkopq\DcatConfigx\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class AfterInjectDcatConfigx
 * @package Jjkkopq\Configx\Http\Middleware
 */
class AfterInjectDcatConfigx
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
