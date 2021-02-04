<?php

namespace App\Http\Controllers\WorkAdvanced;

use App\Http\Controllers\Controller;
use App\Models\Mp;
use App\Models\Users;
use App\Models\WorkChannelCode;
use App\Models\WorkChannelCodeCustomer;
use App\Models\WorkCustomer;
use App\Models\WorkUser;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ChannelCodesController extends Controller
{
    /**
     * 视图
     * @return \Illuminate\Contracts\View\View
     */
    public function indexGet()
    {
        return View::make('work_advanced.channel_codes.index');
    }

    /**
     * 数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataGet(Request $request)
    {
        $rows = $request->input('limit', 30);
        $res = WorkChannelCode::with(['user', 'record'])->paginate($rows);
        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => collect($res->items())->map(function ($item) {
                $item->add_number = collect($item->record)->count();
                unset($item->record);
                return $item;
            })
        ]);
    }

    /**
     * 添加视图
     * @return \Illuminate\Contracts\View\View
     */
    public function createGet()
    {
        return View::make('work_advanced.channel_codes.create');
    }

    /**
     * 添加方法
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createPost(Request $request)
    {
        $data = $request->all();
        try {
            /** @var Users $user */
            $user = Auth::user();
            $data['work_id'] = $user->work->work_id;
            $data['text'] = ['content' => $data['text']];
            $channelCode = WorkChannelCode::create($data);

            $app = work_app($user->work->corpid, $user->work->corpsecret);
            $workUserId = WorkUser::where('w_user_id', $data['w_user_id'])->value('userid');
            $contactWayCreateRes = $app->contact_way->create(1, 2, [
                'remark' => $data['name'] . '渠道客户',
                'skip_verify' => true,
                'state' => "CCODE:{$channelCode->w_ccode_id}:0",
                'user' => [$workUserId],
            ]);
            $contactWayGetRes = $app->contact_way->get($contactWayCreateRes['config_id']);

            $channelCode->update(['config_id' => $contactWayCreateRes['config_id'], 'qr_code_url' => $contactWayGetRes['contact_way']['qr_code']]);
            return Redirect::to(route('work.advanced.channel_codes'))->with(['success' => '添加成功']);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            Log::error($exception->getFile() . ' line:' . $exception->getLine() . ' code:' . $exception->getCode());
            return Redirect::back()->withInput()->withErrors('添加失败');
        }
    }

    /**
     * 编辑视图
     * @param $w_ccode_id
     * @return \Illuminate\Contracts\View\View
     */
    public function editGet($w_ccode_id)
    {
        $workChannelCode = WorkChannelCode::where('w_ccode_id', $w_ccode_id)->first();
        return View::make('work_advanced.channel_codes.edit', compact('workChannelCode'));
    }

    /**
     * 编辑
     * @param $w_ccode_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePut($w_ccode_id, Request $request)
    {
        $data = $request->all();
        try {
            $workChannelCode = WorkChannelCode::where('w_ccode_id', $w_ccode_id)->first();
            $data['text'] = ['content' => $data['text']];
            $workChannelCode->update($data);
            return Redirect::to(route('work.advanced.channel_codes'))->with(['success' => '编辑成功']);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            Log::error($exception->getFile() . ' line:' . $exception->getLine() . ' code:' . $exception->getCode());
            return Redirect::back()->withInput()->withErrors('编辑失败');
        }
    }

    /**
     * 删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyDelete(Request $request)
    {
        $ids = $request->input('ids', '');
        /** @var Users $user */
        $user = Auth::user();
        $app = work_app($user->work->corpid, $user->work->corpsecret);
        WorkChannelCode::whereIn('w_ccode_id', explode(',', $ids))->get()
            ->map(function ($channelCode) use ($app) {
                $app->contact_way->delete($channelCode->config_id);
                $channelCode->delete();
            });
        return Response::json([
            'code' => 0,
            'msg' => '删除成功'
        ]);
    }

    /**
     * 记录视图
     * @return \Illuminate\Contracts\View\View
     */
    public function recordGet($w_ccode_id)
    {
        $workChannelCode = WorkChannelCode::where('w_ccode_id', $w_ccode_id)->first();
        return View::make('work_advanced.channel_codes.record', compact('workChannelCode'));
    }

    /**
     * 记录数据
     * @param $w_ccode_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordDataGet($w_ccode_id, Request $request)
    {
        $rows = $request->input('limit', 30);
        $res = WorkChannelCodeCustomer::where('w_ccode_id', $w_ccode_id)->with('customer')->paginate($rows);
        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items()
        ]);
    }


}
