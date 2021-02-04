<?php

namespace App\Http\Msgs\WorkMsg;

use App\Http\Services\WorkService;

class ChangeExternalContactMsg extends WorkBaseMsg
{
    /**
     * 添加企业客户事件
     */
    public function AddExternalContact()
    {
        $workService = new WorkService($this->app, $this->work, $this->workUser);
        $workService->createCustomer($this->message['ExternalUserID']);

        $msg = [];
        if (isset($this->message['State'])) {
            $msg = $workService->stateWelcomeMsg($this->message['State'], $this->message['ExternalUserID']);
        }

        if (!isset($msg['text'])) {
            $msg = $workService->welcomeMsg();
        }

        if (isset($msg['text'])) {
            $workService->sendWelcome($this->message['WelcomeCode'], $msg);
        }
    }

    /**
     * 编辑企业客户事件
     */
    public function EditExternalContact()
    {
        //:todo
    }

    /**
     * 外部联系人免验证添加成员事件
     */
    public function AddHalfExternalContact()
    {
        $workService = new WorkService($this->app, $this->work, $this->workUser);
        $workService->createCustomer($this->message['ExternalUserID']);

        $msg = [];
        if ($this->message['State']) {
            $msg = $workService->stateWelcomeMsg($this->message['State'], $this->message['ExternalUserID']);
        }

        if (!isset($msg['text'])) {
            $msg = $workService->welcomeMsg();
        }

        if (isset($msg['text'])) {
            $workService->sendWelcome($this->message['WelcomeCode'], $msg);
        }
    }

    /**
     * 删除企业客户事件
     */
    public function DelExternalContact()
    {
        //:todo
    }

    /**
     * 删除跟进成员事件(被外部联系人删除)
     */
    public function DelFollowUser()
    {
        //:todo
    }

    /**
     * 客户接替失败事件
     */
    public function TransferFail()
    {
        //:todo
    }
}
