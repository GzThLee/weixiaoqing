<?php

namespace App\Jobs;

use App\Models\MpTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MpTemplatePush implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $templateId;
    protected $content;

    /**
     * MpTemplatePush constructor.
     * @param $templateId
     * @param $content
     */
    public function __construct($templateId, $content)
    {
        $this->templateId = $templateId;
        $this->content = $content;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $template = MpTemplate::with('mp')->find($this->templateId);
        $app = mp_app($template->mp->app_id, $template->mp->app_secret);
        $pushRes = $app->template_message->send($this->content);
        if ($pushRes['errmsg'] == 'ok') {
            Log::info('template push success openid:' . $this->content['touser']);
            $template->increment('send_success_count');
        } else {
            Log::info('template push fail error:' . $pushRes['errmsg']);
            $template->increment('send_fail_count');
        }
        if ($template->send_count == ($template->send_success_count + $template->send_fail_count)) {
            $template->update(['finish_time' => now()]);
        }
    }
}
