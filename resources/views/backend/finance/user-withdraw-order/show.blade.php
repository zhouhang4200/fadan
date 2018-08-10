<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>窗口</title>
    <link type="image/x-icon" href="/favicon.ico" rel="shortcut icon"/>
    <link rel="stylesheet" type="text/css" href="/vendor/layui/css/layui.css">
    <link rel="stylesheet" type="text/css" href="/backend/css/layui-rewrit.css">
    <style type="text/css">
        table tr th {font-weight: bold !important;}
    </style>
</head>
<body class="pace-done theme-whbl" style="padding: 20px;">

    <table class="layui-table" lay-size="sm">
        <tr>
            <th>提现单号</th>
            <th>主账号ID</th>
            <th>原千手ID</th>
            <th>当前余额</th>
            <th>当前冻结</th>
        </tr>
        <tr>
            <td>{{ $data->no }}</td>
            <td>{{ $data->creator_primary_user_id }}</td>
            <td>{{ $data->user->nickname ?? '' }}</td>
            <td>{{ $data->user->asset->balance ?? '' }}</td>
            <td>{{ $data->user->asset->frozen ?? '' }}</td>
        </tr>
        <tr><th colspan="99"></th></tr>
        <tr>
            <th>姓名</th>
            <th>开户行</th>
            <th>卡号</th>
            <th>提现金额</th>
            <th>类型</th>
        </tr>
        <tr>
            <td>{{ $data->account_name ?: ($data->user->realNameIdent->name ?? '') }}</td>
            <td>{{ $data->bank_name ?: ($data->user->realNameIdent->bank_name ?? '') }}</td>
            <td>{{ $data->bank_card ?: ($data->user->realNameIdent->bank_number ?? '') }}</td>
            <td>{{ $data->fee + 0 }}</td>
            <td>{{ config('withdraw.type')[$data->type] }}</td>
        </tr>
        <tr><th colspan="99"></th></tr>
        <tr>
            <th>状态</th>
            <th>管理员备注</th>
            <th>创建时间</th>
            <th>更新时间</th>
            <th>财务接口单号</th>
        </tr>
        <tr>
            <td>{{ config('withdraw.status')[$data->status] }}</td>
            <td>{{ $data->admin_remark }}</td>
            <td>{{ $data->created_at}}</td>
            <td>{{ $data->updated_at}}</td>
            <td>{{ $data->bill_id }}</td>
        </tr>
        <tr><th colspan="99"></th></tr>
        <tr>
            <th>财务办款结果</th>
            <th>办款人</th>
            <th>付款账号</th>
            <th>付款银行</th>
            <th>转款明细</th>
        </tr>
        <tr>
            <td>{{ $data->bill_status == 0 ? '失败' : '成功' }}</td>
            <td>{{ $data->bill_user_name }}</td>
            <td>{{ $data->pay_account }}</td>
            <td>{{ $data->pay_bank_full_name }}</td>
            <td>
                @if ($data->transfer_detail)
                    @foreach (json_decode($data->transfer_detail) as $value)
                        <p>银行流水号：{{ $value->ReqnbrID ?: '--' }} | 支付状态：{{ $value->Result }}</p>
                    @endforeach
                @endif
            </td>
        </tr>
    </table>

    <fieldset class="layui-elem-field">
        <legend>审核凭证</legend>
        <div class="layui-field-box">
            @if ($data->attach)
                <img src="{{ route('finance.user-widthdraw-order.attach', ['attach' => $data->attach]) }}" />
            @else
                暂未上传凭证
            @endif
        </div>
    </fieldset>
</body>
</html>
