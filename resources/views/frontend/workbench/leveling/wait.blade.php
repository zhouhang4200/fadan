@extends('frontend.layouts.app')

@section('title', '工作台 - 代练 - 待发订单')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .wrapper {
            width: 1600px;
        }
        .main .right {
            width: 1430px;
        }
        .layui-laypage-skip input {
            height: 27px !important;
        }
        .laytable-cell-1-0, .laytable-cell-1-5, .laytable-cell-1-7{
            height: 40px !important;
        }
        th:nth-child(1) > div, th:nth-child(6) > div, th:nth-child(8) > div {
            line-height: 40px !important;
        }
        .laytable-cell-1-13{
            height: 40px !important;
            line-height: 40px !important;
        }
        .layui-laypage-em {
            background-color: #1E9FFF !important;
        }
        .layui-form-select .layui-input {
            padding-right:0 !important;
        }
        .layui-table-cell {
            overflow: inherit;
        }
        .layui-form-item .layui-inline {
            margin-bottom: 5px;
            margin-right: 5px;
        }
        .layui-form-mid {
            margin-right: 4px;
        }
        #info .layui-form-item .layui-input-block{
            margin-left: 200px;
        }
        #info .layui-form-item .layui-form-label{
           width: 160px;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.workbench.submenu')
@endsection

@section('main')
    <form class="layui-form" id="search">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-mid">订单号：</label>
                <div class="layui-input-inline">
                    <input type="text" name="tid" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-inline">
                <label class="layui-form-mid">旺旺号：</label>
                <div class="layui-input-inline" style="">
                    <input type="text" name="buyer_nick" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">状态</label>
                <div class="layui-input-inline">
                    <select name="status" lay-verify="">
                        <option value=""></option>
                        <option value="0" selected>待处理</option>
                        <option value="1">已处理</option>
                        <option value="2">已隐藏</option>
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-mid">下单时间：</label>
                <div class="layui-input-inline" style="">
                    <input type="text" name="start_date" autocomplete="off" class="layui-input" id="start-date" value="">
                </div>
                <div class="layui-input-inline" style="">
                    <input type="text" name="end_date" autocomplete="off" class="layui-input fsDate" id="end-date" value="">
                </div>
                <button class="layui-btn layui-btn-normal " type="button" function="query" lay-submit="" lay-filter="search">查询</button>
            </div>
        </div>

        <div class="layui-form-item">

        </div>
    </form>

    <div class="order">

    </div>

@endsection

<!--START 底部-->
@section('js')
    <script>
        layui.use(['table', 'form', 'layedit', 'laydate', 'laytpl', 'element'], function () {
            var form = layui.form,
                layer = layui.layer,
                layTpl = layui.laytpl,
                element = layui.element,
                laydate = layui.laydate,
                table = layui.table;

            laydate.render({elem: '#start-date'});
            laydate.render({elem: '#end-date'});

            getOrder();
            // 获取订单
            function getOrder(tid, wangWang, status, startDate, endDate) {
                $.post('{{ route('frontend.workbench.leveling.wait-list') }}', {tid:tid, buyer_nick: wangWang, status: status, start_date: startDate, end_date:endDate}, function (result) {
                    $('.order').html(result);
                    layui.form.render();
                }, 'json');
            }

            form.on('submit(search)', function (data) {
               getOrder(data.field.tid, data.field.buyer_nick, data.field.status, data.field.start_date, data.field.end_date);
            });
        });
    </script>
@endsection