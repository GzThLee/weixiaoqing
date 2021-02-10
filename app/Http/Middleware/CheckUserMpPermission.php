<?php

namespace App\Http\Middleware;

use App\Models\Users;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class CheckUserMpPermission
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param $permissionName
     * @return \Illuminate\Http\Response|mixed
     */
    public function handle(Request $request, Closure $next, $permissionName = '')
    {
        if ($request->user()->mp->permissions->where('name', $permissionName)->first()) {
            return $next($request);
        } else {
            session()->flash('warning_tip', '😢 抱歉！您的公众号的配置有误，此功能可能不太行喔~');
            return $next($request);
        }
    }
}
