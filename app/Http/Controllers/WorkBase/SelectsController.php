<?php

namespace App\Http\Controllers\WorkBase;

use App\Http\Controllers\Controller;
use App\Models\WorkTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SelectsController extends Controller
{
    /**
     * 标签选择
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function TagsGet(Request $request)
    {
        $data = $request->all('w_tagg_id');
        return Response::json([
            'code' => 0,
            'msg' => '操作成功',
            'data' => [
                'list' => WorkTag::where('w_tagg_id', $data['w_tagg_id'])->get()
            ]
        ]);
    }
}
