<?php

namespace App\Http\Controllers\WorkBase;

use App\Http\Controllers\Controller;
use App\Models\WorkWelcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class WelcomesController extends Controller
{
    /**
     * 视图
     * @return \Illuminate\Contracts\View\View
     */
    public function indexGet()
    {
        return View::make('work_base.welcomes.index');
    }

    /**
     * 数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataGet(Request $request)
    {
        $rows = $request->input('limit', 30);
        $res = WorkWelcome::with('user')->paginate($rows);
        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items()
        ]);
    }

    /**
     * 添加视图
     * @return \Illuminate\Contracts\View\View
     */
    public function createGet()
    {
        return View::make('work_base.welcomes.create');
    }

    /**
     * 添加方法
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createPost(Request $request)
    {
        $data = $request->all();
        try {
            $data['work_id'] = Auth::user()->work->work_id;
            $data['text'] = ['content' => $data['content']];
            WorkWelcome::create($data);
            return Redirect::to(route('work.base.welcomes'))->with(['success' => '添加成功']);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            Log::error($exception->getFile() . ' line:' . $exception->getLine() . ' code:' . $exception->getCode());
            return Redirect::back()->withInput()->withErrors('添加失败');
        }
    }

    /**
     * 编辑视图
     * @param $w_wlcm_id
     * @return \Illuminate\Contracts\View\View
     */
    public function editGet($w_wlcm_id)
    {
        $workWelcome = WorkWelcome::where('w_wlcm_id', $w_wlcm_id)->first();
        return View::make('work_base.welcomes.edit', compact('workWelcome'));
    }

    /**
     * 编辑欢迎语
     * @param $work_welcome_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePut($w_wlcm_id,Request $request){
        $data = $request->all();
        try {
            $workWelcome = WorkWelcome::where('w_wlcm_id', $w_wlcm_id)->first();
            $data['text'] = ['content' => $data['content']];
            $workWelcome->update($data);
            return Redirect::to(route('work.base.welcomes'))->with(['success' => '编辑成功']);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            Log::error($exception->getFile() . ' line:' . $exception->getLine() . ' code:' . $exception->getCode());
            return Redirect::back()->withInput()->withErrors('编辑失败');
        }
    }
}
