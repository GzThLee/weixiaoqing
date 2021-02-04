<?php

namespace App\Console\Commands\Schedule;

use App\Jobs\MpTemplatePush;
use App\Models\Mp;
use App\Models\MpTemplate;
use EasyWeChat\Factory;
use Illuminate\Console\Command;

class MpTemplatePushs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:mp:template:pushs {mp_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '公众号消息模板推送';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mpId = $this->argument('mp_id');
        $res = MpTemplate::when($mpId != null, function ($query) use ($mpId) {
            return $query->where('mp_id', (int)$mpId);
        })->whereNull('push_time')->orderBy('m_temp_id')->get()->map(function ($template) {
            $template->update(['push_time' => now()]);
            $content = $template->content;
            $pushNum = collect($content->tousers)->map(function ($touser) use ($template, $content) {
                if ($touser == '') {
                    return 0;
                } else {
                    $templateUrl = $content->url ?? '';
                    $templateMiniProgram = $content->miniprogram ?? [];
                    $templateItem = [
                        'touser' => $touser,
                        'template_id' => $content->template_id,
                        'data' => collect($content->data)->map(function ($item) {
                            return (array)$item;
                        })->toArray()
                    ];
                    if ($templateUrl != '') {
                        $templateItem['url'] = $templateUrl;
                    }
                    if ($templateMiniProgram) {
                        $templateItem['miniprogram'] = $templateMiniProgram;
                    }
                    MpTemplatePush::dispatch($template->m_temp_id, $templateItem);
                    return 1;
                }
            })->sum();

            if ($pushNum > 0) {
                return $pushNum;
            } else {
                $template->update(['finish_time' => now()]);    //无记录直接完成
                return 0;
            }
        })->sum();

        if ($res > 0) {
            $this->info(now() . " template push success!");
        }
    }
}
