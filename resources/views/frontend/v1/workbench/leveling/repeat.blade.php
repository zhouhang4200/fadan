@extends('frontend.v1.layouts.app')

@section('title', '工作台 - 代练 - 订单重发')

@section('css')
    <style>
        .layui-layout-admin .layui-body {
            top: 50px;
        }

        .layui-layout-admin .layui-footer {
            height: 52px;
        }
        .layui-footer {
            z-index: 999;
        }
        .layui-card .layui-tab {
            margin: 10px 0;
        }
        .layui-tab-title li {
            min-width: 50px;
        }

        .order-operation {
            float: right;
            padding-top: 5px;
        }
        .order-btn .iconfont {
            position: absolute;
            top: 0%;
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


        /* 改写dl-type 下面的radio样式 */
        #dl-type .layui-form-radio {
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

        #dl-type .layui-form-radio div {
            padding: 0 10px;
            height: 100%;
            font-size: 12px;
            background-color: #d2d2d2;
            color: #fff;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        #dl-type .layui-form-radio i {
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

        #dl-type .layui-form-radioed i {
            color: #5FB878;
            border-color: #5FB878;
        }

        #dl-type .layui-form-radioed div {
            color: #fff;
            background-color: #5FB878;
        }
        .layui-col-lg6 .layui-input-block .tips{
            left: 95%;
        }
        .layui-input-block input,
        .layui-form-select, .layui-textarea,
        .layui-col-lg6 .layui-input-block input,
        .layui-col-lg6 .layui-form-select{
            width: 95%;
        }
        .layui-col-lg6 .layui-form-select input{
            width: 100%
        }
        .tips {
            position: absolute;
            width: 10%;
            height: 30px;
            right: 0;
            top: 5px;
            text-align: center
        }

        .tips .iconfont {
            left: 0px;
            font-size: 25px;
        }
        div[carousel-item]>* {
            text-align: center;
            line-height: 280px;
            color: #fff;
        }
        div[carousel-item]>*:nth-child(2n) {
            background-color: #009688;
        }
        div[carousel-item]>*:nth-child(2n+1) {
            background-color: #5FB878;
        }
        #carousel {
            position: relative;
        }
        .carousel-tips {
            width: 100%;
            height: 40px;
            line-height: 40px;
            text-align: left;
            text-indent: 20px;
            background-color: rgba(0, 0, 0, .8);
            color: #fff;
            position: absolute;
            bottom: 0px;
        }
        .layui-table{
            margin: 0;
        }
        @media screen and (max-width: 990px){
            .layui-col-lg6 .layui-input-block input {
                width: 90%;
            }
            .layui-col-lg6 .layui-input-block .tips {
                left: 90%;
            }
            .layui-col-lg6 .layui-form-select {
                width: 90%;
            }
            .layui-col-lg6 .layui-form-select input{
                width: 100%;
            }
        }
        .layui-layer-btn .layui-layer-btn0 {
            background: #ff8500;
            border: #ff8500;
        }
        .layui-card-header {
            height: 56px;
            line-height: 56px;
        }
        /**橙色字体*/
        .font-color-orange {
            color:#ff8500;
        }
    </style>
@endsection

@section('main')
    <div class="layui-col-md8">
        <div class="layui-card qs-text">
            <div class="layui-card-header">
                订单信息
            </div>
            <div class="layui-card-body" style="padding: 15px 25px 15px 15px">
                <form class="layui-form" action="" lay-filter="component-form-group" id="form-order">
                    <input type="hidden" name="source_order_no" value="{{ $detail['source_order_no'] ?? '' }}">
                    <input type="hidden" name="client_wang_wang" value="{{ $detail['client_wang_wang'] ?? '' }}">
                    <input type="hidden" name="seller_nick" value="{{ $detail['seller_nick'] ?? '' }}">
                    <input type="hidden" name="is_repeat" value="1">
                    <input id="zh_no" type="hidden" name="zh_no" lay-id="{{ $detail['order_no'] ?? '' }}" value="{{ $detail['order_no'] ?? '' }}">

                    <div class="layui-row layui-col-space10 layui-form-item">

                        <div class="layui-col-lg6">
                            <label class="layui-form-label"><span class="font-color-orange">*</span> 游戏</label>
                            <div class="layui-input-block">
                                <select name="game_id" lay-filter="game_id" lay-verify="required">
                                    <option value=""></option>
                                    @forelse($game as $id => $name)
                                        <option value="{{ $id }}" @if($id == $detail['game_id']) selected @endif>{{ $name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        <div class="layui-col-lg6">
                            <label class="layui-form-label"><span class="font-color-orange">*</span> 区</label>
                            <div class="layui-input-block ">
                                <select name="region" lay-verify="required" lay-filter="change-select" class="region">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label"><span class="font-color-orange">*</span> 服</label>
                            <div class="layui-input-block">
                                <select name="serve" lay-verify="required" lay-filter="serve"  class="serve" display-name="服">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>

                        <div class="layui-col-lg6">
                            <label class="layui-form-label"><span class="font-color-orange">*</span> 角色名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="role" lay-verify="required" placeholder="" autocomplete="off" class="layui-input"  display-name="角色名称" value="{{ $detail['role'] }}">
                            </div>
                        </div>
                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label"><span class="font-color-orange">*</span> 账号</label>
                            <div class="layui-input-block">
                                <input type="text" name="account" lay-verify="required" placeholder="" autocomplete="off" class="layui-input" display-name="账号" value="{{ $detail['account'] }}">
                            </div>
                        </div>

                        <div class="layui-col-lg6">
                            <label class="layui-form-label"><span class="font-color-orange">*</span> 密码</label>
                            <div class="layui-input-block">
                                <input type="text" name="password" lay-verify="required|pwd" placeholder="" autocomplete="off" class="layui-input" display-name="密码" value="{{ $detail['password'] }}">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label"><span class="font-color-orange">*</span> 代练类型</label>
                            <div class="layui-input-block">
                                <select name="game_leveling_type" lay-filter="game_leveling_type" class="leveling_type" display-name="代练类型">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                        </div>
                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label"><span class="font-color-orange">*</span> 代练标题</label>
                            <div class="layui-input-block tips-box">
                                <input type="text" name="game_leveling_title" lay-verify="required|title" placeholder="" autocomplete="off" class="layui-input" display-name="代练标题" value="{{ $detail['game_leveling_title'] }}">
                                <div class="tips" lay-tips="王者荣耀标题规范示例：黄金3（2星）-钻石1 （3星） 铭文：129">
                                    <i class="iconfont icon-exclamatory-mark-r"></i>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label"> 代练要求模板</label>
                            <div class="layui-input-block">
                                <select name="game_leveling_requirements_template" lay-verify="" lay-filter="aihao" display-name="代练要求模板">
                                    <option value=""></option>
                                </select>
                                <div class="tips" id="game_leveling_requirements_template">
                                    <i class="iconfont icon-add-r"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item layui-form-text">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label"><span class="font-color-orange">*</span> 代练说明</label>
                            <div class="layui-input-block">
                                <textarea name="game_leveling_instructions" lay-verify="required" placeholder="请输入内容" class="layui-textarea" display-name="代练说明">{{ $detail['game_leveling_instructions'] }}</textarea>
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label"><span class="font-color-orange">*</span> 代练要求</label>
                            <div class="layui-input-block">
                                <textarea name="game_leveling_requirements"  lay-verify="required"  placeholder="请输入内容" class="layui-textarea"  display-name="代练要求">{{ $detail['game_leveling_requirements'] }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label"><span class="font-color-orange">*</span> 代练价格</label>
                            <div class="layui-input-block">
                                <input type="text" name="game_leveling_amount" lay-verify="required|number|gt5" placeholder="" autocomplete="off" class="layui-input"  display-name="代练价格" value="{{ $detail['game_leveling_amount'] }}">
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                        </div>
                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label"><span class="font-color-orange">*</span> 安全保证金</label>
                            <div class="layui-input-block">
                                <input type="text" name="security_deposit" @if($detail['gainer_user_id']) lay-verify="required|number|gt5" @endif placeholder="" autocomplete="off" class="layui-input" display-name="安全保证金" value="{{ $detail['security_deposit'] }}">
                                <div class="tips" lay-tips="安全保证金是指对上家游戏账号安全进行保障时下家所需预先支付的保证形式的费用。当在代练过程中出现账号安全问题，即以双方协商或客服仲裁的部分或全部金额赔付给上家。（安全问题包括游戏内虚拟道具的安全，例如：符文、角色经验、胜点、负场经下家代练后不增反减、私自与号主联系、下家使用第三方软件带来的风险）">
                                    <i class="iconfont icon-exclamatory-mark-r"></i>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label"><span class="font-color-orange">*</span> 效率保证金</label>
                            <div class="layui-input-block">
                                <input type="text" name="efficiency_deposit" @if($detail['gainer_user_id']) lay-verify="required|number|gt5" @endif placeholder="" autocomplete="off" class="layui-input" display-name="效率保证金" value="{{ $detail['efficiency_deposit'] }}">
                                <div class="tips" lay-tips="效率保证金是指对上家的代练要求进行效率保障时下家所需预先支付的保证形式的费用。当下家未在规定时间内完成代练要求，即以双方协商或客服仲裁的部分或全部金额赔付给上家。（代练要求包括：下家在规定时间内没有完成上家的代练要求，接单4小时内没有上号，代练时间过四分之一但代练进度未达六分之一，下家原因退单，下家未及时上传代练截图）">
                                    <i class="iconfont icon-exclamatory-mark-r"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label"><span class="font-color-orange">*</span> 代练天数</label>
                            <div class="layui-input-block">
                                <select name="game_leveling_day" lay-verify="required" lay-filter="game_leveling_day" lay-search="" display-name="代练时间(天)">
                                    @php
                                         preg_match('/\d+/', $detail['game_leveling_day'], $day);
                                         preg_match('/\d+/', $detail['game_leveling_hour'], $hour);
                                    @endphp
                                    <option value=""></option>
                                    @for($i=0; $i<=30; $i++)
                                        <option value="{{ $i }}" @if($i == $day[0]) selected @endif>{{ $i }}天</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label"><span class="font-color-orange">*</span> 代练小时</label>
                            <div class="layui-input-block">
                                <select name="game_leveling_hour" lay-verify="required" lay-filter="aihao" display-name="代练时间(小时)" lay-search="" >
                                    <option value=""></option>
                                    @for($i=0; $i<=24; $i++)
                                        <option value="{{ $i }}" @if($i == $hour[0]) selected @endif>{{ $i }}小时</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label"><span class="font-color-orange">*</span> 玩家电话</label>
                            <div class="layui-input-block">
                                <input type="text" name="client_phone" lay-verify="required" placeholder="" autocomplete="off" class="layui-input" display-name="玩家电话" value="{{ $detail['client_phone'] }}">
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label"><span class="font-color-orange">*</span> 商户QQ</label>
                            <div class="layui-input-block">
                                <select name="user_qq" lay-verify="required" lay-filter="aihao" display-name="商户QQ" value="{{ $detail['user_qq'] }}">
                                    <option value=""></option>
                                </select>
                                <div class="tips"  id="user_qq">
                                    <i class="iconfont icon-add-r"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">加价幅度</label>
                            <div class="layui-input-block">
                                <input type="text" name="markup_range" lay-verify="" placeholder="" autocomplete="off" class="layui-input" value="{{ $detail['markup_range'] ?? '' }}">
                                <div class="tips" lay-tips="设置后，若一小时仍无人接单，将自动补款所填金额，每小时补款一次">
                                    <i class="iconfont icon-exclamatory-mark-r"></i>
                                </div>
                            </div>

                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">加价上限</label>
                            <div class="layui-input-block">
                                <input type="text" name="markup_top_limit" lay-verify="" placeholder="" autocomplete="off" class="layui-input" value="{{ $detail['markup_top_limit'] ?? '' }}">
                                <div class="tips" lay-tips="自动加价将不超过该价格">
                                    <i class="iconfont icon-exclamatory-mark-r"></i>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">接单密码</label>
                            <div class="layui-input-block">
                            <input type="text" name="order_password" lay-verify="" placeholder="" autocomplete="off" class="layui-input" value="{{ $detail['order_password'] }}">
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">指定内部打手</label>
                            <div class="layui-input-block">
                                <select name="gainer_user_id" lay-verify="" lay-filter="gainer-primary-user-id">
                                    <option value=""></option>
                                </select>
                                <div class="tips"  id="gainer_user_id">
                                    <i class="iconfont icon-add-r"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">来源单号1</label>
                            <div class="layui-input-block">
                                <input type="text" name="source_order_no_1" lay-verify="" placeholder="" autocomplete="off" class="layui-input" value="{{ $detail['source_order_no_1'] }}">
                            </div>
                        </div>
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">来源单号2</label>
                            <div class="layui-input-block">
                                <input type="text" name="source_order_no_2" lay-verify="" placeholder="" autocomplete="off" class="layui-input" value="{{ $detail['source_order_no_2'] }}">
                            </div>
                        </div>
                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                            <label class="layui-form-label">来源价格</label>
                            <div class="layui-input-block">
                                <input type="text" name="source_price" placeholder="" autocomplete="off" class="layui-input"  value="{{ $detail['source_price'] }}">
                            </div>
                        </div>
                        <div class="layui-col-lg6"></div>
                    </div>

                    <div class="layui-row layui-col-space10 layui-form-item">
                        <div class="layui-col-lg6">
                        <label class="layui-form-label">客服备注</label>
                        <div class="layui-input-block">
                            <input name="customer_service_remark" placeholder="请输入内容" class="layui-input" value="{{ $detail['customer_service_remark'] }}">
                        </div>
                        </div>
                        <div class="layui-col-lg6"></div>
                    </div>

                    <div class="layui-form-item layui-layout-admin">
                        <div class="layui-input-block">
                            <div class="layui-footer" style="left: 0;">
                                <button class="qs-btn" style="width: 92px;" lay-submit="" lay-filter="order">确定</button>
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
                            {{ $taobaoTrade->seller_nick or '' }}
                        </td>
                    </tr>
                    <tr>
                        <td>天猫单号</td>
                        <td>
                            {{ $taobaoTrade->tid or '' }}
                        </td>
                    </tr>
                    <tr>
                        <td>订单状态</td>
                        <td>
                            {{ isset($taobaoTrade->tid) ? config('order.taobao_trade_status')[$taobaoTrade->trade_status] : '' }}
                        </td>
                    </tr>
                    <tr>
                        <td>买家旺旺</td>
                        <td>
                            @if(!is_null($taobaoTrade) && $taobaoTrade->buyer_nick)
                                <a style="color:#1aa6de" href="http://www.taobao.com/webww/ww.php?ver=3&touid={{ $taobaoTrade->buyer_nick}}&siteid=cntaobao&status=1&charset=utf-8"
                                   class="btn btn-save buyer" target="_blank"><img src="/frontend/images/ww.gif" width="20px"> {{ $taobaoTrade->buyer_nick }}
                                </a>
                            @else
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>购买单价</td>
                        <td>{{ $taobaoTrade->price or '' }}</td>
                    </tr>
                    <tr>
                        <td>购买数量</td>
                        <td>
                            {{ $taobaoTrade->num or '' }}
                        </td>
                    </tr>
                    <tr>
                        <td>实付金额</td>
                        <td>
                            {{ $taobaoTrade->payment or '' }}
                        </td>
                    </tr>
                    <tr>
                        <td>所在区/服</td>
                        <td>
                            {{ $fixedInfo['serve']['value'] or '' }}
                        </td>
                    </tr>
                    <tr>
                        <td>角色名称</td>
                        <td>
                            {{ $fixedInfo['role']['value'] or '' }}
                        </td>
                    </tr>
                    <tr>
                        <td>买家留言</td>
                        <td>
                            {{ $taobaoTrade->buyer_message or '' }}
                        </td>
                    </tr>
                    <tr>
                        <td>下单时间</td>
                        <td>
                            {{ $taobaoTrade->created or '' }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script>
        layui.use(['form', 'layedit', 'laydate', 'laytpl', 'element'], function(){
            var form = layui.form, layer = layui.layer, layTpl = layui.laytpl, element = layui.element;
            var gameId = '{{ $detail['game_id'] }}';
            // 验证规则
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
                pwd: function(value){
                    if (value.length > 30) {
                        return '密码长度过长';
                    }
                },
                gt5:function (value) { // 大于1
                    if (value < 1) {
                        return '输入金额需大于或等于1元';
                    }
                },
                title:function (value) {
                    if (value.length > 60) {
                        return '标题不可大于60个字符';
                    }
                }
            });
            // 下单
            form.on('submit(order)', function (data) {
                layer.confirm('用哪一个客服身份重发？', {
                    btn: ['首次发单客服', '当前发单客服'] //可以无限个按钮
                }, function(){
                    order(data, 1);
                }, function() {
                    order(data, 0);
                });
                return false;
            });
            // 切换游戏时加截新的模版
            form.on('select(game_id)', function (data) {
                gameId = data.value;
                loadGameInfo()
            });
            // 选择区后获取对应的服
            form.on('select(change-select)', function(data){
                var choseId = $(data.elem).find("option:selected").attr("data-id");
                loadSelectChild(choseId);
                return false;
            });
            // 选择要求模版后加载内容到代练说明框中
            form.on('select', function(data){
                var fieldName = $(data.elem).attr("name"); //得到被选中的值
                // 选择要求模后自动填充模板内容
                if (fieldName == 'game_leveling_requirements_template') {
                    $('textarea[name=game_leveling_requirements]').val(data.value);
                }
            });
            // 选择内部接单商户
            form.on('select(gainer-primary-user-id)', function(data){
                if (data.value == '') {
                    $('input[name=security_deposit]').attr('lay-verify', 'required|number|gt5');
                    $('input[name=efficiency_deposit]').attr('lay-verify', 'required|number|gt5');
                } else {
                    $('input[name=security_deposit]').attr('lay-verify', '').val(0);
                    $('input[name=efficiency_deposit]').attr('lay-verify', '').val(0);
                }
            });
            // 按游戏加载区\代练类型\代练模版\商户QQ
            loadGameInfo();
            // 下单
            function order(data, value) {
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
                $.post('{{ route('frontend.workbench.leveling.create') }}', {data: data.field, value: value}, function (result) {
                    if (result.status == 1) {
                        layer.open({
                            content: result.message,
                            btn: ['继续发布', '订单列表'],
                            btn1: function(){
                                window.location.href="{{ route('frontend.workbench.leveling.wait') }}";
                            },
                            btn2: function(){
                                window.location.href="{{ route('frontend.workbench.leveling.index') }}";
                            }
                        });
                    } else {
                        layer.open({
                            content: result.message,
                            btn: ['继续发布', '订单列表'],
                            btn1: function(){
                                window.location.href="{{ route('frontend.workbench.leveling.wait') }}";
                            },
                            btn2: function(){
                                window.location.href="{{ route('frontend.workbench.leveling.index') }}";
                            }
                        });
                    }
                }, 'json');
            }
            // 加载下单必要的信息
            function loadGameInfo() {
                loadRegionType();
                loadGameLevelingTemplate();
                loadBusinessmanContactTemplate();
                setDefaultValueOption();
//                loadDefaultTemplate();
//                loadLevelingTemplate();
//                setFixedInfo();
//                parse();
                $('.serve').html('');
            }
            // 加载下拉框的下级选项
            function loadSelectChild(choseId) {
                $.post('{{ route('frontend.workbench.get-select-child') }}', {parent_id:choseId}, function (result) {
                    var options = '';
                    $(result).each(function (index, value) {
                        options += '<option value="'  + value.field_value + '" data-content="' + value.field_value +  '">' + value.field_value  + '</option>';
//                        $('.serve').append('<option value="' + value.field_value + '">' + value.field_value + '</option>');
                    });
                    $('.serve').html(options);
                    $('select[name=serve]').val('{{ $detail['serve'] }}');
                    layui.form.render();
                }, 'json');
            }
            // 模板使用说明
            $('#instructions').click(function () {
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
            // 点击解析模板
            $('#parse').click(function () {
                parse();
            });
            // 解析模板方法
            function parse() {
                var fieldArrs = $('[name="template"]').val().split('\n');

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
            // 加载区代练类型
            function loadRegionType() {
                $.ajax({
                    type: 'POST',
                    url: '{{ route("frontend.workbench.leveling.get-region-type") }}',
                    data: {game_id:gameId},
                    dataType: 'json',
                    async: false,
                    success: function (result) {
                        var region = '<option value="">请选择</option>';
                        var type = '';
                        $.each(result.content, function (index, value) {
                            if (value.field_name  == 'game_leveling_type') {
                                type += '<option value="'  + value.field_value + '" data-content="' + value.field_value +  ' " data-id=' + value.id  +'> ' + value.field_value  + '</option>';
                            } else {
                                region += '<option value="'  + value.field_value + '" data-content="' + value.field_value +  ' " data-id=' + value.id  +'> ' + value.field_value  + '</option>';
                            }
                        });
                        $('.leveling_type').html(type);
                        $('.region').html(region);
                        $('select[name=region]').val('{{ $detail['region'] }}');
                        var choseId = $('select[name=region]').find("option:selected").attr("data-id");
                        loadSelectChild(choseId);
                        layui.form.render();
                    }
                });
            }
            // 设置默认选中填充的值
            function setDefaultValueOption() {
                $("select[name=game_leveling_type]").val('{{ $detail['game_leveling_type'] }}');
                $('select[name=user_phone]').val('{{ $detail['user_phone']}}');
                $('select[name=user_qq]').val('{{ $detail['user_qq'] }}');
                $('select[name=region]').val('{{ $detail['region'] }}');
                $('select[name=gainer_user_id]').val('{{ $detail['gainer_user_id'] }}');

                @if(isset($taobaoTrade->tid))
                    $('input[name=source_order_no]').val('{{ $taobaoTrade->tid }}');
                    $('input[name=order_source]').val('天猫');
                    $('input[name=client_wang_wang]').val('{{ $taobaoTrade->buyer_nick }}');
                    $('input[name=seller_nick]').val('{{ $taobaoTrade->seller_nick }}');
                @endif
                layui.form.render();
            }
            // 加载代练要求模板
            function loadGameLevelingTemplate() {
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
            // 商户联系方式
            function loadBusinessmanContactTemplate() {
                $.ajaxSettings.async = false;
                $.get('{{ route("frontend.setting.setting.businessman-contact.index") }}', {id:gameId}, function (result) {
                    var qqTemplate = '<option value="">请选择</option>';
                    var phoneTemplate = '<option value="">请选择</option>';
                    var gainerUserTemplate = '<option value="">请选择</option>';
                    var chose = 0;
                    var gainerUserChose = 0;
                    $.each(result, function (index, value) {
                        if (value.type == 1) {
                            if (value.status == 1) {
                                phoneTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" selected> ' + value.name + '-' + value.content  +'</option>';
                            } else if (gameId == value.game_id && value.status == 1) {
                                chose = 1;
                                phoneTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" selected> ' + value.name + '-' + value.content  +'</option>';
                            } else {
                                phoneTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '"> ' + value.name + '-' + value.content  +'</option>';
                            }

                        } else if (value.type == 2) {

                            if (gameId == value.game_id && value.status == 1) {
                                chose = 1;
                                qqTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" selected>' + value.name + '-' + value.content  +'</option>';
                            } else if (value.status == 1 && value.game_id == 0 && chose == 0) {
                                qqTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" selected>' + value.name + '-' + value.content  +'</option>';
                            } else {
                                qqTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" >' + value.name + '-' + value.content  +'</option>';
                            }
                        } else if (value.type == 3) {
                            if (gameId == value.game_id && value.status == 1) {
                                gainerUserChose = 1;
                                gainerUserTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" selected>' + value.name + '-' + value.content  +'</option>';
                            } else if (value.status == 1 && value.game_id == 0 && gainerUserChose == 0) {
                                gainerUserTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" selected>' + value.name + '-' + value.content  +'</option>';
                            } else {
                                gainerUserTemplate += '<option value="'  + value.content + '" data-content="' + value.content +  '" >' + value.name + '-' + value.content  +'</option>';
                            }
                        }
                    });
                    chose = 0;
                    $('select[name=user_qq]').html(qqTemplate);
                    $('select[name=user_phone]').html(phoneTemplate);
                    $('select[name=gainer_user_id]').html(gainerUserTemplate);
                    layui.form.render();
                }, 'json');
            }
            // 生成代练模版
            function loadLevelingTemplate() {
                {{--$.post('{{ route('frontend.workbench.leveling.get-template') }}', {game_id:gameId, tid:'{{ $tid  }}'}, function (result) {--}}
                    {{--var template;--}}
                    {{--if (result.content.sellerMemo) {--}}
                        {{--var temp  = result.content.sellerMemo  + '\r\n';--}}
                        {{--// 替换所有半角除号为全角--}}
                        {{--template = temp.replace(/:/g, '：');--}}
                        {{--template += '商户电话：'+ result.content.businessmanInfoMemo.phone  + '\r\n';--}}
                        {{--template += '商户QQ：'+ result.content.businessmanInfoMemo.qq  + '\r\n';--}}
                        {{--@if(isset($taobaoTrade->tid))--}}
                            {{--template = template.replace(/(?<=\u53f7\u4e3b\u65fa\u65fa\uff1a).*\b/, '{{ $taobaoTrade->buyer_nick }}');--}}
                            {{--template = template.replace(/(?<=\u6765\u6e90\u4ef7\u683c\uff1a).*\b/, '{{ $taobaoTrade->payment }}');--}}
                            {{--template = template.replace(/(?<=\u6765\u6e90\u8ba2\u5355\u53f7\uff1a).*\b/, '{{ $taobaoTrade->tid }}');--}}
                            {{--template = template.replace(/(?<=\u8ba2\u5355\u6765\u6e90\uff1a).*\b/, '天猫');--}}
                            {{--if (template.indexOf('订单来源') == -1) {--}}
                                {{--template += '订单来源：天猫'  + '\r\n';--}}
                            {{--}--}}
                            {{--if (template.indexOf('来源订单号') == -1) {--}}
                                {{--template += '来源订单号：{{ $taobaoTrade->tid }}'  + '\r\n';--}}
                            {{--}--}}
                            {{--if (template.indexOf('来源价格') == -1) {--}}
                                {{--template += '来源价格：{{ $taobaoTrade->payment }}'  + '\r\n';--}}
                            {{--}--}}
                            {{--if (template.indexOf('号主旺旺') == -1) {--}}
                                {{--template += '号主旺旺：{{ $taobaoTrade->buyer_nick }}'  + '\r\n';--}}
                            {{--}--}}
                        {{--@endif--}}
                    {{--}--}}

                    {{--@if(isset($taobaoTrade->tid))--}}
                        {{--$('input[name=source_order_no]').val('{{ $taobaoTrade->tid }}');--}}
                        {{--$('input[name=order_source]').val('天猫');--}}
                        {{--$('input[name=source_price]').val('{{ $taobaoTrade->payment }}');--}}
                        {{--$('input[name=client_wang_wang]').val('{{ $taobaoTrade->buyer_nick }}');--}}
                    {{--@endif--}}

                    {{--$('#template').val(template);--}}
                    {{--setDefaultValueOption();--}}
                    {{--loadBusinessmanContactTemplate();--}}
                    {{--setFixedInfo();--}}
                {{--}, 'json');--}}
            }
            // 设置固定的订单信息
            {{--function setFixedInfo() {--}}
                {{--@forelse($fixedInfo as $name => $item)--}}
                    {{--@if($item['type'] == 1)--}}
                        {{--$('input[name={{ $name }}]').val('{{ $item['value'] }}');--}}
                    {{--@elseif($item['type'] == 2)--}}
                        {{--$("select[name={{ $name }}]").val('{{  $item['value']  }}');--}}
                        {{--@if($name == 'region')--}}
                           {{--loadSelectChild($('.region').find("option:selected").attr('data-id'));--}}
                        {{--@endif--}}
                    {{--@elseif($item['type'] == 3)--}}

                    {{--@elseif($item['type']== 4)--}}
                        {{--$('textarea[name="{{ $name }}"]').val('{{ $item['value'] }}');--}}
                    {{--@endif--}}
                {{--@empty--}}
                {{--@endforelse--}}
                {{--layui.form.render();--}}
            {{--}--}}
            // 加载默认模板
            function loadDefaultTemplate() {
                var template = '游戏：\r\n区：\r\n服：\r\n角色名称：\r\n代练类型：\r\n账号：\r\n密码：\r\n代练标题：\r\n代练价格：\r\n代练时间(天)：\r\n代练时间(小时)：\r\n安全保证金：\r\n效率保证金：\r\n玩家电话：\r\n商户QQ：\r\n来源价格：';
                $('[name="template"]').val(template);
            }
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
            // 添加内部打手
            $('.layui-form').on('click', '#gainer_user_id', function () {
                layer.open({
                    type: 2,
                    area: ['700px', '400px'],
                    content: '{{ route('frontend.setting.setting.businessman-contact.index', ['type' => 3]) }}',
                    cancel: function(index, layero){
                        loadBusinessmanContactTemplate(gameId);
                    }
                });
            });

            // 计算来源价格
            $("body").on('blur', 'input[name=source_order_no]', function(){
                var no = document.getElementById('zh_no').getAttribute('lay-id');
                var source_no= $('input[name=source_order_no]').val();
                var source_name = 'source_order_no';
                var source_no1= $('input[name=source_order_no_1]').val();
                var source_name1 = 'source_order_no_1';
                var source_no2= $('input[name=source_order_no_2]').val();
                var source_name2 = 'source_order_no_2';
                var source_price= $('input[name=source_price]').val();
                $.post('{{ route('frontend.workbench.leveling.source-price') }}', {no:no, source_no:source_no, source_name:source_name, source_no1:source_no1, source_name1:source_name1, source_no2:source_no2, source_name2:source_name2, source_price:source_price}, function (result) {
                    if (result.status == 1) {
                        $('input[name=source_price]').val(result.message);
                        $('input[name=source_price]').html(result.message);
                    }
                    layui.form.render();
                }, 'json');
            });
            $("body").on('blur', 'input[name=source_order_no_1]', function(){
               var no = document.getElementById('zh_no').getAttribute('lay-id');
                var source_no= $('input[name=source_order_no]').val();
                var source_name = 'source_order_no';
                var source_no1= $('input[name=source_order_no_1]').val();
                var source_name1 = 'source_order_no_1';
                var source_no2= $('input[name=source_order_no_2]').val();
                var source_name2 = 'source_order_no_2';
                var source_price= $('input[name=source_price]').val();
                $.post('{{ route('frontend.workbench.leveling.source-price') }}', {no:no, source_no:source_no, source_name:source_name, source_no1:source_no1, source_name1:source_name1, source_no2:source_no2, source_name2:source_name2, source_price:source_price}, function (result) {
                    if (result.status == 1) {
                        $('input[name=source_price]').val(result.message);
                        $('input[name=source_price]').html(result.message);
                    }
                    layui.form.render();
                }, 'json');
            });
            $("body").on('blur', 'input[name=source_order_no_2]', function(){
                var no = document.getElementById('zh_no').getAttribute('lay-id');
                var source_no= $('input[name=source_order_no]').val();
                var source_name = 'source_order_no';
                var source_no1= $('input[name=source_order_no_1]').val();
                var source_name1 = 'source_order_no_1';
                var source_no2= $('input[name=source_order_no_2]').val();
                var source_name2 = 'source_order_no_2';
                var source_price= $('input[name=source_price]').val();
                $.post('{{ route('frontend.workbench.leveling.source-price') }}', {no:no, source_no:source_no, source_name:source_name, source_no1:source_no1, source_name1:source_name1, source_no2:source_no2, source_name2:source_name2, source_price:source_price}, function (result) {
                    if (result.status == 1) {
                        $('input[name=source_price]').val(result.message);
                        $('input[name=source_price]').html(result.message);
                    }
                    layui.form.render();
                }, 'json');
            });
            $("body").on('blur', 'input[name=source_price]', function(){
                var no = document.getElementById('zh_no').getAttribute('lay-id');
                var source_price= $('input[name=source_price]').val();
                var source_no= $('input[name=source_order_no]').val();
                var source_name = 'source_order_no';
                var source_no1= $('input[name=source_order_no_1]').val();
                var source_name1 = 'source_order_no_1';
                var source_no2= $('input[name=source_order_no_2]').val();
                var source_name2 = 'source_order_no_2';
                $.post('{{ route('frontend.workbench.leveling.source-price') }}', {no:no, source_no:source_no, source_name:source_name, source_no1:source_no1, source_name1:source_name1, source_no2:source_no2, source_name2:source_name2, source_price:source_price}, function (result) {
                    if (result.status == 1) {
                        $('input[name=source_price]').val(result.message);
                        $('input[name=source_price]').html(result.message);
                    }
                    layui.form.render();
                }, 'json');
            });
        });
    </script>
@endsection