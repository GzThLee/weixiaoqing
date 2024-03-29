<?php

namespace App\Http\Msgs\MpMsg;

use App\Http\Services\MpService;

class FileMsg extends MpBaseMsg
{
    /**
     * 默认处理入口
     */
    public function handler()
    {
        return MpService::DEFAULT_RETURN;
    }
}
