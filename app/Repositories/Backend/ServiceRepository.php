<?php
namespace App\Repositories\Backend;

use App\Models\Service;

class ServiceRepository
{
    /**
     * 可用的服务类型
     * @return mixed
     */
    public function available()
    {
        return Service::where('status', 1)->pluck('name', 'id');
    }
}
