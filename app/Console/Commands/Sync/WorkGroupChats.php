<?php

namespace App\Console\Commands\Sync;

use App\Models\Work;
use App\Models\WorkCustomer;
use App\Models\WorkGroupchat;
use App\Models\WorkGroupchatCustomer;
use App\Scopes\SuccessScope;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class WorkGroupChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:work:group:chats {work_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步企业微信客户群';

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
            WorkGroupchat::where('work_id', $workItem->work_id)->update(['status' => 0]);
            $groupChatsRes = $work->external_contact->getGroupChats(['limit' => 1000]);
            if (isset($groupChatsRes['errcode']) && $groupChatsRes['errcode'] == 0) {
                foreach ($groupChatsRes['group_chat_list'] as $chatItem) {
                    $chatRes = $work->external_contact->getGroupChat($chatItem['chat_id']);
                    if (isset($chatRes['errcode']) && $chatRes['errcode'] == 0) {
                        $chatData = $chatRes['group_chat'];
                        $workGroupChatData = WorkGroupchat::withoutGlobalScope(SuccessScope::class)->updateOrCreate([
                            'work_id' => $workItem->work_id,
                            'chat_id' => $chatData['chat_id']
                        ], [
                            'name' => $chatData['name'] == '' ? '群聊' : $chatData['name'],
                            'owner' => $chatData['owner'],
                            'create_time' => date('Y-m-d H:i:s', $chatData['create_time']),
                            'status' => 1,
                            'notice' => $chatData['notice'] ?? ''
                        ]);

                        $workCustomers = WorkCustomer::where('work_id', $workItem->work_id)->pluck('w_cust_id', 'external_userid');
                        WorkGroupchatCustomer::where('w_gchat_id',$workGroupChatData->w_gchat_id)->delete();
                        collect($chatData['member_list'])->map(function ($item) use ($workGroupChatData, $workCustomers) {
                            WorkGroupchatCustomer::updateOrCreate([
                                'w_gchat_id' => $workGroupChatData->w_gchat_id,
                                'userid' => $item['userid']
                            ], [
                                'w_cust_id' => $workCustomers[$item['userid']] ?? 0,
                                'join_time' => date('Y-m-d H:i:s', $item['join_time']),
                                'join_scene' => $item['join_scene'],
                                'type' => $item['type']
                            ]);
                        });

                        Log::info("{$workItem->name}:{$workItem->corpid} {$chatData['name']} sync ok");
                    }
                }
            }
        });
    }
}
