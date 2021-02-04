<?php

namespace App\Http\Controllers\MpBase;

use App\Http\Controllers\Controller;
use App\Models\Mp;
use App\Models\MpTag;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class FansTagsController extends Controller
{
    /**
     * 视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexGet()
    {
        return view('mp_base.fans.tags.index');
    }

    /**
     * 数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataGet(Request $request)
    {
        $rows = $request->input('limit', 20);
        $res = MpTag::whereIn('status', [0, 1])
            ->select('m_tag_id', 'name', 'description', 'tag_id')
            ->orderByDesc('m_tag_id')
            ->with('fans')
            ->paginate($rows);

        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => collect($res->items())->map(function ($item) {
                $item->fans_num = collect($item->fans)->count();
                return $item;
            })
        ]);
    }

    /**
     * 创建分组
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createGet()
    {
        return view('mp_base.fans.tags.create');
    }

    /**
     * 创建
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function storePost(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        $mp = $user->mp;
        try {
            $save = $data;
            $save['mp_id'] = $mp->mp_id;
            $tag = MpTag::create($save);
            try {
                $app = mp_app($mp->app_id, $mp->app_secret);
                $tagRes = $app->user_tag->create($data['name']);
                $tag->update(['tag_id' => $tagRes['tag']['id']]);
                return Redirect::to(route('mp.base.fans.tags'))->with(['success' => '创建成功']);
            } catch (\Exception $exception) {
                mark_error_log($exception);
                return Redirect::to(route('mp.base.fans.tags'))->with(['warning' => '创建成功,同步微信失败']);
            }
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('创建失败');
        }
    }

    /**
     * 编辑视图
     * @param $m_tag_id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editGet($m_tag_id, Request $request)
    {
        $tag = MpTag::where('m_tag_id', $m_tag_id)->first();
        return view('mp_base.fans.tags.edit', compact('tag'));
    }

    /**
     * @param $m_tag_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updatePut($m_tag_id, Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        try {
            $mp = $user->mp;
            $save = $data;
            $save['mp_id'] = $mp->mp_id;
            $tag = MpTag::where('m_tag_id', $m_tag_id)->firstOrFail();
            $tag->update($save);
            try {
                $app = mp_app($mp->app_id, $mp->app_secret);
                $app->user_tag->update($tag->tag_id, $data['name']);
                return Redirect::to(route('mp.base.fans.tags'))->with(['success' => '编辑成功']);
            } catch (\Exception $exception) {
                mark_error_log($exception);
                return Redirect::to(route('mp.base.fans.tags'))->with(['warning' => '编辑成功,同步微信失败']);
            }
        } catch (\Exception $exception) {
            mark_error_log($exception);
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
        $tagIds = $request->input('m_tag_ids', '');
        MpTag::whereIn('m_tag_id', explode(',', $tagIds))->get()->map(function ($item) {
            MpTag::where('m_tag_id', $item->m_tag_id)->update(['status' => -1]);
        });

        try {
            $mp = Auth::user()->mp;
            $app = mp_app($mp->app_id, $mp->app_secret);
            MpTag::whereIn('m_tag_id', explode(',', $tagIds))->get()->map(function ($item) use ($app) {
                $app->user_tag->delete($item->tag_id);
            });
            $return = Response::json([
                'code' => 0,
                'msg' => '删除成功',
            ]);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            $return = Response::json([
                'code' => 0,
                'msg' => '删除成功,同步微信失败',
            ]);
        }

        return $return;
    }
}
