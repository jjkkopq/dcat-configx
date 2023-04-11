<?php

namespace Jjkkopq\DcatConfigx\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class BeforeInjectDcatConfigx
 * @package Celaraze\DcatConfigx\Http\Middleware
 */
class BeforeInjectDcatConfigx
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
