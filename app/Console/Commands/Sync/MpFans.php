<?php

namespace App\Console\Commands\Sync;

use App\Models\Mp;
use App\Models\MpFan;
use App\Models\MpTag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MpFans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:mp:fans {mp_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步公众号粉丝';

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
            $mpWhere = ['mp_id' => $mp->mp_id];
            //同步粉丝标签
            Artisan::call(MpFansTags::class, $mpWhere);

            MpFan::where($mpWhere)->update(['is_subscribe' => MpFan::UNSUBSCRIBE]);
            $app = mp_app($mp->app_id, $mp->app_secret);
            if (!$app) return false;
            $listCount = 0;
            $allOpenid = [];
            $nextOpenid = null;
            do {
                $fans = $app->user->list($nextOpenid);
                if (isset($fans['errcode'])) {
                    Log::info("mp:{$mp->mp_id} fans sync false:" . $fans['errmsg']);
                    $fansTotal = 0;
                    $listCount = 0;
                } else {
                    $fansTotal = $fans['total'];
                    $listCount = $listCount + $fans['count'];
                    $allOpenid[] = $fans['data']['openid'];
                    $nextOpenid = $fans['next_openid'];
                    unset($fans);
                }
            } while ($fansTotal != $listCount);

            Log::info("mp:{$mp->mp_id} has fans:" . collect($allOpenid)->collapse()->count());
            collect($allOpenid)->collapse()->chunk(100)->map(function ($openids) use ($mp, $app, $mpWhere) {
                $fansRes = $app->user->select($openids->values()->toArray());
                if (isset($fansRes['user_info_list'])) {
                    foreach ($fansRes['user_info_list'] as $fansItem) {
                        $save = $fansItem;
                        if (isset($fansItem['subscribe_scene']) && $fansItem['subscribe_scene'] == 'ADD_SCENE_QR_CODE') {
                            $save['subscribe_type'] = 1;
                        }
                        $save['is_subscribe'] = MpFan::SUBSCRIBE;
                        $fans = MpFan::updateOrCreate(['openid' => $fansItem['openid'], 'mp_id' => $mp->mp_id], $save);
                        $tagIds = MpTag::where($mpWhere)->whereIn('tag_id', $fansItem['tagid_list'])->pluck('m_tag_id')->toArray();
                        $fans->tags()->sync($tagIds);
                        Log::info("mp:{$mp->mp_id} fans openid:{$fansItem['openid']} sync ok");
                    }
                } else {
                    $this->error("mp:{$mp->mp_id} get fans detail openids:{$openids->implode(',')} fail");
                    exit;
                }
            });

            //删除非当前公众号用户
            MpFan::where($mpWhere)->where('is_subscribe', MpFan::UNSUBSCRIBE)->get()->map(function ($fans) use ($app) {
                $userRes = $app->user->get($fans->openid);
                if (isset($userRes['errcode'])) {
                    $fans->delete();
                }
            });
        });
    }
}
