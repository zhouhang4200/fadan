<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAttachment extends Model
{
    public $fillable = [
        'order_no',
        'channel_name',
        'third_order_no',
        'file_name',
        'third_file_name',
        'third_file_url',
        'size',
        'mime_type',
        'md5',
        'description',
        'url'
    ];
}
