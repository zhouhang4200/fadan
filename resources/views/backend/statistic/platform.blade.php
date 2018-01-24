@extends('backend.layouts.main')

@section('title', ' | 代练平台订单统计')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>代练平台订单统计</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <form class="layui-form">
                        <div class="row">
                            <div class="layui-form-item">
                                <label class="layui-form-label">发布时间</label>
                                <div class="form-group col-xs-1">
                                    <input type="text" name="start_date" id="startDate" autocomplete="off" class="layui-input" placeholder="开始时间" value="{{ $startDate }}">
                                </div>
                                <div class="form-group col-xs-1">
                                    <input type="text" name="end_date" id="endDate" autocomplete="off" class="layui-input" placeholder="结束时间" value="{{ $endDate }}">
                                </div>
                                <div class="form-group col-xs-2">
                                    <button type="submit" class="layui-btn layui-btn-normal ">查询</button>
                                    <a href="{{ $fullUrl }}{{ stripos($fullUrl, '?') === false ? '?' : '&'  }}export=1" class="layui-btn layui-btn-normal layui-btn-small">导出</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </header>
                <div class="main-box-body clearfix">
                    <form class="layui-form" action="">
                    <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th>发布时间</th>
                                <th>发布单数</th>
                                <th>单旺旺号平均发送</th>
                                <th>被接单数</th>
                                <th>已结算单数</th>
                                <th>已结算占比</th>
                                <th>已撤销单数</th>
                                <th>已撤销占比</th>
                                <th>已仲裁单数</th>
                                <th>已仲裁占比</th>
                                <th>完单平均代练时间</th>
                                <th>完单平均安全保证金</th>
                                <th>完单平均效率保证金</th>
                                <th>完单平均来源价格</th>
                                <th>完单总来源价格</th>
                                <th>完单平均发单价格</th>
                                <th>完单总发单价格</th>
                                <th>结算平均支付</th>
                                <th>结算总支付</th>
                                <th>撤销平均支付</th>
                                <th>撤销总支付</th>
                                <th>撤销平均赔偿</th>
                                <th>撤销总赔偿</th>
                                <th>仲裁平均支付</th>
                                <th>仲裁总支付</th>
                                <th>仲裁平均赔偿</th>
                                <th>仲裁总赔偿</th>
                                <th>平均手续费</th>
                                <th>总手续费</th>
                                <th>商户平均利润</th>
                                <th>商户总利润</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($paginatePlatformOrderStatistics as $paginatePlatformOrderStatistic)
                                <tr>
                                    <td>{{ $paginatePlatformOrderStatistic->date }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->total_order_count }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->wang_wang_order_evg }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->receive_order_count }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->complete_order_count }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->revoke_order_count }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->revoke_order_rate }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->arbitrate_order_count }}</td>
                                    <td>{{ $paginatePlatformOrderStatistic->arbitrate_order_rate }}</td>
                                    
                                    <td>
                                        <div class="form-group col-xs-4" style="margin: 10px 0 10px 0">
                                            <select  style="background-color: #1E9FFF" name="status" lay-filter="change_status" data-amount="{{ $paginatePlatformOrderStatistic->amount }}" data-safe="{{ $paginatePlatformOrderStatistic->security_deposit }}"
                                            data-effect="{{ $paginatePlatformOrderStatistic->efficiency_deposit }}" lay-data="{{ $paginatePlatformOrderStatistic->order_no }}">                
                                                <option value="">修改状态</option>
                                                @forelse($ourStatus as $key => $status)
                                                    <option value="{{ $key }}" id="status{{ $key }}" data-status="{{ $status }}" >{{ $status }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                        <button class="layui-btn layui-btn-normal layui-btn" style="margin-top: 10px;" lay-submit="" lay-filter="delete" data-id="{{ $paginatePlatformOrderStatistic->id }}">删除</button>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                        </form>
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $paginatePlatformOrderStatistics->total() }}　本页显示：{{ $paginatePlatformOrderStatistics->count() }}
                        </div>
                            <div class="col-xs-9">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="consult" style="display: none; padding:  0 20px">
        <div class="layui-tab-content">
            <span style="color:red;margin-right:15px;">双方友好协商撤单，若有分歧可以再订单中留言或申请客服介入；若申请成功，此单将被锁定，若双方取消撤单会退回至原有状态。<br/></span>
            <form class="layui-form" method="POST" action="">
                {!! csrf_field() !!}
                <div style="width: 80%" id="info">
                    <div class="layui-form-item">
                        <label class="layui-form-label">*我愿意支付代练费（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="amount" lay-verify="required|number" value="" autocomplete="off" placeholder="请输入代练费" class="layui-input" style="width:400px">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">我已支付代练费（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="order_amount" id="order_amount" lay-verify="" value="" autocomplete="off" placeholder="" class="layui-input" style="width:400px" disabled>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">*需要对方赔付保证金</label>
                        <div class="layui-input-block">
                            <input type="text" name="deposit" lay-verify="required|number" value="" autocomplete="off" placeholder="请输入保证金" class="layui-input" style="width:400px">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">对方已预付安全保证金（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="safe" id="safe" lay-verify="" value="" autocomplete="off" placeholder="" class="layui-input" style="width:400px" disabled>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">对方已预付效率保证金（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="effect" id="effect" lay-verify="" value="" autocomplete="off" placeholder="" class="layui-input" style="width:400px" disabled>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">撤销理由</label>
                        <div class="layui-input-block">
                            <textarea placeholder="请输入撤销理由" name="revoke_message" lay-verify="required" class="layui-textarea" style="width:400px"></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">发起人</label>
                        <div class="layui-input-block">
                            <input type="radio" name="who" value="1" title="发单">
                            <input type="radio" name="who" value="2" title="接单">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <button class="layui-btn  layui-btn-normal" lay-submit lay-filter="consult">立即提交</button>
                            <span cancel class="layui-btn  layui-btn-normal cancel">取消</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="complain" style="display: none; padding: 10px 10px 0 10px">
        <div class="layui-tab-content">
            <form class="layui-form">
            <input type="hidden" id="order_no" name="order_no">
                <div>
                    <div class="layui-form-item">
                        <div class="layui-input-block" style="margin:0px">
                            <textarea placeholder="请输入申请仲裁理由" name="complain_message" lay-verify="required" class="layui-textarea" style="width:90%;margin:auto;height:150px !important;"></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">发起人</label>
                        <div class="layui-input-block">
                            <input type="radio" name="who" value="1" title="发单">
                            <input type="radio" name="who" value="2" title="接单">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block" style="margin: 0 auto;text-align: center;">
                            <button class="layui-btn layui-btn-normal" id="submit" lay-submit lay-filter="complain">确认</button>
                            <span cancel class="layui-btn  layui-btn-normal cancel">取消</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('js')
<script>
    //Demo
    layui.use(['form', 'laytpl', 'element', 'laydate'], function(){
        var form = layui.form, layer = layui.layer, laydate = layui.laydate;

        //日期
        laydate.render({
            elem: '#startDate'
        });
        laydate.render({
            elem: '#endDate'
        });
    });
</script>
@endsection