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
        .layui-footer {
            z-index: 999;
        }
        .layui-card .layui-tab {
            margin: 10px 0;
        }
        .layui-tab-title li {
            min-width: 50px;
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

        /* 改写header高度 */
        .layui-card-header {
            height: 56px;
            line-height: 56px;
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
    </style>
    <link rel="stylesheet" href="/frontend/v1/lib/css/im.css">
    <link rel="stylesheet" href="/frontend/css/bootstrap-fileinput.css">
@endsection

@section('main')
    <div class="layui-col-md8">

        <div class="layui-card qs-text" >
            <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief" style="margin-top: 0;">
                <ul class="layui-tab-title" style="height:56px;line-height: 56px;"  lay-filter="detail">
                    <li class="layui-this" lay-id="1">订单信息</li>
                    <li lay-id="2">仲裁证据</li>
                    <li lay-id="3">操作记录</li>
                    <div class="order-operation">
                        <button class="order-btn" id="carousel-btn">
                            <i class="iconfont icon-image"></i>查看图片</button>
                        <button class="order-btn" id="im" style="margin:0 15px 0 5px">
                            <i class="iconfont icon-duihua"></i>留言</button>
                    </div>
                </ul>
                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show">
                        <form class="layui-form" action="" lay-filter="component-form-group" id="form-order">
                            <input type="hidden" name="no" value="{{ $detail['no'] }}">

                            <div class="layui-row layui-col-space10 layui-form-item">
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">游戏</label>
                                    <div class="layui-input-block">
                                        <select name="game_id" lay-filter="game_id" lay-verify="required" @if(!in_array($detail['status'], [1, 22]))  disabled="disabled" @endif>
                                            <option value=""></option>
                                            @forelse($game as $id => $name)
                                                <option value="{{ $id }}" @if($id == $detail['game_id']) selected @endif>{{ $name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                </div>

                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">区</label>
                                    <div class="layui-input-block ">
                                        <select name="region" lay-verify="required" lay-filter="change-select" class="region"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled" @endif>
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>

                            </div>

                            <div class="layui-row layui-col-space10 layui-form-item">
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">服</label>
                                    <div class="layui-input-block">
                                        <select name="serve" lay-filter="serve" class="serve" display-name="服"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled" @endif>
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">角色名称</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="role" placeholder="" autocomplete="off" class="layui-input"  display-name="角色名称"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled"  @endif value="{{ $detail['role'] ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="layui-row layui-col-space10 layui-form-item">
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">账号</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="account" lay-verify="required" placeholder="" autocomplete="off" class="layui-input" display-name="账号"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled"  @endif value="{{ $detail['account'] ?? '' }}">
                                    </div>
                                </div>

                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">密码</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="password" lay-verify="required" placeholder="" autocomplete="off" class="layui-input" display-name="密码"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled"  @endif value="{{ $detail['password'] ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="layui-row layui-col-space10 layui-form-item">
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">代练类型</label>
                                    <div class="layui-input-block">
                                        <select name="game_leveling_type" lay-filter="game_leveling_type" class="leveling_type" display-name="代练类型"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled"  @endif>
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="layui-col-lg6">
                                </div>
                            </div>

                            <div class="layui-row layui-col-space10 layui-form-item">
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">代练标题</label>
                                    <div class="layui-input-block tips-box">
                                        <input type="text" name="game_leveling_title" lay-verify="required|title" placeholder="" autocomplete="off" class="layui-input" display-name="代练标题"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled"  @endif value="{{ $detail['game_leveling_title'] ?? '' }}">
                                        <div class="tips" lay-tips="王者荣耀标题规范示例：黄金3（2星）-钻石1 （3星） 铭文：129">
                                            <i class="iconfont icon-exclamatory-mark-r"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">代练要求模板</label>
                                    <div class="layui-input-block">
                                        <select name="game_leveling_requirements_template" lay-verify="" lay-filter="aihao" display-name="代练要求模板"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled"  @endif>
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
                                    <label class="layui-form-label">代练说明</label>
                                    <div class="layui-input-block">
                                        <textarea name="game_leveling_instructions" placeholder="请输入内容" class="layui-textarea" display-name="代练说明"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled"  @endif>{{ $detail['game_leveling_instructions'] ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">代练要求</label>
                                    <div class="layui-input-block">
                                        <textarea name="game_leveling_requirements" placeholder="请输入内容" class="layui-textarea"  display-name="代练要求"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled"  @endif>{{ $detail['game_leveling_requirements'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="layui-row layui-col-space10 layui-form-item">
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">代练价格</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="game_leveling_amount" lay-verify="required|number|gt5" placeholder="" autocomplete="off" class="layui-input"  display-name="代练价格"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled"  @endif value="{{ $detail['game_leveling_amount'] ?? '' }}">
                                        @if(in_array($detail['status'], [13, 14, 17]))
                                            <div class="tips"  id="add_price">
                                                <i class="iconfont icon-add-r"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="layui-col-lg6">
                                </div>
                            </div>

                            <div class="layui-row layui-col-space10 layui-form-item">
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">安全保证金</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="security_deposit" lay-verify="required|number|gt5" placeholder="" autocomplete="off" class="layui-input" display-name="安全保证金"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled"  @endif value="{{ $detail['security_deposit'] ?? '' }}">
                                        <div class="tips" lay-tips="安全保证金是指对上家游戏账号安全进行保障时下家所需预先支付的保证形式的费用。当在代练过程中出现账号安全问题，即以双方协商或客服仲裁的部分或全部金额赔付给上家。（安全问题包括游戏内虚拟道具的安全，例如：符文、角色经验、胜点、负场经下家代练后不增反减、私自与号主联系、下家使用第三方软件带来的风险）">
                                            <i class="iconfont icon-exclamatory-mark-r"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">效率保证金</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="efficiency_deposit" lay-verify="required|number|gt5" placeholder="" autocomplete="off" class="layui-input" display-name="效率保证金"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled"  @endif value="{{ $detail['efficiency_deposit'] ?? '' }}">
                                        <div class="tips" lay-tips="效率保证金是指对上家的代练要求进行效率保障时下家所需预先支付的保证形式的费用。当下家未在规定时间内完成代练要求，即以双方协商或客服仲裁的部分或全部金额赔付给上家。（代练要求包括：下家在规定时间内没有完成上家的代练要求，接单4小时内没有上号，代练时间过四分之一但代练进度未达六分之一，下家原因退单，下家未及时上传代练截图）">
                                            <i class="iconfont icon-exclamatory-mark-r"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="layui-row layui-col-space10 layui-form-item">
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">代练天数</label>
                                    <div class="layui-input-block">
                                        <select name="game_leveling_day" lay-verify="required" lay-filter="game_leveling_day" lay-search="" display-name="代练时间(天)"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled"  @endif>
                                            <option value=""></option>
                                            @for($i=0; $i<=30; $i++)
                                                <option value="{{ $i }}" @if($detail['game_leveling_day'] == $i) selected  @endif>{{ $i }}天</option>
                                            @endfor
                                        </select>
                                        @if(in_array($detail['status'], [13, 17]))
                                            <div class="tips"  id="add_time">
                                                <i class="iconfont icon-add-r"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">代练小时</label>
                                    <div class="layui-input-block">
                                        <select name="game_leveling_hour" lay-verify="required" lay-filter="aihao" display-name="代练时间(小时)"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled"  @endif>
                                            <option value=""></option>
                                            @for($i=0; $i<=24; $i++)
                                                <option value="{{ $i }}" @if($detail['game_leveling_hour'] == $i) selected  @endif>{{ $i }}小时</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="layui-row layui-col-space10 layui-form-item">
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">玩家电话</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="client_phone" lay-verify="required" placeholder="" autocomplete="off" class="layui-input" display-name="玩家电话" value="{{ $detail['client_phone'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">商户QQ</label>
                                    <div class="layui-input-block">
                                        <select name="user_qq" lay-verify="required" lay-filter="aihao" display-name="商户QQ">
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
                                        <input type="text" name="markup_range" lay-verify="" placeholder="" autocomplete="off" class="layui-input"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled"  @endif value="{{ $detail['markup_range'] ?? '' }}">
                                        <div class="tips" lay-tips="设置后，若一小时仍无人接单，将自动补款所填金额，每小时补款一次">
                                            <i class="iconfont icon-exclamatory-mark-r"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">加价上限</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="markup_top_limit" lay-verify="" placeholder="" autocomplete="off" class="layui-input"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled"  @endif value="{{ $detail['markup_top_limit'] ?? '' }}">
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
                                        <input type="text" name="order_password" lay-verify="" placeholder="" autocomplete="off" class="layui-input"  @if(!in_array($detail['status'], [1, 22]))  disabled="disabled"  @endif value="{{ $detail['order_password'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="layui-col-lg6">
                                </div>
                            </div>

                            <div class="layui-row layui-col-space10 layui-form-item">
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">补款单号1</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="source_order_no_1" lay-verify="" placeholder="" autocomplete="off" class="layui-input" value="{{ $detail['source_order_no_1'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">补款单号2</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="source_order_no_2" lay-verify="" placeholder="" autocomplete="off" class="layui-input" value="{{ $detail['source_order_no_2'] ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="layui-row layui-col-space10 layui-form-item">
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">来源价格</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="source_price" placeholder="" autocomplete="off" class="layui-input" value="{{ $detail['source_price'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="layui-col-lg6"></div>
                            </div>

                            <div class="layui-row layui-col-space10 layui-form-item">
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">客服备注</label>
                                    <div class="layui-input-block">
                                        <input name="customer_service_remark" placeholder="请输入内容" class="layui-input" value="{{ $detail['customer_service_remark'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="layui-col-lg6"></div>
                            </div>

                            <div class="layui-form-item layui-layout-admin">
                                <div class="layui-input-block">
                                    <div class="layui-footer" style="left: 0;">
                                        
                                            <button class="qs-btn" style="width: 92px;" lay-submit="" lay-filter="order">确定</button>
                                            @if ($detail['master'] && $detail['status'] == 22)
                                                <button  class="qs-btn opt-btn"  data-operation="onSale" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">上架</button>
                                            @endif

                                            @if ($detail['master'] && $detail['status'] == 1)
                                                <button  class="qs-btn opt-btn" style="width: 92px;" data-operation="offSale" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">下架</button>
                                            @endif

                                            @if ($detail['master'] && in_array($detail['status'], [14, 15, 16, 17, 18, 19, 20, 21, 24]))
                                                <button  class="qs-btn opt-btn"  data-operation="repeat" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}" class="qs-btn qs-btn-primary" style="width: 92px;" >重发</button>
                                            @endif

                                            @if ($detail['master'] && isset($detail['urgent_order']) && $detail['urgent_order'] != 1)
                                                <button  class="qs-btn opt-btn"  data-operation="urgent" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">加急</button>
                                            @endif

                                            @if ($detail['master'] && isset($detail['urgent_order']) && $detail['urgent_order'] == 1)
                                                <button  class="qs-btn opt-btn"  data-operation="unUrgent" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">取消加急</button>
                                            @endif

                                            @if ($detail['master'] && in_array($detail['status'], [13, 14,  17]))
                                                <button  class="qs-btn opt-btn"  data-operation="lock" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">锁定</button>
                                            @endif

                                            @if ($detail['master'] && $detail['status'] == 18)
                                                <button  class="qs-btn opt-btn"  data-operation="cancelLock" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">取消锁定</button>
                                            @endif

                                            @if ($detail['master'])
                                                @if ($detail['consult'] == 1 && $detail['status'] == 15)
                                                    <button  class="qs-btn opt-btn"  data-operation="cancelRevoke" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">取消撤销</button>
                                                @elseif ($detail['consult'] == 2 && ($detail['status'] == 15))
                                                    <button  class="qs-btn opt-btn"  data-operation="agreeRevoke" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}"  api_amount="{{ $detail['leveling_consult']['api_amount'] == intval($detail['leveling_consult']['api_amount']) ? intval($detail['leveling_consult']['api_amount']) : $detail['leveling_consult']['api_amount'] }}" api_deposit="{{ $detail['leveling_consult']['api_deposit'] == intval($detail['leveling_consult']['api_deposit']) ? intval($detail['leveling_consult']['api_deposit']) : $detail['leveling_consult']['api_deposit'] }}" api_service="{{ intval($detail['leveling_consult']['api_service']) == $detail['leveling_consult']['api_service'] ? intval($detail['leveling_consult']['api_service']) : $detail['leveling_consult']['api_service'] }}" who="2" reason="{{ $detail['leveling_consult']['revoke_message'] ?? '' }}">同意撤销</button>
                                                    <button class="qs-btn opt-btn"  data-operation="refuseRevoke" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">不同意撤销</button>
                                                @endif
                                            @else
                                                @if ($detail['consult'] == 2 && $detail['status'] == 15)
                                                    <button class="qs-btn opt-btn"  data-operation="cancelRevoke" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">取消撤销</button>
                                                @elseif ($detail['consult'] == 1 && ($detail['status'] == 15))
                                                    <button  class="qs-btn opt-btn"  data-operation="agreeRevoke" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}"  api_amount="{{ $detail['leveling_consult']['api_amount'] == intval($detail['leveling_consult']['api_amount']) ? intval($detail['leveling_consult']['api_amount']) : $detail['leveling_consult']['api_amount'] }}" api_deposit="{{ $detail['leveling_consult']['api_deposit'] == intval($detail['leveling_consult']['api_deposit']) ? intval($detail['leveling_consult']['api_deposit']) : $detail['leveling_consult']['api_deposit'] }}" api_service="{{ intval($detail['leveling_consult']['api_service']) == $detail['leveling_consult']['api_service'] ? intval($detail['leveling_consult']['api_service']) : $detail['leveling_consult']['api_service'] }}" who="2" reason="{{ $detail['leveling_consult']['revoke_message'] ?? '' }}">同意撤销</button>
                                                    <button  class="qs-btn opt-btn"  data-operation="refuseRevoke" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">不同意撤销</button>
                                                @endif
                                            @endif

                                            @if (in_array($detail['status'], [13, 14, 17, 18]))
                                                <button  class="qs-btn opt-btn"  data-operation="revoke" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">协商撤销</button>
                                            @endif

                                            @if (in_array($detail['status'], [13,14,15]))
                                                <button class="qs-btn opt-btn"   data-operation="applyArbitration" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}" class="qs-btn qs-btn-primary qs-btn-table" >申请仲裁</button>
                                            @endif

                                            @if ($detail['master'])
                                                @if ($detail['complain'] == 1 && $detail['status'] == 16)
                                                    <button class="qs-btn opt-btn"  data-operation="cancelArbitration" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}" class="qs-btn qs-btn-primary qs-btn-table" >取消仲裁</button>
                                                    @if($detail['consult'] == 2)
                                                        <button  class="qs-btn opt-btn"  data-operation="agreeRevoke" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}"  api_amount="{{ $detail['leveling_consult']['api_amount'] == intval($detail['leveling_consult']['api_amount']) ? intval($detail['leveling_consult']['api_amount']) : $detail['leveling_consult']['api_amount'] }}" api_deposit="{{ $detail['leveling_consult']['api_deposit'] == intval($detail['leveling_consult']['api_deposit']) ? intval($detail['leveling_consult']['api_deposit']) : $detail['leveling_consult']['api_deposit'] }}" api_service="{{ intval($detail['leveling_consult']['api_service']) == $detail['leveling_consult']['api_service'] ? intval($detail['leveling_consult']['api_service']) : $detail['leveling_consult']['api_service'] }}" who="2" reason="{{ $detail['leveling_consult']['revoke_message'] ?? '' }}">同意撤销</button>
                                                    @endif
                                                @endif
                                            @else
                                                @if ($detail['complain'] == 2 && $detail['status'] == 16)
                                                    <button  class="qs-btn opt-btn"  data-operation="cancelArbitration" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">取消仲裁</button>
                                                    @if($detail['consult'] == 1)
                                                        <button  class="qs-btn opt-btn"  data-operation="agreeRevoke" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}"  api_amount="{{ $detail['leveling_consult']['api_amount'] == intval($detail['leveling_consult']['api_amount']) ? intval($detail['leveling_consult']['api_amount']) : $detail['leveling_consult']['api_amount'] }}" api_deposit="{{ $detail['leveling_consult']['api_deposit'] == intval($detail['leveling_consult']['api_deposit']) ? intval($detail['leveling_consult']['api_deposit']) : $detail['leveling_consult']['api_deposit'] }}" api_service="{{ intval($detail['leveling_consult']['api_service']) == $detail['leveling_consult']['api_service'] ? intval($detail['leveling_consult']['api_service']) : $detail['leveling_consult']['api_service'] }}" who="2" reason="{{ $detail['leveling_consult']['revoke_message'] ?? '' }}">同意撤销</button>
                                                    @endif
                                                @endif
                                            @endif

                                            @if ($detail['master'] && $detail['status'] == 14)
                                                <button  class="qs-btn opt-btn"  data-operation="complete" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">完成验收</button>
                                            @endif


                                            @if ($detail['master'] && ($detail['status'] == 1 || $detail['status'] == 22))
                                                <button  class="qs-btn opt-btn"  style="background-color: #ff5822;" data-operation="delete" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">撤单</button>
                                            @endif

                                            @if (!$detail['master'] && ($detail['status'] == 13))
                                                <button  class="qs-btn opt-btn"  data-operation="applyComplete" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">申请完成</button>
                                            @endif

                                            @if (!$detail['master'] && ($detail['status'] == 14))
                                                <button  class="qs-btn opt-btn"  data-operation="cancelComplete" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">取消验收</button>
                                            @endif

                                            @if (!$detail['master'] && ($detail['status'] == 13))
                                                <button  class="qs-btn"  data-operation="abnormal" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">异常</button>
                                            @endif

                                            @if (!$detail['master'] && ($detail['status'] == 17))
                                                <button  class="qs-btn"  data-operation="cancelAbnormal" data-no="{{ $detail['no'] }}" data-safe="{{ $detail['security_deposit'] ?? '' }}" data-effect="{{ $detail['efficiency_deposit'] ?? '' }}" data-amount="{{ $detail['amount'] }}">取消异常</button>
                                            @endif
                                      

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="layui-tab-item" lay-id="arbitration-info" id="arbitration-info">
                    </div>
                    <div class="layui-tab-item" lay-id="message" id="message">
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="layui-col-md4">
        <div class="layui-card" style="">
            <div class="layui-card-header">平台数据</div>
            <div class="layui-card-body qs-text">
                <table class="layui-table">
                    <colgroup>
                        <col width="115">
                        <col>
                    </colgroup>
                    <tbody>
                    <tr>
                        <td>平台单号</td>
                        <td>{{ $detail['third_order_no']  }}</td>
                    </tr>
                    <tr>
                        <td>订单状态</td>
                        <td>{{ config('order.status_leveling')[$detail['status']] }}</td>
                    </tr>
                    <tr>
                        <td>接单平台</td>
                        <td>{{ config('order.third')[$detail['third']] ?? ''  }}</td>
                    </tr>
                    <tr>
                        <td>打手呢称</td>
                        <td>{{ $detail['hatchet_man_name'] ?? ''  }}</td>
                    </tr>
                    <tr>
                        <td>打手电话</td>
                        <td>{{ $detail['hatchet_man_phone'] ?? ''  }}</td>
                    </tr>
                    <tr>
                        <td>打手QQ</td>
                        <td>{{ $detail['hatchet_man_qq'] ?? ''  }}</td>
                    </tr>
                    <tr>
                        <td>剩余代练时间</td>
                        <td>{{ $detail['left_time'] ?? ''  }}</td>
                    </tr>
                    <tr>
                        <td>发布时间</td>
                        <td> {{ $detail['created_at'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>接单时间</td>
                        <td>{{ $detail['receiving_time'] ?? ''  }}</td>
                    </tr>
                    <tr>
                        <td>提验时间</td>
                        <td>{{ $detail['check_time'] ?? ''  }}</td>
                    </tr>
                    <tr>
                        <td>结算时间</td>
                        <td>{{ $detail['checkout_time'] ?? ''  }}</td>
                    </tr>

                    <tr>
                        <td>发单客服</td>
                        <td>{{ $detail['customer_service_name'] ?? ''  }}</td>
                    </tr>
                    <tr>
                        <td>撤销说明：</td>
                        <td>{{ $detail['consult_desc'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>仲裁说明：</td>
                        <td>{{ $detail['complain_desc'] ?? '无' }}
                            <span style="color:red">提示：客服将根据订单留言和截图的情况进行仲裁，仲裁中有新的情况和证据，请提交留言和截图。</span>
                        </td>
                    </tr>
                    <tr>
                        <td>来源价格</td>
                        <td>{{ $detail['source_price'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>支付代练费用：</td>
                        <td>
                            @if(isset($detail['payment_amount']) && in_array($detail['status'], [19, 20, 21, 23 ,24]))
                                {{ intval($detail['payment_amount']) == $detail['payment_amount'] ? intval($detail['payment_amount']) : $detail['payment_amount'] }}
                            @else

                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>获得赔偿金额</td>
                        <td>{{ $detail['get_amount'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>手续费：</td>
                        <td>{{ $detail['poundage'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>最终支付金额：</td>
                        <td>{{ $detail['profit'] ?? ''  }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @if ($detail['creator_primary_user_id'] == auth()->user()->getPrimaryUserId())
        <div class="layui-card" style="margin-bottom: 72px;">
            <div class="layui-card-header">淘宝数据</div>
            <div class="layui-card-body qs-text">
                <table class="layui-table">
                    <colgroup>
                        <col width="115">
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
        @endif
    </div>
@endsection

@section('pop')
<div class="layui-boxx" id="layui-boxx" style="display: none">
    <div class="layui-layer-titlee" style="cursor: move;">
        <div class="layui-unselect layim-chat-title">
        </div>
    </div>
    <div id="layui-layim-chat" class="layui-layer-contentt">
        <ul class="layui-unselect layim-chat-list">
            <li class="layim-friend1008612 layim-chatlist-friend1008612 layim-this" layim-event="tabChat">
                <img src="lib/css/res/touxiang.jpg">
                <span>小闲</span>
                <i class="layui-icon" layim-event="closeChat">ဇ</i>
            </li>
        </ul>
        <div class="layim-chat-box">
            <div class="layim-chat layim-chat-friend layui-show">
                <div class="layim-chat-main">

                </div>
                <div class="layim-chat-footer">
                    <div class="layim-chat-textarea">
                        <textarea name="layim-chat-textarea"></textarea>
                    </div>
                    <div class="layim-chat-bottom">
                        <div class="layim-chat-send">
                            <span class="qs-btn opt-btn cancel" layim-event="closeThisChat">关闭</span>
                            <span class="qs-btn opt-btn layim-send-btn" layim-event="send">发送</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="layui-layer-setwin">
        <a class="layui-layer-ico layui-layer-close layui-layer-close1" href="javascript:;"></a>
    </span>
</div>
<div class="layui-carousel" id="carousel" style="display: none"></div>
<div class="consult" style="display: none; padding:  0 20px">
    <div class="layui-tab-content">
        <span style="color:red;margin-right:15px;">双方友好协商撤单，若有分歧可以在订单中留言或申请客服介入；若申请成功，此单将被锁定，若双方取消撤单会退回至原有状态。<br/></span>
        <form class="layui-form" method="POST" action="">
            {!! csrf_field() !!}
            <div style="width: 80%" id="info">
                <div class="layui-form-item">
                    <label class="layui-form-label">*我愿意支付代练费（元）</label>
                    <div class="layui-input-block">
                        <input type="text" name="amount" lay-verify="required|number" value="" autocomplete="off" placeholder="请输入代练费" class="layui-input" style="width:400px">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">我已支付代练费（元）</label>
                    <div class="layui-input-block">
                        <input type="text" name="order_amount" id="order_amount" lay-verify="" value="" autocomplete="off" placeholder="" class="layui-input" style="width:400px" disabled>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">*需要对方赔付保证金</label>
                    <div class="layui-input-block">
                        <input type="text" name="deposit" lay-verify="required|number" value="" autocomplete="off" placeholder="请输入保证金" class="layui-input" style="width:400px">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">对方已预付安全保证金（元）</label>
                    <div class="layui-input-block">
                        <input type="text" name="safe" id="safe" lay-verify="" value="" autocomplete="off" placeholder="" class="layui-input" style="width:400px" disabled>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">对方已预付效率保证金（元）</label>
                    <div class="layui-input-block">
                        <input type="text" name="effect" id="effect" lay-verify="" value="" autocomplete="off" placeholder="" class="layui-input" style="width:400px" disabled>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">撤销理由</label>
                    <div class="layui-input-block">
                        <textarea placeholder="请输入撤销理由" name="revoke_message" lay-verify="required" class="layui-textarea" style="width:400px"></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-block">
                        <button class="layui-btn  layui-btn-normal" lay-submit lay-filter="consult">立即提交</button>
                        <span cancel class="layui-btn  layui-btn-normal cancel">取消</span>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="complain" style="display: none; padding: 20px">
    <form class="layui-form">
        <input type="hidden" id="order_no" name="order_no">
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">证据截图</label>
            <div class="layui-input-block">
                <div class="fileinput-group">
                    <div class="fileinput fileinput-new" data-provides="fileinput" id="exampleInputUpload">
                        <div class="fileinput-new thumbnail" style="width: 100px;height: 100px;">
                            <img id='picImg' style="width: 60px;height:60px;margin:auto;margin-top:20px;" src="/frontend/images/upload-btn-bg.png" alt="" />
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail pic-1" style="width: 100px;height: 100px;"></div>
                        <div style="height: 0;">
                                <span class=" btn-file" style="padding: 0;">
                                    <span class="fileinput-new"></span>
                                    <span class="fileinput-exists"></span>
                                    <input type="file" name="pic1" id="picID" accept="image/gif,image/jpeg,image/x-png" />
                                </span>
                            <a href="javascript:;" class="fileinput-exists" data-dismiss="fileinput" style="padding: 0;">
                                <i class="iconfont icon-shanchu4"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="fileinput-group">
                    <div class="fileinput fileinput-new" data-provides="fileinput" id="exampleInputUpload">
                        <div class="fileinput-new thumbnail" style="width: 100px;height: 100px;">
                            <img id='picImg' style="width: 60px;height:60px;margin:auto;margin-top:20px;" src="/frontend/images/upload-btn-bg.png" alt="" />
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail pic-2" style="width: 100px;height: 100px;"></div>
                        <div>
                                <span class="btn-file" style="padding: 0;">
                                    <span class="fileinput-new"></span>
                                    <span class="fileinput-exists"></span>
                                    <input type="file" name="pic1" id="picID" accept="image/gif,image/jpeg,image/x-png" />
                                </span>
                            <a href="javascript:;" class="fileinput-exists" data-dismiss="fileinput" style="padding: 0;">
                                <i class="iconfont icon-shanchu4"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="fileinput-group">
                    <div class="fileinput fileinput-new" data-provides="fileinput" id="exampleInputUpload">
                        <div class="fileinput-new thumbnail" style="width: 100px;height: 100px;">
                            <img id='picImg' style="width: 60px;height:60px;margin:auto;margin-top:20px;" src="/frontend/images/upload-btn-bg.png" alt="" />
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail pic-3" style="width: 100px;height: 100px;"></div>
                        <div>
                               <span class="btn-file" style="padding: 0;">
                                    <span class="fileinput-new"></span>
                                    <span class="fileinput-exists"></span>
                                    <input type="file" name="pic1" id="picID" accept="image/gif,image/jpeg,image/x-png" />
                               </span>
                            <a href="javascript:;" class="fileinput-exists" data-dismiss="fileinput" style="padding: 0;">
                                <i class="iconfont icon-shanchu4"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">仲裁理由</label>
            <div class="layui-input-block">
                <textarea placeholder="请输入申请仲裁理由" name="complain_message"  class="layui-textarea"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-normal" id="submit" lay-submit lay-filter="complain">确认
                </button>
                <span cancel class="layui-btn  layui-btn-normal cancel">取消</span>
            </div>
        </div>
    </form>
</div>
<div style="padding: 20px 20px 0 20px;display: none" id="add_price_pop">
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">增加金额(元)</label>
            <div class="layui-input-block">
                <input type="text" name="price" required  lay-verify="required|number" placeholder="请输入增加金额" autocomplete="off" class="layui-input">
            </div>
        </div>
    </form>
</div>
<div style="padding: 20px 20px 0 20px;display: none" id="add_time_pop">
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">增加时间</label>
                <div class="layui-input-inline" style="width: 100px;">
                    <input type="text" name="day"   autocomplete="off" class="layui-input" lay-verify="required|number">
                </div>
                <div class="layui-form-mid">天</div>
                <div class="layui-input-inline" style="width: 100px;">
                    <input type="text" name="hour"  autocomplete="off" class="layui-input" lay-verify="required|number">
                </div>
                <div class="layui-form-mid">小时</div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
    <script id="imTemplate" type="text/html">
        <ul>
            @{{#  layui.each(d.message, function(index, item){ }}
                @{{# if(item.sender == '您'){ }}
                    <li class="layim-chat-mine">
                        <div class="layim-chat-user">
                            <img src="/frontend/images/service_avatar.jpg">
                            <cite>
                                <i>@{{ item.send_time }}</i>您
                            </cite>
                        </div>
                        <div class="layim-chat-text">@{{ item.send_content}}</div>
                    </li>
                @{{# }else{  }}
                    <li>
                        <div class="layim-chat-user">
                            <img src="/frontend/images/customer_avatar.jpg">
                            <cite>打手
                                <i> @{{ item.send_time }}</i>
                            </cite>
                        </div>
                        <div class="layim-chat-text">@{{ item.send_content}}</div>
                    </li>
                @{{# }  }}
            @{{# }); }}
        </ul>
    </script>
    <script id="images" type="text/html">
        <div carousel-item="" id="">
            @{{# var i = 0; layui.each(d, function(index, item){ }}
                <div  style="background: url(@{{ item.url }}) no-repeat center/contain;"  @{{# if(i == 0){ }} class="layui-this" @{{# } }} >
                    <div class="carousel-tips" >@{{ item.description }} &nbsp;&nbsp;&nbsp; @{{ item.created_at }} </div>
                </div>
                @{{# if(i == 0){   i = 1;  } }}
            @{{# }); }}
        </div>
    </script>
    <script src="/frontend/js/bootstrap-fileinput.js"></script>
    <script src="/vendor/zoomify.min.js"></script>
    <script>
        layui.use(['form', 'layedit', 'laydate', 'laytpl', 'element', 'carousel'], function(){
            var form = layui.form, layer = layui.layer, layTpl = layui.laytpl, element = layui.element, carousel =  layui.carousel;
            var gameId = '{{ $detail['game_id'] }}';
            var delivery = 0;

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

            element.on('tab()', function(){
                var id = this.getAttribute('lay-id');
                if (id == 3) {
                    $.get('{{ route('frontend.workbench.leveling.history', ['order_no' => $detail['no']]) }}', {id:1}, function (result) {
                        $('#message').html(result);
                    });
                }
                if (id == 2) {
                    $.get('{{ route('frontend.workbench.leveling.arbitration-info').'?no=' . $detail['no'] }}', {id:1}, function (result) {
                        $('#arbitration-info').html(result);
                    });
                }
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

                $.post('{{ route('frontend.workbench.leveling.update') }}', {data: data.field}, function (result) {
                    if (result.status == 1) {
                        layer.open({
                            content: '修改成功!',
                            btn: ['继续发布', '订单列表'],
                            btn1: function(index, layero){
                                window.location.href="{{ route('frontend.workbench.leveling.wait') }}";
                            },
                            btn2: function(index, layero){
                                window.location.href="{{ route('frontend.workbench.leveling.index') }}";
                            }
                        });
                    } else {
                        layer.msg(result.message);
                    }
                    layer.close(load);
                }, 'json');
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
            // 对订单操作
            $('.layui-form').on('click', '.opt-btn', function () {

                var opt = $(this).attr("data-operation");
                var orderNo = $(this).attr("data-no");
                var orderAmount = $(this).attr("data-amount");
                var orderSafe = $(this).attr("data-safe");
                var orderEffect = $(this).attr("data-effect");
                var apiAmount = $(this).attr("api_amount");
                var apiDeposit = $(this).attr("api_deposit");
                var apiService = $(this).attr("api_service");
                var who=$(this).attr("who");
                var reason=$(this).attr("reason");

                if (!orderAmount) {
                    orderAmount = 0;
                }
                if (!orderSafe) {
                    orderSafe = 0;
                }
                if (!orderEffect) {
                    orderEffect = 0;
                }
                $('#order_amount').val(orderAmount);
                $('#safe').val(orderSafe);
                $('#effect').val(orderEffect);

                if (!opt) {
                    return false;
                }
                if (opt == 'detail') {
                    window.open('{{ route('frontend.workbench.leveling.detail') }}?no=' + orderNo);
                    return false;
                }
                // 留言
                if (opt == 'message') {
                    window.open('{{ route('frontend.workbench.leveling.detail') }}' + '?no=' + orderNo + '&tab=1');
                    return false;
                }
                // 操作记录
                if (opt == 'operationRecord') {
                    window.open('{{ route('frontend.workbench.leveling.detail') }}' + '?no=' + orderNo + '&tab=2');
                    return false;
                }
                // 重发
                if (opt == 'repeat') {
                    window.open('{{ route('frontend.workbench.leveling.repeat') }}' + '/' + orderNo);
                    return false;
                }
                // 联系旺旺
                if (opt == 'wangWang') {
                    var wangWang = $(data.elem).find("option:selected").attr("data-wang-wang");
                    window.open('http://www.taobao.com/webww/ww.php?ver=3&touid=' + wangWang + '&siteid=cntaobao&status=1&charset=utf-8" class="btn btn-save buyer" target="_blank" title="' + wangWang);
                    return false;
                }
                if (opt == 'sendSms') {
                    $('.send-message  .layui-form').append('<input type="hidden" name="no" data-opt="' + orderNo + '"/>');
                    layer.open({
                        type: 1,
                        shade: 0.2,
                        title: '发送短信',
                        area: ['500px'],
                        content: $('.send-message')
                    });
                    return false
                }
                if (opt == 'revoke') {
                    layer.open({
                        type: 1,
                        shade: 0.2,
                        title: '协商撤销',
                        area: ['650px', '550px'],
                        content: $('.consult')
                    });
                    form.on('submit(consult)', function (data) {
                        $.post("{{ route('frontend.workbench.leveling.consult') }}", {
                            orderNo: orderNo,
                            data: data.field
                        }, function (result) {
                            if (result.status == 1) {
                                layer.closeAll();
                                layer.alert(result.message, function () {
                                    location.reload();
                                });
                            } else {
                                layer.alert(result.message);
                            }
                        });
                        return false;
                    });

                } else if (opt == 'applyArbitration') {
                    layer.open({
                        type: 1,
                        shade: 0.2,
                        title: '申请仲裁',
                        area: ['600px', '380px'],
                        content: $('.complain')
                    });
                    $('#order_no').val(orderNo);
                    form.on('submit(complain)', function (data) {

                        var complainLoad = layer.load(2, {shade:[0.2, '#000']});
                        var pic1 = $('.pic-1 img').attr('src');
                        var pic2 = $('.pic-2 img').attr('src');
                        var pic3 = $('.pic-3 img').attr('src');

                        if (pic1 == undefined && pic2 == undefined && pic3 == undefined) {
                            layer.alert('请至少上传一张图片');
                        } else {
                            $.post("{{ route('frontend.workbench.leveling.complain') }}", {
                                orderNo: orderNo,
                                data: data.field,
                                pic1: pic1,
                                pic2: pic2,
                                pic3: pic3
                            }, function (result) {
                                layer.close(complainLoad);
                                if (result.status == 1) {
                                    layer.alert(result.message, function () {
                                        location.reload();
                                    });
                                } else {
                                    layer.alert(result.message);
                                }
                            });
                        }
                        return false;
                    });

                } else if (opt == 'delete') {
                    layer.confirm('确认删除吗？', {icon: 3, title: '提示'}, function (index) {
                        $.post("{{ route('frontend.workbench.leveling.status') }}", {
                            orderNo: orderNo,
                            keyWord: opt
                        }, function (result) {
                            if (result.status == 1) {
                                layer.alert(result.message, function () {
                                    location.reload();
                                });
                            } else {
                                layer.alert(result.message);
                            }
                        });

                        layer.close(index);
                    });
                } else if (opt == 'complete') {
                    layer.confirm("确定完成订单？<br/> <input type='checkbox' id='delivery'> 同时提交淘宝/天猫订单发货", {
                        title: '提示'
                    }, function (index) {
                        $.post("{{ route('frontend.workbench.leveling.status') }}", {
                            orderNo: orderNo,
                            keyWord: opt,
                            delivery: delivery
                        }, function (result) {
                            if (result.status == 1) {
                                layer.alert(result.message, function () {
                                    location.reload();
                                });
                            } else {
                                layer.alert(result.message);
                            }
                        });
                        layer.close(index);
                    });
                } else if (opt == 'agreeRevoke') {
                    if (who == 1) {
                        var message = "对方发起协商撤销。 对方支付代练费"+apiAmount+"元，你支付保证金"+apiDeposit+"元。原因："+reason+"，确定同意撤销？";
                    } else {
                        var message = "对方发起协商撤销。 你支付代练费"+apiAmount+"元，对方支付保证金"+apiDeposit+"元。原因："+reason+"，确定同意撤销？";
                    }
                    layer.confirm(message, {icon: 3, title: '提示'}, function (index) {
                        $.post("{{ route('frontend.workbench.leveling.status') }}", {
                            orderNo: orderNo,
                            keyWord: opt
                        }, function (result) {
                            if (result.status == 1) {
                                layer.alert(result.message, function () {
                                    location.reload();
                                });
                            } else {
                                layer.alert(result.message, function () {
                                    layer.closeAll();
                                });
                            }
                        });
                        layer.close(index);
                    });
                } else {
                    $.post("{{ route('frontend.workbench.leveling.status') }}", {
                        orderNo: orderNo,
                        keyWord: opt
                    }, function (result) {
                        if (result.status == 1) {
                            layer.alert(result.message, function () {
                                location.reload();
                            });

                        } else {
                            layer.alert(result.message, function () {
                                layer.closeAll();
                            });
                        }
                    });
                }

                return false;
            });
            // 按游戏加载区\代练类型\代练模版\商户QQ
            loadGameInfo();
            // 加载下单必要的信息
            function loadGameInfo() {
                loadRegionType();
                loadGameLevelingTemplate();
                loadBusinessmanContactTemplate();
                setDefault();
                $('.serve').html('');
            }
            // 加载下拉框的下级选项
            function loadSelectChild(choseId) {
                $.post('{{ route('frontend.workbench.get-select-child') }}', {parent_id:choseId}, function (result) {
                    $('.serve').html(result);
                    $(result).each(function (index, value) {
                        $('.serve').append('<option value="' + value.field_value + '">' + value.field_value + '</option>');
                    });
                    layui.form.render();
                }, 'json');
            }
            // 增加代练金额
            $('#add_price').click(function(){
                layer.open({
                    type: 1,
                    title: '增加代练金额',
                    shade: 0.2,
                    area:['400px', 'auto'],
                    btn: ['确定', '取消'],
                    btnAlign:'c',
                    moveType: 1,
                    content: $('#add_price_pop'),
                    yes: function (layero) {
                        var newAmount = $('#add_price_pop').find('input[name=price]').val();
                        if (parseInt(newAmount) <= 0) {
                            layer.msg('增加价格需大于0');
                        } else {
                            $.post("{{ route('frontend.workbench.leveling.add-amount') }}", {no:'{{ $detail['no'] }}', amount:newAmount}, function (result) {
                                if(result.status == 1) {
                                    layer.alert(result.message, function () {
                                        layer.closeAll();
                                        location.reload();
                                    })
                                } else {
                                    layer.msg(result.message);
                                }
                            }, 'json');
                        }
                    }
                });
            });
            // 增加代练时间
            $('#add_time').click(function(){
                layer.open({
                    type: 1,
                    title: '增加代练时间',
                    shade: 0.2,
                    area: ['450px', 'auto'],
                    btn: ['确定', '取消'],
                    btnAlign: 'c',
                    moveType: 1,
                    content: $('#add_time_pop'),
                    yes: function (layero) {
                        var day = parseInt($('#add_time_pop').find('input[name=day]').val());
                        var hour = parseInt($('#add_time_pop').find('input[name=hour]').val());

                        $.post("{{ route('frontend.workbench.leveling.add-time') }}", {
                            no: '{{ $detail['no'] }}',
                            day: day,
                            hour: hour
                        }, function (result) {
                            if (result.status == 1) {
                                layer.alert(result.message, function () {
                                    layer.closeAll();
                                    location.reload();
                                })
                            } else {
                                layer.msg(result.message);
                            }
                        }, 'json');

                    }
                });
            });
            // 模板使用说明
            $('#instructions').click(function () {
                layer.open({
                    type: 1
                    ,title: '使用说明' //不显示标题栏
                    ,closeBtn: false
                    ,area: '470px;'
                    ,shade: 0.2
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
                        layui.form.render();
                    }
                });
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
            // 设置固定的订单信息
            function setDefault() {
                // 设置区
                $("select[name=region]").val('{{  $detail['region']  }}');
                loadSelectChild($('.region').find("option:selected").attr('data-id'));
                $("select[name=serve]").val('{{  $detail['serve']  }}');
                $("select[name=game_leveling_type]").val('{{  $detail['game_leveling_type']  }}');
                $("select[name=user_qq]").val('{{  $detail['user_qq']  }}');
                layui.form.render();
            }
            // 加载留言
            function loadMessage(bingId) {
                var messageBingId = bingId ? bingId : 0;

                $.get("{{ route('frontend.workbench.leveling.leave-message', ['order_no' => $detail['no']]) }}?bing_id=" + messageBingId, function (result) {
                    if (result.status === 1) {
                        var getTpl = imTemplate.innerHTML, view = $('.layim-chat-main');
                        layTpl(getTpl).render(result.content, function(html){
                            view.html(html);
                            layui.form.render();
                        });
                        $('.layim-chat-main').scrollTop( $('.layim-chat-main')[0].scrollHeight );
                    }
                });
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
            // 留言弹窗
            $('#im').click(function () {
                layer.open({
                    type: 1,
                    title: false,
                    closeBtn: false,
                    area: ['850px', '561px'],
                    shade: 0.2,
                    moveType: 1,  //拖拽模式，0或者1
                    content: $('#layui-boxx'),
                    success: function (layero) {
                        loadMessage(1);
                    }
                });
            });
            // 发送留言
            $('.layim-send-btn').click(function(){
                var message = $('[name=layim-chat-textarea]').val();
                if (message) {
                    $.post("{{ route('frontend.workbench.leveling.send-message') }}", {
                        'order_no': "{{ $detail['no'] }}",
                        'message':message
                    }, function (data) {
                        $('[name=layim-chat-textarea]').val('');
                        if (data.status === 1) {
                            loadMessage(0);
                        } else {
                            layer.msg(data.message);
                            return false;
                        }
                    }, 'json');
                } else {
                    layer.msg('请输入要发送的内容');
                }
            });
            // 查看图片
            var ins = carousel.render({
                elem: '#carousel',
                anim: 'fade',
                width: '500px',
                arrow: 'always',
                autoplay: false,
                height: '500px',
                indicator: 'none'
            });
            $('#carousel-btn').click(function () {
                $.get("{{ route('frontend.workbench.leveling.leave-image', ['order_no' => $detail['no']]) }}", function (result) {
                    if (result.status === 1) {
                        if (result.content.length > 0 ) {
                            var getTpl = images.innerHTML, view = $('#carousel');
                            layTpl(getTpl).render(result.content, function(html){
                                view.html(html);
                                layui.form.render();
                            });

                            layer.open({
                                type: 1,
                                title: false ,
                                area: ['50%', '500px'],
                                shade: 0.8,
                                shadeClose: true,
                                moveType: 1,
                                content: $('#carousel'),
                                success: function () {
                                    //改变下时间间隔、动画类型、高度
                                    ins.reload({
                                        elem: '#carousel',
                                        anim: 'fade',
                                        width: '100%',
                                        arrow: 'always',
                                        autoplay: false,
                                        height: '100%',
                                        indicator: 'none'
                                    });
                                }
                            });
                        } else {
                            layer.msg('暂时没有图片');
                        }
                    }
                });
            });
            // 操作记录
            $('#operation').click(function () {
                layer.open({
                    type: 2,
                    title: '操作记录',
                    shadeClose: true,
                    area: ['850px', '500px'],
                    shade: 0.8,
                    moveType: 1,
                    content: '{{ route('frontend.workbench.leveling.history', ['order_no' => $detail['no']]) }}',
                    success: function (layero) {
                    }
                });
            });
            // 取消操作
            $('.cancel').click(function () {
                layer.closeAll();
            });

            // 发送证据
            // $('.add_evidence').click(function(){
            form.on('submit(add_evidence)', function (data) {
                var content = $('[name=content]').val();
                var pic = $('.pic-add img').attr('src');
                var arbitration_id = this.getAttribute('lay-id');
                var no = this.getAttribute('lay-no');
                if (content) {
                    $.post("{{ route('frontend.workbench.leveling.add-arbitration') }}", {
                        'content':content,
                        'pic':pic,
                        'arbitration_id':arbitration_id,
                        'no':no
                    }, function (result) {
                        if (result.status === 1) {
                            autoLoad("{{ route('frontend.workbench.leveling.arbitration-info') }}", '#arbitration-info', no);
                            layer.msg(result.message);
                        } else {
                            layer.msg(result.message);
                        }
                    }, 'json');
                    return false;
                } else {
                    layer.msg('请输入要发送的内容');
                }
                return false;
            });

            // 不刷新页面
            function autoLoad(url, node, no) {
                $.get(url, {no:no}, function (result) {
                    $(node).html(result);
                    form.render();
                }, 'json');
            }

            $('.layui-card').on('click', '.photo', function () {
                var imgSrc = $(this).attr('data-img');
                layer.photos({
                    photos: {
                        "id": 123, //相册id
                        "data": [   //相册包含的图片，数组格式
                            {
                                "src": imgSrc, //原图地址
                                "thumb": imgSrc //缩略图地址
                            }
                        ]
                    },
                    anim: -1,
                    shade: 0.8
                });
            });

            $('body').on('click', '#delivery', function () {
                if ($(this).is(':checked')) {
                    delivery = 1;
                } else {
                    delivery = 0;
                }
            });
        });
    </script>
@endsection