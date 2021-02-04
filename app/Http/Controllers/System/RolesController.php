<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Permissions;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class RolesController extends Controller
{
    /**
     * 角色列表
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return View::make('system.roles.index');
    }

    /**
     * 角色数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $rows = $request->input('limit', 20);
        $res = Roles::paginate($rows);
        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items()
        ]);
    }

    /**
     * 添加角色
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return View::make('system.roles.create');
    }

    /**
     * 添加角色
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        try {
            Roles::create($data);
            return Redirect::to(URL::route('system.roles'))->with(['success' => '添加成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('添加失败');
        }

    }

    /**
     * 更新角色
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $role = Roles::findOrFail($id);
        return View::make('system.roles.edit', compact('role'));
    }

    /**
     * 更新角色
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $role = Roles::findOrFail($id);
        $data = $request->all();
        try {
            $role->update($data);
            return Redirect::to(URL::route('system.roles'))->with(['success' => '更新成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('更新失败');
        }
    }

    /**
     * 删除角色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (!is_array($ids) || empty($ids)) {
            return Response::json(['code' => 1, 'msg' => '请选择删除项']);
        }
        $role = Roles::find($ids[0]);
        if (!$role) {
            return Response::json(['code' => 1, 'msg' => '角色不存在']);
        }
        try {
            $role->delete();
            return Response::json(['code' => 0, 'msg' => '删除成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Response::json(['code' => 0, 'msg' => '存在子角色禁止删除']);
        }
    }

    /**
     * 分配权限
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function permission(Request $request, $id)
    {
        $role = Roles::findOrFail($id);
        $permissions = Permissions::with('allChilds')->where('pid', 0)->get();
        foreach ($permissions as $p1) {
            $p1->own = $role->hasPermissionTo($p1->id) ? 'checked' : false;
            if ($p1->childs->isNotEmpty()) {
                foreach ($p1->childs as $p2) {
                    $p2->own = $role->hasPermissionTo($p2->id) ? 'checked' : false;
                    if ($p2->childs->isNotEmpty()) {
                        foreach ($p2->childs as $p3) {
                            $p3->own = $role->hasPermissionTo($p3->id) ? 'checked' : false;
                        }
                    }
                }
            }
        }
        return View::make('system.roles.permission', compact('role', 'permissions'));
    }

    /**
     * 存储权限
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignPermission(Request $request, $id)
    {
        $permissions = $request->get('permissions', []);
        try {
            $role = Roles::findOrFail($id);
            $role->syncPermissions($permissions);
            return Redirect::to(URL::route('system.roles'))->with(['success' => '授权成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('授权失败');
        }
    }
}
