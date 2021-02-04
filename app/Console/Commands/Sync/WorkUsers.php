<?php

namespace App\Console\Commands\Sync;

use App\Models\Work;
use App\Models\WorkDepartment;
use App\Models\WorkUser;
use App\Scopes\SuccessScope;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class WorkUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:work:users {work_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步企业微信成员';

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
        $workId = $this->argument('work_id');
        Work::when($workId != null, function ($query) use ($workId) {
            return $query->where('work_id', (int)$workId);
        })->get()->map(function ($workItem) {
            $work = work_app($workItem->corpid, $workItem->usersecret);

            WorkDepartment::where('work_id', $workItem->work_id)->update(['status' => 0]);
            WorkUser::where('work_id', $workItem->work_id)->update(['status' => 0]);

            //同步部门
            $departmentRes = $work->department->list();
            if (isset($departmentRes['errcode']) && $departmentRes['errcode'] == 0) {
                foreach ($departmentRes['department'] as $department) {
                    WorkDepartment::withoutGlobalScope(SuccessScope::class)->updateOrCreate([
                        'work_id' => $workItem->work_id,
                        'department_id' => $department['id'],
                    ], [
                        'name' => $department['name'],
                        'name_en' => $department['name_en'] ?? '',
                        'parentid' => $department['parentid'],
                        'order' => $department['order'],
                        'status' => 1
                    ]);
                    Log::info("{$workItem->corpid} {$department['name']} sync ok");
                }
            }

            //同步部门成员
            WorkDepartment::where('work_id', $workItem->work_id)->get()->map(function ($departmentItem) use ($work, $workItem) {
                $userRes = $work->user->getDepartmentUsers($departmentItem->department_id);
                if (isset($userRes['errcode']) && $userRes['errcode'] == 0) {
                    foreach ($userRes['userlist'] as $user) {
                        WorkUser::withoutGlobalScope(SuccessScope::class)->updateOrCreate([
                            'work_id' => $workItem->work_id,
                            'userid' => $user['userid']
                        ], [
                            'department_ids' => $user['department'],
                            'name' => $user['name'],
                            'open_userid' => $user['open_userid'] ?? '',
                            'status' => 1
                        ]);
                        Log::info("{$workItem->corpid} {$departmentItem->name} {$user['name']} sync ok");
                    }
                }
            });

        });
    }
}
