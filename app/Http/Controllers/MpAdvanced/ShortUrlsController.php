<?php

namespace App\Http\Controllers\MpAdvanced;

use App\Http\Controllers\Controller;
use App\Models\MpShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class ShortUrlsController extends Controller
{
    /**
     * 视图
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexGet()
    {
        return view('mp_advanced.short_urls.index');
    }

    /**
     * 数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataGet(Request $request)
    {
        $rows = $request->input('limit', 20);
        $data = $request->all(['name']);
        $res = MpShortUrl::when($data['name'] != '', function ($query) use ($data) {
            return $query->where('name', 'LIKE', '%' . $data['name'] . '%');
        })->orderByDesc('created_at')->paginate($rows);
        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items()
        ]);
    }

    /**
     * 创建视图
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createGet()
    {
        return view('mp_advanced.short_urls.create');
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
            $data = $request->all(['name', 'long_url']);
            $mp = Auth::user()->mp;

            $app = mp_app($mp->app_id, $mp->app_secret);
            $shortUrlOutput = $app->url->shorten($data['long_url']);
            if ($shortUrlOutput['errcode'] != 0) {
                throw new \Exception($shortUrlOutput['errmsg']);
            }
            $data['short_url'] = $shortUrlOutput['short_url'];
            $data['mp_id'] = $mp->mp_id;
            MpShortUrl::create($data);
            DB::commit();
            return Redirect::to(route('mp.advanced.short_urls'))->with(['success' => '创建成功']);
        } catch (\Exception $exception) {
            DB::rollback();
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('创建失败');
        }
    }

    /**
     * 删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyDelete(Request $request)
    {
        $shortUrlIds = $request->input('ids', '');
        MpShortUrl::whereIn('m_short_url_id', explode(',', $shortUrlIds))->update(['status' => -1]);
        return Response::json([
            'code' => 0,
            'msg' => '删除成功'
        ]);
    }
}
