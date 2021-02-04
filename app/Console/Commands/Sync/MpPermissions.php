<?php

namespace App\Console\Commands\Sync;

use App\Models\Mp;
use App\Models\MpMenu;
use App\Models\Permissions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MpPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:mp:permissions {mp_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步检测公众号权限';

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

        $permissions = Permissions::with(['childs'])->whereIn('name', ['pmsn.mp.base', 'pmsn.mp.advanced'])->where(['pid' => 0, 'type' => Permissions::MENU_TYPE])->orderByDesc('pid')->orderByDesc('sort')->get();
        Mp::when($mpId != null, function ($query) use ($mpId) {
            return $query->where('mp_id', (int)$mpId);
        })->get()->map(function ($mp) use ($permissions) {
            $mpPermissions = $permissions->map(function ($item) {
                return $item->childs;
            })->collapse()->map(function ($item) use ($mp) {
                return $this->checkPermission($mp, $item->name) ? $item->id : null;
            })->filter()->toArray();
            $mp->permissions()->sync($mpPermissions);
        });
        Log::info('mp permissions refresh success');
    }

    /**
     * 检测公众号权限
     * @param Mp $mp
     * @param $permissionName
     * @return bool
     */
    private function checkPermission(Mp $mp, $permissionName)
    {
        $app = mp_app($mp->app_id, $mp->app_secret);
        if (!$app) return false;
        switch ($permissionName) {
            case 'pmsn.mp.base.info':
                //公众号配置
                return true;
            case 'pmsn.mp.base.welcomes':
                //欢迎语
                return true;
            case 'pmsn.mp.base.menus':
                //自定义菜单
                $menuRes = $app->menu->current();
                if (!isset($menuRes['errcode']) && $menuRes['is_menu_open'] == 1) {
                    return true;
                } else {
                    MpMenu::where('mp_id', $mp->mp_id)->delete();
                    return false;
                }
            case 'pmsn.mp.base.keywords':
                //关键字回复
                return true;
            case 'pmsn.mp.base.fans':
                //粉丝管理
                $fansRes = $app->user->list(null);
                return !isset($fansRes['errcode']);
            case 'pmsn.mp.advanced.templates':
                //模板消息
                $tempRes = $app->template_message->getPrivateTemplates();
                return !isset($tempRes['errcode']);
            case 'pmsn.mp.advanced.channel_codes':
                //渠道二维码
                $qrcodeRes = $app->qrcode->temporary('test_permission', 3600);
                return !isset($qrcodeRes['errcode']);
            case 'pmsn.mp.advanced.poster_codes':
                //海报二维码
                $qrcodeRes = $app->qrcode->temporary('test_permission', 3600);
                return !isset($qrcodeRes['errcode']);
            case 'pmsn.mp.advanced.act_fans_pushs':
                //活跃用户推送（有粉丝有推送）
                $fansRes = $app->user->list(null);
                return !isset($fansRes['errcode']);
            case 'pmsn.mp.advanced.subscribe_pushs':
                //关注定时推送（有粉丝有推送）
                $fansRes = $app->user->list(null);
                return !isset($fansRes['errcode']);
            case 'pmsn.mp.advanced.short_urls':
                //长链接转短链接
                $shortUrlRes = $app->url->shorten('https://www.qq.com');
                return (isset($shortUrlRes['errcode']) && $shortUrlRes['errcode'] == 0);
            default:
                return false;
        }
    }
}
