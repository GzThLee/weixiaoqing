<?php

namespace App\Models;

use App\Scopes\DeleteScope;
use App\Scopes\MpScope;
use Illuminate\Database\Eloquent\Model;

class MpTag extends Model
{
    protected $table = 'mp_tags';

    protected $primaryKey = 'm_tag_id';

    protected $fillable = [
        'mp_id', 'name', 'description', 'status', 'tag_id',
    ];

    protected $attributes = [
        'description' => '',
    ];

    public function fans()
    {
        return $this->belongsToMany('App\Models\MpFan', 'mp_fans_tags','m_tag_id','m_fan_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new MpScope());
        static::addGlobalScope(new DeleteScope());
    }
}
