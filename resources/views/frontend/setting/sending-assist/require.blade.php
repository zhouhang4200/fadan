@extends('frontend.layouts.app')

@section('title', '设置 - 发单设置')

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

@section('submenu')
    @include('frontend.setting.submenu')
@endsection

@section('main') 
    <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
        <ul class="layui-tab-title">
            <li class="layui-this">代练要求模板</li>
            <li>保证金配置</li>
            <li>自动加价配置</li>
            <li>发单价预警设置</li>
            <li>发单平台设置</li>
            <li>定价器设置</li>
        </ul>
        <div class="layui-tab-content" style="height: 100px;">
            <div class="layui-tab-item layui-show">
                <div style="padding-bottom:40px;">
                <form class="layui-form" method="" action="">
                    <div style="float: left">
                        <div class="layui-inline" >
                            <a href="{{ route('frontend.setting.sending-assist.require.create') }}" style="color:#fff; float:right;" class="layui-btn layui-btn-normal layui-btn-small">新增</a>
                        </div>
                    </div>                     
                </form>
                </div>
                <form class="layui-form" method="" action="">
                <table class="layui-table">
                    <thead>
                    <tr>
                        <th style="width:10%">模板名称</th>
                        <th>模板内容</th>
                        <th style="width:7%;">设为默认</th>
                        <th style="width:15%;">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($orderTemplates as $orderTemplate)
                        <tr>
                            <td>{{ $orderTemplate->name }}</td>
                            <td>{{ $orderTemplate->content }}</td>
                            <td style="padding-bottom: 0px;padding-top: 0px;">
                                <div class="layui-form-item" pane="" style="width:50px;margin-bottom: 0px;">
                                    <div class="layui-input-block" style="margin-left:12px;height:45px;">
                                         <input type="checkbox" name="status" lay-filter="default" lay-skin="primary" lay-data="{{ $orderTemplate->id }}" value="1" title="" {{ $orderTemplate->status == 1 ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="text-align: center">
                                <a href="{{ route('frontend.setting.sending-assist.require.edit', ['id' => $orderTemplate->id]) }}" class="layui-btn layui-btn-normal layui-btn-mini">编辑</a>
                                <a class="layui-btn layui-btn-normal layui-btn-mini" onclick="destroy({{ $orderTemplate->id }})">删除</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>
                </form>
            </div>
            <div class="layui-tab-item">内容2</div>
            <div class="layui-tab-item">内容3</div>
            <div class="layui-tab-item">内容4</div>
            <div class="layui-tab-item">内容5</div>
            <div class="layui-tab-item">内容6</div>
        </div>
    </div> 
@endsection

@section('js')
    <script>
        layui.use(['element', 'form'], function(){
            var $ = layui.jquery
            ,element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
            var form = layui.form ,layer = layui.layer, table = layui.table;
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

            // 监听单选框
            form.on('checkbox(default)', function(data){
                var id = this.getAttribute('lay-data'); 
                var status = data.value;

                $.post("{{ route('frontend.setting.sending-assist.require.set') }}", {id:id, status:status}, function (result) {
                    layer.msg(result.message);
                    window.location.href="{{ route('frontend.setting.sending-assist.require') }}";    
                });     
            });
        });

        function destroy (id) {
            layui.use(['form', 'layedit', 'laydate',], function(){
                var form = layui.form,layer = layui.layer;
                layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('frontend.setting.sending-assist.require.destroy') }}",
                        data:{id:id},
                        success: function (data) {
                            layer.msg(data.message, {icon: 6, time:1000});                                
                            window.location.href = "{{ route('frontend.setting.sending-assist.require') }}";
                        }
                    });
                    layer.close(index);
                });
            });
        }
    </script>
@endsection