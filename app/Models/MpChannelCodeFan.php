<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpChannelCodeFan extends Model
{
    protected $table = 'mp_channel_code_fans';

    protected $primaryKey = 'm_ccode_fan_id';

    protected $fillable = [
        'm_fan_id', 'm_ccode_id', 'scan_type'
    ];
}
