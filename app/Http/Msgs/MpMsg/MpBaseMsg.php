<?php

namespace App\Http\Msgs\MpMsg;

use App\Models\Mp;
use EasyWeChat\OfficialAccount\Application;

class MpBaseMsg
{
    protected $app;
    protected $message;
    protected $mp;

    /**
     * EventService constructor.
     * @param Application $app
     * @param Mp $mp
     * @param array $message
     */
    public function __construct(Application $app, Mp $mp, array $message = [])
    {
        $this->app = $app;
        $this->message = $message;
        $this->mp = $mp;
    }
}
