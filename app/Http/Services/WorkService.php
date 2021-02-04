<?php


namespace App\Http\Services;


use App\Models\Work;
use App\Models\WorkChannelCode;
use App\Models\WorkChannelCodeCustomer;
use App\Models\WorkCustomer;
use App\Models\WorkCustomersFollow;
use App\Models\WorkUser;
use App\Models\WorkWelcome;
use EasyWeChat\Work\Application;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\GuzzleException;

class WorkService
{
    private $app;
    private $work;
    private $workUser;

    /**
     * WorkService constructor.
     * @param Application $app
     * @param Work $work
     * @param WorkUser $workUser
     */
    public function __construct(Application $app, Work $work, WorkUser $workUser)
    {
        $this->app = $app;
        $this->work = $work;
        $this->workUser = $workUser;
    }

    /**
     * 创建客户
     * @param $externalUserId
     */
    /**
     * @param $externalUserId
     * @return |null
     */
    public function createCustomer($externalUserId)
    {
        try {
            $customerData = $this->app->external_contact->get($externalUserId);
            if (isset($customerData['errcode']) && $customerData['errcode'] == 0) {
                $workCustomerData = WorkCustomer::updateOrCreate([
                    'work_id' => $this->work->work_id,
                    'w_user_id' => $this->workUser->w_user_id,
                    'external_userid' => $externalUserId
                ], [
                    'name' => $customerData['external_contact']['name'],
                    'unionid' => $customerData['external_contact']['unionid'],
                    'type' => $customerData['external_contact']['type'],
                    'avatar' => $customerData['external_contact']['avatar'],
                    'gender' => $customerData['external_contact']['gender']
                ]);

                collect($customerData['follow_user'])->map(function ($followUser) use ($workCustomerData) {
                    $followUser['add_way'] = $followUser['add_way'] ?? 0;
                    return WorkCustomersFollow::updateOrCreate([
                        'w_cust_id' => $workCustomerData->w_cust_id,
                        'userid' => $followUser['userid']
                    ], $followUser);
                });

                return $workCustomerData;
            } else {
                return null;
            }
        } catch (\Exception $exception) {
            mark_error_log($exception);
            return null;
        }
    }

    /**
     * state获取msg
     * @param string $state
     * @param string $externalUserID
     * @return array
     */
    public function stateWelcomeMsg(string $state, string $externalUserID)
    {
        $msg = [];
        $actCode = '';  //功能标识
        $actId = '';    //功能id
        $actUserId = '';//用户id
        try {
            //state {活动标识}:{活动id}:{用户id}
            list($actCode, $actId, $actUserId) = explode(':', $state);
        } catch (\Exception $exception) {
            mark_error_log($exception);
            Log::error('work add_external_contact state error ' . $state);
        }

        $workCustomerId = WorkCustomer::where([
            'work_id' => $this->work->work_id,
            'external_userid' => $externalUserID
        ])->value('w_cust_id');

        if ($actCode == 'CCODE') {
            //渠道二维码
            $welcome = WorkChannelCode::where('w_ccode_id', $actId)
                ->where('work_id', $this->work->work_id)
                ->where('w_user_id', $this->workUser->w_user_id)
                ->where('end_time', '>=', now()->toDateTimeString())->first();
            if ($welcome) {
                WorkChannelCodeCustomer::firstOrCreate([
                    'w_ccode_id' => $actId,
                    'w_cust_id' => $workCustomerId
                ]);
            }
        } else {
            $welcome = (object)[];
        }
        if ($welcome) {
            $msg['text'] = (array)$welcome->text;
            if (isset($defaultWelcomeType) && isset($defaultImageUrl)) {
                $msg['image']['pic_url'] = $defaultImageUrl;
            } else {
                switch ($welcome->welcome_type) {
                    case WorkWelcome::IMAGE:
                        $msg['image'] = (array)$welcome->image;
                        break;
                    case WorkWelcome::LINK:
                        $msg['link'] = (array)$welcome->link;
                        break;
                    case WorkWelcome::MINI:
                        $msg['miniprogram'] = (array)$welcome->miniprogram;
                        break;
                    default:
                        break;
                }
            }

        }

        return $msg;
    }

    /**
     * 获取欢迎语msg
     * @return array
     */
    public function welcomeMsg()
    {
        $msg = [];
        $welcome = WorkWelcome::where('work_id', $this->work->work_id)->where('w_user_id', $this->workUser->w_user_id)->first();
        if ($welcome) {
            $msg['text'] = (array)$welcome->text;
            switch ($welcome->welcome_type) {
                case WorkWelcome::IMAGE:
                    $msg['image'] = (array)$welcome->image;
                    break;
                case WorkWelcome::LINK:
                    $msg['link'] = (array)$welcome->link;
                    break;
                case WorkWelcome::MINI:
                    $msg['miniprogram'] = (array)$welcome->miniprogram;
                    break;
                default:
                    break;
            }
        }
        return $msg;
    }

    /**
     * 发送欢迎语
     * @param $welcomeCode
     * @param $msg
     */
    public function sendWelcome($welcomeCode, $msg)
    {
        try {
            if (isset($msg['image'])) {
                $imageUri = str_replace(config('app.url'), '', $msg['image']['pic_url']);
                if (strpos($imageUri, 'https://wework.qpic.cn') === false) {
                    //非企业微信图片
                    $materialRes = $this->app->media->uploadImage(public_path($imageUri));
                } else {
                    $materialRes = $this->app->media->uploadImage($imageUri);
                }
                $msg['image']['media_id'] = $materialRes['media_id'];
            }
            $this->app->external_contact_message->sendWelcome($welcomeCode, $msg);
        } catch (GuzzleException $exception) {

        } catch (\Exception $exception) {
            mark_error_log($exception);
        }

    }
}
