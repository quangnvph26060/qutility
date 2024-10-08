<?php

namespace Wuang\Qutility;

use Closure;

class Utility{

    public function handle($request, Closure $next)
    {
        if (!Helpmate::sysPass()) {
            return redirect()->route(Wuang::acRouter());
        }
        abort_if(Helpmate::sysPass() === 99 && request()->isMethod('post'),401);
        return $next($request);
    }
}