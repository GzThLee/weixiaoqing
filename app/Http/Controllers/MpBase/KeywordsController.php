<?php

namespace App\Http\Controllers\MpBase;

use App\Http\Controllers\Controller;
use App\Http\Services\MpService;
use App\Models\Mp;
use App\Models\MpKeyword;
use App\Models\MpKeywordRecord;
use App\Models\MpTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class KeywordsController extends Controller
{
    /**
     * 视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexGet()
    {
        return view('mp_base.keywords.index');
    }

    /**
     * 数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataGet(Request $request)
    {
        $rows = $request->input('limit', 20);
        $data = $request->all(['keyword']);
        $res = MpKeyword::whereIn('status', [0, 1])
            ->when($data['keyword'] != '', function ($query) use ($data) {
                return $query->where('keyword', 'LIKE', '%' . $data['keyword'] . '%');
            })->select('m_kw_id', 'keyword', 'media_type', 'created_at')
            ->orderByDesc('m_kw_id')
            ->paginate($rows);

        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => collect($res->items())->map(function ($item) {
                $item->trigger_num = MpKeywordRecord::where('m_kw_id', $item->m_kw_id)->count();
                $item->media_type_name = MpService::getMediaName($item->media_type);
                return $item;
            })
        ]);
    }

    /**
     * 创建视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createGet()
    {
        $tags = MpTag::pluck('name', 'm_tag_id');
        return view('mp_base.keywords.create', compact('tags'));
    }

    /**
     * 创建保存
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePost(Request $request)
    {
        $mediaType = $request->input('media_type', 0);
        $content = $request->input('content', []);
        $user = Auth::user();
        try {
            $mediaContent = MpService::getMediaContent($mediaType, $content);
            $save = $request->all();
            $save['mp_id'] = $user->mp->mp_id;
            $save['content'] = $mediaContent;

            MpKeyword::create($save);
            return Redirect::to(route('mp.base.keywords'))->with(['success' => '创建成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('创建失败');
        }
    }

    /**
     * 编辑视图
     * @param $m_kw_id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editGet($m_kw_id, Request $request)
    {
        $tags = MpTag::pluck('name', 'm_tag_id');
        $keyword = MpKeyword::where('m_kw_id', $m_kw_id)->first();
        return view('mp_base.keywords.edit', compact('tags', 'keyword'));
    }

    /**
     * 提交设置
     * @param $m_kw_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updatePut($m_kw_id, Request $request)
    {
        $mediaType = $request->input('media_type', 0);
        $content = $request->input('content', []);
        $user = Auth::user();
        try {
            $mediaContent = MpService::getMediaContent($mediaType, $content);
            $save = $request->all();
            $save['mp_id'] = $user->mp->mp_id;
            $save['content'] = $mediaContent;

            MpKeyword::updateOrCreate(['m_kw_id' => $m_kw_id], $save);
            return Redirect::to(route('mp.base.keywords'))->with(['success' => '保存成功']);
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
        $keywordIds = $request->input('m_kw_ids', '');
        MpKeyword::whereIn('m_kw_id', explode(',', $keywordIds))->update(['status' => -1]);
        return Response::json([
            'code' => 0,
            'msg' => '删除成功'
        ]);
    }
}
