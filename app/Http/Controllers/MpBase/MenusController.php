<?php

namespace App\Http\Controllers\MpBase;

use App\Http\Controllers\Controller;
use App\Models\Mp;
use App\Models\MpMenu;
use App\Models\Users;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class MenusController extends Controller
{
    /**
     * 视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexGet()
    {
        $menu = MpMenu::where('pindex', 0)->orderBy('sort')->get()->map(function ($item) {
            $item->is_sub = ($item['content'] == '' ? 1 : 0);
            return $item;
        })->map(function ($item) {
            $item->sub_menu = MpMenu::where('pindex', $item->index)->orderBy('sort')->get();
            return $item;
        })->all();

        $access = ['set' => true];
        return view('mp_base.menu.index', compact('access', 'menu'));
    }

    /**
     * 设置
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updatePut(Request $request)
    {
        $mainMenu = $request->input('main_menu');
        $subMenu = $request->input('sub_menu');
        $mp = Auth::user()->mp;
        try {
            MpMenu::where('mp_id', $mp->mp_id)->delete();
            foreach ($mainMenu as $pIndex => $value) {
                $index = $pIndex + 1;
                $sort = $pIndex;
                if ($value['is_sub'] == 0) {
                    if ($value['menu_type'] == MpMenu::KEYWORD_MENU) {
                        $content = [
                            "type" => "click",
                            "name" => $value['name'],
                            "key" => $value['keyword']
                        ];
                    } else if ($value['menu_type'] == MpMenu::LINK_MENU) {
                        $content = [
                            "type" => "view",
                            "name" => $value['name'],
                            "url" => $value['url']
                        ];
                    } else if ($value['menu_type'] == MpMenu::MINI_APP_MENU) {
                        $content = [
                            "type" => "miniprogram",
                            "url" => $value['url'],
                            "name" => $value['name'],
                            "pagepath" => $value['pagepath'],
                            "appid" => $value['appid']
                        ];
                    } else {
                        continue;
                    }

                    MpMenu::updateOrCreate([
                        'mp_id' => $mp->mp_id,
                        'menu_type' => $value['menu_type'],
                        'pindex' => 0,
                        'index' => $index,
                        'sort' => $sort
                    ], [
                        'name' => $value['name'],
                        'content' => $content
                    ]);
                } else if ($value['is_sub'] == 1) {
                    MpMenu::updateOrCreate([
                        'mp_id' => $mp->mp_id,
                        'menu_type' => MpMenu::MAIN_MENU,
                        'pindex' => 0,
                        'index' => $index,
                        'sort' => $sort
                    ], [
                        'name' => $value['name'],
                        'content' => ''
                    ]);
                    $subMenuArray = $subMenu[$pIndex] ?? [];
                    foreach ($subMenuArray as $key => $subItem) {
                        if ($subItem['name'] != '') {
                            if ($subItem['menu_type'] == MpMenu::KEYWORD_MENU) {
                                $content = [
                                    "type" => "click",
                                    "name" => $subItem['name'],
                                    "key" => $subItem['keyword']
                                ];
                            } elseif ($subItem['menu_type'] == MpMenu::LINK_MENU) {
                                $content = [
                                    "type" => "view",
                                    "name" => $subItem['name'],
                                    "url" => $subItem['url']
                                ];
                            } elseif ($subItem['menu_type'] == MpMenu::MINI_APP_MENU) {
                                $content = [
                                    "type" => "miniprogram",
                                    "name" => $subItem['name'],
                                    "url" => $subItem['url'],
                                    "pagepath" => $subItem['pagepath'],
                                    "appid" => $subItem['appid']
                                ];
                            } else {
                                continue;
                            }

                            MpMenu::updateOrCreate([
                                'mp_id' => $mp->mp_id,
                                'pindex' => $index,
                                'sort' => $key
                            ], [
                                'name' => $subItem['name'],
                                'menu_type' => $subItem['menu_type'],
                                'content' => $content
                            ]);
                        }
                    }
                }
            }

            try {
                //推送到微信
                $mainMenuList = MpMenu::where('pindex', 0)->orderBy('sort')->get();
                $buttons = [];
                foreach ($mainMenuList as $mainValue) {
                    $button = [];
                    if ($mainValue['content']) {
                        $button['name'] = $mainValue['name'];
                        $button = $mainValue['content'];
                    } else {
                        $button['name'] = $mainValue['name'];
                        $contentList = MpMenu::where('pindex', $mainValue['index'])->orderBy('sort')->pluck('content');
                        $button['sub_button'] = collect($contentList)->map(function ($content) {
                            return $content;
                        })->all();
                    }
                    $buttons[] = $button;
                }

                $app = mp_app($mp->app_id, $mp->app_secret);
                $res = $app->menu->create($buttons);
                if ($res['errcode'] == 0) {
                    return Redirect::to(route('mp.base.menus'))->with(['success' => '设置成功']);
                } else {
                    return Redirect::back()->with(['warning' => '保存成功，微信设置保存失败:' . $res['errmsg']]);
                }
            } catch (\Exception $exception) {
                return Redirect::back()->with(['warning' => '保存成功，微信设置保存失败:微信配置有误']);
            }
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return Redirect::back()->withInput()->withErrors('保存失败');
        }
    }
}
