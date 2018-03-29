<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller
{
    //上传excel
    public function fileExcel(Request $request)
    {
        if ($request->hasFile('Filedata') and $request->file('Filedata')->isValid()) {
            $result = array();
            //文件类型
            $allow =array('xls','xlsx','csv');
            $mine = $request->file('Filedata')->getClientOriginalExtension();

            if (!in_array($mine, $allow)) {
                $result['status'] = 0;
                $result['info'] = '文件类型错误，只能上传Excel格式';
                return $result;
            }

            //文件大小判断
            $max_size = 1024 * 1024 * 20;
            $size = $request->file('Filedata')->getClientSize();
            if ($size > $max_size) {
                $result['status'] = 0;
                $result['info'] = '文件大小不能超过20M';
                return $result;
            }

            //上传文件夹，如果不存在，建立文件夹
            $path = getcwd() . '/resources/excel/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            //生成新文件名
            $extension = $request->file('Filedata')->getClientOriginalExtension();      //取得之前文件的扩展名

            $file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $extension;
            $request->file('Filedata')->move($path, $file_name);

            //返回新文件名
            $result['status'] = 1;
            $result['info'] = $file_name;
            return $result;
        }
    }
}
