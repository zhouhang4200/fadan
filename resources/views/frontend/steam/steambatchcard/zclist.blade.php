@extends('frontend.steam.steambatchcard.layouts.app')
@section('title', "Steam直充")
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
                    <th>steam卡ID</th>
                    <th>订单号</th>
                    <th>Steamid</th>
                    <th>使用时间</th>
                    <th>商品价格</th>
                    <th>进货商</th>
                    <th>商品名称</th>
                    <th>使用状态</th>
                    <th>操作</th>
                    <th>密码</th>
                    <th>描述</th>
                </tr>
                </thead>
                <tbody>
                @if($data)
                    @foreach($data as $item)
                        <tr data-id="{{ $item->Tb_id }}">
                            <td>{{ $item->Tb_id }}</td>
                            <td>{{ $item->SteamCardId }}</td>
                            <td>{{ $item->OrderId }}</td>
                            <td>{{ $item->Steamid }}</td>
                            <td>{{ $item->InsertTime }}</td>
                            <td>{{ $item->ProductPrice }}</td>
                            <td>{{ $item->Jsitid }}</td>
                            <td>{{ $item->ProductName }}</td>
                            <td>
                                @if($item->UseState == null)
                                    未知
                                @elseif($item->UseState == '1')
                                    已使用
                                @elseif($item->UseState == '2')
                                    手动操作
                                @endif
                            </td>
                            <td>
                                <div class="layui-input-block" style="margin-left: 0px;">
                                    <select name="useState"  lay-verify="" lay-filter="useState"
                                            data-id="{{ $item->Tb_id }}" data-steamCardId="{{$item->SteamCardId}}" data-isNull="{{$item->UseState}}">
                                        <option value="0" @if($item->UseState == '0') selected @endif>还原</option>
                                        <option value="1" @if($item->UseState == '1') selected @endif>已使用</option>
                                    </select>
                                </div>
                            </td>
                            <td>{{ $item->Psw }}</td>
                            <td>{{ $item->Desc }}</td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
                <div class="common-boxs" style="margin-bottom: 15px;">
                    <div class="layui-form layui-form-pane vip-form">
                        <form class="layui-form-item" id="form-search">
                            <div class="layui-input-inline" style="width: 150px;">
                                <input type="text" name="SteamCardId" placeholder="steam卡id" autocomplete="off"
                                       class="layui-input" value="{{ Request::input('SteamCardId') }}">
                            </div>

                            <div class="layui-input-inline" style="width: 150px;">
                                <input type="text" name="OrderId" placeholder="订单号" autocomplete="off"
                                       class="layui-input" value="{{ Request::input('OrderId') }}">
                            </div>

                            <div class="layui-input-inline" style="width: 150px;">
                                <input type="text" name="Jsitid" placeholder="进货商" autocomplete="off"
                                       class="layui-input" value="{{ Request::input('Jsitid') }}">
                            </div>

                            <div class="layui-input-inline" style="width: 150px;">
                                <input type="text" name="ProductName" placeholder="商品名称" autocomplete="off"
                                       class="layui-input" value="{{ Request::input('ProductName') }}">
                            </div>

                            <div class="layui-input-inline">
                                <select name="UseState">
                                    <option value="-1" @if(Request::input('UseState') =='-1') selected @endif>使用状态

                                    <option value="0" @if(Request::input('UseState') =='0') selected @endif>未知
                                    </option>
                                    <option value="1" @if(Request::input('UseState') =='1') selected @endif>已使用
                                    </option>
                                    <option value="2" @if(Request::input('UseState') =='2') selected @endif>手动操作
                                    </option>
                                </select>
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
                location.href = '?page=' + e.curr + '&SteamCardId=' + "{{Request::input('SteamCardId')}}" + '&OrderId=' + "{{Request::input('OrderId')}}" + '&Jsitid=' + "{{Request::input('Jsitid')}}" + '&ProductName=' + "{{Request::input('ProductName')}}" + '&UseState=' + "{{Request::input('UseState')}}";
            }
        }
    });

    // 是否显示分页控件
    if (TOTAL_PAGE == 0 || TOTAL_PAGE == 1) {
        $('.pageCounts').addClass('none');
    }

    layui.use(['layer', 'form'], function () {
        var $ = layui.jquery, layer = layui.layer, form = layui.form, laypage = layui.laypage;

        // 发送状态
        form.on('select(useState)', function (data) {
            var id = $(this).parents('tr').attr('data-id');
            var steamCardId = data.elem.getAttribute('data-steamCardId');
            var isNull = data.elem.getAttribute('data-isNull');
            if(isNull != ""){
                layer.msg('只有使用状态是未知才能操作')
                return false;
            }
            var value = data.value;
            layer.confirm('是否修改状态', {icon: 7, title: '修改'}, function (index) {
                $.post('{{ url("card/updateZhichongState") }}', {
                    id: id,
                    steamCardId: steamCardId,
                    value: value
                }, function (result) {
                    layer.msg(result.message)
                }, 'json');
            });
            return false;
        });

    });


</script>
@endsection