<?php

namespace App\Http\Controllers\WorkBase;

use App\Http\Controllers\Controller;
use App\Jobs\RunCommand;
use App\Models\Work;
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
        $work = Work::where('user_id', Auth::id())->first();
        return View::make('work_base.info.index', compact('work'));
    }

    /**
     * 配置保存
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function savePost(Request $request)
    {
        $data = $request->all(['corpid', 'corpsecret', 'usersecret', 'token', 'aes_key', 'app_agentid', 'app_secret', 'app_token', 'app_aes_key']);
        try {
            Work::updateOrCreate(['user_id' => Auth::id()], $data);
            return Redirect::to(route('work.base.info'))->with(['success' => '保存成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('保存失败');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function commandPost(Request $request)
    {
        $command = $request->input('command', '');
        if ($command != '') {
            $workId = Work::where('user_id', Auth::id())->value('work_id');
            RunCommand::dispatch($command, ['work_id' => $workId]);
        }
        return Response::json([
            'code' => 0,
            'msg' => '同步中，请稍后刷新即可',
        ]);
    }
}
