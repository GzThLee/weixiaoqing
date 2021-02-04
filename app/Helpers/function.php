<?php
use Illuminate\Support\Facades\Log;

if (!function_exists('system_config')) {
    /**
     * 系统配置
     * @param string $name
     * @return string
     */
    function system_config(string $name = '')
    {
        return \App\Models\Config::where('key_name', $name)->value('value') ?? '';
    }
}

if (!function_exists('mp_app')) {
    /**
     * easywechat公众号App
     * @param string $app_id
     * @param string $secret
     * @param string $token
     * @param string $aes_key
     * @return \EasyWeChat\OfficialAccount\Application|null
     */
    function mp_app(string $app_id = '', string $secret = '', string $token = '', string $aes_key = '')
    {
        try {
            $app = \EasyWeChat\Factory::officialAccount([
                'app_id' => $app_id, 'secret' => $secret, 'token' => $token, 'aes_key' => $aes_key, 'log' => [
                    'level' => env('WECHAT_LOG_LEVEL', 'debug'),
                    'file' => env('WECHAT_LOG_FILE', storage_path('logs/easywechat_mp_' . date('Ym') . '.log'))
                ]]);
            $app->access_token->getToken();
            return $app;
        } catch (\Psr\SimpleCache\InvalidArgumentException $exception) {
            \Illuminate\Support\Facades\Log::error('work app_id:' . $app_id . ' get access_token fail');
            return null;
        } catch (Exception $exception) {
            \Illuminate\Support\Facades\Log::error('mp app_id:' . $app_id . ' get access_token fail');
            return null;
        }


    }
}

if (!function_exists('work_app')) {
    /**
     * easywechat企业微信App
     * @param string $corp_id
     * @param string $secret
     * @param string $token
     * @param string $aes_key
     * @return \EasyWeChat\Work\Application|null
     */
    function work_app(string $corp_id = '', string $secret = '', string $token = '', string $aes_key = '')
    {
        try {
            $app = \EasyWeChat\Factory::work([
                'corp_id' => $corp_id, 'secret' => $secret, 'token' => $token, 'aes_key' => $aes_key, 'log' => [
                    'level' => env('WECHAT_LOG_LEVEL', 'debug'),
                    'file' => env('WECHAT_LOG_FILE', storage_path('logs/easywechat_work_' . date('Ym') . '.log'))
                ]
            ]);
            $app->access_token->getToken();
            return $app;
        } catch (\Psr\SimpleCache\InvalidArgumentException $exception) {
            \Illuminate\Support\Facades\Log::error('work corp_id:' . $corp_id . ' get access_token fail');
            return null;
        } catch (Exception $exception) {
            \Illuminate\Support\Facades\Log::error('work corp_id:' . $corp_id . ' get access_token fail');
            return null;
        }
    }
}

if (!function_exists('fetchImg')) {
    /**
     * 获取图片
     * @param $headUrl
     * @return bool|string
     */
    function fetchImg($headUrl)
    {
        $ch = curl_init($headUrl);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $headImg = curl_exec($ch);
        curl_close($ch);
        return $headImg;
    }
}

if (!function_exists('array_to_object')) {
    /**
     * 数组 转 对象
     * @param $array
     * @return object
     */
    function array_to_object($array)
    {
        if (gettype($array) != 'array') {
            return $array;
        }
        foreach ($array as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object') {
                $arr[$k] = (object)array_to_object($v);
            }
        }
        return (object)$array;
    }
}

if (!function_exists('content_replace_nickname')) {
    /**
     * 文案替换昵称
     * @param $content
     * @param string $nickname
     * @return string|string[]
     */
    function content_replace_nickname($content, $nickname = '')
    {
        return str_replace('@nickname', $nickname, $content);
    }
}

if (!function_exists('mark_error_log')) {
    /**
     * 记录错误日志
     * @param Exception $exception
     */
    function mark_error_log(Exception $exception)
    {
        Log::error($exception->getMessage());
        Log::error($exception->getFile() . ' line:' . $exception->getLine() . ' code:' . $exception->getCode());
    }
}








