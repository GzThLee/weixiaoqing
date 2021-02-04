<?php

namespace App\Http\Controllers\System;

use App\Models\Roles;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class UsersController extends Controller
{
    use AuthenticatesUsers;

    /**
     * 用于登录的字段
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * 登录视图
     * @return \Illuminate\Contracts\View\View
     */
    public function loginForm()
    {
        return View::make('system.users.login');
    }

    /**
     * 验证登录字段
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'captcha' => 'required|captcha',
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * 列表视图
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return View::make('system.users.index');
    }

    /**
     * 列表数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $search = $request->all(['username', 'nickname', 'mobile']);
        $rows = $request->input('limit', 20);
        $res = Users::when($search['username'] != null && $search['username'] != '', function ($query) use ($search) {
            return $query->where('username', 'LIKE', '%' . $search['username'] . '%');
        })->when($search['nickname'] != null && $search['nickname'] != '', function ($query) use ($search) {
            return $query->where('nickname', 'LIKE', '%' . $search['nickname'] . '%');
        })->when($search['mobile'] != null && $search['mobile'] != '', function ($query) use ($search) {
            return $query->where('mobile', 'LIKE', '%' . $search['mobile'] . '%');
        })->paginate($rows);

        return Response::json([
            'code' => 0,
            'msg' => '请求成功',
            'count' => $res->total(),
            'data' => collect($res->items())->map(function ($user) {
                $user->roles = $user->roles()->pluck('display_name')->implode(',');
                return $user;

            })
        ]);
    }

    /**
     * 添加用户视图
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $roles = Roles::get();
        return View::make('system.users.create', compact('roles'));
    }

    /**
     * 添加用户
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $roles = $request->input('roles', []);
        try {
            $user = Users::create($data);
            $user->syncRoles($roles);
            return Redirect::to(URL::route('system.users'))->with(['success' => '添加成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('添加失败');
        }
    }

    /**
     * 编辑用户视图
     * @param $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            /** @var Users $user */
            $user = Users::findOrFail($id);
            $roles = Roles::get();
            foreach ($roles as $role) {
                $role->own = $user->hasRole($role);
            }
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('数据有误');
        }
        return View::make('system.users.edit', compact('roles', 'user'));
    }

    /**
     * 更新用户
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request)
    {
        $password = $request->input('password', '');
        $data = $request->except('password');
        $roles = $request->input('roles', []);
        if ($password != '') {
            $data['password'] = $password;
        }
        try {
            $user = Users::findOrFail($id);
            $user->update($data);
            $user->syncRoles($roles);
            return Redirect::to(URL::route('system.users'))->with(['success' => '修改成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('数据有误');
        }
    }

    /**
     * 删除用户
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids', []);
        Users::whereIn('id', $ids)->delete();
        return Response::json(['code' => 0, 'msg' => '删除成功']);
    }

    /**
     * 修改密码
     * @return \Illuminate\Contracts\View\View
     */
    public function changePasswordForm()
    {
        return View::make('system.users.change_password');
    }

    /**
     * 修改密码
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePasswordPut(Request $request)
    {
        $data = $request->all();
        /** @var Users $user */
        $user = Auth::user();
        if (!Hash::check($data['password'], $user->getAuthPassword())) {
            return Redirect::back()->withInput()->withErrors('密码有误');
        }

        if ($data['new_password'] != $data['new_password_confirmation']) {
            return Redirect::back()->withInput()->withErrors('新密码有误');
        }

        try {
            $user->update(['password' => $data['new_password']]);
            return Redirect::to(URL::route('users.changePasswordForm'))->with(['success' => '修改成功']);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            Log::error($exception->getFile() . ' line:' . $exception->getLine() . ' code:' . $exception->getCode());
            return Redirect::back()->withInput()->withErrors('修改失败');
        }
    }

    /**
     * 注册视图
     * @return \Illuminate\Contracts\View\View
     */
    public function registerForm()
    {
        return View::make('system.users.register');
    }

    /**
     * 注册
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $data = $request->all(['nickname', 'username', 'password', 'password_confirmation']);
        if ($data['password'] != $data['password_confirmation']) {
            return Redirect::back()->withInput()->withErrors('密码不一致');
        }

        $validator = Validator::make($data, [
            'username' => ['required', 'unique:users'],
        ]);
        if($validator->fails()){
            return Redirect::back()->withInput()->withErrors($validator->errors()->first());
        }

        $user = Users::create($data);

        $user->assignRole('user');
        return Redirect::to(URL::route('users.login'))->with(['success' => '注册成功']);
    }

}
