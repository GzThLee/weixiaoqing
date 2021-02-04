<?php

namespace App\Models;

use App\Scopes\WorkScope;
use Illuminate\Database\Eloquent\Model;

class WorkTagGroup extends Model
{
    protected $primaryKey = 'w_tagg_id';

    protected $fillable = [
        'work_id', 'group_id', 'name', 'order', 'created_at'
    ];

    public function tags()
    {
        return $this->hasMany('App\Models\WorkTag','w_tagg_id','w_tagg_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new WorkScope());
    }
}
