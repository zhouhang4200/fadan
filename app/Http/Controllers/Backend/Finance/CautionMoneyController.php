<?php
namespace App\Http\Controllers\Backend\Finance;

use App\Exceptions\CustomException;
use App\Extensions\Asset\Refund;
use Asset, DB, Auth;
use App\Exceptions\AssetException;
use App\Extensions\Asset\Income;
use App\Models\CautionMoney;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 商户保证金
 * Class CautionMoneyController
 * @package App\Http\Controllers\Backend\Businessman
 */
class CautionMoneyController extends Controller
{
    /**
     * 保证金列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $userId = $request->user_id;

        $cautionMoneys = CautionMoney::filter(compact('userId'))->paginate(30);

        return view('backend.finance.caution-money.index', compact('cautionMoneys', 'userId'));
    }

    /**
     * 退款
     * @param Request $request
     */
    public function refund(Request $request)
    {
        DB::beginTransaction();
        try {
            $cautionMoney = CautionMoney::where('id', $request->id)->where('status', 3)->first();

            if ($cautionMoney) {
                Asset::handle(new Refund($cautionMoney->amount, 3, $cautionMoney->no, config('cautionmoney.type')[$cautionMoney->type], $cautionMoney->user_id, Auth::user()->id));
                $cautionMoney->status = 2;
                $cautionMoney->save();
            } else {
                return response()->ajax(0, '单据不可操作');
            }
        } catch (AssetException $assetException) {
            DB::rollback();
            return response()->ajax(0, $assetException->getMessage());
        } catch (CustomException $customException) {
            DB::rollback();
            return response()->ajax(0, $customException->getMessage());
        }
        DB::commit();
        return response()->ajax(1, '退款成功');
    }

    /**
     * 扣除保证金
     * @param Request $request
     * @return mixed
     */
    public function deduction(Request $request)
    {
        DB::beginTransaction();
        try {
            $cautionMoney = CautionMoney::where('id', $request->id)->where('status', 1)->first();

            if ($cautionMoney) {
                $cautionMoney->status = 3;
                $cautionMoney->save();
            } else {
                return response()->ajax(0, '单据不可操作');
            }
        } catch (AssetException $assetException) {
            DB::rollback();
            return response()->ajax(0, $assetException->getMessage());
        } catch (CustomException $customException) {
            DB::rollback();
            return response()->ajax(0, $customException->getMessage());
        }
        DB::commit();
        return response()->ajax(1, '退款成功');
    }
}
