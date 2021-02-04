<?php


namespace App\Http\Msgs\WorkMsg;


use App\Models\Work;
use App\Models\WorkUser;
use EasyWeChat\Work\Application;

class WorkBaseMsg
{
    /** @var Application $app */
    protected $app;
    /** @var array $message */
    protected $message;
    /** @var Work $work */
    protected $work;
    /** @var WorkUser $work */
    protected $workUser;

    /**
     * ChangeExternalChatService constructor.
     * @param Application $app
     * @param array $message
     */
    public function __construct(Application $app, array $message)
    {
        $this->app = $app;
        $this->message = $message;
        $this->work = Work::where('corpid', $message['ToUserName'])->first();
        $this->workUser = WorkUser::where(['work_id' => $this->work->work_id, 'userid' => ($message['UserID'] ?? 0)])->first();
    }
}
