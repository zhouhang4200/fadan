<?php
namespace App\Http\Controllers\Frontend\Steam\Excel;

use Maatwebsite\Excel\Facades\Excel;

class CdkeyLlibrarytExport
{

    // 导出
    public function export($data,$cdkey)
    {
        $exportData = [];
        foreach ($data as $key => $value) {
            $exportData[] = [
                'cdk'                   => $value->cdk,
                '添加时间'                   => $value->created_at,
                '到期时间'                   => $value->effective_time,
                '状态'                   => config('frontend.cdkeyLibraries_status')[$value->status],
                '商户号'                   => $value->user_id
            ];
        }
        Excel::create($cdkey->goodses->name.'-'.$cdkey->number.'张', function($excel) use($exportData) {

            $excel->sheet('Sheet1', function($sheet) use($exportData) {
                $sheet->row(1, array(
                    '序号',
                    'CDK',
                ));
                $sheet->fromArray($exportData);
            });

        })->export('xls');

    }
}
