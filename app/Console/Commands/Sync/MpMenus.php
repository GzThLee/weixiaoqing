<?php

namespace App\Console\Commands\Sync;

use App\Models\Mp;
use App\Models\MpKeyword;
use App\Models\MpMenu;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MpMenus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:mp:menus {mp_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步公众号菜单';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mpId = $this->argument('mp_id');
        Mp::when($mpId != null, function ($query) use ($mpId) {
            return $query->where('mp_id', (int)$mpId);
        })->get()->map(function ($mp) {
            $app = mp_app($mp->app_id, $mp->app_secret);

            DB::beginTransaction();
            try {
                MpMenu::where('mp_id', $mp->mp_id)->delete();
                $menuTypeList = [
                    'click' => MpMenu::KEYWORD_MENU,
                    'view' => MpMenu::LINK_MENU,
                    'miniprogram' => MpMenu::MINI_APP_MENU,
                    'scancode_push' => MpMenu::SCANCODE_PUSH_MENU,
                    'scancode_waitmsg' => MpMenu::SCANCODE_WAITMSG_MENU,
                    'pic_sysphoto' => MpMenu::PIC_SYSPHOTO_MENU,
                    'pic_photo_or_album' => MpMenu::PIC_PHOTO_OR_ALBUM_MENU,
                    'pic_weixin' => MpMenu::PIC_WEIXIN_MENU,
                    'location_select' => MpMenu::LOCATION_SELECT_MENU,
                    'media_id' => MpMenu::MEDIA_ID_MENU,
                    'view_limited' => MpMenu::VIEW_LIMITED_MENU,
                    'text' => MpMenu::TEXT_MENU,
                ];

                $menuData = $app->menu->current();
                $menuList = $menuData['selfmenu_info']['button'];
                foreach ($menuList as $pIndex => $menuItem) {
                    $index = $pIndex + 1;
                    $sort = $pIndex;
                    if (isset($menuItem['sub_button'])) {
                        //有菜单
                        MpMenu::updateOrCreate([
                            'mp_id' => $mp->mp_id,
                            'menu_type' => MpMenu::MAIN_MENU,
                            'pindex' => 0,
                            'index' => $index,
                            'sort' => $sort
                        ], [
                            'name' => $menuItem['name'],
                            'content' => ''
                        ]);

                        //子菜单列表
                        foreach ($menuItem['sub_button']['list'] as $key => $menuSubItem) {
                            if ($menuTypeList[$menuSubItem['type']] == MpMenu::TEXT_MENU) {
                                //菜单文本回复改成关键字回复
                                MpKeyword::updateOrCreate([
                                    'mp_id' => $mp->mp_id,
                                    'keyword' => $menuSubItem['name'],
                                    'm_tag_id' => 0,
                                    'rule_type' => MpKeyword::EXACT,
                                    'media_type' => Mp::TEXT_MEDIA,
                                ], [
                                    'status' => 1,
                                    'content' => ['content' => $menuSubItem['value']],
                                ]);
                            }

                            MpMenu::updateOrCreate([
                                'mp_id' => $mp->mp_id,
                                'pindex' => $index,
                                'sort' => $key
                            ], [
                                'name' => $menuSubItem['name'],
                                'menu_type' => $menuTypeList[$menuSubItem['type']],
                                'content' => $menuSubItem
                            ]);
                        }

                    } else {
                        if ($menuTypeList[$menuItem['type']] == MpMenu::TEXT_MENU) {
                            //菜单文本回复改成关键字回复
                            MpKeyword::updateOrCreate([
                                'mp_id' => $mp->mp_id,
                                'keyword' => $menuItem['name'],
                                'm_tag_id' => 0,
                                'rule_type' => MpKeyword::EXACT,
                                'media_type' => Mp::TEXT_MEDIA,
                            ], [
                                'status' => 1,
                                'content' => ['content' => $menuItem['value']],
                            ]);
                        }

                        //无菜单
                        MpMenu::updateOrCreate([
                            'mp_id' => $mp->mp_id,
                            'menu_type' => $menuTypeList[$menuItem['type']],
                            'pindex' => 0,
                            'index' => $index,
                            'sort' => $sort
                        ], [
                            'name' => $menuItem['name'],
                            'content' => $menuItem
                        ]);
                    }
                }
                DB::commit();
                Log::info("mp:{$mp->mp_id} menu sync ok");
            } catch (\Exception $exception) {
                DB::rollback();
                mark_error_log($exception);
                Log::info("mp:{$mp->mp_id} menu sync fail");
            }
        });
    }
}
