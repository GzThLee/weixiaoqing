<?php

namespace App\Console\Commands\Sync;

use App\Models\Mp;
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
