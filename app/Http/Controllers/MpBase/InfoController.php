<?php

namespace App\Http\Controllers\MpBase;

use App\Console\Commands\Sync\MpPermissions;
use App\Http\Controllers\Controller;
use App\Jobs\RunCommand;
use App\Models\Mp;
use App\Models\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class InfoController extends Controller
{
    /**
     * 信息页面
     * @return \Illuminate\Contracts\View\View
     */
    public function indexGet()
    {
        $mp = Mp::where('user_id', Auth::id())->with('permissions')->first();
        $mpPermissionIds = $mp->permissions->pluck('id')->toArray();
        $permissions = Permissions::with(['childs'])->whereIn('name', ['pmsn.mp.base', 'pmsn.mp.advanced'])->where(['pid' => 0, 'type' => Permissions::MENU_TYPE])->orderByDesc('pid')->orderByDesc('sort')->get();
        return View::make('mp_base.info.index', compact('mp', 'permissions', 'mpPermissionIds'));
    }

    /**
     * 配置保存
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function savePost(Request $request)
    {
        $data = $request->all(['app_id', 'app_secret', 'valid_token']);
        try {
            $mp = Mp::updateOrCreate(['user_id' => Auth::id()], $data);
            try {
                $app = mp_app($mp->app_id, $mp->app_secret);
                $app->access_token->getToken(true); // 强制重新从微信服务器获取 token.
                $mp->update(['valid_status' => Mp::VALID_SUCCESS]);
                RunCommand::dispatch(MpPermissions::class, ['mp_id' => $mp->mp_id]);     //同步权限
            } catch (\Exception $exception) {
                $mp->update(['valid_status' => Mp::VALID_FAIL]);
                mark_error_log($exception);
                return Redirect::to(route('mp.base.info'))->with(['warning' => '保存成功:微信配置有误']);
            }
            return Redirect::to(route('mp.base.info'))->with(['success' => '保存成功:配置正确']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('保存失败');
        }
    }

    /**
     * 执行脚本
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function commandPost(Request $request)
    {
        $command = $request->input('command', '');
        if ($command != '') {
            $mpId = Mp::where('user_id', Auth::id())->value('mp_id');
            RunCommand::dispatch($command, ['mp_id' => $mpId]);
        }
        return Response::json([
            'code' => 0,
            'msg' => '同步中，请稍后刷新即可',
        ]);
    }
}
