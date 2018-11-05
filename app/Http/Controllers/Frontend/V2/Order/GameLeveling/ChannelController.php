<?php

namespace App\Http\Controllers\Frontend\V2\Order\GameLeveling;

use DB;
use Auth;
use Exception;
use Carbon\Carbon;
use App\Models\TaobaoTrade;
use App\Models\OrderHistory;
use App\Models\OrderBasicData;
use App\Models\GameLevelingOrder;
use App\Models\GameLevelingOrderLog;
use App\Http\Controllers\Controller;
use App\Models\GameLevelingPlatform;
use App\Models\GameLevelingOrderDetail;
use App\Services\OrderOperateController;
use App\Exceptions\GameLevelingOrderOperateException;

/**
 * 游戏代练渠道订单控制器
 * Class GameLevelingController
 * @package App\Http\Controllers\Frontend\V2\Order
 */
class ChannelController extends Controller
{

    public function index()
    {

    }

    public function dataList()
    {

    }
}