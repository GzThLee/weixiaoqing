<?php

namespace App\Http\Controllers\WorkBase;

use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Models\WorkCustomer;
use App\Models\WorkCustomersFollow;
use App\Models\WorkTag;
use App\Models\WorkUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Builder;

class CustomersController extends Controller
{
    /**
     * 视图
     * @return \Illuminate\Contracts\View\View
     */
    public function indexGet()
    {
        $follow_users = WorkUser::select('userid', 'name')->get();
        return View::make('work_base.customers.index', compact('follow_users'));
    }

    /**
     * 数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataGet(Request $request)
    {
        $rows = $request->input('limit', 30);
        $data = $request->all(['customer_name', 'gender', 'follow_user_id']);
        $res = WorkCustomersFollow::when($data['customer_name'] != '', function ($query) use ($data) {
            return $query->whereHas('customer', function (Builder $query) use ($data) {
                return $query->where('name', 'LIKE', '%' . $data['customer_name'] . '%');
            });
        })->when($data['gender'] != '', function ($query) use ($data) {
            return $query->whereHas('customer', function (Builder $query) use ($data) {
                return $query->where('gender', $data['gender']);
            });
        })->when($data['follow_user_id'] != '', function ($query) use ($data) {
            return $query->where('userid', $data['follow_user_id']);
        })->with(['customer', 'user'])->paginate($rows);
        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => collect($res->items())->map(function ($item) {
                $item->follow_user = $item->user;
                $item->customer_tags = $item->customer->tags ?? [];
                return $item;
            })
        ]);
    }

    /**
     * 设置标签
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function tagsPut(Request $request)
    {
        $data = $request->all('userid', 'w_cust_id', 'w_tag_id');
        /** @var Users $user */
        $user = Auth::user();
        $customer = WorkCustomer::with('tags')->findOrFail($data['w_cust_id']);
        $customerTagIds = $customer->tags->pluck('w_tag_id');
        $removeTagIds = $customerTagIds->diff($data['w_tag_id'])->values()->toArray();
        $addTagIds = collect($data['w_tag_id'])->diff($customerTagIds)->values()->toArray();
        $customer->tags()->sync($data['w_tag_id']);

        $app = work_app($user->work->corpid, $user->work->corpsecret);
        $app->external_contact->markTags([
            "userid" => $data['userid'],
            "external_userid" => $customer->external_userid,
            "add_tag" => WorkTag::whereIn('w_tag_id', $addTagIds)->pluck('tag_id')->toArray(),
            "remove_tag" => WorkTag::whereIn('w_tag_id', $removeTagIds)->pluck('tag_id')->toArray()
        ]);
        return Response::json([
            'code' => 0,
            'msg' => '操作成功',
        ]);
    }
}
