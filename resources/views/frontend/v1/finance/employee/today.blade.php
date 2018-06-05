@extends('frontend.v1.layouts.app')

@section('title', '统计 - 员工统计')

@section('css')
    <link href="{{ asset('/css/index.css') }}" rel="stylesheet">
    <style>
        .layui-laypage-em {
            background-color: #ff7a00 !important;
        }
        .layui-form-label {
            width: 50px;
            padding-left: 0px;
        }
    </style>
@endsection

@section('main')
<div class="layui-card qs-text">
    <div class="layui-card-body">
    <form class="layui-form" method="" action="">
        <div class="layui-input-inline">
            <div class="layui-form-item">
                <label class="layui-form-label">员工姓名</label> 
                    <div class="layui-input-inline">               
                        <select name="user_id" lay-verify="" lay-search="">
                            <option value="">请输入员工姓名</option>
                            <option value="{{ $parent->id }}" {{ $parent->id == $userId ? 'selected' : '' }}>{{ $parent->username ?? '--' }}</option>
                            @forelse($children as $child)
                                <option value="{{ $child->id }}" {{ $child->id == $userId ? 'selected' : '' }}>{{ $child->username ?? '--' }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                <label class="layui-form-label" >发布时间</label>
                <div class="layui-input-inline">  
                    <input type="text" id="start_date" lay-start_date="{{ $startDate }}" class="layui-input" value="{{ $startDate ?: null }}" name="start_date" placeholder="年-月-日">
                </div>
                <div class="layui-input-inline">  
                    <input type="text" id="end_date" lay-end_date="{{ $endDate }}" class="layui-input" value="{{ $endDate ?: null }}"  name="end_date" placeholder="年-月-日">
                </div>
                <div class="layui-inline" >
                    <button class="qs-btn qs-btn-normal qs-btn-small" lay-submit="" lay-filter="demo1" style="margin-left: 10px">查询</button>
                     <a href="{{ $fullUrl }}{{ stripos($fullUrl, '?') === false ? '?' : '&'  }}export=1" class="qs-btn qs-btn-normal layui-btn-small" >导出</a>
                </div>                 
            </div>
        </div>
    </form>

    <div class="layui-tab-item layui-show" lay-size="sm">
        <form class="layui-form" action="">
        <table class="layui-table" lay-size="sm" style="text-align:center;">
            <thead>
            <tr>
                <th>员工</th>
                <th>发布数量</th>
                <th>来源价格</th>
                <th>发布价格</th>
                <th>来源/发布差价</th>
                <th>已结算单数</th>
                <th>已结算发单金额</th>
                <th>已撤销单数</th>
                <th>已仲裁单数</th>
                <th>利润</th>
            </tr>
            </thead>
            <tbody>
                @if(! empty($userDatas) && isset($userDatas) && count($userDatas) > 0)
                    @forelse($userDatas as $userData)
                        @if (!$userData->username)
                            <tr>
                                <td colspan="10">暂无</td>
                            </tr>
                        @else
                        <tr>
                            <td>{{ $userData->username }}</td>
                            <td>{{ $userData->count }}</td>
                            <td>{{ number_format($userData->original_price, 2) }}</td>
                            <td>{{ number_format($userData->price, 2) }}</td>
                            <td>{{ number_format($userData->diff_price, 2) }}</td>
                            <td>{{ $userData->complete_count }}</td>
                            <td>{{ number_format($userData->complete_price, 2) ?? '--' }}</td>
                            <td>{{ $userData->revoked_count }}</td>
                            <td>{{ $userData->arbitrationed_count }}</td>
                            <td>{{ number_format($userData->profit, 2) ?? '--' }}</td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="10">暂无</td>
                        </tr>
                    @endforelse
                @else
                    <tr>
                        <td colspan="10">暂无</td>
                    </tr>
                @endif
                @if(! empty($totalDatas))
                <tr style="color: red;">
                        <td>总计:  {{ $totalDatas->creator_count }}</td>
                        <td>{{ $totalDatas->count ?? 0 }}</td>
                        <td>{{ number_format($totalDatas->original_price, 2) }}</td>
                        <td>{{ number_format($totalDatas->price, 2) }}</td>
                        <td>{{ number_format($totalDatas->diff_price, 2) }}</td>
                        <td>{{ $totalDatas->complete_count ?? 0 }}</td>
                        <td>{{ number_format($totalDatas->complete_price, 2) ?? '--' }}</td>
                        <td>{{ $totalDatas->revoked_count ?? 0 }}</td>
                        <td>{{ $totalDatas->arbitrationed_count ?? 0 }}</td>
                        <td>{{ number_format($totalDatas->profit, 2) ?? '--' }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
        </form>
    </div>
        <div id="render" lay-count="{{ $total->count ?? 0 }}" lay-page="{{ $page ?? 0 }}"></div>
    </div>
</div>
@endsection
<!--START 底部-->
@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate', 'laypage'], function(){
            var laydate = layui.laydate;
            var form = layui.form;
            var laypage = layui.laypage;
            //常规用法
            laydate.render({
                elem: '#start_date'
            });

            //常规用法
            laydate.render({
                elem: '#end_date'
            });
           
            var count = document.getElementById('render').getAttribute('lay-count');
        });
    </script>
@endsection