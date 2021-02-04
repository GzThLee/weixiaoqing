<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $primaryKey = 'config_id';

    protected $fillable = [
        'key_name', 'display_name', 'value'
    ];
}
