@extends('frontend.steam.steambatchcard.layouts.app')
@section('title', "封号记录")
@section('css')

@endsection
@section('content')
<div class="out-wrap relative">

    <div class="cm-wrap" style="width:90%">
        <!-- 表单 -->
        <div class="layui-form">
            <table class="layui-table">
                <thead>
                <tr>
                    <th>序号</th>
                    <th>封号账号</th>
                    <th>封号游戏</th>
                    <th>SteamId</th>
                    <th>最后使用时间</th>
                    <th>封号时间</th>
                    <th>余额</th>
                    <th>是否启用</th>
                    <th>是否使用中</th>
                    <th>账号验证类型</th>
                    <th>供应商</th>
                </tr>
                </thead>
                <tbody>
                @if($data)
                    @foreach(collect($data) as $item)
                        <tr id="{{ $item->Tb_id }}">
                            <td>{{ $item->Tb_id }}</td>
                            <td>{{ $item->Account }}</td>
                            <td>{{ $item->GameName }}</td>
                            <td>{{ $item->SteamId }}</td>
                            <td>{{ $item->LastUseTime }}</td>
                            <td>{{ $item->InsertTime }}</td>
                            <td>{{ $item->Balance }}</td>
                            <td>
                                @if($item->UsingState == '0')
                                    未启用
                                @elseif($item->UsingState == '1')
                                    启用
                                @elseif($item->UsingState == '2')
                                    禁用
                                @endif
                            </td>
                            <td>{{ $item->IsUsing == 'False' ? "未使用" : "使用中"}}</td>
                            <td>
                                @if($item->AuthType == '0')
                                    正常
                                @elseif($item->AuthType == '1')
                                    密码错误
                                @elseif($item->AuthType == '2')
                                    邮箱认证
                                @elseif($item->AuthType == '3')
                                    手机令牌
                                @else
                                    未知错误
                                @endif
                            </td>
                            <td>{{ $item->Supplier }}</td>
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

                            <div class="layui-input-inline" style="width: 150px;">
                                <input type="text" name="GameName" placeholder="封号游戏" autocomplete="off"
                                       class="layui-input" value="{{ Request::input('GameName') }}">
                            </div>

                            <div class="layui-input-inline" style="width: 150px;">
                                <input type="text" name="Supplier" placeholder="供应商" autocomplete="off"
                                       class="layui-input" value="{{ Request::input('Supplier') }}">
                            </div>
                            <button class="layui-btn" lay-submit="" lay-filter="account">查询</button>
                        </form>
                    </div>
                </div>
            </table>
        </div>
        <div style="margin-top:15px">
            <a href="{{url('card/seal?export=1&'.http_build_query(Request::all()))}}" class="btn btn-save add-group" style="height: 15px;line-height: 15px;">导出</a>
            <button class="btn btn-save fr" style="height: 34px;line-height: 13px;"><span>总数：{{$count}}</span></button>
            <button class="btn btn-save fr" style="height: 34px;line-height: 13px;"><span>总页数：{{$totalPage}}</span>
            </button>
            <button class="btn btn-save fr" style="height: 34px;line-height: 13px;">
                <span>当前页：{{Request::input('page')?:1}}</span></button>
            <button class="btn btn-save fr" style="height: 34px;line-height: 13px;"><span>每页显示：{{$pageSize}}</span>
            </button>
        </div>

        <!-- 分页 -->
        <div class="pageCounts overflow right" id="tcdPageCode" style="margin: 15px -15px 15px 0;">

        </div>
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
                location.href = '?page=' + e.curr + '&Account=' + "{{Request::input('Account')}}" + '&GameName=' + "{{Request::input('GameName')}}" + '&Supplier=' + "{{Request::input('Supplier')}}";
            }
        }
    });

    // 是否显示分页控件
    if (TOTAL_PAGE == 0 || TOTAL_PAGE == 1) {
        $('.pageCounts').addClass('none');
    }



</script>
@endsection