<?php

namespace App\Http\Middleware;

use App\Models\UserLog;
use Closure;
use Illuminate\Http\Request;

class UserOperateLog
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        UserLog::create([
            'user_id' => $request->user()->id,
            'uri' => $request->getUri(),
            'parameter' => urldecode(http_build_query($request->except(['_token', '_method']))),
            'method' => $request->getMethod(),
            'user_agent' => $request->header('User-Agent'),
            'ip' => $request->ip(),
            'route_name' => $request->route()->getName() ?? ''
        ]);

        return $next($request);
    }
}
