<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class PermissionsController extends Controller
{
    /**
     * 权限列表
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return View::make('system.permissions.index');
    }

    /**
     * 权限数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function data()
    {
        $res = Permissions::orderByDesc('sort')->get();
        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->count(),
            'data' => $res
        ]);
    }

    /**
     * 添加权限
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $permissions = Permissions::with('allChilds')->where('pid', 0)->orderByDesc('sort')->get();
        return View::make('system.permissions.create', compact('permissions'));
    }

    /**
     * 添加权限
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        try {
            Permissions::create($data);
            return Redirect::to(URL::route('system.permissions'))->with(['success' => '添加成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('添加失败');
        }

    }

    /**
     * 更新权限
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $permission = Permissions::findOrFail($id);
        $permissions = Permissions::with('allChilds')->where('pid', 0)->orderByDesc('sort')->get();
        return View::make('system.permissions.edit', compact('permission', 'permissions'));
    }

    /**
     * 更新权限
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $permission = Permissions::findOrFail($id);
        $data = $request->all();
        try {
            $permission->update($data);
            return $request->ajax() ? Response::json(['code' => 0, 'msg' => '更新成功']) : Redirect::to(URL::route('system.permissions'))->with(['success' => '更新成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return $request->ajax() ? Response::json(['code' => 400, 'msg' => '更新失败']) : Redirect::back()->withErrors('更新失败');
        }
    }

    /**
     * 删除权限
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (!is_array($ids) || empty($ids)) {
            return Response::json(['code' => 1, 'msg' => '请选择删除项']);
        }
        $permission = Permissions::with('childs')->find($ids[0]);
        if (!$permission) {
            return Response::json(['code' => 1, 'msg' => '权限不存在']);
        }
        //如果有子权限，则禁止删除
        if ($permission->childs->isNotEmpty()) {
            return Response::json(['code' => 1, 'msg' => '存在子权限禁止删除']);
        }
        try {
            $permission->delete();
            return Response::json(['code' => 0, 'msg' => '删除成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Response::json(['code' => 0, 'msg' => '存在子权限禁止删除']);
        }
    }
}
