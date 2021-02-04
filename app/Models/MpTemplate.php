<?php

namespace App\Models;

use App\Scopes\MpScope;
use Illuminate\Database\Eloquent\Model;

class MpTemplate extends Model
{
    protected $table = 'mp_templates';

    protected $primaryKey = 'm_temp_id';

    protected $fillable = [
        'mp_id', 'theme', 'push_obj', 'push_time', 'template_id', 'content', 'finish_time', 'send_count', 'send_success_count', 'send_fail_count', 'status',
    ];

    protected $attributes = [
        'content' => ''
    ];

    protected $casts = [
        'content' => 'object',
    ];

    public function mp()
    {
        return $this->belongsTo('App\Models\Mp', 'mp_id', 'mp_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new MpScope());
    }
}
