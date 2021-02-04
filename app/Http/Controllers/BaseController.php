<?php

namespace App\Http\Controllers;

use App\Models\Mp;
use App\Models\MpFan;
use App\Models\Permissions;
use App\Models\Users;
use App\Models\Work;
use App\Models\WorkCustomer;
use App\Models\WorkGroupchat;
use App\Models\WorkUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ixudra\Curl\Facades\Curl;

class BaseController extends Controller
{
    /**
     * 后台初始化
     * @return \Illuminate\Http\JsonResponse
     */
    public function initGet()
    {
        /** @var Users $user */
        $user = Auth::user();
        Work::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'name' => $user->nickname,
                'api_token' => Str::random(8),
                'valid_token' => Str::random(16),
                'aes_key' => Str::random(43),
            ]
        );
        Mp::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'name' => $user->nickname,
                'api_token' => Str::random(8),
                'valid_token' => Str::random(16),
                'encodingaeskey' => Str::random(43)
            ]
        );

        return response()->json([
            'homeInfo' => [
                'title' => '首页',
                'href' => route('system.home', [], false)
            ],
            'logoInfo' => [
                'title' => system_config('site_title'),
                'image' => 'layuimini/images/logo.png',
                'href' => route('system.home', [], false)
            ],
            'menuInfo' => Permissions::with(['allChilds'])->where(['pid' => 0, 'type' => Permissions::MENU_TYPE])->orderByDesc('sort')->get()->map(function ($menu) use ($user) {
                if ($user->can($menu->name)) {
                    return [
                        "title" => $menu->display_name,
                        "icon" => "fa " . $menu->icon,
                        "href" => '',
                        "target" => "_self",
                        "child" => collect($menu->childs)->map(function ($menu) use ($user) {
                            if ($user->can($menu->name)) {
                                return [
                                    "title" => $menu->display_name,
                                    "icon" => "fa " . $menu->icon,
                                    "href" => route($menu->route_name, [], false),
                                    "target" => "_self",
                                    "child" => collect($menu->childs)->map(function ($menu) use ($user) {
                                        if ($user->can($menu->name)) {
                                            return [
                                                "title" => $menu->display_name,
                                                "icon" => "fa " . $menu->icon,
                                                "href" => route($menu->route_name, [], false),
                                                "target" => "_self"
                                            ];
                                        } else {
                                            return null;
                                        }
                                    })->filter()->toArray()
                                ];
                            } else {
                                return null;
                            }
                        })->filter()->toArray()
                    ];
                } else {
                    return null;
                }
            })->filter()->toArray()]);
    }

    /**
     * 清空缓存
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearGet()
    {
        return response()->json(['code' => 1, 'msg' => '服务端清理缓存成功']);
    }

    /**
     * 企业微信图片上传
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function uploadWorkImagePost(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileExt = $file->extension();  //文件后缀
            $fileMiniType = $file->getMimeType();  //类型
            $fileName = date('His', time()) . '_' . uniqid() . ".{$fileExt}";
            $filePath = 'public' . '/' . 'images' . '/' . date('Ym', time()) . '/';
            $file->storeAs($filePath, $fileName); //本地存储
            $filePath = storage_path('app/' . $filePath . $fileName);
            /** @var Users $userWork */
            $userWork = Auth::user()->work;
            $work = work_app($userWork->corpid, $userWork->corpsecret);
            $accessToken = $work->access_token->getToken();
            $uploadRes = Curl::to('https://qyapi.weixin.qq.com/cgi-bin/media/uploadimg?' . http_build_query(['access_token' => $accessToken['access_token']]))
                ->withFile('file', $filePath, $fileMiniType, $fileName)
                ->asJsonResponse()
                ->post();

            if ($uploadRes->errcode == 0) {
                $return = Response::json([
                    'code' => 0,
                    'msg' => '上传成功',
                    'data' => [
                        "image_url" => $uploadRes->url
                    ]
                ]);
            } else {
                $return = Response::json(['msg' => '上传失败:' . $uploadRes->errmsg, 'code' => 1118]);
            }
        } else {
            $return = Response::json(['msg' => '上传失败', 'code' => 1118]);
        }
        return $return;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImagePost(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileExt = $file->extension();  //文件后缀
            $fileName = date('His', time()) . '-' . uniqid() . ".{$fileExt}";
            $filePath = 'public' . '/' . 'images' . '/' . date('Ym', time()) . '/' . date('d', time()) . '/';
            $file->storeAs($filePath, $fileName); //本地存储
            //      $fileContents = Storage::get($filePath . $fileName);  //用于存储cos
//            $fileMd5 = MD5(URL::previous() . Storage::get($filePath . $fileName));  //用于存储cos
            $url = Storage::url($filePath);
            $imageUrl = config('app.url') . $url . $fileName;
            return Response::json([
                'code' => 0,
                'msg' => '上传成功',
                'data' => [
                    "image_url" => $imageUrl,
                ]

            ]);
        } else {
            return Response::json([
                'code' => 400,
                'msg' => '上传失败'
            ]);
        }
    }

    /**
     * 首页版本
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function homeGet()
    {
        $fansCount = MpFan::where('is_subscribe', MpFan::SUBSCRIBE)->count();
        $fansActCount = MpFan::where('is_subscribe', MpFan::SUBSCRIBE)->where('last_time', '>=', now()->addDays(-2)->toDateTimeString())->count();
        $userCount = WorkUser::count();
        $userCustomerCount = WorkCustomer::count();
        $userCustomerGroupCount = WorkGroupchat::count();
        return view('home', compact('fansCount', 'fansActCount', 'userCount', 'userCustomerCount', 'userCustomerGroupCount'));
    }
}
