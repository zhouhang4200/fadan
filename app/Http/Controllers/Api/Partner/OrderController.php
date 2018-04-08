<?php
namespace App\Http\Controllers\Api\Partner;

use Order, Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class OrderController
 * @package App\Http\Controllers\Api\Partner
 */
class OrderController extends Controller
{
    /**
     * 查询订单信息
     * @param Request $request
     */
    public function query(Request $request)
    {
    }

    /**接单
     * @param Request $request
     */
    public function receive(Request $request)
    {
    }

    /**
     * 申请验收
     * @param Request $request
     */
    public function applyComplete(Request $request)
    {
    }

    /**
     * 取消验收
     * @param Request $request
     */
    public function cancelComplete(Request $request)
    {
    }

    /**
     * 撤销
     * @param Request $request
     */
    public function revoke(Request $request)
    {
    }

    /**
     * 取消撤销
     * @param Request $request
     */
    public function cancelRevoke(Request $request)
    {
    }

    /**
     * 不同意撤销
     * @param Request $request
     */
    public function refuseRevoke(Request $request)
    {
    }

    /**
     * 同意撤销
     * @param Request $request
     */
    public function agreeRevoke(Request $request)
    {
    }

    /**
     * 强制撤销
     * @param Request $request
     */
    public function forceRevoke(Request $request)
    {
    }

    /**
     * 申请仲裁
     * @param Request $request
     */
    public function applyArbitration(Request $request)
    {
    }

    /**
     * 取消仲裁
     * @param Request $request
     */
    public function cancelArbitration(Request $request)
    {
    }

    /**
     * 强制仲裁
     * @param Request $request
     */
    public function forceArbitration(Request $request)
    {
    }

    /**
     * 异常
     * @param Request $request
     */
    public function abnormal(Request $request)
    {
    }

    /**
     * 取消异常
     * @param Request $request
     */
    public function cancelAbnormal(Request $request)
    {
    }
}
