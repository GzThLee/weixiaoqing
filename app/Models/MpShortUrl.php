<?php

namespace App\Models;

use App\Scopes\DeleteScope;
use App\Scopes\MpScope;
use Illuminate\Database\Eloquent\Model;

class MpShortUrl extends Model
{
    protected $table = 'mp_short_urls';

    protected $primaryKey = 'm_short_url_id';

    protected $fillable = [
        'name', 'mp_id', 'long_url', 'short_url', 'status'
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new MpScope());
        static::addGlobalScope(new DeleteScope());
    }
}
