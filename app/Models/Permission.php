<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    public function __construct()
    {
    	parent::__construct();
    }

    public function rbacGroups()
    {
    	return $this->belongsToMany(RbacGroup::class, 'rbac_group_permissions', 'permission_id', 'rbac_group_id');
    }
}
