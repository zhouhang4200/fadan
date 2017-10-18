<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRbacGroup extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_rbac_groups', 'rbac_group_id', 'user_id');
    }
}
