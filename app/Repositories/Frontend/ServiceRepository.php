<?php
namespace App\Repositories\Frontend;

use App\Models\Service;
use Auth;
use DB;
use Exception;
use Carbon\Carbon;
use App\Models\Goods;


class ServiceRepository
{
    /**
     * 可用的服务
     * @return mixed
     */
    public function available()
    {
        return Service::where('status', 1)->pluck('name', 'id');
    }
}
