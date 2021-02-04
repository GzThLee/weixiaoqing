<?php

namespace App\Models;

use App\Scopes\DeleteScope;
use App\Scopes\MpScope;
use Illuminate\Database\Eloquent\Model;

class MpMenu extends Model
{
    protected $table = 'mp_menus';

    protected $primaryKey = 'm_menu_id';

    protected $fillable = [
        'mp_id', 'menu_type', 'pindex', 'index', 'sort', 'name', 'content', 'status'
    ];

    protected $casts = [
        'content' => 'object',
    ];

    protected $attributes = [
        'content' => '',
    ];

    const MAIN_MENU = 1;
    const LINK_MENU = 2;
    const KEYWORD_MENU = 3;
    const EVENT_MENU = 4;
    const MINI_APP_MENU = 5;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new MpScope());
        static::addGlobalScope(new DeleteScope());
    }
}
