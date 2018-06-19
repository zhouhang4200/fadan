@extends('frontend.v1.layouts.app')

@section('title', '工作台-订单投诉')

@section('css')
    <style>
        .layui-laypage .layui-laypage-curr .layui-laypage-em {
            background-color: #ff8500;
        }
        .layui-form-item {
            margin-bottom: 12px;
        }
        .layui-card-body {
            padding-top: 0;
        }
        .layui-tab {
            padding: 0;
            font-size: 12px;
        }
        .layui-card-header {
            height: auto;
            border-bottom: none;
            padding-bottom: 0;
        }

        .layui-tab-title li{
            min-width: 50px;
            font-size: 12px;
        }
        .qs-btn-sm {
            height: 27px;
            line-height: 27px;
            vertical-align: top;
        }
        .layui-table-view .layui-table[lay-size=sm] .layui-table-cell {
            height: 28px;
            line-height: 28px;
        }
        /* 改写header高度 */
        .layui-card-header {
            font-size:12px;
        }
    </style>
@endsection

@section('main')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-header" style="padding-top: 20px;">
                <div class="layui-row layui-col-space5">
                    <form class="layui-form" action="">
                        <input type="hidden" name="status" value="0">
                        <div class="layui-col-md3 first">
                            <div class="layui-form-item">
                                <label class="layui-form-label" style="">订单单号</label>
                                <div class="layui-input-block" style="">
                                    <input type="text" name="order_no" lay-verify="" autocomplete="off" placeholder="请输入" class="layui-input">
                                </div>
                            </div>
                        </div>

                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">游戏</label>
                                <div class="layui-input-block">
                                    <select name="game_id" lay-filter="game" lay-search="">
                                        <option value="">请选择</option>
                                        @forelse($games as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="layui-col-md4 first">
                            <div class="layui-form-item">
                                <label class="layui-form-label">投诉时间</label>
                                <div class="layui-input-block">
                                    <input type="text"  class="layui-input qsdate" id="test-laydate-start" name="start_date" placeholder="开始日期">
                                    <div class="layui-form-mid" style="float:none;display: inline-block;width: 8%;text-align: center;margin:0;">
                                        -
                                    </div>
                                    <input type="text" class="layui-input qsdate" id="test-laydate-end"  name="end_date" placeholder="结束日期">
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md2">
                            <div class="layui-form-item">
                                <div class="layui-input-block" style="margin-left: 40px;">
                                    <button class="qs-btn" lay-submit="" lay-filter="search">搜索</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="layui-card-body">
                <div class="layui-tab layui-tab-brief layui-form" lay-filter="list-data">
                    <ul class="layui-tab-title">
                        <li class="" lay-id="0">全部
                            <span  class="qs-badge quantity-9 layui-hide"></span>
                        </li>
                        <li class="layui-this" lay-id="1">投诉
                            <span class="qs-badge quantity-1 layui-hide"></span>
                        </li>
                        <li class="" lay-id="2">已取消
                            <span class="qs-badge quantity-2 layui-hide"></span>
                        </li>
                        <li class="" lay-id="3">投诉成功
                            <span class="qs-badge quantity-3 layui-hide"></span>
                        </li>
                        <li class="" lay-id="4">投诉失败
                            <span class="qs-badge quantity-4 layui-hide"></span>
                        </li>
                    </ul>
                </div>
                <div id="list-data" lay-filter="">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/html" id="noTemplate">
        @{{# if(d.foreign_order_no) { }}
        天猫：<a style="color:#1f93ff"  href="{{ route('frontend.workbench.leveling.detail') }}?no=@{{ d.no }}" target="_blank"> @{{ d.foreign_order_no }}</a> <br/>
        @{{#  } }}
        @{{# if(d.third_name) { }}
            @{{ d.third_name }}：<a style="color:#1f93ff" href="{{ route('frontend.workbench.leveling.detail') }}?no=@{{ d.no }}" target="_blank"> @{{  d.third_order_no }} </a>
        @{{#  } }}
    </script>
    <script type="text/html" id="changeStyleTemplate">
        <style>
            .layui-table-view .layui-table[lay-size=sm] td .laytable-cell-@{{ d  }}-no{
                height: 40px;
                line-height: 20px;
            }
            .layui-table-view .layui-table[lay-size=sm] td  .laytable-cell-@{{ d  }}-button{
                display: block;
                height: 40px;
                line-height: 40px;
                word-break: break-all;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                padding-left: 15px;
            }
            .layui-laypage .layui-laypage-curr .layui-laypage-em {
                background-color: #ff8500;
            }
        </style>
    </script>
    <script>
        layui.use(['table', 'form', 'layedit', 'laydate', 'laytpl', 'element'], function () {
            var form = layui.form, layer = layui.layer, element = layui.element, layTpl = layui.laytpl, table = layui.table , tableIns;
            // 状态切换
            element.on('tab(list-data)', function () {
                $('[name=status]').val(this.getAttribute('lay-id'));
                reloadOrderList();
            });
            // 搜索
            form.on('submit(search)', function (data) {
                reloadOrderList(data.field);
                return false;
            });
            // 加载数据
            table.render({
                elem: '#list-data',
                url: '{{ route('frontend.workbench.leveling.complaints-list-data') }}',
                method: 'post',
                cols: [[
                    {field: 'no', title: '订单号', width: 150, templet: '#noTemplate', style:"height: 40px;line-height: 20px;"},
                    {field: 'taobao_status', title: '淘宝订单状态', width: 200},
                    {field: 'order_status', title: '平台订单状态', width: 120},
                    {field: 'game_name', title: '游戏', width: 120},
                    {field: 'amount', title: '要求赔偿金额', width: 150},
                    {field: 'status_text', title: '投诉状态'},
                    {field: 'created_at', title: '投诉时间'},
                    {field: 'button', title: '操作', width: 155,  toolbar: '#operation', fixed: 'right'},
                ]],
                height: 'full-235',
                size: 'sm',
                page: {
                    layout: [ 'count', 'prev', 'page', 'next', 'skip'],
                    groups: 10,
                    prev: '上一页',
                    next: '下一页'
                },
                done: function(res, curr, count){
                    changeStyle(layui.table.index);
                    setStatusNumber(res.status_count);
                }
            });
            // 订单表格重载
            function reloadOrderList(parameter) {
                var condition = {};
                if (parameter == undefined) {
                    var formCondition = $('form').serializeArray();
                    $.each(formCondition, function() {
                        condition[this.name] = this.value;
                    });
                } else {
                    condition = parameter;
                }
                //执行重载
                table.reload('list-data', {
                    where: condition,
                    height: 'full-260',
                    page: {
                        curr: 1
                    },
                    done: function(res, curr, count){
                        changeStyle(layui.table.index);
                        setStatusNumber(res.status_count);
                        $('.send').addClass('qs-btn-message');
                        layui.form.render();
                    }
                });
            }
            // 设置订单状态数
            function setStatusNumber(parameter) {
                if (parameter.length == 0) {
                    $('.qs-badge').addClass('layui-hide');
                }
                $.each(parameter, function(key, val) {
                    var name = 'quantity-'  +  key;
                    if ($('span').hasClass(name) && val > 0) {
                        $('.' + name).html(val).removeClass('layui-hide');
                    } else {
                        $('.' + name).addClass('layui-hide');
                    }
                });
            }
            // 重新渲染后重写样式
            function changeStyle(index) {
                var getTpl = changeStyleTemplate.innerHTML, view = $('body');
                layTpl(getTpl).render(index, function(html){
                    view.append(html);
                });
            }
        });
    </script>
@endsection