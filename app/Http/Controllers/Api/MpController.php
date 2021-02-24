<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\MpService;
use App\Models\Mp;
use App\Models\MpFan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class MpController extends Controller
{
    /**
     * 微信对接接口
     * @param string $api_token
     * @param Request $request
     * @return string|\Symfony\Component\HttpFoundation\Response
     */
    public function serveHandler(string $api_token, Request $request)
    {
        $echoStr = $request->input('echostr', '');  //服务器对接
        try {
            $mp = Mp::where('api_token', $api_token)->first();
            $openid = $request->input('openid', '');
            $app = mp_app($mp->app_id, $mp->app_secret, $mp->valid_token, $mp->encodingaeskey);
            $app->server->push(function ($message) use ($openid, $app, $mp) {
                MpFan::where('mp_id', $mp->mp_id)->where('openid', $openid)->update(['last_time' => date('Y-m-d H:i:s', time())]);
                $msgClassName = 'App\\Http\\Msgs\\MpMsg\\' . Str::studly($message['MsgType']) . 'Msg';
                Log::info('mp msg:' . json_encode($message, JSON_UNESCAPED_UNICODE));
                if (class_exists($msgClassName)) {
                    $message['openid'] = $openid;
                    $msgMethod = new $msgClassName($app, $mp, $message);
                    if (method_exists($msgMethod, 'handler')) {
                        return $msgMethod->handler(); //默认方法
                    } else {
                        Log::error('mp msg function undefined');
                        return MpService::DEFAULT_RETURN;
                    }
                } else {
                    Log::error('mp msg class undefined');
                    return MpService::DEFAULT_RETURN;
                }
            });
            $response = $app->server->serve();
        } catch (\Exception $exception) {
            mark_error_log($exception);
            $response = $echoStr != '' ? $echoStr : MpService::DEFAULT_RETURN;
        }
        return $response;
    }

    /**
     * 获取微信token
     * @param $api_token
     * @return \Illuminate\Http\JsonResponse
     */
    public function accessTokenGet($api_token)
    {
        $mp = Mp::where('api_token', $api_token)->first();

        try {
            $app = mp_app($mp->app_id, $mp->app_secret, $mp->valid_token, $mp->encodingaeskey);
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
            mark_error_log($exception);
            return Response::json([
                'code' => 400,
                'msg' => '配置有误'
            ]);
        }
    }
}
