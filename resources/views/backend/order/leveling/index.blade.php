@extends('backend.layouts.main')

@section('title', ' | 代练订单报警')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>代练订单报警</span></li>
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
                                <div class="form-group col-xs-1">
                                    <input type="text" name="start_date" id="startDate" autocomplete="off" class="layui-input" placeholder="开始时间" value="{{ $startDate }}">
                                </div>
                                <div class="form-group col-xs-1">
                                    <input type="text" name="start_date" id="startDate" autocomplete="off" class="layui-input" placeholder="开始时间" value="{{ $startDate }}">
                                </div>
                                <div class="form-group col-xs-1">
                                    <input type="text" name="end_date" id="endDate" autocomplete="off" class="layui-input" placeholder="结束时间" value="{{ $endDate }}">
                                </div>
                            </div>
                            <div class="form-group col-xs-2">
                                <button type="submit" class="layui-btn layui-btn-normal ">查询</button>
                                <button type="submit" class="layui-btn layui-btn-normal">导出</button>
                            </div>
                        </div>
                    </form>
                </header>
                <div class="main-box-body clearfix">
                    <div class="row">
                        <div class="col-xs-3">
                            总数：　本页显示：
                        </div>
                        <div class="col-xs-9">
                        </div>
                    </div>
                    <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th>订单号</th>
                                <th>千手状态</th>
                                <th>外部状态</th>
                                <th>接单平台</th>
                                <th>发布时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    <div class="row">
                        <div class="col-xs-3">
                            总数：　本页显示：
                        </div>
                            <div class="col-xs-9">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
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