<?php

namespace App\Scopes;

use App\Models\Users;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MpScope implements Scope
{
    /**
     * 把约束加到 Eloquent 查询构造中。
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();
        if ($user && $user instanceof Users) {
            $builder->where('mp_id', $user->mp->mp_id);
        }
    }
}
