<?php

namespace App\Http\Msgs\MpMsg;

use App\Http\Services\MpService;
use App\Models\MpFan;
use App\Models\MpWelcome;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\Text;
use Illuminate\Support\Str;

class EventMsg extends MpBaseMsg
{
    /**
     * 事件处理处理入口
     * @return Image|News|Text|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handler()
    {
        if (isset($this->message['Ticket'])) {
            $return = $this->ticketEvent();
        } else {
            switch ($this->message['Event']) {
                case 'subscribe':
                    $return = $this->subscribeEvent();
                    break;
                case 'unsubscribe':
                    $return = $this->unsubscribeEvent();
                    break;
                case 'CLICK':
                    $return = $this->clickEvent();
                    break;
                default:
                    $return = MpService::DEFAULT_RETURN;
                    break;
            }
        }
        return $return;
    }

    /**
     * 点击事件
     * @return News|Text|string
     */
    public function clickEvent()
    {
        $mpService = new MpService($this->app, $this->mp);
        return $mpService->keywordReply($this->message['openid'], $this->message['EventKey']);
    }

    /**
     * 关注事件
     * @return News|Text|string
     */
    public function subscribeEvent()
    {
        try {
            $mpService = new MpService($this->app, $this->mp);
            $fan = $mpService->subscribe($this->message['openid'], MpFan::SCAN_UNDEFINED);
            $welcome = MpWelcome::where('mp_id', $this->mp->mp_id)->select('content', 'media_type')->firstOrFail();
            $return = MpService::getResponseMedia($welcome->media_type, $welcome->content, $fan);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            $return = MpService::DEFAULT_RETURN;
        }
        return $return;
    }

    /**
     * 取消关注事件
     * @return string
     */
    public function unsubscribeEvent()
    {
        $mpService = new MpService($this->app, $this->mp);
        $mpService->unsubscribe($this->message['openid']);
        return MpService::DEFAULT_RETURN;
    }

    /**
     * 带参数的二维码事件
     * @return Image|News|Text|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function ticketEvent()
    {
        $message = $this->message;
        $event = $message['Event'];
        $openId = $message['FromUserName'];
        $eventKey = $message['EventKey'];
        $return = MpService::DEFAULT_RETURN;
        $mpService = new MpService($this->app, $this->mp);
        $sceneStr = str_replace('qrscene_', '', $eventKey);
        try {
            if ($event == 'subscribe') {
                $scanType = MpFan::SCAN_SUBSCRIBE;  //新用户
                $fan = $mpService->subscribe($openId, MpFan::SCAN_SUBSCRIBE);
            } else if ($event == 'SCAN') {
                $scanType = MpFan::SCAN_SUBSCRIBED; //旧用户
                $fan = MpFan::firstOrCreate(['openid' => $openId, 'mp_id' => $this->mp->mp_id]);
            } else {
                $scanType = MpFan::SCAN_UNDEFINED;
                $fan = MpFan::firstOrCreate(['openid' => $openId, 'mp_id' => $this->mp->mp_id]);
            }

            if (Str::contains($sceneStr, 'MP_')) {
                if (Str::contains($sceneStr, 'MP_CC')) {
                    $return = $mpService->channelCode($fan, $sceneStr, $scanType, $event);
                } else {
                    $return = MpService::DEFAULT_RETURN;
                }
            } else {
                $return = MpService::DEFAULT_RETURN;
            }
        } catch (\Exception $exception) {
            mark_error_log($exception);
        }

        return $return;
    }
}

