<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkTag extends Model
{
    protected $primaryKey = 'w_tag_id';

    protected $fillable = [
        'w_tagg_id', 'tag_id', 'name', 'order', 'created_at'
    ];
}
