<?php

namespace App\Models;

use App\Scopes\DeleteScope;
use App\Scopes\MpScope;
use App\Scopes\SuccessScope;
use Illuminate\Database\Eloquent\Model;

class MpKeyword extends Model
{
    protected $table = 'mp_keywords';

    protected $primaryKey = 'm_kw_id';

    protected $fillable = [
        'mp_id', 'keyword', 'm_tag_id', 'rule_type', 'content', 'status', 'media_type', 'trigger_count'
    ];

    const EXACT = 1;    //精准
    const FUZZY = 2;    //模糊

    protected $casts = [
        'content' => 'object',
    ];

    protected $attributes = [
        'content' => '',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new MpScope());
        static::addGlobalScope(new DeleteScope());
        static::addGlobalScope(new SuccessScope());
    }
}
