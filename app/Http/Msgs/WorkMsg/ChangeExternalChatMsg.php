<?php

namespace App\Http\Msgs\WorkMsg;

use App\Models\MpFan;
use App\Models\Users;
use App\Models\Work;
use App\Models\WorkCustomer;
use App\Models\WorkGroupchat;
use App\Models\WorkGroupchatCustomer;
use App\Models\WorkGroupCode;
use App\Models\WorkGroupCodeCustomer;
use EasyWeChat\Kernel\Messages\Text;
use Illuminate\Support\Facades\Log;

class ChangeExternalChatMsg extends WorkBaseMsg
{
    /**
     * 客户群创建事件
     */
    public function Create()
    {
        try {
            $chatId = $this->message['ChatId'];
            $chatRes = $this->app->external_contact->getGroupChat($chatId);
            WorkGroupchat::create([
                'work_id' => $this->work->work_id,
                'chat_id' => $chatRes['group_chat']['chat_id'],
                'name' => $chatRes['group_chat']['name'],
                'owner' => $chatRes['group_chat']['owner'],
                'create_time' => date('Y-m-d H:i:s', $chatRes['group_chat']['create_time'])
            ]);
        } catch (\Exception $exception) {
            mark_error_log($exception);
        }
    }

    /**
     * 客户群解散事件
     */
    public function Dismiss()
    {
        $chatId = $this->message['ChatId'];
        WorkGroupchat::updateOrCreate([
            'work_id' => $this->work->work_id,
            'chat_id' => $chatId
        ], ['status' => -1]);
    }

    /**
     * 客户群变更事件
     */
    public function Update()
    {
        $chatId = $this->message['ChatId'];
        $chatRes = $this->app->external_contact->getGroupChat($chatId);
        $chatData = $chatRes['group_chat'];
        $workGroupChatData = WorkGroupchat::updateOrCreate([
            'work_id' => $this->work->work_id,
            'chat_id' => $chatId
        ], [
            'name' => $chatData['name'],
            'owner' => $chatData['owner'],
            'create_time' => date('Y-m-d H:i:s', $chatData['create_time'])
        ]);

        //判断是否任务群
        $workGroupCode = WorkGroupCode::where('work_id', $this->work->work_id)->where('end_time', '>=', now()->toDateTimeString())->where('w_gchat_id', $workGroupChatData->w_gchat_id)->first();

        $workCustomers = WorkCustomer::where('work_id', $this->work->work_id)->pluck('w_cust_id', 'external_userid');
        collect($chatData['member_list'])->map(function ($item) use ($workGroupChatData, $workCustomers, $workGroupCode) {
            WorkGroupchatCustomer::updateOrCreate([
                'w_gchat_id' => $workGroupChatData->w_gchat_id,
                'userid' => $item['userid']
            ], [
                'w_cust_id' => $workCustomers[$item['userid']] ?? 0,
                'join_time' => date('Y-m-d H:i:s', $item['join_time']),
                'join_scene' => $item['join_scene'],
                'type' => $item['type']
            ]);

            //更新加群记录
            $customer = WorkCustomer::where('work_id', $this->work->work_id)->where('external_userid', $item['userid'])->first();
            if ($workGroupCode && $customer) {
                $workGroupCodeCustomer = WorkGroupCodeCustomer::where('w_gcode_id', $workGroupCode->w_gcode_id)->where('w_cust_id', $customer->w_cust_id)->where('w_gcode_cust_pid', '<>', 0)->where('is_join_group', 0)->with('customer')->first();
                if ($workGroupCodeCustomer) {
                    $workGroupCodeCustomer->update(['is_join_group' => 1]);


                    $user = Users::with('mp')->find($this->work->user_id);
                    $mpApp = mp_app($user->mp->app_id, $user->mp->app_secret);

                    //获取入群客户的上级客户的公众号粉丝信息
                    $workGroupCodeParentCustomer = WorkGroupCodeCustomer::where('w_gcode_cust_id', $workGroupCodeCustomer->w_gcode_cust_pid)->with('customer')->first();
                    $parentMpFan = MpFan::where('mp_id', $user->mp->mp_id)->where('unionid', $workGroupCodeParentCustomer->customer->unionid)->first();
                    if ($parentMpFan && $mpApp) {
                        //邀请入群人数统计
                        $joinCustomers = WorkGroupCodeCustomer::where('w_gcode_cust_pid', $workGroupCodeCustomer->w_gcode_cust_pid)->where('w_gcode_cust_pid', '<>', 0)->sum('is_join_group');

                        //特殊企业模板有消息推送
                        if ($this->work->corpid = 'ww161ac771bb94b9f4') {
                            if ($joinCustomers < $workGroupCode->task_num) {
                                $needNum = $workGroupCode->task_num - $joinCustomers;
                                //普通提醒
                                $mpApp->template_message->send([
                                    'touser' => $parentMpFan->openid,
                                    'template_id' => 'Ej_yrDoRJMA8nUuwv-tAqBoeBXglteHB26VpH9MsqgI',
                                    'data' => [
                                        'first' => '恭喜您！您成功邀请一个新素友进群',
                                        'keyword1' => $workGroupCodeCustomer->customer->name ?? '',
                                        'keyword2' => now()->toDateTimeString(),
                                        'remark' => '还差' . $needNum . '个即可完成任务！加油哦~',
                                    ]
                                ]);
                            } elseif ($joinCustomers == $workGroupCode->task_num) {
                                //任务完成提醒
                                $mpApp->template_message->send([
                                    'touser' => $parentMpFan->openid,
                                    'template_id' => 'Ej_yrDoRJMA8nUuwv-tAqBoeBXglteHB26VpH9MsqgI',
                                    'data' => [
                                        'first' => '恭喜您！您成功邀请一个新素友进群',
                                        'keyword1' => $workGroupCodeCustomer->customer->name ?? '',
                                        'keyword2' => now()->toDateTimeString(),
                                        'remark' => '您已完成任务！请联系小助手，并提供公众号截图领取苹果吧～',
                                    ]
                                ]);
                            }
                        } else {
                            if ($joinCustomers < $workGroupCode->task_num) {
                                $needNum = $workGroupCode->task_num - $joinCustomers;
                                $message = new Text('恭喜您！您成功邀请一个好友进群，还差' . $needNum . '个即可完成任务！加油哦~');
                                $mpApp->customer_service->message($message)->to($parentMpFan->openid)->send();
                            } elseif ($joinCustomers == $workGroupCode->task_num) {
                                $message = new Text('恭喜您！您已完成任务！请联系小助手领取奖品吧');
                                $mpApp->customer_service->message($message)->to($parentMpFan->openid)->send();
                            }
                        }
                    }
                }
            }
        });
    }
}
