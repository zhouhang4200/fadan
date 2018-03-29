<?php

namespace App\Http\Controllers\Backend\Data;

use Excel;
use Carbon\Carbon;
use App\Models\DayData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DayDataController extends Controller
{
	/**
	 * 昨日数据统计-成功交易额
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function index(Request $request)
    {
    	$date = $request->date;

    	$fullUrl = $request->fullUrl();
        // 导出
        if ($request->export) {
            return $this->export($date);
        }
        // 筛选
    	$dayDatas = DayData::filter($date)->latest('created_at')->paginate(config('backend.page'));

    	return view('backend.data.index', compact('dayDatas', 'fullUrl', 'date'));
    }

     /**
     * 每日数据列表导出.多分页导出
     * @param  [type] $filters [description]
     * @return [type]          [description]
     */
    public function export($date = null)
    {
    	if ($date) {
    		$date = $date . ' 00:00:00';
        	$dayDatas = DayData::filter($date)->latest('created_at')->get();
    	} else {
    		$dayDatas = DayData::latest('created_at')->get();
    	}

        if ($dayDatas->count() < 1) {
        	return back()->with('miss', '数据为空,不能导出!');
        } else {   	
	        // 标题
	        $title = [
	            '序号',
	            '数据日期',
	            '库存托管',
	            '库存交易',
	            '转单市场',
	            '慢充',
	            '订单集市',        
	            '创建时间',
	            '更新时间',
	        ];
	        // 数组分割,反转
	        $chunkDatas = array_chunk(array_reverse($dayDatas->toArray()), 500);
	        // 导出
	        Excel::create(iconv('UTF-8', 'gbk', '每日数据'), function ($excel) use ($chunkDatas, $title) {

	            foreach ($chunkDatas as $chunkData) {
	                // 内容
	                $datas = [];
	                foreach ($chunkData as $key => $data) {
	                    $datas[] = [
	                        $data['id'],
	                        $data['date'],
	                        $data['stock_trusteeship'],
	                        $data['stock_transaction'],
	                        $data['transfer_transaction'],
	                        $data['slow_recharge'],
	                        $data['order_market'],
	                        $data['created_at'],
	                        $data['updated_at'],
	                    ];
	                }
	                // 将标题加入到数组
	                array_unshift($datas, $title);
	                // 每页多少数据
	                $excel->sheet("页数", function ($sheet) use ($datas) {
	                    $sheet->rows($datas);             
	                });
	            }
	        })->export('xls');
        }
    }
}
