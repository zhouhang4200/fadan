<?php
namespace App\Extensions\Excel;

class SteamAccountBalanceExport extends \Maatwebsite\Excel\Files\NewExcelFile
{
    public function getFilename()
    {
        return '账号列表余额';
    }

    // 导出
    public function export($data)
    {
        $exportData = [];
        foreach ($data as $key => $value) {

            $exportData[] = [
                'Account'                   => $value->Account,
                'Balance'              => $value->Balance,
            ];
        }
        $result = $this->sheet('Sheet1', function ($sheet) use ($exportData) {
            $sheet->row(1, array(
                '账号',
                '余额',
            ));
            $sheet->fromArray($exportData, null, 'A2', true, false);
        })->export('xlsx');
        return $result;
    }
}
