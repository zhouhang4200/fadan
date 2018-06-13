@extends('frontend.v1.layouts.app')

@section('title', '设置 - 发单设置')

@section('css')
    <style>
        .layui-input, .layui-textarea {
            width: 310px;
        }
        .layui-form-label {
            width:110px;
            height:24px;
        }
        .layui-input, .layui-select {
            height:32px;
        }
        .layui-form-item .layui-input-inline {
            width: 310px;
        }

        .tips {
            position: absolute;
            width: 10%;
            height: 30px;
            right: -35px;
            top: 5px;
            text-align: center
        }

        .tips .iconfont {
            left: -5px;
            font-size: 25px;
        }
    </style>
@endsection

@section('main') 
<div class="layui-card qs-text">
    <div class="layui-card-header">自动加价添加</div>
    <div class="layui-card-body">
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <form class="layui-form" action="" lay-filter="component-form-group">
                        {!! csrf_field() !!}
                        <div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">*发单价≤:</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="markup_amount" lay-verify="required|number|overZero" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                                    <div class="tips" lay-tips="填写值必须为大于0的正整数">
                                        <i class="iconfont icon-exclamatory-mark-r"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">*加价开始时间(h):</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="hours" lay-verify="required|integer" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">*加价开始时间(m):</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="minutes" lay-verify="required|minute|integer" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                                    <div class="tips" lay-tips="订单上架后第1次加价的时间，填写值必须为正整数，可以为0">
                                        <i class="iconfont icon-exclamatory-mark-r"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">*加价类型:</label>
                                <div class="layui-input-inline">
                                    <select name="markup_type" lay-verify="required">
                                        <option value=""></option>
                                        <option value="0" selected="">绝对值</option>
                                        <option value="1">百分比</option>
                                    </select>
                                    <div class="tips" lay-tips="选择“绝对值”，则“增加值”中填写的值为增加的金额；选择“百分比”，则“增加值”中填写的值（百分数）乘以订单代练价格为增加的金额，所填写的值均为正整数或带2位小数">
                                        <i class="iconfont icon-exclamatory-mark-r"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">*增加金额:</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="markup_money" lay-verify="required|number|overZero" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">*加价频率(m):</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="markup_frequency" lay-verify="required|integer|overZero" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">加价次数限制:</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="markup_number" lay-verify="integer" value="" autocomplete="off" placeholder="请输入" class="layui-input">
                                    <div class="tips" lay-tips="不填则为无次数限制">
                                        <i class="iconfont icon-exclamatory-mark-r"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label"></label>
                                <div class="layui-input-inline">
                                    <button class="qs-btn layui-btn-normal" lay-submit="" lay-filter="submit">确认</button>
                                    <a class="qs-btn qs-btn-primary" onclick="cancel()">取消</a>
                                </div>
                            </div>
                        </div>
                    </form>
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

            // 自定义规则
            form.verify({
                minute : function(value) {
                    if (value < 0) {
                        return '输入值不能小于0';
                    } else if (value > 60) {
                        return '输入值不能大于60';
                    }
                },
                integer : [/^[0-9]*$/, '必须输入整数'],
                hour: function(value) {
                    if (value < 0) {
                        return '输入值不能小于0';
                    }
                },
                overZero : function (value) {
                    if (value <= 0) {
                        return '输入值必须大于0';
                    }
                }
            });
            // 同意
            form.on('submit(submit)', function (data) {
                $.post("{{ route('frontend.setting.sending-assist.auto-markup.store') }}", {data:data.field}, function (result) {
                    layer.msg(result.message);
                    if (result.status > 0) {
                        window.location.href="{{ route('frontend.setting.sending-assist.auto-markup') }}";
                    }
                });
                return false;
            })
        });
        //取消
        function cancel() {
            window.location.href="{{ route('frontend.setting.sending-assist.auto-markup') }}";
        }
    </script>
@endsection