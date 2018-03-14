<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class QianShouUser
 * @package App
 */
class QianShouUser extends Model
{
    protected $connection = 'qianshou';
    protected $table = "thousand_client_members";
}
