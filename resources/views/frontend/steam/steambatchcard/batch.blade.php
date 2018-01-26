@extends('frontend.steam.steambatchcard.layouts.app')
@section('title', "账号列表")
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
                        <th><input type="checkbox" name="ids" lay-skin="primary" lay-filter="allChoose"></th>
                        <th>序号</th>
                        <th>账号</th>
                        <th>当前余额</th>
                        <th>供应商</th>
                        <th>商户号</th>
                        <th>SteamId</th>
                        <th>限制时间</th>
                        <th>最后使用时间</th>
                        <th>是否在使用中</th>
                        <th>导入时间</th>
                        <th>AuthType</th>
                        <th>优先级</th>
                        <th>是否限制</th>
                        <th width="100px;">是否禁用</th>
                        <th>修改密码</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($dataList)
                        <?php $i = 0; $total = 0;?>
                        @foreach($dataList as $item)
                            <tr data-id="{{ $item->Tb_id }}">
                                <input type="hidden" name="id" id="idsString" value="">
                                <input type="hidden" name="id" data-tbid="{{ $item->Tb_id }}" value="{{ $item->Tb_id }}">
                                <td><input type="checkbox" class="checked_id" value="{{ $item->Tb_id }}" name="tb_id[]"
                                           lay-filter="choose" lay-skin="primary"></td>
                                <td>{{ $item->Tb_id }}</td>
                                <td>{{ $item->Account }}</td>
                                <td><input type="text" name="balance" value="{{ $item->Balance }}" autocomplete="off"
                                           class="layui-input" style="width: 80px;"></td>
                                <td>{{ $item->Supplier }}</td>
                                <td>{{ $item->TraderId }}</td>
                                <td>{{ $item->SteamId }}</td>
                                <td>{{ $item->LimitTime}}</td>
                                <td>{{ $item->LastUseTime}}</td>
                                <td>
                                    <div class="layui-input-block" style="margin-left: 0px;">
                                        <select name="isUsing" id="isUsing" lay-verify="" lay-filter="isUsing">
                                            <option value="0" @if($item->IsUsing == 'False') selected @endif>未使用</option>
                                            <option value="1" @if($item->IsUsing =='True') selected @endif>使用中</option>
                                        </select>
                                    </div>
                                </td>
                                <td>{{ $item->ImportTime }}</td>
                                <td>
                                    <div class="layui-input-block" style="margin-left: 0px;">
                                        <select name="authType" id="authType" lay-verify="" lay-filter="authType">
                                            <option value="0" @if($item->AuthType == '0') selected @endif>正常</option>
                                            <option value="1" @if($item->AuthType == '1') selected @endif>密码错误</option>
                                            <option value="2" @if($item->AuthType == '2') selected @endif>邮箱认证</option>
                                            <option value="3" @if($item->AuthType == '3') selected @endif>手机令牌</option>
                                            <option value="4" @if($item->AuthType == '4') selected @endif>未知错误</option>
                                        </select>
                                    </div>

                                </td>
                                <td>
                                    <div class="layui-input-block" style="margin-left: 0px;">
                                        <select name="priority" id="priority" lay-verify="" lay-filter="priority"
                                                data-id="{{ $item->Tb_id }}" data-status="{{ $item->UsingState }}">
                                            <option value="0">请选择</option>
                                            <option value="100" @if($item->Priority == '100') selected @endif>高</option>
                                            <option value="50" @if($item->Priority == '50') selected @endif>中</option>
                                            <option value="1" @if($item->Priority == '1') selected @endif>低</option>
                                        </select>
                                    </div>
                                </td>
                                <td>{{ $item->IsInlimit == 'False' ? "未限制" : "被限制"}}</td>
                                <td>
                                    <div class="layui-input-block" style="margin-left: 0px;">
                                        <select name="status" id="status" lay-verify="" lay-search="" lay-filter="status"
                                                data-id="{{ $item->Tb_id }}" data-priority="{{ $item->Priority }}">
                                            <option value="0" @if($item->UsingState == '0') selected @endif>未启用</option>
                                            <option value="1" @if($item->UsingState=='1') selected @endif>启用</option>
                                            <option value="2" @if($item->UsingState=='2') selected @endif>禁用</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <button class="layui-btn btn-update-pwd">修改</button>
                                </td>
                            </tr>
                            <?php $i++; $total += $item->Balance;?>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td colspan="1">合计</td>
                            <td>{{ number_format($total,4) }}</td>
                            <td colspan="10"></td>
                        </tr>
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
                                    <input type="text" name="Supplier" placeholder="供应商" autocomplete="off"
                                           class="layui-input" value="{{ Request::input('Supplier') }}">
                                </div>

                                <div class="layui-input-inline">
                                    <select name="IsInlimit">
                                        <option value="-1" @if(Request::input('IsInlimit') == "-1") selected @endif>是否限制
                                        </option>
                                        <option value="0" @if(Request::input('IsInlimit') == '0') selected @endif>未限制
                                        </option>
                                        <option value="1" @if(Request::input('IsInlimit') == '1') selected @endif>被限制
                                        </option>
                                    </select>
                                </div>

                                <div class="layui-input-inline">
                                    <select name="Priority">
                                        <option value="-1" @if(Request::input('Priority') == "-1") selected @endif>优先级(所有)
                                        </option>
                                        <option value="100" @if(Request::input('Priority') =='100') selected @endif>高(100)
                                        </option>
                                        <option value="50" @if(Request::input('Priority') =='50') selected @endif>中(50)
                                        </option>
                                        <option value="1" @if(Request::input('Priority') =='1') selected @endif>低(1)
                                        </option>
                                        <option value="0" @if(Request::input('Priority') =='0') selected @endif>默认(0)
                                        </option>
                                    </select>
                                </div>

                                <div class="layui-input-inline">
                                    <select name="IsUsing">
                                        <option value="-1" @if(Request::input('IsUsing') == "-1") selected @endif>是否在使用中
                                        </option>
                                        <option value="0" @if(Request::input('IsUsing') =='0') selected @endif>未使用</option>
                                        <option value="1" @if(Request::input('IsUsing') =='1') selected @endif>使用中</option>
                                    </select>
                                </div>

                                <div class="layui-input-inline">
                                    <select name="AuthType">
                                        <option value="-1" @if(Request::input('AuthType') == "-1") selected @endif>账号验证类型
                                        </option>
                                        <option value="0" @if(Request::input('AuthType') =='0') selected @endif>正常</option>
                                        <option value="1" @if(Request::input('AuthType') =='1') selected @endif>密码错误
                                        </option>
                                        <option value="2" @if(Request::input('AuthType') =='2') selected @endif>邮箱认证
                                        </option>
                                        <option value="3" @if(Request::input('AuthType') =='3') selected @endif>手机令牌
                                        </option>
                                        <option value="4" @if(Request::input('AuthType') =='4') selected @endif>未知错误
                                        </option>
                                    </select>
                                </div>

                                <div class="layui-input-inline">
                                    <select name="IsUsed">
                                        <option value="-1" @if(Request::input('IsUsed') =='-1') selected @endif>是否被使用过
                                        </option>
                                        <option value="0" @if(Request::input('IsUsed') == '0') selected @endif>未使用</option>
                                        <option value="1" @if(Request::input('IsUsed') =='1') selected @endif>已经使用</option>
                                    </select>
                                </div>

                                <div class="layui-input-inline">
                                    <select name="UsingState">
                                        <option value="-1" @if(Request::input('UsingState') =='-1') selected @endif>是否启用
                                        </option>
                                        <option value="0" @if(Request::input('UsingState') =='0') selected @endif>未启用
                                        </option>
                                        <option value="1" @if(Request::input('UsingState') =='1') selected @endif>启用
                                        </option>
                                        <option value="2" @if(Request::input('UsingState') =='2') selected @endif>禁用
                                        </option>
                                    </select>
                                </div>

                                <button class="layui-btn" lay-submit="" lay-filter="account">查询</button>
                            </form>
                        </div>
                    </div>


                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <button class="layui-btn" lay-submit="" lay-filter="all">批量启用</button>
                        </div>
                    </div>

                </table>
            </div>
            <div style="margin-top:15px">
                <button class="btn btn-save add-group" style="height: 34px;line-height: 13px;">导入Excel</button>
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

    <div class="add-goods-group " style="padding: 15px;display: none">
        <form class="layui-form layui-form-pane" action="">
            <input type="hidden" name="fileExcel" value=""/>
            <div class="layui-form-item">
                <label class="layui-form-label">请上传excel</label>
                <div class="layui-input-block">
                    <input type="file" id="file-excel" name="file" autocomplete="off" placeholder="账号" lay-verify="required"
                           style="margin-left: 20px;">
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn" lay-submit="" lay-filter="save">导入</button>
                <a href="{{asset('frontend/excel/steam账号导入.xlsx')}}" class="" style="color:red; padding-left: 20px;">下载excel模板</a>
            </div>
        </form>
    </div>

    <div class="update-pwd" style="padding: 15px;display: none">
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">密码</label>
                <div class="layui-input-block">
                    <input type="text" name="password" autocomplete="off" placeholder="请输入密码" class="layui-input" lay-verify="required">
                </div>
            </div>
            <div class="layui-form-item">
                <input type="hidden" name="id">
                <button class="layui-btn" lay-submit="" lay-filter="save-pwd">保存</button>
            </div>
        </form>
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
                    location.href = '?page=' + e.curr + '&Account=' + "{{Request::input('Account')}}" + '&Supplier=' + "{{Request::input('Supplier')}}" + '&IsInlimit=' + "{{Request::input('IsInlimit')}}" + '&Priority=' + "{{Request::input('Priority')}}" + '&IsUsing=' + "{{Request::input('IsUsing')}}" + '&AuthType=' + "{{Request::input('AuthType')}}" + '&IsUsed=' + "{{Request::input('IsUsed')}}" + '&UsingState=' + "{{Request::input('UsingState')}}";
                }
            }
        });

        // 是否显示分页控件
        if (TOTAL_PAGE == 0 || TOTAL_PAGE == 1) {
            $('.pageCounts').addClass('none');
        }

        layui.use(['layer', 'form'], function () {
            var $ = layui.jquery, layer = layui.layer, form = layui.form, laypage = layui.laypage;

            // 弹出层
            $('.add-group').on('click', function () {
                layer.open({
                    type: 1,
                    title: '新增账号',
                    closeBtn: 2,
                    skin: 'layui-layer-molv',
                    area: ['600', '350'],
                    shift: 4,
                    moveType: 2,
                    shadeClose: false,
                    content: $('.add-goods-group')
                });
            });

            // 弹出层
            $('.btn-update-pwd').on('click', function () {
                var id = $(this).parents('tr').attr('data-id');
                $('.update-pwd [name="id"]').val(id);
                layer.open({
                    type: 1,
                    title: '修改密码',
                    closeBtn: 2,
                    skin: 'layui-layer-molv',
                    shift: 4,
                    moveType: 2,
                    shadeClose: false,
                    content: $('.update-pwd')
                });
            });


            // 修改金额
            $('input[name="balance"]').change(function () {
                var id = $(this).parents('tr').attr('data-id');
                var balance = $(this).val()
                layer.confirm('是否修改金额', {icon: 7, title: '修改金额'}, function (index) {
                    $.post('{{ url("steam/card/balance") }}', {id: id, balance: balance}, function (result) {
                        layer.msg(result.message)
                    }, 'json');
                });
                return false;
            });

            form.on('submit(save-pwd)',function (data) {
                var groupInfo = JSON.stringify(data.field);
                layer.confirm('是否修改密码', {icon: 7, title: '修改密码'}, function (index) {
                    $.post('{{ url("steam/card/updatePwd") }}', {data: groupInfo}, function (result) {
                        layer.msg(result.message)
                    }, 'json');
                });
                return false;
            })


            //监听提交
            form.on('submit(save)', function (data) {
                var groupInfo = JSON.stringify(data.field);
                var index = layer.load(3, {
                    shade: [0.1, '#fff'] //0.1透明度的白色背景
                });
                $.post('{{ url("steam/card/import-card") }}', {data: groupInfo}, function (result) {
                    layer.close(index);
                    if (result.status == 1) {
                        layer.msg(result.message)
                        setTimeout(function () {
                            location.reload()
                        }, 3000);
                    }
                    layer.msg(result.message)
                }, 'json');
                return false;
            });

            // 发送使用状态
            form.on('select(isUsing)', function (data) {
                var id = $(this).parents('tr').attr('data-id');
                var isUsing = data.value;
                $.post('{{ url("steam/card/updateIsUsing") }}', {
                    id: id,
                    isUsing: isUsing
                }, function (result) {
                    layer.msg(result.message)
                }, 'json');
                return false;
            });

            // 发送AuthType状态
            form.on('select(authType)', function (data) {
                var id = $(this).parents('tr').attr('data-id');
                var authType = data.value;
                $.post('{{ url("steam/card/updateAuthType") }}', {
                    id: id,
                    authType: authType
                }, function (result) {
                    layer.msg(result.message)
                }, 'json');
                return false;
            });


            // 发送状态
            form.on('select(status)', function (data) {
                var id = data.elem.getAttribute('data-id');
                var priority = data.elem.getAttribute('data-priority');
                $.get('{{ url("steam/card/send/status") }}', {
                    status: data.value,
                    id: id,
                    priority: priority
                }, function (result) {
                    layer.msg(result.message)
                }, 'json');
                return false;
            });

            // 发送优先级
            form.on('select(priority)', function (data) {
                var id = data.elem.getAttribute('data-id');
                var status = data.elem.getAttribute('data-status');
                console.log(status);
                console.log(id);
                console.log(data.value);
                $.get('{{ url("steam/card/send/status") }}', {
                    priority: data.value,
                    id: id,
                    status: status
                }, function (result) {
                    layer.msg(result.message)
                }, 'json');
                return false;
            });

            // 全选
            form.on('checkbox(allChoose)', function (data) {
                var child = $(data.elem).parents('table').find('tbody .checked_id');
                child.each(function (index, item) {
                    item.checked = data.elem.checked;
                });
                form.render('checkbox');
            })

            //监听提交
            form.on('submit(save-paragraph)', function (data) {
                var groupInfo = JSON.stringify(data.field);
                console.log(groupInfo);
                return false;
            });

            // 批量启用
            form.on('submit(all)', function (data) {

                var length = $(".checked_id:checked").length;
                if (length == 0) {
                    layer.msg('请至少选择一条数据')
                    return false;
                }
                var checked_id = $(".checked_id:checked").serialize();
                $.post('{{url("steam/card/all")}}', checked_id, function (result) {
                    layer.msg(result.message)
                }, 'json');
                return false;
            })
        });

    </script>
@endsection