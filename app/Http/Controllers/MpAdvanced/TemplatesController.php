<?php

namespace App\Http\Controllers\MpAdvanced;

use App\Http\Controllers\Controller;
use App\Models\Mp;
use App\Models\MpFan;
use App\Models\MpTag;
use App\Models\MpTemplate;
use App\Models\Users;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class TemplatesController extends Controller
{
    /**
     * 视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexGet()
    {
        return view('mp_advanced.templates.index');
    }

    /**
     * 数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataGet(Request $request)
    {
        $rows = $request->input('limit', 20);
        $data = $request->all(['theme']);
        $res = MpTemplate::where('status', 1)
            ->when($data['theme'] != '', function ($query) use ($data) {
                return $query->where('theme', 'LIKE', '%' . $data['theme'] . '%');
            })->select('m_temp_id', 'theme', 'created_at', 'push_time', 'finish_time', 'send_success_count', 'send_fail_count', 'send_count')
            ->orderByDesc('m_temp_id')
            ->paginate($rows);

        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items()
        ]);
    }

    /**
     * 创建视图
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createGet(Request $request)
    {
        /** @var Mp $mp */
        $mp = Auth::user()->mp;
        try {
            if (Cache::has("{$mp->mp_id}_temp_list")) {
                $tempListOutput = Cache::get("{$mp->mp_id}_temp_list");
                $templates = $tempListOutput['template_list'] ?? [];
            } else {
                $app = mp_app($mp->app_id, $mp->app_secret);
                $tempListOutput = $app->template_message->getPrivateTemplates();
                $templates = $tempListOutput['template_list'] ?? [];
                Cache::put("{$mp->mp_id}_temp_list", $tempListOutput, 10);
            }
        } catch (\Exception $exception) {
            $templates = [];
        }

        $tags = MpTag::pluck('name', 'm_tag_id');
        return view('mp_advanced.templates.create', compact('templates', 'tags'));
    }

    /**
     * 添加
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePost(Request $request)
    {
        $data = $request->all(['content', 'template_id', 'push_obj', 'jump_type', 'url', 'theme', 'miniprogram', 'touser', 'tousers', 'touser_ids']);
        try {
            $mp = Auth::user()->mp;
            if ($data['push_obj'] == -1) {
                $tousers = MpFan::where('is_subscribe', MpFan::SUBSCRIBE)->pluck('openid');
            } elseif ($data['push_obj'] > 0) {
                $tag = MpTag::where('m_tag_id', $data['push_obj'])->with('fans')->first();
                $tousers = $tag->fans->pluck('openid')->toArray();
            } elseif ($data['push_obj'] == -2) {
                $touserIds = explode(',', $data['touser_ids']);
                $tousers = MpFan::whereIn('m_fan_id', $touserIds)->pluck('openid');
            } elseif ($data['push_obj'] == 0) {
                $tousers[] = $data['touser'];
            } else {
                $tousers = [];
            }

            $pushContent = [
                "tousers" => $tousers,
                "template_id" => $data['template_id'],
                "data" => $data['content']
            ];
            if ($data['jump_type'] == 'url') {
                $pushContent['url'] = $data['url'];
            } elseif ($data['jump_type'] == 'miniprogram') {
                $pushContent['miniprogram'] = $data['miniprogram'];
            }

            $save = $data;
            $save['mp_id'] = $mp->mp_id;
            $save['content'] = $pushContent;
            $save['send_count'] = count($tousers);
            MpTemplate::create($save);

            return Redirect::to(route('mp.advanced.templates'))->with(['success' => '添加成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('添加失败');
        }
    }

    /**
     * 编辑视图
     * @param $m_temp_id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function editGet($m_temp_id, Request $request)
    {
        $reset = $request->input('reset', 0);
        /** @var Users $mp */
        $mp = Auth::user()->mp;

        if (Cache::has("{$mp->mp_id}_temp_list")) {
            $tempListOutput = Cache::get("{$mp->mp_id}_temp_list");
            $templates = $tempListOutput['template_list'] ?? '';
        } else {
            $app = mp_app($mp->app_id, $mp->app_secret);
            $tempListOutput = $app->template_message->getPrivateTemplates();
            $templates = $tempListOutput['template_list'] ?? '';
            Cache::put("{$mp->mp_id}_temp_list", $tempListOutput, 10);
        }

        $tags = MpTag::pluck('name', 'm_tag_id');
        $template = MpTemplate::where('m_temp_id', $m_temp_id)->first();
        if ($template) {
            $template->content_data = $template->content;
            $template->content_data->tousers = implode(',', $template->content_data->tousers);
            $template->template_demo = collect($tempListOutput['template_list'] ?? [])->filter(function ($item) use ($template) {
                return $item['template_id'] == $template['template_id'];
            })->first();
        }

        return view('mp_advanced.templates.edit', compact('templates', 'tags', 'template', 'reset'));
    }

    /**
     * 添加重用
     * @param $m_temp_id
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updatePut($m_temp_id, Request $request)
    {
        $data = $request->all(['content', 'template_id', 'push_obj', 'jump_type', 'url', 'theme', 'miniprogram', 'touser', 'touser_ids']);
        try {
            $mp = Auth::user()->mp;
            if ($data['push_obj'] == -1) {
                $tousers = MpFan::where('is_subscribe', MpFan::SUBSCRIBE)->pluck('openid');
            } elseif ($data['push_obj'] > 0) {
                $tag = MpTag::where('m_tag_id', $data['push_obj'])->with('fans')->first();
                $tousers = $tag->fans->pluck('openid')->toArray();
            } elseif ($data['push_obj'] == -2) {
                $touserIds = explode(',', $data['touser_ids']);
                $tousers = MpFan::whereIn('m_fan_id', $touserIds)->pluck('openid');
            } elseif ($data['push_obj'] == 0) {
                $tousers[] = $data['touser'];
            } else {
                $tousers = [];
            }

            $pushContent = [
                "tousers" => $tousers,
                "template_id" => $data['template_id'],
                "data" => $data['content']
            ];
            if ($data['jump_type'] == 'url') {
                $pushContent['url'] = $data['url'];
            } elseif ($data['jump_type'] == 'miniprogram') {
                $pushContent['miniprogram'] = $data['miniprogram'];
            }

            $save = $data;
            $save['mp_id'] = $mp->mp_id;
            $save['content'] = $pushContent;
            $save['send_count'] = count($tousers);
            MpTemplate::create($save);

            return Redirect::to(route('mp.advanced.templates'))->with(['success' => '添加成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('添加失败');
        }
    }

    /**
     * 删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyDelete(Request $request)
    {
        $templateIds = $request->input('m_temp_ids', '');
        MpTemplate::whereIn('m_temp_id', explode(',', $templateIds))->update(['status' => -1]);
        return Response::json([
            'code' => 0,
            'msg' => '删除成功',
        ]);
    }

    /**
     * 选择用户视图
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function usersGet()
    {
        return view('mp_advanced.templates.users');
    }

    /**
     * 用户数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function usersDataGet(Request $request)
    {
        $res = MpFan::select('m_fan_id', 'headimgurl', 'nickname', 'openid', 'subscribe_time', 'last_time')
            ->where('is_subscribe', MpFan::SUBSCRIBE)
            ->orderByDesc('subscribe_time')
            ->get();

        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->count(),
            'data' => $res
        ]);
    }
}
