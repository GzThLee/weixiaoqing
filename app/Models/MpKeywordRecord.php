<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpKeywordRecord extends Model
{
    protected $table = 'mp_keyword_records';

    protected $primaryKey = 'm_kwr_id';

    protected $fillable = [
        'm_kw_id', 'm_fan_id'
    ];
}
