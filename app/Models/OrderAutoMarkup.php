<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAutoMarkup extends Model
{
    protected $fillable = [
    	'user_id', 'markup_amount', 'markup_time', 'markup_type', 'markup_money', 'markup_frequency', 'markup_number'
    ];
}
