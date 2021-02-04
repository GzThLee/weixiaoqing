<?php

namespace App\Console\Commands\Sync;

use App\Models\Work;
use App\Models\WorkTag;
use App\Models\WorkTagGroup;
use Illuminate\Console\Command;

class WorkTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:work:tags {work_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步企业微信标签库';

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
        $workId = $this->argument('work_id');
        Work::when($workId != null, function ($query) use ($workId) {
            return $query->where('work_id', (int)$workId);
        })->get()->map(function ($workItem) {
            $work = work_app($workItem->corpid, $workItem->corpsecret);

            //清空数据
            WorkTagGroup::where('work_id', $workItem->work_id)->get()->map(function ($tagGroup) {
                WorkTag::where('w_tagg_id', $tagGroup->w_tagg_id)->delete();
                $tagGroup->delete();
            });

            $tagRes = $work->external_contact->getCorpTags();
            if (isset($tagRes['errcode']) && $tagRes['errcode'] == 0) {
                foreach ($tagRes['tag_group'] as $tagGroupItem) {
                    $tagGroup = WorkTagGroup::firstOrCreate([
                        'work_id' => $workItem->work_id,
                        'group_id' => $tagGroupItem['group_id']
                    ], [
                        'name' => $tagGroupItem['group_name'],
                        'order' => $tagGroupItem['order'],
                        'created_at' => date('Y-m-d H:i:s', $tagGroupItem['create_time'])
                    ]);

                    $tagGroup->tags()->saveMany(collect($tagGroupItem['tag'])->map(function ($tag) use ($tagGroupItem) {
                        return new WorkTag([
                            'tag_id' => $tag['id'],
                            'name' => $tag['name'],
                            'order' => $tag['order'],
                            'created_at' => date('Y-m-d H:i:s', $tag['create_time'])
                        ]);
                    })->all());
                }
            }
        });
    }
}
