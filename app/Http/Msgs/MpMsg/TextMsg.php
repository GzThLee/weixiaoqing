<?php

namespace App\Http\Msgs\MpMsg;

use App\Http\Services\MpService;
use App\Models\MpKeyword;
use App\Models\MpKeywordRecord;
use App\Models\Mp;
use App\Models\MpFan;

class TextMsg extends MpBaseMsg
{
    /**
     * 默认处理入口
     */
    public function handler()
    {
        $mpService = new MpService($this->app, $this->mp);
        return $mpService->keywordReply($this->message['openid'], $this->message['Content']);
    }
}
