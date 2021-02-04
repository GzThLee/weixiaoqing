<?php

namespace App\Models;

use App\Scopes\DeleteScope;
use App\Scopes\MpScope;
use Illuminate\Database\Eloquent\Model;

class MpChannelCode extends Model
{
    protected $table = 'mp_channel_codes';

    protected $primaryKey = 'm_ccode_id';

    protected $fillable = [
        'mp_id', 'ticket', 'scene_str', 'name', 'm_tag_id', 'status', 'url', 'ticket_url', 'scan_count', 'content', 'media_type', 'new_fans_subscribe', 'new_fans_unsubscribe', 'old_fans_subscribe', 'old_fans_unsubscribe'
    ];

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
    }
}
