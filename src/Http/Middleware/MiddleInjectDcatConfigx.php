<?php

namespace Jjkkopq\DcatConfigx\Http\Middleware;

use Jjkkopq\DcatConfigx\Support;
use Closure;
use Illuminate\Http\Request;

class MiddleInjectDcatConfigx
{
    public function handle(Request $request, Closure $next)
    {
        $support = new Support();
        $support->initConfig();
        $support->gridRowActionsRight();
        $support->injectFields();
        $support->footerRemove();

        return $next($request);
    }
}
