@extends('frontend.v1.layouts.app')

@section('title', '设置 - 发布渠道设置')

@section('css')
    <style>
        .layui-form-checkbox[lay-skin=primary] i {
            width: 36px;
            height: 25px;
            font-size: 18px;
            line-height: 27px;
        }
    </style>
@endsection

@section('main') 
<div class="layui-card qs-text">
    <div class='layui-card-header'>
        <ul class="layui-tab layui-tab-title layui-tab-brief ">
            <li><a href="{{ route('frontend.setting.sending-assist.auto-markup') }}">自动加价配置</a></li>
            <li class="layui-this"><a href="{{ route('frontend.setting.sending-assist.auto-markup') }}">发布渠道设置</a></li>
        </ul>
    </div>
    <div class="layui-card-body">
        <blockquote class="layui-elem-quote">
            操作提示<br/>
            用途：发布渠道设置可以控制发布的订单所能转单的平台，每种游戏至少选择一家平台。
        </blockquote>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show" id="auto-markup-show">
                <div style="padding-bottom:40px;">
                <form class="layui-form" method="" action="">
                    <div style="float: left">
                        <div class="layui-inline">
                            <a href="{{ route('frontend.setting.sending-assist.auto-markup.create') }}" style="color:#fff; float:right;" class="qs-btn layui-btn-normal layui-btn-small">新增</a>
                        </div>
                    </div>                     
                </form>
                </div>
                <div id="auto-markup-index">
                @include('frontend.v1.setting.sending-assist.auto-markup-list', ['orderAutoMarkups' => $orderAutoMarkups])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
        layui.use(['element', 'form'], function(){
            var $ = layui.jquery,element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
            var form = layui.form, layer = layui.layer, table = layui.table;
            //触发事件
            var active = {
                tabAdd: function(){
                  //新增一个Tab项
                    element.tabAdd('demo', {
                        title: '新选项'+ (Math.random()*1000|0) //用于演示
                        ,content: '内容'+ (Math.random()*1000|0)
                        ,id: new Date().getTime() //实际使用一般是规定好的id，这里以时间戳模拟下
                    })
                }
                ,tabDelete: function(othis){
                  //删除指定Tab项
                  element.tabDelete('demo', '44'); //删除：“商品管理”
                  
                  
                  othis.addClass('layui-btn-disabled');
                }
                ,tabChange: function(){
                  //切换到指定Tab项
                  element.tabChange('demo', '22'); //切换到：用户管理
                }
              };
              
            $('.site-demo-active').on('click', function(){
                var othis = $(this), type = othis.data('type');
                active[type] ? active[type].call(this, othis) : '';
            });
              
            //Hash地址的定位
            var layid = location.hash.replace(/^#test=/, '');
            element.tabChange('test', layid);
              
            element.on('tab(test)', function(elem){
                location.hash = 'test='+ $(this).attr('lay-id');
            });

            // 删除
            form.on("submit(delete)", function (result) {
                var id=this.getAttribute('lay-del-id');
                var s = window.location.search; //先截取当前url中“?”及后面的字符串
                var page=s.getAddrVal('page'); 

                layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
                    $.post("{{ route('frontend.setting.sending-assist.auto-markup.destroy') }}", {id:id}, function (result) {
                        layer.msg(result.message); 
                        if (page) {
                            $.get("{{ route('frontend.setting.sending-assist.auto-markup') }}?page="+page, function (result) {
                                $('#auto-markup-index').html(result);
                                form.render();
                            }, 'json');
                        } else {
                            $.get("{{ route('frontend.setting.sending-assist.auto-markup') }}", function (result) {
                                $('#auto-markup-index').html(result);
                                form.render();
                            }, 'json');
                        }
                    });
                    layer.close(index);
                });
                return false;
            })

            String.prototype.getAddrVal = String.prototype.getAddrVal||function(name){
                var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
                var data = this.substr(1).match(reg);
                return data!=null?decodeURIComponent(data[2]):null;
            }
        });
    </script>
@endsection