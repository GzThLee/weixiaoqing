<?php

namespace App\Http\Controllers\WorkBase;

use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Models\WorkTag;
use App\Models\WorkTagGroup;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class CustomerTagsController extends Controller
{
    /**
     * 视图
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexGet()
    {
        $tagGroups = WorkTagGroup::with('tags')->get();
        return view('work_base.customers.tags.index', compact('tagGroups'));
    }

    /**
     * 设置
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePut(Request $request)
    {
        $data = $request->all(['group_name', 'w_tagg_id', 'tag_name', 'w_tag_id']);

        /** @var Users $user */
        $user = Auth::user();
        $app = work_app($user->work->corpid, $user->work->corpsecret);

        try {
            $tagNameHasEmpty = collect($data['tag_name'])->every(function ($value) {
                return $value == '';
            });

            if ($tagNameHasEmpty) {
                throw new \Exception('标签不能为空');
            }

            if ($data['w_tagg_id'] == '') {
                $addTagRes = $app->external_contact->addCorpTag([
                    "group_name" => $data['group_name'],
                    "tag" => collect($data['tag_name'])->map(function ($tagName) {
                        return [
                            'name' => $tagName,
                            'order' => 0
                        ];
                    })->toArray()
                ]);

                $addTagGroup = $addTagRes['tag_group'];

                $tagGroup = WorkTagGroup::firstOrCreate([
                    'work_id' => $user->work->work_id,
                    'group_id' => $addTagGroup['group_id']
                ], [
                    'name' => $addTagGroup['group_name'],
                    'order' => $addTagGroup['order'],
                    'created_at' => date('Y-m-d H:i:s', $addTagGroup['create_time'])
                ]);

                $tagGroup->tags()->saveMany(collect($addTagGroup['tag'])->map(function ($tag) {
                    return new WorkTag([
                        'tag_id' => $tag['id'],
                        'name' => $tag['name'],
                        'order' => $tag['order'],
                        'created_at' => date('Y-m-d H:i:s', $tag['create_time'])
                    ]);
                })->all());

                $return = Response::json([
                    'code' => 0,
                    'msg' => '添加成功'
                ]);
            } else {
                $workTagGroup = WorkTagGroup::with('tags')->findOrFail($data['w_tagg_id']);
                $workTagGroup->update(['name' => $data['group_name']]);
                $app->external_contact->updateCorpTag($workTagGroup->group_id, $data['group_name'], $workTagGroup->order);

                //删除标签
                $deleteTagIds = WorkTag::where('w_tagg_id', $data['w_tagg_id'])->whereNotIn('w_tag_id', $data['w_tag_id'])->get()->map(function ($tag) {
                    $tag->delete();
                    return $tag->tag_id;
                })->toArray();
                $app->external_contact->deleteCorpTag($deleteTagIds, []);

                //编辑&添加标签
                collect($data['tag_name'])->map(function ($tagName, $key) use ($data, $workTagGroup, $app) {
                    if (isset($data['w_tag_id'][$key])) {
                        $tag = $workTagGroup->tags->where('w_tag_id', $data['w_tag_id'][$key])->first();
                        $tag->update(['name' => $tagName]);
                        $app->external_contact->updateCorpTag($tag->tag_id, $tagName, $workTagGroup->order);
                    } else {
                        $addTagRes = $app->external_contact->addCorpTag([
                            "group_id" => $workTagGroup->group_id,
                            "tag" => [["name" => $tagName, "order" => 0]]
                        ]);
                        $addTag = $addTagRes['tag_group']['tag'][0];
                        WorkTag::create([
                            'w_tagg_id' => $workTagGroup->w_tagg_id,
                            'tag_id' => $addTag['id'],
                            'name' => $addTag['name'],
                            'order' => $addTag['order'],
                            'created_at' => date('Y-m-d H:i:s', $addTag['create_time'])
                        ]);
                    }
                });

                $return = Response::json([
                    'code' => 0,
                    'msg' => '编辑成功'
                ]);
            }

            return $return;
        } catch (ModelNotFoundException $exception) {
            Log::error($exception->getMessage());
            Log::error($exception->getFile() . ' line:' . $exception->getLine() . ' code:' . $exception->getCode());
            return Response::json([
                'code' => 400,
                'msg' => '操作失败:标签组id有误'
            ]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            Log::error($exception->getFile() . ' line:' . $exception->getLine() . ' code:' . $exception->getCode());
            return Response::json([
                'code' => 400,
                'msg' => '操作失败:' . $exception->getMessage()
            ]);
        }
    }

    /**
     * 删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function destroyDelete(Request $request)
    {
        $data = $request->all(['w_tagg_id']);

        /** @var Users $user */
        $user = Auth::user();
        $app = work_app($user->work->corpid, $user->work->corpsecret);
        $workTagGroup = WorkTagGroup::with('tags')->findOrFail($data['w_tagg_id']);
        $tagIds = $workTagGroup->tags->pluck('tag_id')->toArray();
        $groupIds = [$workTagGroup->group_id];
        $app->external_contact->deleteCorpTag($tagIds, $groupIds);

        //删除数据
        $workTagGroup->tags->map(function ($tag) {
            $tag->delete();
        });
        $workTagGroup->delete();

        return Response::json([
            'code' => 0,
            'msg' => '删除成功'
        ]);
    }
}
