<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class BaseController extends Controller
{
    /**
     * 配置列表视图
     * @return \Illuminate\Contracts\View\View
     */
    public function logs()
    {
        return View::make('system.base.log');
    }

    /**
     * 用户日志
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logsData(Request $request)
    {
        $rows = $request->input('limit', 20);
        $res = UserLog::orderByDesc('log_id')->paginate($rows);
        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items(),
        ]);
    }
}
