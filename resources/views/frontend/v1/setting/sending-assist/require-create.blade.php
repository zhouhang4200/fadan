@extends('frontend.v1.layouts.app')

@section('title', '设置 - 发单设置')

@section('css')
    <style>
        .layui-input, .layui-textarea,.layui-form-select{
            width: 450px;
        }
    </style>
@endsection

@section('main')
<div class="layui-card qs-text"> 
    <div class="layui-card-body">
        <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
            <ul class="layui-tab-title">
                <li class="layui-this"><a href="{{ route('frontend.setting.sending-assist.require') }}">代练要求模板</a></li>
                <li><a href="{{ route('frontend.setting.sending-assist.auto-markup') }}">自动加价配置</a></li>
            </ul>
            <div class="layui-tab-content">
                <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                  <legend>添加</legend>
                </fieldset>

                <div class="layui-tab-item layui-show">
                    <form class="layui-form" method="POST" action="">
                        {!! csrf_field() !!}
                        <div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">模板名称:</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="name" lay-verify="required" value="" autocomplete="off" placeholder="请输入名称" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">关联游戏:</label>
                                <div class="layui-input-inline">
                                    <select name="game_id" lay-verify="required" lay-search>
                                        <option value="0"></option>
                                        @foreach($game as $index => $value)
                                        <option value="{{ $index }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="layui-form-item layui-form-text">
                                <label class="layui-form-label">模板内容:</label>
                                <div class="layui-input-block">
                                  <textarea placeholder="请输入内容" lay-verify="required" name="content" class="layui-textarea"></textarea>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button class="qs-btn layui-btn-normal" lay-submit="" lay-filter="submit">确认</button>
                                    <a class="qs-btn layui-btn-normal" onclick="cancel()">取消</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div> 
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
            // 同意
            form.on('submit(submit)', function (data) {
                $.post("{{ route('frontend.setting.sending-assist.require.store') }}", {name:data.field.name, game_id:data.field.game_id,content:data.field.content}, function (result) {
                    layer.msg(result.message);
                    window.location.href="{{ route('frontend.setting.sending-assist.require') }}";
                });
                return false;
            })
        });
        //取消
        function cancel() {
            window.location.href="{{ route('frontend.setting.sending-assist.require') }}";
        }
    </script>
@endsection