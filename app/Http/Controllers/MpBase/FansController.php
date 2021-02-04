<?php

namespace App\Http\Controllers\MpBase;

use App\Http\Controllers\Controller;
use App\Models\MpFan;
use App\Models\MpTag;
use App\Models\Users;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class FansController extends Controller
{

    /**
     * 视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexGet()
    {
        /** @var Users $user */
        $user = Auth::user();
        $mp = $user->mp;
        $tags = MpTag::where('mp_id', $mp->mp_id)->where('status', 1)->pluck('name', 'm_tag_id');
        return view('mp_base.fans.index', compact('tags'));
    }

    /**
     * 列表数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function dataGet(Request $request)
    {
        $rows = $request->input('limit', 20);
        $sortField = $request->input('sort_field', 'last_time');
        $data = $request->all(['nickname', 'm_tag_id', 'remark', 'active_start_time', 'active_end_time', 'subscribe_start_time', 'subscribe_end_time', 'area', 'is_subscribe','subscribe_scene']);
        $res = MpFan::when($data['nickname'] != '', function ($query) use ($data) {
            return $query->where('nickname', 'LIKE', '%' . $data['nickname'] . '%');
        })->when($data['remark'] != '', function ($query) use ($data) {
            return $query->where('remark', 'LIKE', '%' . $data['remark'] . '%');
        })->when($data['is_subscribe'] != '', function ($query) use ($data) {
            return $query->where('is_subscribe', $data['is_subscribe']);
        })->when($data['subscribe_scene'] != '', function ($query) use ($data) {
            return $query->where('subscribe_scene', $data['subscribe_scene']);
        })->when($data['area'] != '', function ($query) use ($data) {
            return $query->where('country', 'LIKE', '%' . $data['area'] . '%')->orWhere('province', 'LIKE', '%' . $data['area'] . '%')->orWhere('city', 'LIKE', '%' . $data['area'] . '%');
        })->when($data['active_start_time'] != '', function ($query) use ($data) {
            return $query->where('last_time', '>=', $data['active_start_time']);
        })->when($data['active_end_time'] != '', function ($query) use ($data) {
            return $query->where('last_time', '<=', $data['active_end_time']);
        })->when($data['subscribe_start_time'] != '', function ($query) use ($data) {
            return $query->where('subscribe_time', '>=', $data['subscribe_start_time']);
        })->when($data['subscribe_end_time'] != '', function ($query) use ($data) {
            return $query->where('subscribe_time', '<=', $data['subscribe_end_time']);
        })->when($data['m_tag_id'] != '', function ($query) use ($data) {
            return $query->whereHas('tags', function (Builder $query) use ($data) {
                $query->where('mp_tags.m_tag_id', $data['m_tag_id']);
            });
        })->with(['tags' => function ($query) {
            return $query->select('name', 'mp_tags.m_tag_id');
        }])->orderByDesc($sortField)->paginate($rows);

        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items()
        ]);
    }

    /**
     * @param $m_fan_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updatePut($m_fan_id, Request $request)
    {
        $mpTagIds = $request->input('tags', []);
        $remark = $request->input('remark', '');

        /** @var Users $user */
        $user = Auth::user();
        $app = mp_app($user->mp->app_id, $user->mp->app_secret);

        $fan = MpFan::find($m_fan_id);
        if ($remark != '') {
            $res = $app->user->remark($fan->openid, $remark);
            if ($res['errcode'] == 0) {
                $fan->update(['remark' => $remark]);
            }
        } else {
            $delTagIds = array_diff($fan->tags->pluck('m_tag_id')->toArray(), $mpTagIds);
            collect($delTagIds)->map(function ($tagId) use ($fan, $app) {
                $tag = MpTag::find($tagId);
                $app->user_tag->untagUsers([$fan->openid], $tag->tag_id);
            });

            $tagIds = collect($mpTagIds)->map(function ($tagId) use ($fan, $app) {
                $tag = MpTag::find($tagId);
                $res = $app->user_tag->tagUsers([$fan->openid], $tag->tag_id);
                return $res['errcode'] == 0 ? $tagId : null;
            })->filter()->toArray();

            $fan->tags()->sync($tagIds);
        }

        return Response::json([
            'code' => 0,
            'msg' => '操作成功'
        ]);
    }
}
