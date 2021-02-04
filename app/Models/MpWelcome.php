<?php

namespace App\Models;

use App\Scopes\DeleteScope;
use App\Scopes\MpScope;
use App\Scopes\SuccessScope;
use Illuminate\Database\Eloquent\Model;

class MpWelcome extends Model
{
    protected $table = 'mp_welcomes';

    protected $primaryKey = 'mp_id';

    protected $fillable = [
        'mp_id', 'content', 'media_type', 'status'
    ];

    protected $casts = [
        'content' => 'object',
    ];

    protected $attributes = [
        'content' => '',
    ];

    public function mp()
    {
        return $this->belongsTo('App\Models\Mp', 'mp_id', 'mp_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new MpScope());
        static::addGlobalScope(new DeleteScope());
        static::addGlobalScope(new SuccessScope());
    }
}
