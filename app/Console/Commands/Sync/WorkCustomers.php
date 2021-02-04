<?php

namespace App\Console\Commands\Sync;

use App\Models\Work;
use App\Models\WorkCustomer;
use App\Models\WorkCustomersFollow;
use App\Models\WorkTag;
use App\Models\WorkUser;
use App\Scopes\SuccessScope;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class WorkCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:work:customers {work_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步企业微信客户';

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
            //清空客户
            WorkCustomer::where('work_id', $workItem->work_id)->update(['status' => 0]);
            $workUserIds = WorkUser::where('work_id', $workItem->work_id)->pluck('w_user_id', 'userid')->toArray();
            WorkUser::where('work_id', $workItem->work_id)->get()->map(function ($userItem) use ($work, $workItem, $workUserIds) {
                $customerRes = $work->external_contact->list($userItem->userid);
                if (isset($customerRes['errcode']) && $customerRes['errcode'] == 0) {
                    foreach ($customerRes['external_userid'] as $externalUserId) {
                        $customerData = $work->external_contact->get($externalUserId);
                        if (isset($customerData['errcode']) && $customerData['errcode'] == 0) {
                            $workCustomer = WorkCustomer::withoutGlobalScope(SuccessScope::class)->updateOrCreate([
                                'work_id' => $workItem->work_id,
                                'external_userid' => $externalUserId
                            ], [
                                'name' => $customerData['external_contact']['name'],
                                'unionid' => $customerData['external_contact']['unionid'] ?? '',
                                'type' => $customerData['external_contact']['type'],
                                'avatar' => $customerData['external_contact']['avatar'],
                                'gender' => $customerData['external_contact']['gender'],
                                'status' => 1
                            ]);

                            WorkCustomersFollow::where('w_cust_id', $workCustomer->w_cust_id)->update(['status' => 0]);
                            $tagIds = collect($customerData['follow_user'])->map(function ($followUser) use ($workCustomer, $userItem, $workUserIds) {
                                $followUser['status'] = 1;
                                $followUser['w_user_id'] = $workUserIds[$followUser['userid']] ?? 0;
                                $followUser['create_time'] = $followUser['createtime'];
                                WorkCustomersFollow::withoutGlobalScope(SuccessScope::class)->updateOrCreate([
                                    'w_cust_id' => $workCustomer->w_cust_id,
                                    'userid' => $followUser['userid']
                                ], $followUser);
                                return $followUser['tags'];
                            })->collapse()->pluck('tag_id')->toArray();
                            $wTagIds = WorkTag::whereIn('tag_id', $tagIds)->pluck('w_tag_id')->toArray();
                            $workCustomer->tags()->sync($wTagIds);
                            Log::info("{$workItem->corpid} {$userItem->name} {$customerData['external_contact']['name']} sync ok");
                        } else {
                            $this->error("{$workItem->corpid} {$userItem->name} {$externalUserId} sync fail");
                        }
                    }
                }
            });

        });
    }
}
