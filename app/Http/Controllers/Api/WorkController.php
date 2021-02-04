<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\MpService;
use App\Models\Work;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class WorkController extends Controller
{
    /**
     * 接收企业微信事件消息
     * @param $api_token
     */
    public function serveHandler($api_token)
    {
        $work = Work::where('api_token', $api_token)->first();
        $app = work_app($work->corpid, $work->corpsecret, $work->token, $work->aes_key);
        try {
            $app->server->push(function ($message) use ($app) {
                Log::info('work msg:' . json_encode($message, JSON_UNESCAPED_UNICODE));
                try {
                    //外部联系人默认event消息类型
                    if ($message['MsgType'] == 'event') {
                        $msgClassName = 'App\\Http\\Msgs\\WorkMsg\\' . Str::studly($message['Event']) . 'Msg';
                        if (class_exists($msgClassName)) {
                            $msgMethod = new $msgClassName($app, $message);
                            $ChangeTypeFunctionName = Str::studly($message['ChangeType'] ?? 'undefined');
                            if (method_exists($msgMethod, $ChangeTypeFunctionName)) {
                                $msgMethod->$ChangeTypeFunctionName();
                            } elseif (method_exists($msgMethod, 'handler')) {
                                $msgMethod->handler(); //默认方法
                            } else {
                                Log::error('work msg function undefined');
                            }
                        } else {
                            Log::error('work msg class undefined');
                        }
                    }
                } catch (\Exception $exception) {
                    mark_error_log($exception);
                }

                return MpService::DEFAULT_RETURN;
            });

            $response = $app->server->serve();
            $response->send();
        } catch (\Exception $exception) {
            mark_error_log($exception);
        }
    }

    /**
     * 获取企业微信token
     * @param $api_token
     * @return \Illuminate\Http\JsonResponse
     */
    public function accessTokenGet($api_token)
    {
        $work = Work::where('api_token', $api_token)->first();
        try {
            $app = work_app($work->corpid, $work->corpsecret, $work->token, $work->aes_key);
            $accessToken = $app->access_token->getToken();
            return Response::json([
                'code' => 0,
                'msg' => '请求成功',
                'data' => $accessToken
            ]);
        } catch (\Psr\SimpleCache\InvalidArgumentException $exception) {
            return Response::json([
                'code' => 400,
                'msg' => '配置有误'
            ]);
        } catch (\Exception $exception) {
            return Response::json([
                'code' => 400,
                'msg' => '配置有误'
            ]);
        }

    }
}
