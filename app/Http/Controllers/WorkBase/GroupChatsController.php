<?php

namespace App\Http\Controllers\WorkBase;

use App\Http\Controllers\Controller;
use App\Models\WorkGroupchat;
use App\Models\WorkGroupchatCustomer;
use App\Models\WorkUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class GroupChatsController extends Controller
{
    /**
     * 视图
     * @return \Illuminate\Contracts\View\View
     */
    public function indexGet()
    {
        $follow_users = WorkUser::select('userid', 'name')->get();
        return View::make('work_base.groupchats.index', compact('follow_users'));
    }

    /**
     * 数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataGet(Request $request)
    {
        $rows = $request->input('limit', 30);
        $data = $request->all(['follow_user_id', 'customer_name']);
        $res = WorkGroupchat::when($data['follow_user_id'] != '', function ($query) use ($data) {
            return $query->where('owner', $data['follow_user_id']);
        })->when($data['customer_name'] != '', function ($query) use ($data) {
            return $query->whereHas('customers', function (Builder $query) use ($data) {
                return $query->where('name', 'LIKE', '%' . $data['customer_name'] . '%');
            });
        })->with(['owner', 'customers'])->paginate($rows);
        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => collect($res->items())->map(function ($item) {
                $item->customer_num = $item->customers->count();
                return $item;
            })
        ]);
    }

    /**
     * 群客户视图
     * @return \Illuminate\Contracts\View\View
     */
    public function customersGet()
    {
        return View::make('work_base.groupchats.customers');
    }

    /**
     * 群客户数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customersDataGet(Request $request)
    {
        $workGroupChatId = $request->input('w_gchat_id', 0);
        $rows = $request->input('limit', 30);
        $res = WorkGroupchatCustomer::where('w_gchat_id', $workGroupChatId)->with(['customer', 'user'])->paginate($rows);
        return Response::json([
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items()
        ]);
    }
}
