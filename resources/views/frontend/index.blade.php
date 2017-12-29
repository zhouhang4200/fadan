@extends('frontend.layouts.app')

@section('title', '首页')

@section('css')
<style>
    .user-info{
        /*width: 970px;*/
        width: 1370px;
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
        width: 80px;
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

@section('submenu')
    @include('frontend.submenu')
@endsection

@section('main')
<div class="user-info" style="height: 200px;">
    <div alt="" id="user-img" class="info-img fl" style="float:left;width:80px;height:80px;background-image:url('{{ $user->voucher }}');background-size: cover !important;background-position: center !important;margin-bottom:3px;" >
        <button class="layui-btn layui-btn-normal layui-btn-mini" id="voucher-user" style="width:100%;padding:0;margin-top:85px;">修改头像</button>
        @if(Auth::user()->parent_id == 0)
            <button class="layui-btn layui-btn-normal layui-btn-mini" id="persional-user"  style="width:100%;margin-top:5px;margin-left:0px; padding:0">修改资料</button>
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
                <label class="layui-form-label">年龄 ：</label>
                <div class="layui-input-inline">
                    {{ $user->age }}
                </div>
            </div>
            <div class="layui-inline" style="width:270px;">
                <label class="layui-form-label">QQ ：</label>
                <div class="layui-input-inline" style="margin-right:0;">
                    {{ $user->qq }}
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">微信 ：</label>
                <div class="layui-input-inline">
                    {{ $user->wechat }}
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">电话 ：</label>
                <div class="layui-input-inline">
                    {{ $user->phone }}
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-inline" >
                <label class="layui-form-label">旺旺号 ：</label>
                <div class="layui-input-inline" style="margin-right:0;">
                    {{ $user->store_wang_wang }}
                </div>
            </div>
            <div class="layui-inline" >
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
            {{ $user->userAsset->balance + 0 }}
        </div>
        <div class="blocked-balances">冻结余额：
            {{ $user->userAsset->frozen + 0 }}
         </div>

        <button class="layui-btn layui-btn-normal layui-btn-custom-mini">余额充值</button>
        @inject('withdraw', 'App\Services\Views\WithdrawService')
        {{ $withdraw->button('余额提现', 'layui-btn layui-btn-normal layui-btn-custom-mini') }}
    </div>
</div>
<div class="layui-tab layui-hide">
    <ul class="layui-tab-title">
        <li class="layui-this">昨日接单数据</li>
        <li class="">网站设置</li>
    </ul>
    <div class="layui-tab-content">
        <div class="layui-tab-item layui-show" >

        </div>
        <div class="layui-tab-item" >
            <table class="layui-table" lay-size="sm">
                <colgroup>
                    <col width="150">
                    <col width="200">
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th>昵称</th>
                    <th>加入时间</th>
                    <th>签名</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>贤心</td>
                    <td>2016-11-29</td>
                    <td>人生就像是一场修行</td>
                </tr>
                <tr>
                    <td>许闲心</td>
                    <td>2016-11-28</td>
                    <td>于千万人之中遇见你所遇见的人，于千万年之中，时间的无涯的荒野里…</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="persional" style="display: none; padding: 10px">
    <form class="layui-form" method="" action="">
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
                    <button type="hidden" class="layui-btn layui-btn-normal" lay-submit="" lay-filter="update-persional" style="margin-left: 180px">提交</button>
                </div>
            </div>
        </div>
    </form>
</div>
<div id="voucher" style="display: none; padding: 10px">
    <form class="layui-form" method="" action="">
        <div class="layui-upload">
            <button type="button" class="layui-btn layui-btn-normal" id="test1">上传图片</button>
            <div class="layui-upload-list">
            <img class="layui-upload-img" id="demo1" style="width:200px;height:200px">
                <p id="demoText"></p>
            </div>
        </div> 
        <input type="hidden" name="voucher" id="voucher-img" value="">
        <div class="layui-form-item">
            <div class="layui-input-inline">
                <button type="hidden" class="layui-btn layui-btn-normal" lay-submit="" lay-filter="update-voucher">提交</button>
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
                var styles="url('"+result+"')";
                $('#user-img').css('background-image', styles);
              });
            }
            ,done: function(res){
              if(res.code == 2){
                return layer.msg('上传失败');
              }
              $('#voucher-img').val(res.path);
            }
        });
        // 修改资料
        $('#persional-user').on('click', function () {
            window.location.href="{{ route('users.persional') }}";
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
                    })
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

        var notice = "{{ session('notice') }}";

        if (notice) {
            layer.open({
                type: 1
                ,title: false //不显示标题栏
                ,closeBtn: false
                ,area: '300px;'
                ,shade: 0.8
                ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
                ,btn: ['确定']
                ,btnAlign: 'c'
                ,moveType: 1 //拖拽模式，0或者1
                ,content: '<div style="padding: 50px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;">你知道吗？亲！<br>layer ≠ layui<br><br>layer只是作为Layui的一个弹层模块，由于其用户基数较大，所以常常会有人以为layui是layerui<br><br>layer虽然已被 Layui 收编为内置的弹层模块，但仍然会作为一个独立组件全力维护、升级。<br><br>我们此后的征途是星辰大海 ^_^</div>'
            });
        }
    });
</script>
@endsection