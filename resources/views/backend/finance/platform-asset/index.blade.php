@extends('backend.layouts.main')

@section('title', ' | 平台当前资产')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <div class="main-box-body clearfix">
                    <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="add">平台当前资产</li>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <table class="layui-table" lay-size="sm">
                                <thead>
                                <tr>
                                    <th width="135px">模版ID</th>
                                    <th>模版名称</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>平台资金</td>
                                        <td>{{ $platformAsset->amount + 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td>平台托管资金</td>
                                        <td>{{ $platformAsset->managed + 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td>用户总余额</td>
                                        <td>{{ $platformAsset->balance + 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td>用户总冻结</td>
                                        <td>{{ $platformAsset->frozen + 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td>累计用户加款</td>
                                        <td>{{ $platformAsset->total_recharge + 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td>累计用户提现</td>
                                        <td>{{ $platformAsset->total_withdraw + 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td>累计用户消费</td>
                                        <td>{{ $platformAsset->total_consume + 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td>累计退款给用户</td>
                                        <td>{{ $platformAsset->total_refund + 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td>累计用户成交次数</td>
                                        <td>{{ $platformAsset->total_trade_quantity }}</td>
                                    </tr>
                                    <tr>
                                        <td>累计用户成交金额</td>
                                        <td>{{ $platformAsset->total_trade_amount + 0 }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection
