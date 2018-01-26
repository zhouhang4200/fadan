<?php
namespace App\Http\Controllers\Frontend\Steam\Excel;

class SteamAccountExport extends \Maatwebsite\Excel\Files\NewExcelFile
{
    public function getFilename()
    {
        return '封号记录';
    }

    // 导出
    public function export($data)
    {
        $exportData = [];
        foreach ($data as $key => $value) {

            $exportData[] = [
                'Tb_id'                   => $value->Tb_id,
                'Account'              => $value->Account,
                'GameName'        => $value->GameName,
                'SteamId'           => $value->SteamId,
                'LastUseTime'        => $value->LastUseTime,
                'InsertTime'             => $value->InsertTime,
                'Balance'                  => $value->Balance,
                'UsingState'               => $value->UsingState,
                'IsUsing'               => $value->IsUsing,
                'AuthType'              => $value->AuthType,
                'Supplier'               => $value->Supplier,
            ];
        }
        $result = $this->sheet('Sheet1', function ($sheet) use ($exportData) {
            $sheet->row(1, array(
                '序号',
                '封号账号',
                '封号游戏',
                'SteamId',
                '最后使用时间',
                '封号时间',
                '余额',
                '是否启用',
                '是否使用中',
                '账号验证类型',
                '供应商',
            ));
            $sheet->fromArray($exportData, null, 'A2', true, false);
        })->export('xlsx');
        return $result;
    }
}
