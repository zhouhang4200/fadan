<?php

namespace App\Http\Controllers\Frontend\Steam;

use App\Http\Controllers\Frontend\Steam\Excel\SteamAccountExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\Steam\Services\SteamImportAccountAip;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Frontend\Steam\Custom\Helper;
use Illuminate\Http\Request;

class BatchCardController extends Controller
{

    //导入steam账户
    public function importCard(Request $request)
    {
        $arr = [];
        $fileExcel = json_decode($request->data)->fileExcel;
        $excelUrl = public_path() . '/resources/excel/' . $fileExcel;
        if (!file_exists($excelUrl)) {
            $arr = ['status' => 0, 'message' => '找不到文件'];
            return $arr;
        };
        Excel::selectSheetsByIndex(0)->load($excelUrl, function ($reader) use (&$arr) {
            $readers = $reader->all();
            unset($readers[0]); //去除表头

            if ($readers->isEmpty()) { //判断表格数据是否为空
                $arr = ['status' => 0, 'message' => '表格无数据'];
                return $arr;
            }

            foreach ($readers as $key => $value) {

                if (trim($value[0]) == null && trim($value[1]) == null && trim($value[2]) == null && trim($value[3]) == null && trim($value[4]) == null) {
                    unset($readers[$key]); //去除为null的数据
                    continue;
                }
                //判断表格数据不能为空
                if (trim($value[0]) == null || trim($value[1]) == null || trim($value[2]) == null || trim($value[3]) == null || trim($value[4]) == null) {
                    $arr = ['status' => 0, 'message' => '表格数据不能为空,请仔细检查第' . ($key + 1) . '条后再次上传'];
                    return $arr;
                }
                //判断金额必须是数字
                if (!is_numeric(trim($value[2]))) {
                    $arr = ['status' => 0, 'message' => '金额必须是数字,请仔细检查第' . ($key + 1) . '条后再次上传'];
                    return $arr;
                }
                //SteamId必须是17位字符串
                if (strlen(trim($value[4])) != 17) {
                    $arr = ['status' => 0, 'message' => 'SteamId必须是17位字符串,请仔细检查第' . ($key + 1) . '条后再次上传'];
                    return $arr;
                }
            }

            try {
                foreach ($readers as $key => $value) {
                    $sendToApiData[] = [
                        'Account' => trim($value[0]),
                        'Pwd' => trim($value[1]),
                        'Balance' => trim($value[2]),
                        'Supplier' => trim($value[3]),
                        'SteamId' => trim($value[4]),
                        'TraderId' => Auth::user()->id,
                    ];
                }
                //调用导入账户接口
                $steamImportAccountAip = new SteamImportAccountAip();
                $result = $steamImportAccountAip->importSteamAccount($sendToApiData);

                if ($result['Code'] != 1) {
                    $arr = ['status' => 0, 'message' => $result['Message']];
                    return $arr;
                }
                $arr = ['status' => 1, 'message' => $result['Message']];
                return $arr;

            } catch (\Exception $e) {
                Helper::log('steam-excel-error', ['错误信息' => $e->getMessage()]);
                $arr = ['status' => 0, 'message' => $e->getMessage()];
                return $arr;
            }

        }, 'UTF-8');

        return $arr;

    }

    //获取接口数据列表
    public function getAccountList(Request $request)
    {
        $page = $request->input('page', 1);
        $pageSize = 50;

        //多条件查找
        $data = array();
        if ($request->has('Account') || $request->Account == '') {
            $data['Account'] = trim($request->Account) == '' ? '' : trim($request->Account);
        }

        if ($request->has('Supplier') || $request->Supplier == '') {
            $data['Supplier'] = trim($request->Supplier) == '' ? '' : trim($request->Supplier);
        }

        if ($request->has('IsInlimit')) {
            if ($request->IsInlimit == '-1') {
                $data['IsInlimit'] = '';
            } else {
                $data['IsInlimit'] = $request->IsInlimit;
            }
        } else {
            $data['IsInlimit'] = '';
        }
        if ($request->has('Priority')) {
            if ($request->Priority == '-1') {
                $data['Priority'] = '';
            } else {
                $data['Priority'] = $request->Priority;
            }
        } else {
            $data['Priority'] = '';
        }
        if ($request->has('IsUsing')) {
            if ($request->IsUsing == '-1') {
                $data['IsUsing'] = '';
            } else {
                $data['IsUsing'] = $request->IsUsing;
            }
        } else {
            $data['IsUsing'] = '';
        }
        if ($request->has('AuthType')) {
            if ($request->AuthType == '-1') {
                $data['AuthType'] = '';
            } else {
                $data['AuthType'] = $request->AuthType;
            }
        } else {
            $data['AuthType'] = '';
        }

        if ($request->has('IsUsed')) {
            if ($request->IsUsed == '-1') {
                $data['IsUsed'] = '';
            } else {
                $data['IsUsed'] = $request->IsUsed;
            }
        } else {
            $data['IsUsed'] = '';
        }

        if ($request->has('UsingState')) {
            if ($request->UsingState == '-1') {
                $data['UsingState'] = '';
            } else {
                $data['UsingState'] = $request->UsingState;
            }
        } else {
            $data['UsingState'] = '';
        }
        if(Auth::guard('web')->check()){
            $data['TraderId'] =Auth::user()->id;
        };
        $steamImportAccountAip = new SteamImportAccountAip();
        $result = $steamImportAccountAip->getAccountList($page, $pageSize, $data);
        $datArr = collect($result->data)->toArray();

        $totalPage = $datArr ? ceil((int)$result->count / $pageSize) : 0;
        $pageDate = $datArr;
        return view('frontend.steambatchcard.batch')->with([
            'totalPage' => $totalPage,
            'dataList' => $pageDate,
            'pageSize' => $pageSize,
            'count' => $result->count,
        ]);
    }

    public function updateStatus(Request $request)
    {
        $id = json_encode([$request->id]);
        $priority = $request->priority;
        $status = $request->status;

        // 0，未启用 1，启用， 2，禁用
        $steamImportAccountAip = new SteamImportAccountAip();

        $result = $steamImportAccountAip->updateStatus($id, $priority, $status);

        return response()->ajax(1, $result->Message);
    }

    //批量启用
    public function all(Request $request)
    {
        $checked_id = $request->input('tb_id');
        $priority = 0;
        $status = 1;
        $steamImportAccountAip = new SteamImportAccountAip();
        $result = $steamImportAccountAip->updateStatus(json_encode($checked_id), $priority, $status);
        return response()->ajax(1, $result->Message);
    }

    // 获取列表
    // 获取列表
    public function show(Request $request)
    {
        $page = $request->page ?: 1;
        $pageSize = 50;
        $data = array();
        if ($request->has('Account') || $request->Account == '') {
            $data['Account'] = trim($request->Account) == '' ? '' : trim($request->Account);
        }
        $data['TraderId'] =Auth::user()->id;
        $steamImportAccountAip = new SteamImportAccountAip();
        $result = $steamImportAccountAip->show($page, $pageSize, $data);
        $data = $result->data;
        $totalPage = $data ? ceil((int)$result->count / $pageSize) : 0;
        return view('frontend.steambatchcard.show')->with([
            'data' => $data,
            'totalPage' => $totalPage,
            'pageSize' => $pageSize,
            'count' => $result->count
        ]);
    }

    //取号记录
    public function listData(Request $request)
    {
        $page = $request->page ?: 1;
        $pageSize = 50;

        //多条件查找
        $data = array();
        if ($request->has('Account') || $request->Account == '') {
            $data['Account'] = trim($request->Account) == '' ? '' : trim($request->Account);
        }

        if ($request->has('Orderid') || $request->Orderid == '') {
            $data['Orderid'] = trim($request->Orderid) == '' ? '' : trim($request->Orderid);
        }

        if ($request->has('IsBack')) {
            if ($request->IsBack == '-1') {
                $data['IsBack'] = '';
            } else {
                $data['IsBack'] = $request->IsBack;
            }
        } else {
            $data['IsBack'] = '';
        }
        $data['TraderId'] =Auth::user()->id;
        $steamImportAccountAip = new SteamImportAccountAip();
        $result = $steamImportAccountAip->listData($page, $pageSize, $data);
        $data = $result->data;
        $totalPage = $data ? ceil((int)$result->count / $pageSize) : 0;
        return view('frontend.steambatchcard.list')->with([
            'data' => $data,
            'totalPage' => $totalPage,
            'pageSize' => $pageSize,
            'count' => $result->count
        ]);
    }

    //更新金额
    public function balance(Request $request)
    {
        $id = $request->id;
        $balance = $request->balance;
        $username = Auth::user()->name;
        Helper::log('steam-update-balance', ['id' => $id, '金额' => $balance, '谁更改的' => $username]);
        $steamImportAccountAip = new SteamImportAccountAip();
        $result = $steamImportAccountAip->updateBalance($id, $balance, $username);
        return response()->ajax(1, $result->Message);
    }

    //封号记录
    public function seal(Request $request ,SteamAccountExport $excel)
    {

        $page = $request->input('page', 1);
        $pageSize = 50;

        //多条件查找
        $data = array();
        if ($request->has('Account') || $request->Account == '') {
            $data['Account'] = trim($request->Account) == '' ? '' : trim($request->Account);
        }

        if ($request->has('Supplier') || $request->Supplier == '') {
            $data['Supplier'] = trim($request->Supplier) == '' ? '' : trim($request->Supplier);
        }

        if ($request->has('GameName') || $request->GameName == '') {
            $data['GameName'] = trim($request->GameName) == '' ? '' : trim($request->GameName);
        }
        $data['TraderId'] =Auth::user()->id;
        $steamImportAccountAip = new SteamImportAccountAip();
        if($request->has('export')){
            $result = $steamImportAccountAip->getAccountForbiddenList($page, 20000, $data);
            $data = $result->data;
            $excel->export($data);
        }else{
            $result = $steamImportAccountAip->getAccountForbiddenList($page, $pageSize, $data);
            $data = $result->data;
            $totalPage = $data ? ceil((int)$result->count / $pageSize) : 0;
        }


        return view('frontend.steambatchcard.seal')->with([
            'data' => $data,
            'totalPage' => $totalPage,
            'pageSize' => $pageSize,
            'count' => $result->count
        ]);
    }

    /**
     * 直充
     * @param Request $request
     */
    public function getZhiChongList(Request $request)
    {

        $page = $request->input('page', 1);
        $pageSize = 50;

        //多条件查找
        $data = array();
        if ($request->has('SteamCardId') || $request->SteamCardId == '') {
            $data['SteamCardId'] = trim($request->SteamCardId) == '' ? '' : trim($request->SteamCardId);
        }

        if ($request->has('OrderId') || $request->OrderId == '') {
            $data['OrderId'] = trim($request->OrderId) == '' ? '' : trim($request->OrderId);
        }

        if ($request->has('Jsitid') || $request->Jsitid == '') {
            $data['Jsitid'] = trim($request->Jsitid) == '' ? '' : trim($request->Jsitid);
        }

        if ($request->has('ProductName') || $request->ProductName == '') {
            $data['ProductName'] = trim($request->ProductName) == '' ? '' : trim($request->ProductName);
        }

        if ($request->has('UseState')) {
            if ($request->UseState == '-1') {
                $data['UseState'] = '';
            } else {
                $data['UseState'] = $request->UseState;
            }
        } else {
            $data['UseState'] = '';
        }

        $steamImportAccountAip = new SteamImportAccountAip();
        $result = $steamImportAccountAip->getZhiChongList($page, $pageSize, $data);
        $data = $result->data;
        $totalPage = $data ? ceil((int)$result->count / $pageSize) : 0;
        return view('frontend.steambatchcard.zclist')->with([
            'data' => $data,
            'totalPage' => $totalPage,
            'pageSize' => $pageSize,
            'count' => $result->count
        ]);

    }

    /**
     * 改直充状态
     * @param Request $request
     * @return string
     */
    public function updateZhichongState(Request $request)
    {
        $id = $request->input('id');
        $steamCardId = $request->input('steamCardId');
        $value = $request->input('value');
        $steamImportAccountAip = new SteamImportAccountAip();
        $result = $steamImportAccountAip->updateZhichongState($id, $steamCardId, 2, $value);
        return response()->ajax(1, $result->Message);
    }

    /**
     * 修改密码
     * @param Request $request
     * @return string
     */
    public function updatePwd(Request $request)
    {
        $id = json_decode($request->data)->id;
        $pwd = json_decode($request->data)->password;
        $username = Auth::user()->name;
        Helper::log('steam-update-pwd', ['id' => $id, '密码' => $pwd, '谁更改的' => $username]);
        $steamImportAccountAip = new SteamImportAccountAip();
        $result = $steamImportAccountAip->updatePwd($id, trim($pwd), $username);
        return response()->ajax(1, $result->Message);
    }

    /**
     * 游戏模板
     * @return $this
     */
    public function game()
    {

        $steamImportAccountAip = new SteamImportAccountAip();
        $result = $steamImportAccountAip->getGameTmpList();
        if (isset($result->Data) && $result->Data == null) {
            $data = null;
        } else {
            $data = isset($result->data) ? $result->data : null;
        }
        return view('frontend.steambatchcard.game')->with([
            'data' => $data,
        ]);

    }

    /**
     * 新增游戏
     * @return $this
     */
    public function addGame(Request $request)
    {
        $gameUrl = json_decode($request->data)->gameUrl;
        $gameName = json_decode($request->data)->gameName;
        $tmpGuid = json_decode($request->data)->key;
        $username = Auth::user()->name;
        $steamImportAccountAip = new SteamImportAccountAip();
        $result = $steamImportAccountAip->insertGameTmp($tmpGuid, $gameName, $gameUrl, $username);
        if ($result->Code != 1) {
            return response()->ajax(0, $result->Message);
        }
        return response()->ajax(1, $result->Message);

    }

    /**
     * 删除游戏
     * @param Request $request
     * @return string
     */
    public function delGameTmp(Request $request)
    {
        $tmpGuid = $request->input('id');
        $username = Auth::user()->name;
        $steamImportAccountAip = new SteamImportAccountAip();
        $result = $steamImportAccountAip->delGameTmp($tmpGuid, $username);
        return response()->ajax(1, $result->Message);
    }

    /**
     * 修改使用状态
     * @param Request $request
     * @return string
     */
    public function updateIsUsing(Request $request)
    {
        $id = $request->id;
        $isUsing = $request->isUsing;
        $username = Auth::user()->name;
        Helper::log('steam-update-isUsing', ['id' => $id, '状态' => $isUsing, '谁更改的' => $username]);
        $steamImportAccountAip = new SteamImportAccountAip();
        $result = $steamImportAccountAip->updateIsUsing($id, $isUsing, $username);
        return response()->ajax(1, $result->Message);
    }

    /**
     * 修改使用状态
     * @param Request $request
     * @return string
     */
    public function updateAuthType(Request $request)
    {
        $id = $request->id;
        $authType = $request->authType;
        $username = Auth::user()->name;
        Helper::log('steam-update-authType', ['id' => $id, '状态' => $authType, '谁更改的' => $username]);
        $steamImportAccountAip = new SteamImportAccountAip();
        $result = $steamImportAccountAip->updateAuthType($id, $authType, $username);
        return response()->ajax(1, $result->Message);

    }



}
