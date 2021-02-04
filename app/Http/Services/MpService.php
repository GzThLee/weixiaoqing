<?php


namespace App\Http\Services;

use App\Models\Mp;
use App\Models\MpChannelCode;
use App\Models\MpChannelCodeFan;
use App\Models\MpFan;
use App\Models\MpKeyword;
use App\Models\MpKeywordRecord;
use App\Models\MpTag;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use EasyWeChat\OfficialAccount\Application;
use Illuminate\Support\Facades\Log;

class MpService
{
    /** @var string 默认返回 */
    const DEFAULT_RETURN = 'success';

    private $app;
    private $mp;

    /**
     * MpService constructor.
     * @param Application $app
     * @param Mp $mp
     */
    public function __construct(Application $app, Mp $mp)
    {
        $this->app = $app;
        $this->mp = $mp;
    }

    /**
     * 获取媒体的中文名称
     * @param $mediaType
     * @return mixed|string
     */
    public static function getMediaName($mediaType)
    {
        return Arr::get([
            Mp::TEXT_MEDIA => '文本消息',
            Mp::NEWS_MEDIA => '单图文消息',
            Mp::MORE_NEWS_MEDIA => '多图文消息',
            Mp::IMAGE_MEDIA => '图片消息',
            Mp::VOICE_MEDIA => '语音消息',
            Mp::VIDEO_MEDIA => '视频消息',
            Mp::CARD_MEDIA => '卡券消息',
            Mp::MINI_APP_MEDIA => '小程序消息'
        ], $mediaType, '未知');
    }

    /**
     * 获取素材内容
     * @param int $mediaType
     * @param array $content
     * @return array|mixed
     */
    public static function getMediaContent($mediaType = 0, $content = [])
    {
        switch ($mediaType) {
            case Mp::TEXT_MEDIA:
                $mediaContent = ['content' => $content[Mp::TEXT_MEDIA]['content']];
                break;
            case Mp::NEWS_MEDIA:
                $mediaContent = $content[Mp::NEWS_MEDIA];
                break;
            case Mp::MORE_NEWS_MEDIA:
                $mediaContent = $content[Mp::MORE_NEWS_MEDIA];
                break;
            default:
                $mediaContent = [];
                break;
        }
        return $mediaContent;
    }

    /**
     * @param $mediaType
     * @param $content
     * @param $user
     * @return News|Text|string
     */
    public static function getResponseMedia($mediaType, $content, $user)
    {
        $nickname = $user['nickname'] ?? $user->nickname ?? '';
        switch ($mediaType) {
            case Mp::TEXT_MEDIA:
                $content = content_replace_nickname($content->content, $nickname);
                $mediaContent = new Text($content);
                break;
            case Mp::NEWS_MEDIA:
                $mediaContent = new News([
                    new NewsItem([
                        'title' => content_replace_nickname($content->title, $nickname),
                        'description' => content_replace_nickname($content->description, $nickname),
                        'url' => $content->url,
                        'image' => $content->cover_url
                    ]),
                ]);
                break;
            case Mp::MORE_NEWS_MEDIA:
                $items = [];
                for ($k = 0; $k < count($content->title); $k++) {
                    $items[] = new NewsItem([
                        'title' => content_replace_nickname($content->title[$k], $nickname),
                        'description' => '',
                        'url' => $content->url[$k],
                        'image' => $content->cover_url[$k]
                    ]);
                }
                $mediaContent = new News($items);
                break;
            case Mp::VOICE_MEDIA:
            case Mp::VIDEO_MEDIA:
            case Mp::IMAGE_MEDIA:
                $mediaContent = MpService::DEFAULT_RETURN;
                break;
            default:
                $mediaContent = MpService::DEFAULT_RETURN;
        }

        return $mediaContent;
    }

    /**
     * 关键字回复
     * @param $openid
     * @param $keyword
     * @return News|Text|string
     */
    public function keywordReply($openid, $keyword)
    {
        $return = MpService::DEFAULT_RETURN;

        $exactKeyword = MpKeyword::where('keyword', $keyword)->where('rule_type', MpKeyword::EXACT)->first();
        $fuzzyKeyword = MpKeyword::where('keyword', 'LIKE', "%{$keyword}%")->where('rule_type', MpKeyword::FUZZY)->orderByDesc('m_kw_id')->first();
        $keyword = $exactKeyword ?? $fuzzyKeyword ?? null;
        if ($keyword) {
            //增加记录
            $keyword->increment('trigger_count');

            //存储关键字记录
            $fan = MpFan::where('openid', $openid)->first();
            MpKeywordRecord::create(['m_kw_id' => $keyword->m_kw_id, 'm_fan_id' => ($fan->m_fan_id ?? 0)]);

            $this->attachTag($openid, $keyword->m_tag_id);
            $return = MpService::getResponseMedia($keyword->media_type, $keyword->content, $fan);
        }

        return $return;
    }

    /**
     * 设置标签
     * @param $openid
     * @param $mTagId
     */
    public function attachTag($openid, $mTagId)
    {
        try {
            $tag = MpTag::findOrFail($mTagId);
            $this->app->user_tag->tagUsers([$openid], $tag->tag_id);
            $fans = MpFan::where('openid', $openid)->with('tags')->first();
            $mTagIds = $fans->tags->pluck('m_tag_id')->concat([$mTagId])->unique()->toArray();
            $fans->tags()->sync($mTagIds);
        } catch (ModelNotFoundException $exception) {
            Log::error("not found tag openid:{$openid} set tag fail");
        } catch (GuzzleException $exception) {
            Log::error("mp set tag fail openid:{$openid} set tag fail");
        } catch (\Exception $exception) {
            Log::error("mp set tag fail openid:{$openid} set tag fail");
        }
    }

    /**
     * 关注订阅
     * @param $openid
     * @param $subscribeType
     * @return MpFan
     */
    public function subscribe($openid, $subscribeType): MpFan
    {
        try {
            $user = $this->app->user->get($openid);
            $user['subscribe_type'] = $subscribeType;
            $user['is_subscribe'] = MpFan::SUBSCRIBE;
            $user['subscribe_time'] = date('Y-m-d H:i:s', $user['subscribe_time']);
            $user['last_time'] = now();
            $fan = MpFan::updateOrCreate(['openid' => $openid, 'mp_id' => $this->mp->mp_id], $user);
        } catch (\Exception $exception) {
            $fan = MpFan::firstOrCreate(['openid' => $openid, 'mp_id' => $this->mp->mp_id], []);
        }
        return $fan;
    }

    /**
     * 取消关注
     * @param $openid
     */
    public function unsubscribe($openid)
    {
        $fan = MpFan::updateOrCreate(
            ['openid' => $openid, 'mp_id' => $this->mp->mp_id],
            ['is_subscribe' => MpFan::UNSUBSCRIBE, 'unsubscribe_time' => now(), 'last_time' => now()]
        );

        //是否渠道二维码的取消关注
        try {
            $channelCodeLastRecord = MpChannelCodeFan::where('m_fan_id', $fan->m_fan_id)->orderByDesc('created_at')->firstOrFail();
            $channelCode = MpChannelCode::where('m_ccode_id', $channelCodeLastRecord->m_ccode_id)->firstOrFail();
            if ($channelCodeLastRecord->scan_type == MpFan::SCAN_SUBSCRIBE) {
                //新用户关注记录
                MpChannelCodeFan::create([
                    'scan_type' => MpFan::SCAN_SUBSCRIBE_UNSUBSCRIBE,
                    'm_ccode_id' => $channelCodeLastRecord->m_ccode_id,
                    'm_fan_id' => $fan->m_fan_id
                ]);
                $channelCode->increment('new_fans_unsubscribe');
            } else if ($channelCodeLastRecord->scan_type == MpFan::SCAN_SUBSCRIBED) {
                //旧用户关注记录
                MpChannelCodeFan::create([
                    'scan_type' => MpFan::SCAN_SUBSCRIBED_UNSUBSCRIBE,
                    'm_ccode_id' => $channelCodeLastRecord->m_ccode_id,
                    'm_fan_id' => $fan->m_fan_id
                ]);
                $channelCode->increment('old_fans_unsubscribe');
            }
        } catch (ModelNotFoundException $exception) {
            Log::info('fans unsubscribe not channel code');
        }
    }

    /**
     * 渠道二维码
     * @param MpFan $fan
     * @param string $sceneStr
     * @param int $scanType
     * @param string $event
     * @return News|Text|string
     */
    public function channelCode(MpFan $fan, string $sceneStr, int $scanType, string $event)
    {
        $channelCode = MpChannelCode::where('scene_str', $sceneStr)->where('status', 1)->first();
        $this->attachTag($fan->openid, $channelCode->m_tag_id);

        $channelCode->increment('scan_count');
        if ($event == 'subscribe') {
            $channelCode->increment('new_fans_subscribe');  // 新粉丝数+1
        } else if ($event == 'SCAN') {
            $channelCode->increment('old_fans_subscribe');  // 旧粉丝数粉丝数+1
        }

        //增加记录
        $fan->channelCode()->attach($channelCode->m_ccode_id, ['scan_type' => $scanType]);
        return MpService::getResponseMedia($channelCode->media_type, $channelCode->content, $fan);
    }
}
