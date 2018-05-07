<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessmanComplaint extends Model
{
    public $fillable = [
      'complaint_primary_user_id',
      'be_complaint_primary_user_id',
      'order_no',
      'amount',
      'remark',
    ];
}
