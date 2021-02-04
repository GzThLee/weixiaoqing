<?php

namespace App\Http\Controllers\MpAdvanced;

use App\Http\Controllers\Controller;
use App\Http\Services\MpService;
use App\Models\Mp;
use App\Models\MpChannelCode;
use App\Models\MpChannelCodeFan;
use App\Models\MpTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ChannelCodesController extends Controller
{
    /**
     * 视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexGet()
    {
        return view('mp_advanced.channel_codes.index');
    }

    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataGet(Request $request)
    {
        $rows = $request->input('limit', 20);
        $data = $request->all(['name']);
        $res = MpChannelCode::whereIn('status', [0, 1])
            ->when($data['name'] != '', function ($query) use ($data) {
                return $query->where('name', 'LIKE', '%' . $data['name'] . '%');
            })->selectRaw('name,m_ccode_id,scene_str,new_fans_subscribe,new_fans_unsubscribe,old_fans_subscribe,old_fans_unsubscribe,(new_fans_subscribe - new_fans_unsubscribe) AS new_fans_net_increase,(old_fans_subscribe - old_fans_unsubscribe) AS old_fans_net_increase')->orderByDesc('m_ccode_id')
            ->paginate($rows);

        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items()
        ]);
    }

    /**
     * 添加视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createGet()
    {
        $tags = MpTag::pluck('name', 'm_tag_id');
        return view('mp_advanced.channel_codes.create', compact('tags'));
    }

    /**
     * 添加
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function storePost(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all(['m_tag_id', 'name', 'media_type', 'content']);
            $mp = Auth::user()->mp;
            $content = MpService::getMediaContent($data['media_type'], $data['content']);
            $sceneStr = 'MP_CC_' . Str::random(16);
            $app = mp_app($mp->app_id, $mp->app_secret);
            $result = $app->qrcode->temporary($sceneStr, 30 * 24 * 3600);

            $save = $data;
            $save['mp_id'] = $mp->mp_id;
            $save['scene_str'] = $sceneStr;
            $save['ticket'] = $result['ticket'];
            $save['url'] = $result['url'];
            $save['ticket_url'] = $app->qrcode->url($result['ticket']);
            $save['content'] = $content;
            $createRes = MpChannelCode::create($save);
            QrCode::format('png')->size(200)->margin(0)->generate($createRes->url, storage_path('app/public/channel_codes') . "/{$createRes->m_ccode_id}_{$sceneStr}.png");
            DB::commit();
            return Redirect::to(route('mp.advanced.channel_codes'))->with(['success' => '创建成功']);
        } catch (\Exception $exception) {
            DB::rollback();
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('创建失败');
        }
    }

    /**
     * 编辑视图
     * @param $m_ccode_id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editGet($m_ccode_id, Request $request)
    {
        $channelCode = MpChannelCode::where('m_ccode_id', $m_ccode_id)->first();
        $tags = MpTag::pluck('name', 'm_tag_id');
        return view('mp_advanced.channel_codes.edit', compact('channelCode', 'tags'));
    }

    /**
     * 编辑
     * @param $m_ccode_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePut($m_ccode_id, Request $request)
    {
        try {
            $data = $request->all(['m_tag_id', 'name', 'media_type', 'content']);

            $save = $data;
            $save['content'] = MpService::getMediaContent((int)$data['media_type'], $data['content']);;
            MpChannelCode::updateOrCreate(['m_ccode_id' => $m_ccode_id], $save);

            return Redirect::to(route('mp.advanced.channel_codes'))->with(['success' => '保存成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('保存失败');
        }
    }

    /**
     * 删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyDelete(Request $request)
    {
        $channelCodeIds = $request->input('m_ccode_ids', '');
        MpChannelCode::whereIn('m_ccode_id', explode(',', $channelCodeIds))->update(['status' => -1]);
        return Response::json([
            'code' => 0,
            'msg' => '删除成功'
        ]);
    }

    /**
     * 下载
     * @param $m_ccode_id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadGet($m_ccode_id, Request $request)
    {
        $channelCode = MpChannelCode::where('m_ccode_id', $m_ccode_id)->select('scene_str', 'm_ccode_id', 'url')->first();
        $fileUri = "public/channel_codes/{$channelCode->m_ccode_id}_{$channelCode->scene_str}.png";
        if (!Storage::exists($fileUri)) {
            QrCode::format('png')->size(200)->margin(0)->generate($channelCode->url, storage_path('app/public/channel_codes') . "/{$channelCode->m_ccode_id}_{$channelCode->scene_str}.png");
        }

        return response()->download(storage_path("app/{$fileUri}"));
    }

    /**
     * 记录
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function recordGet()
    {
        return view('mp_advanced.channel_codes.record');
    }

    /**
     * 记录数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordDataGet(Request $request)
    {
        $channelCodeId = $request->input('m_ccode_id', 0);
        $res = MpChannelCodeFan::where('m_ccode_id', $channelCodeId)->selectRaw(
            "DATE_FORMAT(created_at,'%Y-%m-%d') date," .
            "sum(IF(`scan_type` = 1, 1, 0)) as subscribe_num," .
            "sum(IF(`scan_type` = 2, 1, 0)) as old_fans_num," .
            "sum(IF(`scan_type` = 4, 1, 0)) as old_unsubscribe_num," .
            "sum(IF(`scan_type` = 3, 1, 0)) as new_unsubscribe_num"
        )->groupBy('date')->orderByDesc('date')->get()->map(function ($item) {
            $item->net_growth = $item->subscribe_num + $item->new_unsubscribe_num;
            $item->unsubscribe_num = $item->old_unsubscribe_num + $item->new_unsubscribe_num;
            return $item;
        })->toArray();

        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => count($res),
            'data' => $res
        ]);
    }
}
