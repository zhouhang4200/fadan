@extends('frontend.v1.layouts.app')

@section('title', '个人资料')

@section('css')
<style>
    .user-info{
        /*width: 970px;*/
        /*width: 1370px;*/
        height: 200px;
        border: 1px solid #ddd;
        display: flex;
        margin: auto;
        font-size: 13px;
    }
    .info-img{
        width: 80px;
        height: 80px;
        margin: 20px 0 20px 20px;
        border: 1px solid #ddd;
        background-size: 102%;
        background-image: url('/frontend/images/3.png');
    }
    .info-left{
        flex: 3;
        margin-top: 15px;
    }
    .layui-form-item{
        margin-bottom: 0px;
        margin: 5;
    }
    .info-left .layui-form-item  .layui-inline .layui-input-inline{
        width: auto;
        text-indent: 20px;
    }
    .info-left .layui-form-item .layui-form-label{
        width: 85px;
        padding: 0;
        height: 25px;
        line-height: 30px;
    }
    .info-left .layui-form-item  .layui-inline{
        width: 250px;
        height: 25px;
        line-height: 30px;
    }
    .info-balance{
        flex: 1.2;
        height: 100px;
        margin: 8px 0 0 30px;
        position: relative;
    }
    .info-balance .available-balance{
        height: 33px;
        line-height: 34px;
    }
    .info-balance .blocked-balances{
        height: 33px;
        line-height: 33px;
    }
    .info-balance::before{
        content: "";
        position: absolute;
        left: -20px;
        top:20px;
        width: 1px;
        height: 70px;
        background-color: #ddd;
    }
    .icon{
        margin-left: 34px;
    }
    .icon > span + span{
        margin-left: 10px;
    }
    .info-left .layui-form-item .layui-inline {
        line-height: 17px;
    }
</style>
@endsection

@section('main')
    <div class="layui-card qs-text">
        <div class="layui-card-body">
            <div class="user-info" style="height: 200px;">
                <div  id="user-img" class="info-img fl"
                     style="float:left;width:80px;height:80px;background-image:url('{{ $user->voucher }}');background-size: cover !important;background-position: center !important;margin-bottom:3px;">
                    <button class="qs-btn layui-btn-normal layui-btn-mini" id="voucher-user"
                            style="width:100%;padding:0;margin-top:85px;">修改头像
                    </button>
                    @if(Auth::user()->parent_id == 0)
                        <a class="qs-btn layui-btn-normal layui-btn-mini" id="persional-user" href="{{ route('users.persional') }}"
                                style="width:100%;margin-top:5px;margin-left:0; padding:0">修改资料
                        </a>
                    @endif
                </div>
                <div class="info-left">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">账号 ：</label>
                            <div class="layui-input-inline">
                                {{ $user->name }}
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">主账号ID ：</label>
                            <div class="layui-input-inline">
                                {{ Auth::user()->getPrimaryUserId() }}
                            </div>
                        </div>

                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">类型 ：</label>
                            <div class="layui-input-inline">
                                @if ($user->parent_id == 0)
                                    主账号
                                @else
                                    子账号
                                @endif
                            </div>
                        </div>
                        <div class="layui-inline" style="width:270px;">
                            <label class="layui-form-label">最后登录 ：</label>
                            <div class="layui-input-inline" style="margin-right:0;">
                                {{ $loginHistoryTime }}
                            </div>
                        </div>
                    </div>
                    @if ($user->parent_id == 0)
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">昵称 ：</label>
                                <div class="layui-input-inline">
                                    {{ $user->username }}
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">年龄 ：</label>
                                <div class="layui-input-inline">
                                    {{ $user->age }}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">QQ ：</label>
                                <div class="layui-input-inline">
                                    {{ $user->qq }}
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">微信 ：</label>
                                <div class="layui-input-inline">
                                    {{ $user->wechat }}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">电话 ：</label>
                                <div class="layui-input-inline">
                                    {{ $user->phone }}
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">店铺旺旺号 ：</label>
                                <div class="layui-input-inline">
                                    {{ $user->store_wang_wang }}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">实名认证 ：</label>
                                <div class="layui-input-inline" style="margin-right:0;">
                                    @if ($ident && $ident->status == 1)
                                        已实名认证
                                    @elseif ($ident && $ident->status == 2)
                                        实名认证未通过
                                    @elseif (! $ident && $user->parent_id == 0)
                                        <a href="{{ route('idents.create') }}" style="color:#707070">未实名认证</a>
                                    @else
                                        未实名认证
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="info-balance ">
                    <div class="available-balance">可用余额：
                        <span class="balance">{{ $user->userAsset->balance + 0 }}</span>
                    </div>
                    <div class="blocked-balances">冻结金额：
                        {{ $user->userAsset->frozen + 0 }}
                    </div>
                    <div class="blocked-balances">代练金额：
                        {{ \App\Models\Order::ingOrderAmount() + 0 }}
                    </div>
                    <div class="blocked-balances">代练双金：
                        {{ \App\Models\Order::ingOrderDeposit() + 0 }}
                    </div>
                    <div class="blocked-balances">剩余短信：
                        <span id="sms-balance">{{ optional($user->smsBalance)->amount + 0 }}</span>
                    </div>
                    <button class="qs-btn layui-btn-normal layui-btn-custom-mini charge" lay-filter="charge" lay-submit="">余额充值</button>
                    <button id="withdraw" class="qs-btn qs-btn-normal qs-btn-custom-mini" type="button" >余额提现</button>
                    <button id="sms-recharge" class="qs-btn qs-btn-normal qs-btn-custom-mini" type="button" >短信充值</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pop')
<div id="persional" style="display: none; padding: 10px">
    <form class="layui-form"  action="">
        <div>
            <div class="layui-form-item">
                <label class="layui-form-label">昵称:</label>
                <div class="layui-input-inline">
                    <input type="text" name="nick_name" lay-verify="required|length" value="{{ $parentUser->nick_name }}" autocomplete="off" placeholder="请输入账号" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">年龄:</label>
                <div class="layui-input-inline">
                    <input type="text" name="age" lay-verify="required|length" value="{{ $parentUser->age }}" autocomplete="off" placeholder="请输入账号" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">QQ:</label>
                <div class="layui-input-inline">
                    <input type="text" name="qq" lay-verify="required|length" value="{{ $parentUser->qq }}" autocomplete="off" placeholder="请输入账号" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">微信:</label>
                <div class="layui-input-inline">
                    <input type="text" name="wechat" lay-verify="required|length" value="{{ $parentUser->wechat }}" autocomplete="off" placeholder="请输入账号" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">电话:</label>
                <div class="layui-input-inline">
                    <input type="text" name="phone" lay-verify="required|length" value="{{ $parentUser->phone }}" autocomplete="off" placeholder="请输入账号" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">旺旺号:</label>
                <div class="layui-input-inline">
                    <input type="text" name="wangwang" lay-verify="required|length" value="{{ $parentUser->wangwang }}" autocomplete="off" placeholder="请输入账号" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-inline">
                    <button type="hidden" class="qs-btn layui-btn-normal" lay-submit="" lay-filter="update-persional" style="margin-left: 180px">提交</button>
                </div>
            </div>
        </div>
    </form>
</div>
<div id="voucher" style="display: none; padding: 10px">
    <form class="layui-form"  action="">
            <div class="layui-upload">
                <button type="button" class="qs-btn layui-btn-normal" id="test1">上传图片</button>
                <div class="layui-upload-list">
                <img class="layui-upload-img" id="demo1" style="width:200px;height:200px">
                    <p id="demoText"></p>
                </div>
            </div>
            <input type="hidden" name="voucher" id="voucher-img" value="">
            <div class="layui-form-item">
                <div class="layui-input-inline">
                    <button type="hidden" class="qs-btn layui-btn-normal" lay-submit="" lay-filter="update-voucher">提交</button>
                </div>
            </div>
        </form>
</div>
@endsection

@section('js')
<script>
    layui.use(['form', 'table', 'upload'], function(){
        var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
        var layer = layui.layer;
        var upload = layui.upload;
        // 验证
        form.verify({
            length: [
                /^\S{1,30}$/
                ,'长度超出允许范围'
            ]
            ,pass: [
                /^[\S]{6,12}$/
                ,'密码必须6到12位，且不能出现空格'
            ]
        });
        //普通图片上传
        var uploadInst = upload.render({
            elem: '#test1'
            ,url: "{{ route('users.upload-images') }}"
            ,before: function(obj){
              //预读本地文件示例，不支持ie8
              obj.preview(function(index, file, result){
                $('#demo1').attr('src', result); //图片链接（base64）

              });
            }
            ,done: function(res){
              if(res.code == 2){
                return layer.msg('上传失败');
              }
              $('#voucher-img').val(res.path);
            }
        });
        form.on('radio()', function(data){
            if (data.value == '支付宝') {
                $('#bank').addClass('layui-hide');
                $('#alipay').removeClass('layui-hide');
            } else {
                $('#alipay').addClass('layui-hide');
                $('#bank').removeClass('layui-hide');
            }
        });
        form.on('submit(update-persional)', function(data) {
            $.post("{{ route('users.update-persional') }}", {data:data.field}, function (result) {
                if (result.code == 1) {
                    layer.msg(result.message, {
                        time:1500,
                        icon:6
                    })
                } else {
                    layer.msg(result.message, {
                        time:1500,
                        icon:5
                    })
                }
            });
            layer.closeAll();
            window.location.href="{{ route('users.persional') }}";
            return false;
        });
         // 修改头像
        $('#voucher-user').on('click', function () {
            layer.open({
                type: 1,
                shade: 0.2,
                title: '修改头像',
                area: ['250px', '370px'],
                content: $('#voucher')
            });
        });

        form.on('submit(update-voucher)', function(data) {
            $.post("{{ route('users.update-voucher') }}", {data:data.field}, function (result) {
                if (result.code == 1) {
                    layer.msg(result.message, {
                        time:1500,
                        icon:6
                    });
                     var styles="url('"+result.path+"')";
                    $('#user-img').css('background-image', styles);
                } else {
                    layer.msg(result.message, {
                        time:1500,
                        icon:5
                    })
                }
            });
            layer.closeAll();
            return false;
        });

        form.on('submit(charge)', function () {
            layer.open({
                type: 1,
                title: '提示',
                area: '400px;',
                shade: 0.2,
//                btn: ['确定'],
//                btnAlign: 'c',
                content: $('#charge-pop')
            });
        });

        var notice = "{{ session('notices') }}";

        if (notice) {
            layer.open({
                type: 1
                ,title: '提示' //不显示标题栏
                ,closeBtn: false
                ,area: '300px;'
                ,shade: 0.8
                ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
                ,btn: ['确定']
                ,btnAlign: 'c'
                ,moveType: 1 //拖拽模式，0或者1
                ,content: '<div style="padding: 150px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;">你知道吗？亲！<br>layer ≠ layui<br><br>layer只是作为Layui的一个弹层模块，由于其用户基数较大，所以常常会有人以为layui是layerui<br><br>layer虽然已被 Layui 收编为内置的弹层模块，但仍然会作为一个独立组件全力维护、升级。<br><br>我们此后的征途是星辰大海 ^_^</div>'
            });
        }
    });
</script>
@endsection