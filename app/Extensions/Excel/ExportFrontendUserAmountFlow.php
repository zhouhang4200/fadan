<?php
namespace App\Extensions\Excel;

class ExportFrontendUserAmountFlow extends \Maatwebsite\Excel\Files\NewExcelFile
{
    public function getFilename()
    {
        return '资金流水';
    }

    // 导出
    public function export($data)
    {
        $tradeType = config('tradetype.user');
        $exportData = [];

        foreach ($data as $key => $value) {
            $exportData[] = [
                'id'         => $value['id'],
                'trade_no'   => $value['trade_no'],
                'trade_type' => $tradeType[$value['trade_type']],
                'fee'        => $value['fee'] + 0,
                'remark'     => $value['remark'],
                'created_at' => $value['created_at'],
            ];
        }

        $result = $this->sheet('Sheet1', function ($sheet) use ($exportData) {
            $sheet->row(1, array(
                '流水号',
                '相关单号',
                '天猫单号',
                '类型',
                '金额',
                '说明',
                '时间',
            ));
            $sheet->fromArray($exportData, null, 'A2', true, false);
        })->export('xlsx');

        return $result;
    }
}
