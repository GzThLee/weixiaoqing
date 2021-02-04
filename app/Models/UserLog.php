<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    protected $primaryKey = 'log_id';

    protected $fillable = [
        'user_id', 'route_name', 'ip', 'user_agent', 'uri', 'parameter', 'method'
    ];
}
