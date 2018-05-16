@extends('frontend.v1.layouts.app')

@section('title', '工作台 - 代练 - 订单发布')

@section('css')
    <style>
        .layui-layout-admin .layui-body {
            top: 50px;
        }

        .layui-layout-admin .layui-footer {
            height: 52px;
        }

        .footer {
            height: 72px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .main {
            padding: 20px;
        }

        .layui-footer {
            z-index: 999;
        }

        .iconfont {
            position: absolute;
            top: 50%;
            left: 20px;
            transform: translateY(-50%);
            margin-top: 0;
        }

        .layui-card .layui-tab {
            margin: 10px 0;
        }

        .layui-form-item {
            margin-bottom: 20px;
        }

        .layui-table-cell {
            display: block;
            height: 40px;
            line-height: 20px;
            word-break: break-all;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding-left: 15px;
        }

        .layui-tab-title li {
            min-width: 50px;
        }

        .laytable-cell-1-button {
            width: 280px !important;
        }

        .layui-card-header {
            color: #303133;
            font-size: 14px;
        }

        .order-operation {
            float: right;
            padding-top: 5px;
        }

        .order-btn .iconfont {
            position: absolute;
            top: 50%;
            left: -8px;
            font-size: 22px;
        }

        .layui-footer .qs-btn {
            margin: 5px 3px 0 5px;
        }

        .template {
            float: right;
        }

        .template .qs-btn {
            height: 32px;
            line-height: 32px;

        }

        /* 改写header高度 */

        .layui-card-header {
            height: 56px;
            line-height: 56px;
        }
        /* 改写dl-type 下面的radio样式 */
        #dl-type .layui-form-radio{
            position: relative;
            height: 30px;
            line-height: 28px;
            margin-right: 10px;
            padding-right: 30px;
            border: 1px solid #d2d2d2;
            cursor: pointer;
            font-size: 0;
            border-radius: 2px;
            -webkit-transition: .1s linear;
            transition: .1s linear;
            box-sizing: border-box;
        }
        #dl-type .layui-form-radio div{
            padding: 0 10px;
            height: 100%;
            font-size: 12px;
            background-color: #d2d2d2;
            color: #fff;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        #dl-type .layui-form-radio i{
            position: absolute;
            right: 0;
            width: 30px;
            color: #fff;
            font-size: 20px;
            text-align: center;
            margin-right: 0;
        }



        #dl-type .layui-form-checked i,
        #dl-type .layui-form-checked:hover i {
            color: #5FB878
        }
        #dl-type .layui-form-radio:hover i {
            border-color: #5FB878;
            color: #d2d2d2
        }
        #dl-type .layui-form-radioed:hover i {
            border-color: #5FB878;
            color: #5FB878;
        }
        #dl-type .layui-form-radioed i{
            color: #5FB878;
            border-color: #5FB878;
        }
        #dl-type  .layui-form-radioed div{
            color: #fff;
            background-color: #5FB878;
        }
        .tips-box input{
            width: 85%;
        }
        .tips{
            position: absolute;
            width: 10%;
            height: 30px;
            right: 0%;
            top: 0px;
            text-align: center

        }
        .tips .iconfont{
            left: 0px;
            font-size: 25px;
        }
    </style>
@endsection

@section('main')
    <div class="layui-col-md8">
        <div class="layui-card qs-text">
            <div class="layui-card-header">
                订单信息
                <div class="order-operation">
                    <button class="order-btn">
                        <i class="iconfont icon-image-text"></i>操作记录</button>
                    <button class="order-btn">
                        <i class="iconfont icon-image"></i>查看图片</button>
                    <button class="order-btn">
                        <i class="iconfont icon-duihua"></i>留言</button>
                </div>
            </div>
            <div class="layui-card-body" style="padding: 15px;">
                <form class="layui-form" action="" lay-filter="component-form-group">
                    <div class="layui-form-item">
                        <label class="layui-form-label">游戏</label>
                        <div class="layui-input-block">
                            <select name="game" lay-filter="game">
                                <option value=""></option>
                                @forelse($game as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">区</label>
                        <div class="layui-input-block">
                            <input type="radio" name="qu" value="IOS微信" title="IOS微信" checked="">
                            <input type="radio" name="qu" value="IOSQQ" title="IOSQQ">
                            <input type="radio" name="qu" value="安卓微信" title="安卓微信">
                            <input type="radio" name="qu" value="安卓QQ" title="安卓QQ" >
                        </div>
                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">服</label>
                            <div class="layui-input-block">
                                <select name="game" lay-filter="aihao">
                                    <option value=""></option>
                                    <option value="0">1</option>
                                    <option value="1">2</option>
                                    <option value="2">3</option>
                                    <option value="3">4</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">角色名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="username" placeholder="" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item ">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">账号</label>
                            <div class="layui-input-block">
                                <input type="text" name="fullname" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                            </div>
                        </div>

                        <div class="layui-col-lg6">
                            <label class="layui-form-label">密码</label>
                            <div class="layui-input-block">
                                <select name="type" lay-verify="required" lay-filter="aihao">
                                    <option value=""></option>
                                    <option value="0">前端工程师</option>
                                    <option value="1">Node.js工程师</option>
                                    <option value="2">PHP工程师</option>
                                    <option value="3">Java工程师</option>
                                    <option value="4">运维</option>
                                    <option value="4">视觉设计师</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item dl-type" id="dl-type" >
                        <label class="layui-form-label">代练类型</label>
                        <div class="layui-input-block tips-box">
                            <input type="radio" name="dl-type" lay-filter="dl-tyle" title="陪玩" value="陪玩" checked>
                            <input type="radio" name="dl-type"  lay-filter="dl-tyle" title="排位" value="排位">
                            <input type="radio" name="dl-type"  lay-filter="dl-tyle" title="金币" value="金币">
                            <input type="radio" name="dl-type"  lay-filter="dl-tyle" title="成就" value="成就">

                        </div>
                    </div>
                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">代练标题</label>
                            <div class="layui-input-block tips-box">
                                <input type="text" name="fullname" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                                <div class="tips" lay-tips="输入账号啊输入账号啊输入账号啊输入账号啊输入账号啊输入账号啊输入账号啊输入账号啊输入账号啊输入账号啊输入账号啊输入账号啊输入账号啊输入账号啊输入账号啊">
                                    <i class="iconfont icon-exclamatory-mark-r"></i>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">代练模板</label>
                            <div class="layui-input-block tips-box">
                                <select name="type" lay-verify="required" lay-filter="aihao">
                                    <option value=""></option>
                                    <option value="0">通用模板1</option>
                                    <option value="1">通用模板2</option>
                                    <option value="2">通用模板3</option>
                                    <option value="3">通用模板4</option>
                                </select>
                                <div class="tips" lay-tips="输入账号啊">
                                    <i class="iconfont icon-exclamatory-mark-r"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">代练说明</label>
                            <div class="layui-input-block">
                                <textarea name="text" placeholder="请输入内容" class="layui-textarea"></textarea>
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">代练模板</label>
                            <div class="layui-input-block">
                                <textarea name="text" placeholder="请输入内容" class="layui-textarea"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">代练天数</label>
                            <div class="layui-input-block">
                                <select name="type" lay-verify="required" lay-filter="aihao">
                                    <option value=""></option>
                                    <option value="0">通用模板1</option>
                                    <option value="1">通用模板2</option>
                                    <option value="2">通用模板3</option>
                                    <option value="3">通用模板4</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">代练小时</label>
                            <div class="layui-input-block">
                                <select name="type" lay-verify="required" lay-filter="aihao">
                                    <option value=""></option>
                                    <option value="0">通用模板1</option>
                                    <option value="1">通用模板2</option>
                                    <option value="2">通用模板3</option>
                                    <option value="3">通用模板4</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">代练价格</label>
                            <div class="layui-input-block">
                                <input type="text" name="fullname" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">接单密码</label>
                            <div class="layui-input-block">
                                <input type="text" name="fullname" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">

                            </div>
                        </div>
                    </div>


                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">安全保证金</label>
                            <div class="layui-input-block">
                                <input type="text" name="fullname" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">效率保证金</label>
                            <div class="layui-input-block">
                                <input type="text" name="fullname" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                            </div>
                        </div>

                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">玩家电话</label>
                            <div class="layui-input-block">
                                <input type="text" name="fullname" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">商户QQ</label>
                            <div class="layui-input-block">
                                <input type="text" name="fullname" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">

                            </div>
                        </div>
                    </div>

                    <div class="layui-form-item dl-type" id="dl-type" >
                        <label class="layui-form-label">订单来源</label>
                        <div class="layui-input-block">
                            <input type="text" name="username" placeholder="" autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">来源单号</label>
                            <div class="layui-input-block">
                                <input type="text" name="fullname" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">来源价格</label>
                            <div class="layui-input-block">
                                <input type="text" name="fullname" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">

                            </div>
                        </div>
                    </div>
                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">来源单号1</label>
                            <div class="layui-input-block">
                                <input type="text" name="fullname" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">来源单号2</label>
                            <div class="layui-input-block">
                                <input type="text" name="fullname" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">

                            </div>
                        </div>
                    </div>

                    <div class="layui-form-item dl-type" id="dl-type" >
                        <label class="layui-form-label">玩家旺旺</label>
                        <div class="layui-input-block">
                            <input type="text" name="username" placeholder="" autocomplete="off" class="layui-input">
                        </div>
                    </div>


                    <div class="layui-form-item dl-type" id="dl-type" >
                        <label class="layui-form-label">客服备注</label>
                        <div class="layui-input-block">
                            <textarea name="text" placeholder="请输入内容" class="layui-textarea"></textarea>
                        </div>
                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">加价幅度</label>
                            <div class="layui-input-block">
                                <input type="text" name="fullname" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">加价上限</label>
                            <div class="layui-input-block">
                                <input type="text" name="fullname" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                    </div>

                    <div class="layui-form-item layui-layout-admin">
                        <div class="layui-input-block">
                            <div class="layui-footer" style="left: 0;">
                                <button class="qs-btn" style="width: 92px;margin-left: 535px;" lay-submit="" lay-filter="component-form-demo1">确定</button>
                                <button type="reset" class="qs-btn" style="background-color: #ff5822;">申请仲裁</button>
                                <button type="reset" class="qs-btn qs-btn-primary qs-btn-table">取消仲裁</button>
                                <button type="reset" class="qs-btn qs-btn-primary" style="width: 92px;">重发</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="layui-col-md4">
        <div class="layui-card">
            <div class="layui-card-header">淘宝数据</div>
            <div class="layui-card-body qs-text">
                <table class="layui-table">
                    <colgroup>
                        <col width="100">
                        <col>
                    </colgroup>
                    <tbody>
                    <tr>
                        <td>店铺名</td>
                        <td>
                            斗奇网游专营店
                        </td>
                    </tr>
                    <tr>
                        <td>买家旺旺</td>
                        <td>
                            <a href="#" style="color: #1aa6de">skai丶孤独终老</a>
                        </td>
                    </tr>
                    <tr>
                        <td>天猫单号</td>
                        <td>149141802641700214</td>
                    </tr>
                    <tr>
                        <td>订单状态</td>
                        <td>
                            买家发起退款
                        </td>
                    </tr>
                    <tr>
                        <td>购买单价</td>
                        <td>
                            20.00
                        </td>
                    </tr>
                    <tr>
                        <td>购买数量</td>
                        <td>
                            5
                        </td>
                    </tr>
                    <tr>
                        <td>实付金额</td>
                        <td>
                            100.00
                        </td>
                    </tr>
                    <tr>
                        <td>下单时间</td>
                        <td>
                            2018-5-10 14:52:55
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="layui-card">
            <div class="layui-card-header">发单模板
                <div class="template">
                    <button class="qs-btn">解析模板</button>
                    <button class="qs-btn qs-btn-primary">使用说明</button>
                </div>
            </div>
            <div class="layui-card-body qs-text">
                <form class="layui-form" action="" lay-filter="component-form-group">
                                                <textarea name="text" class="layui-textarea" style="height: 800px;">
                                                </textarea>
                </form>
            </div>
        </div>
        <div class="layui-card" style="margin-bottom: 72px;">
            <div class="layui-card-header">平台数据</div>
            <div class="layui-card-body qs-text">
                <table class="layui-table">
                    <colgroup>
                        <col width="100">
                        <col>
                    </colgroup>
                    <tbody>
                    <tr>
                        <td>平台单号</td>
                        <td>
                            XQ20180505152607-88760
                        </td>
                    </tr>
                    <tr>
                        <td>订单状态</td>
                        <td>
                            已仲裁
                        </td>
                    </tr>
                    <tr>
                        <td>接单平台</td>
                        <td>DD373</td>
                    </tr>
                    <tr>
                        <td>打手名称</td>
                        <td>
                            DD373打手
                        </td>
                    </tr>
                    <tr>
                        <td>打手电话</td>
                        <td>
                            17538323063
                        </td>
                    </tr>
                    <tr>
                        <td>打手QQ</td>
                        <td>
                            297544973
                        </td>
                    </tr>
                    <tr>
                        <td>剩余时间</td>
                        <td>
                            0天 0小时 0分
                        </td>
                    </tr>
                    <tr>
                        <td>发布时间</td>
                        <td>
                            2018-5-10 14:52:55
                        </td>
                    </tr>
                    <tr>
                        <td>接单时间</td>
                        <td>
                            2018-5-10 14:52:55
                        </td>
                    </tr>
                    <tr>
                        <td>结算时间</td>
                        <td>
                            2018-5-10 14:52:55
                        </td>
                    </tr>
                    <tr>
                        <td>仲裁说明</td>
                        <td>
                            你进行仲裁操作。原因：测试订单，撤单
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

<!--START 底部-->
@section('js')
    <script id="goodsTemplate" type="text/html">
        <input type="hidden" name="id" value="@{{ d.id }}">
        <input type="hidden" name="seller_nick" value="">
        <input type="hidden" name="pre_sale" value="" display-name="接单客服">
        <div class="layui-row form-group">
            @{{# var row = 0;}}
            @{{#  layui.each(d.template, function(index, item){ }}

            @{{#  if(row == 0) { row = item.display_form;  }  }}

            <div class="layui-col-md6">
                <div class="layui-col-md3 layui-form-mid">
                    @{{# if (item.field_required == 1) {  }}<span style="color: orangered;">*</span>@{{# }  }} @{{ item.field_display_name  }}
                </div>
                <div class="layui-col-md8">

                    @{{# if(item.field_type == 1) {  }}

                    <input type="text" name="@{{ item.field_name }}"  autocomplete="off" class="layui-input" lay-verify="@{{# if (item.field_required == 1) {  }}required@{{# } }}|@{{ item.verify_rule }}" display-name="@{{item.field_display_name}}" value="@{{# if (item.field_default_value != "null") { item.field_default_value  } }}">

                    @{{# } }}

                    @{{# if(item.field_type == 2) {  }}


                    <select name="@{{ item.field_name }}"  lay-search="" lay-verify="@{{# if (item.field_required == 1) { }}required@{{# } }}"  display-name="@{{item.field_display_name}}"  lay-filter="change-select" data-id="@{{ item.id }}" id="select-parent-@{{ item.field_parent_id }}">
                        <option value=""></option>
                        @{{#  if(item.user_values.length > 0){ }}
                        @{{#  layui.each(item.user_values, function(i, v){ }}
                        <option value="@{{ v.field_value }}" data-id="@{{ v.id  }}">@{{ v.field_value }}</option>
                        @{{#  }); }}
                        @{{#  } else { }}
                        @{{#  if(item.values.length > 0){ }}
                        @{{#  layui.each(item.values, function(i, v){ }}
                        <option value="@{{ v.field_value }}" data-id="@{{ v.id  }}">@{{ v.field_value }}</option>
                        @{{#  }); }}
                        @{{#  }  }}
                        @{{#  }  }}
                    </select>


                    @{{# } }}

                    @{{# if(item.field_type == 3) {  }}
                    @{{# } }}

                    @{{# if(item.field_type == 4) {  }}
                    <textarea name="@{{ item.field_name }}" placeholder="请输入内容" class="layui-textarea"  lay-verify="@{{# if (item.field_required == 1) {  }}required@{{# } }}"  display-name="@{{item.field_display_name}}"></textarea>
                    @{{# } }}

                    @{{# if(item.field_type == 5) {  }}
                    <input type="checkbox" name="@{{ item.field_name }}" lay-skin="primary"  lay-verify="@{{# if (item.field_required == 1) {  }}required@{{# }  }}"  display-name="@{{item.field_display_name}}">
                    @{{# } }}

                    @{{# if(item.help_text != '无') {  }}
                    <a href="#" class="tooltip">
                        <i class="iconfont icon-iconset0143" id="recharge"></i>
                        <span>@{{ item.help_text }}</span>
                    </a>
                    @{{# }  }}

                    @{{# if(item.field_name == 'game_leveling_requirements_template') {  }}
                    <a href="#" class="tooltip" id="game_leveling_requirements_template">
                        <i class="iconfont icon-plus-bd" id="recharge"></i>
                    </a>
                    @{{# }  }}

                    @{{# if(item.field_name == 'user_phone') {  }}
                    <a href="#" class="tooltip" id="user_phone">
                        <i class="iconfont icon-plus-bd" id="recharge"></i>
                    </a>
                    @{{# }  }}

                    @{{# if(item.field_name == 'user_qq') {  }}
                    <a href="#" class="tooltip" id="user_qq">
                        <i class="iconfont icon-plus-bd" id="recharge"></i>
                    </a>
                    @{{# }  }}

                </div>
            </div>

            @{{#  row--; }}

            @{{# if(row == 0) { }}
        </div>
        <div class="layui-row form-group @{{ item.field_display_name  }}">
            @{{# }  }}

            @{{# })  }}
        </div>

    </script>
    <script>
        layui.use(['form', 'layedit', 'laydate', 'laytpl', 'element'], function(){
            var form = layui.form, layer = layui.layer, layTpl = layui.laytpl, element = layui.element;
            var gameId = '{{ $gameId }}';
            //自定义验证规则
            form.verify({
                zero: function(value){
                    if(value <= 0){
                        return '该数值需大于0';
                    }
                },
                money:function (value) {
                    if (value.indexOf(".") > -1) {
                        var temp  = value.split(".");
                        if (temp.length > 2) {
                            return '请输入合法的金额';
                        }
                        if (temp[1].length > 2) {
                            return '输入的小数请不要大于两位';
                        }
                    }
                },
                gt5:function (value) { // 大于5
                    if (value < 5) {
                        return '输入金额需大于或等于5元';
                    }
                }

            });

            // 按游戏加载区\代练类型\代练模版\商户QQ
            function loadGameInfo() {

            }

            // 模板使用说明
            form.on('submit(instructions)', function () {
                layer.open({
                    type: 1
                    ,title: '使用说明' //不显示标题栏
                    ,closeBtn: false
                    ,area: '470px;'
                    ,shade: 0.2
                    ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
                    ,btn: ['确定']
                    ,btnAlign: 'c'
                    ,content: '<div style="padding: 10px 15px; line-height: 22px;   font-weight: 300;">1.选择“游戏”后会自动显示对应模板。<br/>2.将模版复制，发给号主填写。<br/>3.粘贴号主填写好的模版，粘贴至模板输入框内。<br/>4.点击“解析模板”按钮将资料导入至左侧表格内，点击“发布”按钮，即可创建订单。</div>'
                });
            });
            // 下单
            form.on('submit(order)', function (data) {

                if(data.field.game_leveling_day == 0 && data.field.game_leveling_hour == 0) {
                    layer.msg('代练时间不能都为0');
                    return false;
                }

                if(data.field.game_leveling_hour > 24) {
                    layer.msg('代练小时不能大于24小时');
                    return false;
                }

                var load = layer.load(0, {
                    shade: [0.2, '#000000']
                });

                $.post('{{ route('frontend.workbench.leveling.create') }}', {data: data.field}, function (result) {

                    if (result.status == 1) {
                        layer.open({
                            content: result.message,
                            btn: ['继续发布', '订单列表', '待发订单'],
                            btn1: function(index, layero){
                                window.location.href="{{ route('frontend.workbench.leveling.wait') }}";
                            },
                            btn2: function(index, layero){
                                window.location.href="{{ route('frontend.workbench.leveling.index') }}";
                            },
                            btn3: function(index, layero){
                                window.location.href="{{ route('frontend.workbench.leveling.index') }}";
                            }
                        });
                    } else {
                        layer.open({
                            content: result.message,
                            btn: ['继续发布', '订单列表', '待发订单'],
                            btn1: function(index, layero){
                                window.location.href="{{ route('frontend.workbench.leveling.wait') }}";
                            },
                            btn2: function(index, layero){
                                window.location.href="{{ route('frontend.workbench.leveling.index') }}";
                            },
                            btn3: function(index, layero){
                                window.location.href="{{ route('frontend.workbench.leveling.index') }}";
                            }
                        });
                        // layer.msg(result.message);
                    }
                    layer.close(load);

                }, 'json');
                return false;
            });
            // 切换游戏时加截新的模版
            form.on('select(game)', function (data) {
                gameId = data.value;
                loadTemplate(gameId)
            });
            // 模版预览 下拉框值
            form.on('select(change-select)', function(data){
                var subordinate = "#select-parent-" + data.elem.getAttribute('data-id');
                var choseId = $(data.elem).find("option:selected").attr("data-id");
                if($(subordinate).length > 0){
                    $.post('{{ route('frontend.workbench.get-select-child') }}', {parent_id:choseId}, function (result) {
                        $(subordinate).html(result);
                        $(result).each(function (index, value) {
                            $(subordinate).append('<option value="' + value.field_value + '">' + value.field_value + '</option>');
                        });
                        layui.form.render();
                    }, 'json');
                }
                return false;
            });
            // 加载默认模板
            loadTemplate(gameId);
            // 加载模板
            function loadTemplate(id) {
                var getTpl = goodsTemplate.innerHTML, view = $('#template');
                $.post('{{ route('frontend.workbench.leveling.get-template') }}', {game_id:id, tid:'{{ $tid  }}'}, function (result) {
                    var template;
                    if (result.content.sellerMemo) {

                        var temp  = result.content.sellerMemo  + '\r\n';
                        // 替换所有半角除号为全角
                        template = temp.replace(/:/g, '：');
                        template += '商户电话：'+ result.content.businessmanInfoMemo.phone  + '\r\n';
                        template += '商户QQ：'+ result.content.businessmanInfoMemo.qq  + '\r\n';
                        @if(isset($taobaoTrade->tid))

                                try {
                            template = template.replace(/(?<=\u53f7\u4e3b\u65fa\u65fa\uff1a).*\b/, '{{ $taobaoTrade->buyer_nick }}');
                            template = template.replace(/(?<=\u6765\u6e90\u4ef7\u683c\uff1a).*\b/, '{{ $taobaoTrade->payment }}');
                            template = template.replace(/(?<=\u6765\u6e90\u8ba2\u5355\u53f7\uff1a).*\b/, '{{ $taobaoTrade->tid }}');
                            template = template.replace(/(?<=\u8ba2\u5355\u6765\u6e90\uff1a).*\b/, '天猫');
                        } catch(err){
                            $('input[name=source_order_no]').val({{ $taobaoTrade->tid }});
                            $('input[name=order_source]').val('天猫');
                            $('input[name=source_price]').val('{{ $taobaoTrade->payment }}');
                            $('input[name=client_wang_wang]').val('{{ $taobaoTrade->buyer_nick }}');
                        }
                        if (template.indexOf('订单来源') == -1) {
                            template += '订单来源：天猫'  + '\r\n';
                        }
                        if (template.indexOf('来源订单号') == -1) {
                            template += '来源订单号：{{ $taobaoTrade->tid }}'  + '\r\n';
                        }
                        if (template.indexOf('来源价格') == -1) {
                            template += '来源价格：{{ $taobaoTrade->payment }}'  + '\r\n';
                        }
                        if (template.indexOf('号主旺旺') == -1) {
                            template += '号主旺旺：{{ $taobaoTrade->buyer_nick }}'  + '\r\n';
                        }
                        @endif
                        $('#user-template').val(template);
                    } else {
                        template = '游戏：\r\n';
                        $.each(result.content.template, function(index,element){
                            if (element.field_display_name != '商户电话' || element.field_display_name != '商户QQ') {
                                template += element.field_display_name + '：\r\n'
                            }
                        });
                        @if(isset($taobaoTrade->tid))

                                try {
                            $('input[name=source_order_no]').val('{{ $taobaoTrade->tid }}');
                            $('input[name=order_source]').val('天猫');
                            $('input[name=source_price]').val('{{ $taobaoTrade->payment }}');
                            $('input[name=client_wang_wang]').val('{{ $taobaoTrade->buyer_nick }}');
                        } catch(err){

                        }
                        @endif
                        $('#user-template').val(template);
                    }
                    layTpl(getTpl).render(result.content, function(html){
                        view.html(html);
                        layui.form.render();
                    });
                    setDefaultValueOption();
                    loadGameLevelingTemplate(id);
                    loadBusinessmanContactTemplate(id);
                    analysis()
                }, 'json');
            }
            // 解析模板
            $('#parse').click(function () {
                analysis();
            });
            function analysis() {

                var fieldArrs = $('[name="desc"]').val().split('\n');

                for (var i = fieldArrs.length - 1; i >= 0; i--) {
                    var arr = fieldArrs[i].split('：');

                    // 跳过格式不对的行或空行
                    if (typeof arr[1] == "undefined") {
                        continue;
                    }

                    // 去两端空格
                    var name = $.trim(arr[0]);
                    var value = $.trim(arr[1]);

                    // 获取表单dom
                    var $formDom = $('#form-order').find('[display-name="' + name + '"]');

                    // 填充表单
                    switch ($formDom.prop('type')) {
                        case 'select-one':
                            $formDom.find('option').each(function () {
                                if ($(this).text() == value && $(this).text() != '') {
                                    $formDom.val($(this).val());
                                    return false;
                                }
                            });
                            break;
                        case 'checkbox':
                            if (value == 1) {
                                $formDom.prop('checked', true);
                            } else {
                                $formDom.prop('checked', false);
                            }
                            break;
                        default:
                            if (value != '') {
                                $formDom.val(value);
                            }
                            break;
                    }
                }
                layui.form.render();
            }
            // 设置默认选中填充的值
            function setDefaultValueOption() {
                $("select[name=game_leveling_type]").val('排位');
                $('select[name=user_phone]').val('{{ $businessmanInfo->phone }}');
                $('select[name=user_qq]').val('{{ $businessmanInfo->qq }}');
                @if(isset($taobaoTrade->tid))
                    $('input[name=source_order_no]').val('{{ $taobaoTrade->tid }}');
                $('input[name=order_source]').val('天猫');
                $('input[name=source_price]').val('{{ $taobaoTrade->payment }}');
                $('input[name=client_wang_wang]').val('{{ $taobaoTrade->buyer_nick }}');
                $('input[name=seller_nick]').val('{{ $taobaoTrade->seller_nick }}');
                @endif
                layui.form.render();
            }
            // 加载代练要求模板
            function loadGameLevelingTemplate(gameId) {
                $.post('{{ route("frontend.workbench.leveling.game-leveling-template") }}', {game_id:gameId}, function (result) {
                    var optionsHtml = '<option value="">请选择模板</option>';
                    $.each(result, function (index, value) {
                        if (value.status  == 1) {
                            optionsHtml += '<option value="'  + value.content + '" data-content="' + value.content +  ' "  selected> ' + value.name  + '</option>';
                            $('textarea[name=game_leveling_requirements]').val(value.content);
                        } else {
                            optionsHtml += '<option value="'  + value.content + '" data-content="' + value.content +  '"> ' + value.name  +'</option>';
                        }
                    });
                    $('select[name=game_leveling_requirements_template]').html(optionsHtml);
                    layui.form.render();
                }, 'json');
            }
            // 加载代练要求模板
            function loadBusinessmanContactTemplate() {
                $.get('{{ route("frontend.setting.setting.businessman-contact.index") }}', {id:gameId}, function (result) {
                    var qqTemplate = '<option value="">请选择</option>';
                    var phoneTemplate = '<option value="">请选择</option>';
                    var chose = 0;
                    $.each(result, function (index, value) {
                        if (value.type == 1 && (value.game_id == 0 || gameId == value.game_id)) {

                            if (value.status == 1 && value.game_id == 0 && chose == 0) {
                                phoneTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" selected> ' + value.name + '-' + value.content  +'</option>';
                            } else if (gameId == value.game_id && value.status == 1) {
                                chose = 1;
                                phoneTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" selected> ' + value.name + '-' + value.content  +'</option>';
                            } else {
                                phoneTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '"> ' + value.name + '-' + value.content  +'</option>';
                            }

                        } else if (value.type == 2 && (value.game_id == 0 || gameId == value.game_id)) {

                            if (gameId == value.game_id && value.status == 1) {
                                chose = 1;
                                qqTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" selected>' + value.name + '-' + value.content  +'</option>';
                            } else if (value.status == 1 && value.game_id == 0 && chose == 0) {
                                qqTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" selected>' + value.name + '-' + value.content  +'</option>';
                            } else {
                                qqTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" >' + value.name + '-' + value.content  +'</option>';
                            }
                        }
                    });
                    chose = 0;
                    $('select[name=user_qq]').html(qqTemplate);
                    $('select[name=user_phone]').html(phoneTemplate);
                    layui.form.render();
                }, 'json');
            }

            form.on('select', function(data){
                var fieldName = $(data.elem).attr("name"); //得到被选中的值
                // 选择要求模后自动填充模板内容
                if (fieldName == 'game_leveling_requirements_template') {
                    $('textarea[name=game_leveling_requirements]').val(data.value);
                }
            });
            // 添加代练要求模板
            $('.layui-form').on('click', '#game_leveling_requirements_template', function () {
                layer.open({
                    type: 2,
                    area: ['700px', '400px'],
                    content: '{{ route('frontend.setting.sending-assist.require.pop') }}',
                    cancel: function(index, layero){
                        loadGameLevelingTemplate(gameId);
                    }
                });
            });
            // 添加商户电话模版
            $('.layui-form').on('click', '#user_phone', function () {
                layer.open({
                    type: 2,
                    area: ['700px', '400px'],
                    content: '{{ route('frontend.setting.setting.businessman-contact.index', ['type' => 1]) }}',
                    cancel: function(index, layero){
                        loadBusinessmanContactTemplate(gameId);
                    }
                });
            });
            // 添加商户QQ模版
            $('.layui-form').on('click', '#user_qq', function () {
                layer.open({
                    type: 2,
                    area: ['700px', '400px'],
                    content: '{{ route('frontend.setting.setting.businessman-contact.index', ['type' => 2]) }}',
                    cancel: function(index, layero){
                        loadBusinessmanContactTemplate(gameId);
                    }
                });
            });
        });
    </script>
@endsection