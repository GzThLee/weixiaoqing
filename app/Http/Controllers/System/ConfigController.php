<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class ConfigController extends Controller
{
    /**
     * 配置列表视图
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return View::make('system.configs.index');
    }

    /**
     * 配置数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $rows = $request->input('limit', 20);
        $res = Config::paginate($rows);
        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items()
        ]);
    }

    /**
     * 添加配置
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return View::make('system.configs.create');
    }

    /**
     * 添加配置
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        try {
            Config::create($data);
            return Redirect::to(URL::route('system.configs'))->with(['success' => '添加成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('添加失败');
        }

    }

    /**
     * 更新配置
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $config = Config::find($id);
        return View::make('system.configs.edit', compact('config'));
    }

    /**
     * 更新配置
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        try {
            $config = Config::find($id);
            $config->update($data);
            return Redirect::to(URL::route('system.configs'))->with(['success' => '更新成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('更新失败');
        }
    }

    /**
     * 删除配置
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids', []);
        try {
            Config::whereIn('config_id', $ids)->delete();
            return Response::json(['code' => 0, 'msg' => '删除成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Response::json(['code' => 0, 'msg' => '删除失败']);
        }
    }
}
