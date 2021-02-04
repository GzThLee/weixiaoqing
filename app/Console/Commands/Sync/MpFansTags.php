<?php

namespace App\Console\Commands\Sync;

use App\Models\Mp;
use App\Models\MpTag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MpFansTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:mp:fans:tags {mp_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步公众号粉丝标签';

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
            if (!$app) return false;
            $userTagsRes = $app->user_tag->list();
            if (isset($userTagsRes['errcode'])) {
                Log::info("mp:{$mp->mp_id} fans:tags sync false:" . $userTagsRes['errmsg']);
            } else {
                foreach ($userTagsRes['tags'] as $tagItem) {
                    MpTag::updateOrCreate(['tag_id' => $tagItem['id'], 'mp_id' => $mp->mp_id], ['name' => $tagItem['name']]);
                    Log::info("mp:{$mp->mp_id} fans:tags name:{$tagItem['name']} sync ok");
                }
            }
        });
    }
}
