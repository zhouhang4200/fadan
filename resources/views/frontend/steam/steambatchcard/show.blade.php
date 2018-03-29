@extends('frontend.steam.steambatchcard.layouts.app')
@section('title', "赠送记录")
@section('css')

@endsection
@section('content')
<div class="out-wrap relative">

    <div class="cm-wrap" style="width:90%">
        <!-- 表单 -->
        <div class="layui-form">
            <table class="layui-table" >
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>赠送账号</th>
                        <th>赠送时间</th>
                        <th>游戏名字</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($data)
                    @foreach(collect($data) as $item)
                    <tr id="{{ $item->Tb_id }}">
                        <td>{{ $item->Tb_id }}</td>
                        <td>{{ $item->Account }}</td>
                        <td>{{ $item->GiveTime }}</td>
                        <td>{{ $item->GameName }}</td>
                    </tr>
                    @endforeach
                    @endif
                    </tbody>
                    <div class="common-boxs" style="margin-bottom: 15px;">
                        <div class="layui-form layui-form-pane vip-form">
                            <form class="layui-form-item" id="form-search">
                                <div class="layui-input-inline" style="width: 150px;">
                                    <input type="text" name="Account" placeholder="账号" autocomplete="off"
                                           class="layui-input" value="{{ Request::input('Account') }}">
                                </div>
                                <button class="layui-btn" lay-submit="" lay-filter="account">查询</button>
                            </form>
                        </div>
                    </div>
            </table>
        </div>
        <div style="margin-top:15px">
            <button class="btn btn-save" style="height: 34px;line-height: 13px;"><span>总数：{{$count}}</span></button>
            <button class="btn btn-save" style="height: 34px;line-height: 13px;"><span>总页数：{{$totalPage}}</span>
            </button>
            <button class="btn btn-save" style="height: 34px;line-height: 13px;">
                <span>当前页：{{Request::input('page')?:1}}</span></button>
            <button class="btn btn-save" style="height: 34px;line-height: 13px;"><span>每页显示：{{$pageSize}}</span>
            </button>
        </div>
        <div class="pageCounts overflow right" id="tcdPageCode" style="margin: 15px -15px 15px 0;"></div>
    </div>
</div>

@endsection

@section('js')
<script>
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
    var TOTAL_PAGE = "{{ $totalPage }}";
    laypage({
        cont: 'tcdPageCode',
        skin: 'molv',
        skip: true,
        pages: "{{ $totalPage }}",
        curr: function () {
            var page = location.search.match(/page=(\d+)/);
            return page ? page[1] : 1;
        }(),
        jump: function (e, first) { //触发分页后的回调
            if (!first) { //一定要加此判断，否则初始时会无限刷新
                location.href = '?page=' + e.curr + '&Account=' + "{{Request::input('Account')}}";
            }
        }
    });

    // 是否显示分页控件
    if (TOTAL_PAGE == 0 || TOTAL_PAGE == 1) {
        $('.pageCounts').addClass('none');
    }
    layui.use(['layer', 'form'], function () {
        var $ = layui.jquery, layer = layui.layer, form = layui.form, laypage = layui.laypage;

    });
</script>
@endsection