@extends('backend.layouts.main')

@section('title', ' | 订单列表')

@section('css')
    <style>
        .layui-form-pane .layui-form-label {
            width: 120px;
            padding: 8px 15px;
            height: 36px;
            line-height: 20px;
            border-radius: 2px 0 0 2px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            box-sizing: border-box;
        }
        blockquote:before{
            content: ""
        }
        .theme-whbl blockquote, .theme-whbl blockquote.pull-right{
            border-color: #e6e6e6;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>平台订单</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <form class="layui-form">
                        <div class="row">
                            <div class="form-group col-xs-2">
                                <input type="text" name="start_date" id="startDate" autocomplete="off" class="layui-input" placeholder="开始时间" value="{{ $startDate }}">
                            </div>
                            <div class="form-group col-xs-2">
                                <input type="text" name="end_date" id="endDate" autocomplete="off" class="layui-input" placeholder="结束时间" value="{{ $endDate }}">
                            </div>
                            <div class="form-group col-xs-2">
                                <select  name="status"  lay-search="">
                                    <option value="0">订单状态</option>
                                    @foreach(config('order.status_leveling') as $key => $value)
                                        <option value="{{ $key }}" @if($key == $status) selected  @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-xs-2">
                                <select class="layui-input" name="game_id" lay-search="">
                                    <option value="0">请选择游戏</option>
                                    @foreach($games as $key => $value)
                                        <option value="{{ $key }}" @if($key == $gameId) selected  @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xs-1">
                                <input type="text" class="layui-input" name="creator_primary_user_id"  placeholder="发单用户" value="{{ $creatorPrimaryUserId }}">
                            </div>
                            <div class="form-group col-xs-1">
                                <input type="text" class="layui-input" name="gainer_primary_user_id"  placeholder="接单用户" value="{{ $gainerPrimaryUserId }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-3">
                                <input type="text" class="layui-input" name="no"  placeholder="千手订单号" value="{{ $no }}">
                            </div>
                            <div class="form-group col-xs-3">
                                <input type="text" class="layui-input" name="foreign_order_no"  placeholder="外部订单号" value="{{ $foreignOrderNo }}">
                            </div>
                            <div class="form-group col-xs-2">
                                <button type="submit" class="layui-btn layui-btn-normal ">搜索</button>
                                {{--<a href="{{ $fullUrl }}{{ stripos($fullUrl, '?') === false ? '?' : '&'  }}export=1" class="layui-btn layui-btn-normal" >导出</a>--}}
                            </div>
                        </div>
                    </form>
                </header>
                <div class="main-box-body clearfix">

                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $orders->total() }}　本页显示：{{$orders->count()}}
                        </div>
                        <div class="col-xs-9">
                            <div class=" pull-right">
                                {!! $orders->appends([
                                      'status' => $status,
                                      'source' => $source,
                                      'start_date' => $startDate,
                                      'end_date' => $endDate,
                                      'service_id' => $serviceId,
                                      'game_id' => $gameId,
                                      'creator_primary_user_id' => $creatorPrimaryUserId,
                                      'gainer_primary_user_id' => $gainerPrimaryUserId,
                                      'no' => $no,
                                      'foreign_order_no' => $foreignOrderNo,
                                  ])->render() !!}
                            </div>
                        </div>
                    </div>
                    <table class="layui-table layui-form" lay-size="sm">
                            <thead>
                            <tr>
                                <th width="19%">订单号</th>
                                <th>来源</th>
                                <th>状态</th>
                                <th>商品</th>
                                <th>服务</th>
                                <th>游戏</th>
                                <th>原单价</th>
                                <th>原总额</th>
                                <th>数量</th>
                                <th>单价</th>
                                <th>总额</th>
                                <th>发单</th>
                                <th>接单</th>
                                <th>下单时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>千手：{{ $order->no }} <br> 外部：{{ $order->foreign_order_no }}</td>
                                    <td>{{ config('order.source')[$order->source] ?? '' }}</td>
                                    <td>{{ isset(config('order.status')[$order->status]) ? config('order.status')[$order->status] : (isset(config('order.status_leveling')[$order->status]) ? config('order.status_leveling')[$order->status] : '') }}</td>
                                    <td>{{ $order->goods_name }}</td>
                                    <td>{{ $order->service_name }}</td>
                                    <td>{{ $order->game_name }}</td>
                                    <td>{{ $order->original_price }}</td>
                                    <td>{{ $order->original_amount }}</td>
                                    <td>{{ $order->quantity }}</td>
                                    <td>{{ $order->price }}</td>
                                    <td>{{ $order->amount }}</td>
                                    <td> {{ $order->creator_primary_user_id }}</td>
                                    <td>{{ $order->gainerPrimaryUser->nickname ?? $order->gainer_primary_user_id }}</td>
                                    <td>{{ $order->created_at }}</td>
                                    <td>
                                        <div class="layui-input-inline">
                                            <select  lay-filter="operation" data-no="{{ $order->no }}" data-original-amount="{{ $order->original_amount }}" data-id="{{ $order->id }}">
                                                <option value="">请选择操作</option>
                                                    {{--<option value="execute2">罚款</option>--}}
                                                    {{--<option value="execute3">加权重</option>--}}
                                                    {{--<option value="execute4">减权重</option>--}}
                                                    {{--<option value="execute5">禁止接单</option>--}}
                                                    {{--<option value="execute6">发起售后</option>--}}
                                                <option value="detail">订单详情</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                    <div class="row">
                        <div class="col-xs-3">
                            总数：{{ $orders->total() }}　本页显示：{{$orders->count()}}
                        </div>
                        <div class="col-xs-9">
                            <div class=" pull-right">
                                {!! $orders->appends([
                                      'status' => $status,
                                      'source' => $source,
                                      'start_date' => $startDate,
                                      'end_date' => $endDate,
                                      'service_id' => $serviceId,
                                      'game_id' => $gameId,
                                      'creator_primary_user_id' => $creatorPrimaryUserId,
                                      'gainer_primary_user_id' => $gainerPrimaryUserId,
                                      'no' => $no,
                                      'foreign_order_no' => $foreignOrderNo,
                                  ])->render() !!}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

<!-- 奖励加钱-->
<div class="add-money" style="display: none;padding: 20px">
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label  class="layui-form-label">订单号</label>
            <div class="layui-input-block">
            <input id="add" type="text" name="order_no" value="" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>

       <div class="layui-form-item">
            <label  class="layui-form-label">奖励金额</label>
            <div class="layui-input-block">
                <select name="money" lay-verify="required">
                    <option value="">请选择金额</option>
                    <option value="10">10 元</option>
                    <option value="20">20 元</option>
                    <option value="50">50 元</option>
                    <option value="1000">1000 元</option>
                    <option value="2000">2000 元</option>
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label  class="layui-form-label">备注说明</label>
            <div class="layui-input-block">
                <select name="remark">
                    <option value="">请选择</option>
                    <option value="奖励 10 元">奖励 10 元</option>
                    <option value="奖励 20 元">奖励 20 元</option>
                    <option value="奖励 50 元">奖励 50 元</option>
                </select>
            </div>
        </div>

        <div class="layui-upload">
            <button type="button" class="layui-btn layui-btn-normal" id="test1">多凭证图片上传</button> 
            <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                <div class="layui-upload-list" id="demo1"></div>
            </blockquote>
        </div>

        <div class="layui-form-item">
            <label  class="layui-form-label"></label>   
            <div class="layui-input-block">
            <button class="layui-btn layui-btn-normal" lay-submit="" style="float:right" lay-filter="add">确定</button>
            </div>
        </div>
    </form>
</div>

<!-- 惩罚-->
<div class="sub-money" style="display: none;padding: 20px">
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label  class="layui-form-label">订单号</label>
            <div class="layui-input-block">
            <input id="sub" type="text" name="order_no" value="" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label  class="layui-form-label">罚款金额</label>
            <div class="layui-input-block">
                <select name="money" lay-verify="required">
                    <option value="">请选择金额</option>
                    <option value="10">10 元</option>
                    <option value="20">20 元</option>
                    <option value="50">50 元</option>
                    <option value="1000">1000 元</option>
                    <option value="2000">2000 元</option>
                    <option id="original_amount" value="original_amount">原订单价格的 1% </option>
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label  class="layui-form-label">备注说明</label>
            <div class="layui-input-block">
                <select name="remark">
                    <option value="">请选择</option>
                    <option value="未按要求给顾客反馈信息和话术">未按要求给顾客反馈信息和话术,处罚 10 元</option>
                    <option value="因沟通问题造成用户差评">因沟通问题造成用户差评,处罚 20 元</option>
                    <option value="聊天记录抽查发现怼客户">聊天记录抽查发现怼客户,处罚 50 元</option>
                    <option value="接单超时未完成罚款,处罚原订单价格的 1%">接单超时未完成罚款,处罚原订单价格的 1%</option>
                </select>
            </div>
        </div>

        <div class="layui-upload">
            <button type="button" class="layui-btn layui-btn-normal" id="test2">多凭证图片上传</button> 
            <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                <div class="layui-upload-list" id="demo2"></div>
            </blockquote>
        </div>

        <div class="layui-form-item">
            <label  class="layui-form-label"></label>
            <div class="layui-input-block">
            <button class="layui-btn layui-btn-normal" lay-submit="" style="float:right" lay-filter="sub">确定</button>
            </div>
        </div>
    </form>
</div>

<!-- 加权重-->
<div class="add-weight" style="display: none;padding: 20px">
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label  class="layui-form-label">订单号</label>
            <div class="layui-input-block">
            <input id="add-weight" type="text" name="order_no" value="" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label  class="layui-form-label">增加权重</label>
            <div class="layui-input-block">
                <select name="ratio" lay-verify="required">
                    <option value="">请选择</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="30">30</option>
                    <option value="40">40</option>
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label  class="layui-form-label">备注说明</label>
            <div class="layui-input-block">
                <select name="remark">
                    <option value="">请选择</option>
                    <option value="月度统计第五名,奖励 5">月度统计第五名,奖励 5</option>
                    <option value="月度统计第四名,奖励 10">月度统计第四名,奖励 10</option>
                    <option value="月度统计第三名,奖励 15">月度统计第三名,奖励 15</option>
                    <option value="月度统计第二名,奖励 30">月度统计第二名,奖励 30</option>
                    <option value="月度统计第一名,奖励 40">月度统计第一名,奖励 40</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label  class="layui-form-label">生效时间</label>
            <div class="layui-input-block">
                <input type="text" name="start_time" id="start_time" autocomplete="off" class="layui-input" placeholder="生效时间" value="">
            </div>
        </div>
        <div class="layui-form-item">
            <label  class="layui-form-label">截止时间</label>
            <div class="layui-input-block">
                <input type="text" name="end_time" id="end_time" autocomplete="off" class="layui-input" placeholder="截止时间" value="">
            </div>
        </div>

        <div class="layui-upload">
            <button type="button" class="layui-btn layui-btn-normal" id="test3">多凭证图片上传</button> 
            <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                <div class="layui-upload-list" id="demo3"></div>
            </blockquote>
        </div>

        <div class="layui-form-item">
            <label  class="layui-form-label"></label>
            <div class="layui-input-block">
            <button class="layui-btn layui-btn-normal" lay-submit="" style="float:right" lay-filter="add-weight">确定</button>
            </div>
        </div>
    </form>
</div>

<!-- 减权重-->
<div class="sub-weight" style="display: none;padding: 20px">
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label  class="layui-form-label">订单号</label>
            <div class="layui-input-block">
            <input id="sub-weight" type="text" name="order_no" value="" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label  class="layui-form-label">减少权重</label>
            <div class="layui-input-block">
                <select name="ratio" lay-verify="required">
                    <option value="">请选择</option>
                    <option value="-5">5</option>
                    <option value="-10">10</option>
                    <option value="-15">15</option>
                    <option value="-30">30</option>
                    <option value="-40">40</option>
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label  class="layui-form-label">备注说明</label>
            <div class="layui-input-block">
                <select name="remark">
                    <option value="">请选择</option>
                    <option value="违规，减少权重 5">违规，减少权重 5</option>
                    <option value="违规，减少权重 10">违规，减少权重 10</option>
                    <option value="违规，减少权重 15">违规，减少权重 15</option>
                    <option value="违规，减少权重 30">违规，减少权重 30</option>
                    <option value="违规，减少权重 40">违规，减少权重 40</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label  class="layui-form-label">生效时间</label>
            <div class="layui-input-block">
                <input type="text" name="start_time" id="start_time1" autocomplete="off" class="layui-input" placeholder="生效时间" value="">
            </div>
        </div>
        <div class="layui-form-item">
            <label  class="layui-form-label">截止时间</label>
            <div class="layui-input-block">
                <input type="text" name="end_time" id="end_time1" autocomplete="off" class="layui-input" placeholder="截止时间" value="">
            </div>
        </div>

        <div class="layui-upload">
            <button type="button" class="layui-btn layui-btn-normal" id="test4">多凭证图片上传</button> 
            <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                <div class="layui-upload-list" id="demo4"></div>
            </blockquote>
        </div>

        <div class="layui-form-item">
            <label  class="layui-form-label"></label>
            <div class="layui-input-block">
            <button class="layui-btn layui-btn-normal" lay-submit="" style="float:right" lay-filter="sub-weight">确定</button>
            </div>
        </div>
    </form>
</div>

<!-- 禁止接单一天-->
<div class="forbidden" style="display: none;padding: 20px">
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label  class="layui-form-label">订单号</label>
            <div class="layui-input-block">
            <input id="forbidden" type="text" name="order_no" value="" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label  class="layui-form-label">备注说明</label>
            <div class="layui-input-block">
                <select name="remark">
                    <option value="">请选择</option>
                    <option value="一天累计2次未按要求操作">一天累计2次未按要求操作</option>
                    <option value="10分钟内未回复千手客服/运营信息超过2次">10分钟内未回复千手客服/运营信息超过2次</option>
                    <option value="续3天，充值成功率低于平均水平">续3天，充值成功率低于平均水平</option>
                </select>
            </div>
        </div>

        <div class="layui-upload">
            <button type="button" class="layui-btn layui-btn-normal" id="test5">多凭证图片上传</button> 
            <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                <div class="layui-upload-list" id="demo5"></div>
            </blockquote>
        </div>

        <div class="layui-form-item">
            <label  class="layui-form-label"></label>
            <div class="layui-input-block">
            <button class="layui-btn layui-btn-normal" lay-submit="" style="float:right" lay-filter="forbidden">确定</button>
            </div>
        </div>
    </form>
</div>

@section('js')
<script>
    //Demo
    layui.use(['form', 'layedit', 'laytpl', 'element', 'laydate', 'table', 'upload'], function(){
        var form = layui.form, layer = layui.layer, laydate = layui.laydate, layTpl = layui.laytpl,
                element = layui.element, table=layui.table, upload = layui.upload;

        //日期
        laydate.render({
            elem: '#startDate'
        });
        laydate.render({
            elem: '#endDate'
        });
        //日期
        laydate.render({
            elem: '#start_time'
        });
        laydate.render({
            elem: '#end_time'
        });
        //日期
        laydate.render({
            elem: '#start_time1'
        });
        laydate.render({
            elem: '#end_time1'
        });

        // 订单操作
        form.on('select(operation)', function (data) {
            eval(data.value + "('" + data.elem.getAttribute('data-no')  + "',"+data.elem.getAttribute('data-id')+")");
        });

        //订单详情
        function detail(no, id)
        {
            window.open("/admin/order/platform/content/"+id);
        }

        //多图片上传
        upload.render({
            elem: '#test1'
            ,url: "{{ route('punishes.upload-images') }}"
            ,size: 3000
            ,multiple: true
            ,accept: 'file'
            ,exts: 'jpg|jpeg|png|gif'
            ,before: function(obj){
              //预读本地文件示例，不支持ie8
                obj.preview(function(index, file, result){
                    $('#demo1').append('<img style="width:200px; height:200px;padding:2px" src="'+ result +'" alt="'+ file.name +'" class="layui-upload-img">');

                });
            }
            ,done: function(res){
                $('#demo1').append('<input type="hidden" value="'+res.path+'" name="voucher['+']" class="layui-upload-img">');
            }
        });

        //多图片上传
        upload.render({
            elem: '#test2'
            ,url: "{{ route('punishes.upload-images') }}"
            ,size: 3000
            ,multiple: true
            ,accept: 'file'
            ,exts: 'jpg|jpeg|png|gif'
            ,before: function(obj){
              //预读本地文件示例，不支持ie8
                obj.preview(function(index, file, result){
                    $('#demo2').append('<img style="width:200px; height:200px;padding:2px" src="'+ result +'" alt="'+ file.name +'" class="layui-upload-img">');

                });
            }
            ,done: function(res){
                $('#demo2').append('<input type="hidden" value="'+res.path+'" name="voucher['+']" class="layui-upload-img">');
            }
        });

         //多图片上传
        upload.render({
            elem: '#test3'
            ,url: "{{ route('punishes.upload-images') }}"
            ,size: 3000
            ,multiple: true
            ,accept: 'file'
            ,exts: 'jpg|jpeg|png|gif'
            ,before: function(obj){
              //预读本地文件示例，不支持ie8
                obj.preview(function(index, file, result){
                    $('#demo3').append('<img style="width:200px; height:200px;padding:2px" src="'+ result +'" alt="'+ file.name +'" class="layui-upload-img">');

                });
            }
            ,done: function(res){
                $('#demo3').append('<input type="hidden" value="'+res.path+'" name="voucher['+']" class="layui-upload-img">');
            }
        });

         //多图片上传
        upload.render({
            elem: '#test4'
            ,url: "{{ route('punishes.upload-images') }}"
            ,size: 3000
            ,multiple: true
            ,accept: 'file'
            ,exts: 'jpg|jpeg|png|gif'
            ,before: function(obj){
              //预读本地文件示例，不支持ie8
                obj.preview(function(index, file, result){
                    $('#demo4').append('<img style="width:200px; height:200px;padding:2px" src="'+ result +'" alt="'+ file.name +'" class="layui-upload-img">');

                });
            }
            ,done: function(res){
                $('#demo4').append('<input type="hidden" value="'+res.path+'" name="voucher['+']" class="layui-upload-img">');
            }
        });

         //多图片上传
        upload.render({
            elem: '#test5'
            ,url: "{{ route('punishes.upload-images') }}"
            ,size: 3000
            ,multiple: true
            ,accept: 'file'
            ,exts: 'jpg|jpeg|png|gif'
            ,before: function(obj){
              //预读本地文件示例，不支持ie8
                obj.preview(function(index, file, result){
                    $('#demo5').append('<img style="width:200px; height:200px;padding:2px" src="'+ result +'" alt="'+ file.name +'" class="layui-upload-img">');

                });
            }
            ,done: function(res){
                $('#demo5').append('<input type="hidden" value="'+res.path+'" name="voucher['+']" class="layui-upload-img">');
            }
        });

        // 奖励加款
        function execute1(no, id)
        {
            var index = layer.open({
                type: 1,
                offset: '100px',
                area: ['700px', '650px'],
                shade: 0.2,
                title: '奖励加款',
                content: $('.add-money')
            });

            $('#add').val(no);

            form.on('submit(add)', function (data) {
                $.post('{{ route('execute.add-money') }}', {data: data.field}, function (result) {
                    console.log(result);
                    if (result.code == 1) {     
                        layer.msg(result.message, {
                            icon:6,
                            time:1500
                        })
                    } else {
                        layer.msg(result.message, {
                            icon:5,
                            time:1500
                        }) 
                    }
                }, 'json');
                layer.close(index);
                return false;
            });
        }


        // 违规扣款
        function execute2(no, id)
        {
            var index = layer.open({
                type: 1,
                offset: '100px',
                area: ['700px', '650px'],
                shade: 0.2,
                title: '惩罚扣款',
                content: $('.sub-money')
            });
            $('#sub').val(no);

            form.on('submit(sub)', function (data) {
                $.post('{{ route('execute.sub-money') }}', {data: data.field}, function (result) {
                    if (result.code == 1) {     
                        layer.msg(result.message, {
                            icon:6,
                            time:1500
                        })
                    } else {
                        layer.msg(result.message, {
                            icon:5,
                            time:1500
                        }) 
                    }
                }, 'json');
                layer.close(index);
                return false;
            });
        }

        // 加权重
        function execute3(no, id)
        {
            var index = layer.open({
                type: 1,
                offset: '100px',
                area: ['700px', '650px'],
                shade: 0.2,
                title: '奖励加权重',
                content: $('.add-weight')
            });

            $('#add-weight').val(no);

            form.on('submit(add-weight)', function (data) {
                $.post('{{ route('execute.add-weight') }}', {data: data.field}, function (result) {
                    if (result.code == 1) {     
                        layer.msg(result.message, {
                            icon:6,
                            time:1500
                        })
                    } else {
                        layer.msg(result.message, {
                            icon:5,
                            time:1500
                        }) 
                    }
                }, 'json');
                layer.close(index);
                return false;
            });
        }


        // 减权重
        function execute4(no, id)
        {
            var index = layer.open({
                type: 1,
                offset: '100px',
                area: ['700px', '650px'],
                shade: 0.2,
                title: '奖励加权重',
                content: $('.sub-weight')
            });

            $('#sub-weight').val(no);

            form.on('submit(sub-weight)', function (data) {
                $.post('{{ route('execute.sub-weight') }}', {data: data.field}, function (result) {
                    if (result.code == 1) {     
                        layer.msg(result.message, {
                            icon:6,
                            time:1500
                        })
                    } else {
                        layer.msg(result.message, {
                            icon:5,
                            time:1500
                        }) 
                    }
                }, 'json');
                layer.close(index);
                return false;
            });
        }


        // 禁止接单
        function execute5(no, id)
        {
            var index = layer.open({
                type: 1,
                offset: '100px',
                area: ['700px', '650px'],
                shade: 0.2,
                title: '禁止接单一天',
                content: $('.forbidden')
            });

            $('#forbidden').val(no);

            form.on('submit(forbidden)', function (data) {
                $.post('{{ route('execute.forbidden') }}', {data: data.field}, function (result) {
                    if (result.code == 1) {     
                        layer.msg(result.message, {
                            icon:6,
                            time:1500
                        })
                    } else {
                        layer.msg(result.message, {
                            icon:5,
                            time:1500
                        }) 
                    }
                }, 'json');
                layer.close(index);
                return false;
            });
        }
        // 发起售后
        function execute6(no, id) {
            layer.confirm('您确定要"发起售后"吗?', {icon: 3, title:'提示'}, function(index) {
                $.post('{{ route('order.platform.apply-after-service') }}', {no:no}, function (result) {
                    layer.msg(result.message, {
                        icon:5,
                        time:1500
                    });
                    return false;
                }, 'json')
            });
        }
    });

</script>
@endsection