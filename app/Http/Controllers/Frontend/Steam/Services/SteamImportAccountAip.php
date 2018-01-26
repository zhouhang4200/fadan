<?php

namespace App\Http\Controllers\Frontend\Steam\Services;

use GuzzleHttp\Client;

/**
 * steam账号
 * @package App\Publics
 */
class SteamImportAccountAip extends HttpService
{
    private $apiUrl = [
        'importSteam' => 'http://121.41.23.236:18119/TraderSteamAccount/ImprotAccount?',
//        'importSteam' => 'http://121.41.23.236:18119/SteamAccount/ImprotAccount?',
        'getAccountList' => 'http://121.41.23.236:18119/TraderSteamAccount/getAccountList?',
//        'getAccountList' => 'http://121.41.23.236:18119/SteamAccount/getAccountList?',
        'getZhiChongList' => 'http://121.41.23.236:18119/TraderSteamAccount/getZhiChongList?',
        'updateZhichongState' => 'http://121.41.23.236:18119/TraderSteamAccount/UpdateZhichongState?',
        'updateBalance' => 'http://121.41.23.236:18119/TraderSteamAccount/UpdateBalance?',
        'updatePwd' => 'http://121.41.23.236:18119/TraderSteamAccount/UpdatePwd?',
        'getAccountForbiddenList' => 'http://121.41.23.236:18119/TraderSteamAccount/getAccountForbiddenList?',
        'updateAccount' => 'http://121.41.23.236:18119/TraderSteamAccount/updateAccount?',
        'show' => 'http://121.41.23.236:18119/TraderSteamAccount/SteamAccountUseTimeLog?',
        'list' => 'http://121.41.23.236:18119/TraderSteamAccount/SteamReqLog',
        'getGameTmpList' => 'http://121.41.23.236:18119/TraderSteamAccount/getGameTmpList',
        'insertGameTmp' => 'http://121.41.23.236:18119/TraderSteamAccount/insertGameTmp',
        'delGameTmp' => 'http://121.41.23.236:18119/TraderSteamAccount/DelGameTmp',
        'updateIsUsing' => 'http://121.41.23.236:18119/TraderSteamAccount/updateIsUsing',
        'updateAuthType' => 'http://121.41.23.236:18119/TraderSteamAccount/UpdateAuthType',

        'getGameNameList' => 'http://121.41.23.236:18119/TraderSteamAccount/getGameNameList',
        'insertGameName' => 'http://121.41.23.236:18119/TraderSteamAccount/InsertGameName',
    ];

    // 导入steam账户
    public function importSteamAccount($data)
    {
        return $this->request(json_encode($data), $this->apiUrl['importSteam']);
    }

    // 查询steam账户
    public function getAccountList($pageNo = 1, $pageSize = 15, $filter)
    {
        $client = new Client();
        $res = $client->request('POST', $this->apiUrl['getAccountList'], [
            'form_params' => [
                'pageNo' => $pageNo,
                'pageSize' => $pageSize,
                'filter' => json_encode($filter),
            ]
        ]);

        return json_decode($res->getBody()->getContents());
    }

    // 更新金额
    public function updateBalance($tbid, $balance, $username)
    {
        $client = new Client();

        $res = $client->request('POST', $this->apiUrl['updateBalance'], [
            'form_params' => [
                'tbid' => $tbid,
                'balance' => $balance,
                'username' => $username,
            ]
        ]);

        return json_decode($res->getBody()->getContents());
    }

    public function updateStatus($id, $priority, $status)
    {
        $client = new Client();

        $url = $this->apiUrl['updateAccount'] . "Tb_id=$id&UsingState=$status&Priority=$priority";

        $res = $client->request('GET', $url);

        return json_decode($res->getBody()->getContents());
    }

    public function show($pageNo, $pageSize, $filter)
    {
        $client = new Client();

        $res = $client->request('POST', $this->apiUrl['show'], [
            'form_params' => [
                'pageNo' => $pageNo,
                'pageSize' => $pageSize,
                'filter' => json_encode($filter),
            ]
        ]);

        return json_decode($res->getBody()->getContents());
    }

    public function listData($pageNo, $pageSize, $filter)
    {
        $client = new Client();

        $res = $client->request('POST', $this->apiUrl['list'], [
            'form_params' => [
                'pageNo' => $pageNo,
                'pageSize' => $pageSize,
                'filter' => json_encode($filter),
            ]
        ]);

        return json_decode($res->getBody()->getContents());
    }

    // 查询封号记录
    public function getAccountForbiddenList($pageNo = 1, $pageSize = 15, $filter)
    {
        $client = new Client();
        $res = $client->request('POST', $this->apiUrl['getAccountForbiddenList'], [
            'form_params' => [
                'pageNo' => $pageNo,
                'pageSize' => $pageSize,
                'filter' => json_encode($filter)
            ]
        ]);

        return json_decode($res->getBody()->getContents());
    }

    // 直充
    public function getZhiChongList($pageNo = 1, $pageSize = 15, $filter)
    {
        $client = new Client();
        $res = $client->request('POST', $this->apiUrl['getZhiChongList'], [
            'form_params' => [
                'pageNo' => $pageNo,
                'pageSize' => $pageSize,
                'filter' => json_encode($filter),
            ]
        ]);

        return json_decode($res->getBody()->getContents());
    }

    public function updateZhichongState($tbid, $steamCardId, $state, $cardstate)
    {
        $client = new Client();
        $res = $client->request('POST', $this->apiUrl['updateZhichongState'], [
            'form_params' => [
                'tbid' => $tbid,
                'steamCardId' => $steamCardId,
                'state' => $state,
                'cardstate' => $cardstate,
            ]
        ]);

        return json_decode($res->getBody()->getContents());
    }

    /**
     * 修改密码
     * @param $tbid
     * @param $pwd
     * @param $username
     * @return mixed
     */
    public function updatePwd($tbid, $pwd, $username)
    {
        $client = new Client();
        $res = $client->request('POST', $this->apiUrl['updatePwd'], [
            'form_params' => [
                'tbid' => $tbid,
                'pwd' => $pwd,
                'username' => $username
            ]
        ]);

        return json_decode($res->getBody()->getContents());
    }

    /**
     * 修改密码
     * @param $tbid
     * @param $pwd
     * @param $username
     * @return mixed
     */
    public function getGameTmpList()
    {
        $client = new Client();
        $res = $client->request('POST', $this->apiUrl['getGameTmpList'], [
            'form_params' => []
        ]);

        return json_decode($res->getBody()->getContents());
    }

    /**
     * 新增
     * @param $tbid
     * @param $pwd
     * @param $username
     * @return mixed
     */
    public function insertGameTmp($tmpGuid, $gameName, $gameUrl, $username)
    {
        $client = new Client();
        $res = $client->request('POST', $this->apiUrl['insertGameTmp'], [
            'form_params' => [
                'TmpGuid' => $tmpGuid,
                'GameName' => $gameName,
                'GameUrl' => $gameUrl,
                'username' => $username
            ]
        ]);

        return json_decode($res->getBody()->getContents());
    }

    /**
     * 删除
     * @param $tbid
     * @param $pwd
     * @param $username
     * @return mixed
     */
    public function delGameTmp($tmpGuid, $username)
    {
        $client = new Client();
        $res = $client->request('POST', $this->apiUrl['delGameTmp'], [
            'form_params' => [
                'TmpGuid' => $tmpGuid,
                'username' => $username
            ]
        ]);

        return json_decode($res->getBody()->getContents());
    }

    /**
     * 修改密码
     * @param $tbid
     * @param $pwd
     * @param $username
     * @return mixed
     */
    public function updateIsUsing($tbid, $isUsing, $username)
    {
        $client = new Client();
        $res = $client->request('POST', $this->apiUrl['updateIsUsing'], [
            'form_params' => [
                'tbid' => $tbid,
                'isUsing' => $isUsing,
                'username' => $username
            ]
        ]);

        return json_decode($res->getBody()->getContents());
    }

    /**
     * 修改authType
     * @param $tbid
     * @param $pwd
     * @param $username
     * @return mixed
     */
    public function updateAuthType($tbid, $authType, $username)
    {
        $client = new Client();
        $res = $client->request('POST', $this->apiUrl['updateAuthType'], [
            'form_params' => [
                'tbid' => $tbid,
                'authType' => $authType,
                'username' => $username
            ]
        ]);

        return json_decode($res->getBody()->getContents());
    }

    /**
     * Steam直充游戏名
     * @param $tbid
     * @param $pwd
     * @param $username
     * @return mixed
     */
    public function getGameNameList()
    {
        $client = new Client();
        $res = $client->request('POST', $this->apiUrl['getGameNameList'], [
            'form_params' => []
        ]);

        return json_decode($res->getBody()->getContents());
    }

    /**
     * Steam直充游戏名
     * @param $tbid
     * @param $pwd
     * @param $username
     * @return mixed
     */
    public function insertGameName($gameName, $username)
    {
        $client = new Client();
        $res = $client->request('POST', $this->apiUrl['insertGameName'], [
            'form_params' => [
                'GameName' => $gameName,
                'username' => $username
            ]
        ]);

        return json_decode($res->getBody()->getContents());
    }

}
