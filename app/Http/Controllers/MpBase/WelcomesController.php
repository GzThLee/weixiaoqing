<?php

namespace App\Http\Controllers\MpBase;

use App\Http\Controllers\Controller;
use App\Http\Services\MpService;
use App\Models\Mp;
use App\Models\MpWelcome;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class WelcomesController extends Controller
{

    /**
     * 视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexGet()
    {
        $user = Auth::user();
        $mp = $user->mp;
        $welcome = MpWelcome::firstOrCreate(['mp_id' => $mp->mp_id], ['media_type' => Mp::TEXT_MEDIA]);
        return view('mp_base.welcomes.index', compact('welcome'));
    }

    /**
     * 提交
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePut(Request $request)
    {
        /** @var Users $user */
        $user = Auth::user();
        $mediaType = $request->input('media_type', 0);
        $content = $request->input('content', []);
        try {
            $mp = $user->mp;
            $mediaContent = MpService::getMediaContent($mediaType, $content);
            MpWelcome::updateOrCreate(['mp_id' => $mp->mp_id], [
                'status' => 1,
                'media_type' => $mediaType,
                'content' => $mediaContent
            ]);

            return Redirect::to(route('mp.base.welcomes'))->with(['success' => '保存成功']);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('保存失败');
        }
    }
}
