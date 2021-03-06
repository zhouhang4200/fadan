@extends('frontend.v1.layouts.app')

@section('title', '工作台-代练订单')

@section('css')
    <link rel="stylesheet" href="/frontend/css/bootstrap-fileinput.css">
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

        .layui-card-header {
            height: auto;
            border-bottom: none;
            padding-bottom: 0;
        }

        .layui-card .layui-tab {
            margin-top: 3px;
            margin-bottom: 12px;
        }
        .layui-form-item {
            margin-bottom: 12px;
        }
        .last-item{
            margin-bottom: 5px;
        }
        .layui-tab-title li{
            min-width: 50px;
            font-size: 12px;
        }
        .qsdate{
            float: left;
            width: 40% !important;
        }
        .layui-card-header{
            padding: 15px 15px 0 15px;;

        }
        .layui-card-body{
            padding-top: 0;
        }
        .layui-card .layui-tab-brief .layui-tab-content {
            padding: 0px;
        }
        /* 修改同意字体为12px */
        .last-item .last-item-btn {
            margin-left: 0;
        }
        @media screen and (max-width: 990px){
            .layui-col-md12 .layui-card .layui-card-header .layui-row .layui-form .first .layui-form-label{
                width: 80px;
                padding: 5px 10px;
                text-align: right;
            }
            .layui-col-md12 .first .layui-input-block{
                margin-left: 110px;
            }
            .last-item .last-item-btn {
                margin-left: 40px;
            }
        }
        /* 改写header高度 */
        .layui-card-header {
            font-size:12px;
        }
        .layui-table-edit {
            height: 50px;
        }

        .layui-layer-btn .layui-layer-btn0 {
            border-color: #ff8500;
            background-color: #ff8500;
            color: #fff;
        }
        .layui-table-edit:focus {
            border-color: #e6e6e6 !important
        }
    </style>
@endsection

@section('main')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-header">
                <div class="layui-row layui-col-space5">
                    <form class="layui-form" action="">
                        <div class="layui-col-md3 first">
                            <div class="layui-form-item">
                                <label class="layui-form-label" >订单单号</label>
                                <div class="layui-input-block" style="">
                                    <input type="text" name="no" lay-verify="title" autocomplete="off" placeholder="请输入订单号" class="layui-input">
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">玩家旺旺</label>
                                <div class="layui-input-block">
                                    <input type="text" name="wang_wang" lay-verify="title" autocomplete="off" placeholder="请输入玩家旺旺" class="layui-input">
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">代练游戏</label>
                                <div class="layui-input-block">
                                    <select name="game_id" lay-search="" lay-filter="game">
                                        <option value="">请选择游戏</option>
                                        @foreach($game as  $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">代练类型</label>
                                <div class="layui-input-block">
                                    <select name="game_leveling_type" lay-search="">
                                        <option value="">请选择代练类型</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3 first">
                            <div class="layui-form-item ">
                                <label class="layui-form-label"  style="">发单客服</label>
                                <div class="layui-input-block" style="">
                                    <select name="customer_service_name" lay-search="">
                                        <option value="">请选择</option>
                                        @forelse($employee as $item)
                                            <option value="{{ $item->username }}">{{ $item->username }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3 ">
                            <div class="layui-form-item last-item">
                                <label class="layui-form-label">代练平台</label>
                                <div class="layui-input-block">
                                    <select name="platform">
                                        <option value="">全部</option>
                                        @foreach (config('partner.platform') as $key => $value)
                                            <option value="{{ $key }}">{{ $value['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md4">
                            <div class="layui-form-item last-item">
                                <label class="layui-form-label">发布时间</label>
                                <div class="layui-input-block">
                                    <input type="text"  class="layui-input qsdate" id="test-laydate-start" name="start_date" placeholder="开始日期">
                                    <div class="layui-form-mid">
                                        -
                                    </div>
                                    <input type="text" class="layui-input qsdate" id="test-laydate-end" name="end_date" placeholder="结束日期">
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md2">
                            <div class="layui-form-item last-item">
                                <div class="layui-input-block last-item-btn">
                                    <button class="qs-btn" lay-submit="" lay-filter="search" style="height: 30px;line-height: 30px;float: left;font-size: 12px;">搜索</button>
                                    <button class="qs-btn" lay-submit="" lay-filter="export" style="margin-left:10px;height: 30px;line-height: 30px;float: left;font-size: 12px;">导出</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="layui-card-body">
                <div class="layui-tab layui-tab-brief layui-form" lay-filter="order-list">
                    <ul class="layui-tab-title">
                        <li class="layui-this" lay-id="0">全部 <span  class="layui-badge layui-bg-blue wait-handle-quantity @if(waitHandleQuantity(Auth::user()->id) == 0) layui-hide  @endif">{{ waitHandleQuantity(Auth::user()->id) }}</span></li>
                        <li class="" lay-id="1">未接单
                            <span class="qs-badge quantity-1 layui-hide"></span>
                        </li>
                        <li class="" lay-id="13">代练中
                            <span class="qs-badge quantity-13 layui-hide"></span>
                        </li>
                        <li class="" lay-id="14">待验收
                            <span class="qs-badge quantity-14 layui-hide"></span>
                        </li>
                        <li class="" lay-id="15">撤销中
                            <span class="qs-badge quantity-15 layui-hide"></span>
                        </li>
                        <li class="" lay-id="16">仲裁中
                            <span class="qs-badge quantity-16 layui-hide"></span>
                        </li>
                        <li class="" lay-id="100">淘宝退款中
                            <span class="qs-badge quantity-100 layui-hide"></span>
                        </li>
                        <li class="" lay-id="17">异常
                            <span class="qs-badge quantity-17 layui-hide"></span>
                        </li>
                        <li class="" lay-id="18">锁定
                            <span class="qs-badge quantity-18 layui-hide"></span>
                        </li>
                        <li class="" lay-id="19">已撤销
                        </li>
                        <li class="" lay-id="20">已结算
                        </li>
                        <li class="" lay-id="21">已仲裁
                        </li>
                        <li class="" lay-id="22">已下架
                        </li>
                        <li class="" lay-id="24">已撤单
                        </li>
                    </ul>
                </div>
                <div id="order-list" lay-filter="order-list">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pop')
    <div class="consult" style="display: none; padding:  0 20px">
        <div class="layui-tab-content">
            <span style="color:red;margin-right:15px;">双方友好协商撤单，若有分歧可以在订单中留言或申请客服介入；若申请成功，此单将被锁定，若双方取消撤单会退回至原有状态。<br/></span>
            <form class="layui-form" method="POST" action="">
                {!! csrf_field() !!}
                <div style="width: 80%" id="info">
                    <div class="layui-form-item">
                        <label class="layui-form-label">*我愿意支付代练费（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="amount" lay-verify="required|number" data-opt="" autocomplete="off"
                                   placeholder="请输入代练费" class="layui-input" style="width:400px">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">我已支付代练费（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="order_amount" id="order_amount" lay-verify="" data-opt=""
                                   autocomplete="off" placeholder="" class="layui-input order_amount" style="width:400px" disabled>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">*需要对方赔付保证金</label>
                        <div class="layui-input-block">
                            <input type="text" name="deposit" lay-verify="required|number" data-opt=""
                                   autocomplete="off"
                                   placeholder="请输入保证金" class="layui-input" style="width:400px">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">对方已预付安全保证金（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="safe" id="safe" lay-verify="" data-opt="" autocomplete="off"
                                   placeholder="" class="layui-input safe" style="width:400px" disabled>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">对方已预付效率保证金（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="effect" id="effect" lay-verify="" data-opt="" autocomplete="off"
                                   placeholder="" class="layui-input effect" style="width:400px" disabled>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">撤销理由</label>
                        <div class="layui-input-block">
                            <textarea placeholder="请输入撤销理由" name="revoke_message" lay-verify="required"
                                      class="layui-textarea" style="width:400px"></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <button class="qs-btn  layui-btn-normal" lay-submit lay-filter="consult">立即提交</button>
                            <span cancel class="qs-btn  layui-btn-normal cancel">取消</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="consult2" style="display: none; padding:  0 20px">
        <div class="layui-tab-content">
            <span style="color:red;margin-right:15px;">双方友好协商撤单，若有分歧可以在订单中留言或申请客服介入；若申请成功，此单将被锁定，若双方取消撤单会退回至原有状态。<br/></span>
            <form class="layui-form" method="POST" action="">
                {!! csrf_field() !!}
                <div style="width: 80%" id="info">
                    <div class="layui-form-item">
                        <label class="layui-form-label">*需要对方支付代练费（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="amount" lay-verify="required|number" data-opt="" autocomplete="off"
                                   placeholder="请输入代练费" class="layui-input" style="width:400px">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">对方已支付代练费（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="order_amount" id="order_amount" lay-verify="" data-opt=""
                                   autocomplete="off" placeholder="" class="layui-input order_amount" style="width:400px" disabled>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">*我原意赔付保证金</label>
                        <div class="layui-input-block">
                            <input type="text" name="deposit" lay-verify="required|number" data-opt=""
                                   autocomplete="off"
                                   placeholder="请输入保证金" class="layui-input" style="width:400px">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">我已预付安全保证金（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="safe" id="safe" lay-verify="" data-opt="" autocomplete="off"
                                   placeholder="" class="layui-input safe" style="width:400px" disabled>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">我已预付效率保证金（元）</label>
                        <div class="layui-input-block">
                            <input type="text" name="effect" id="effect" lay-verify="" data-opt="" autocomplete="off"
                                   placeholder="" class="layui-input effect" style="width:400px" disabled>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">撤销理由</label>
                        <div class="layui-input-block">
                            <textarea placeholder="请输入撤销理由" name="revoke_message" lay-verify="required"
                                      class="layui-textarea" style="width:400px"></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <button class="qs-btn  layui-btn-normal" lay-submit lay-filter="consult">立即提交</button>
                            <span cancel class="qs-btn  layui-btn-normal cancel">取消</span>
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
                    <button class="qs-btn layui-btn-normal" id="submit" lay-submit lay-filter="complain">确认
                    </button>
                    <span cancel class="qs-btn  layui-btn-normal cancel">取消</span>
                </div>
            </div>
        </form>
    </div>
    <div class="complete-upload" style="display: none; padding: 20px">
        <form class="layui-form">
            <input type="hidden" id="order_no" name="order_no">
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">完成截图</label>
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
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="qs-btn layui-btn-normal" id="submit" lay-submit lay-filter="complete-upload">确认
                    </button>
                    <span cancel class="qs-btn  layui-btn-normal cancel">取消</span>
                </div>
            </div>
        </form>
    </div>
    <div class="layui-carousel" id="carousel" style="display: none"></div>
@endsection

@section('js')
    <script src="/frontend/js/bootstrap-fileinput.js"></script>
    <script type="text/html" id="operation">

        @{{# if (d.master) {  }}

            @{{# if (d.status == 1) {  }}
                {{--<button class="qs-btn qs-btn-sm" style="width: 80px;" data-opt="offSale" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">下架</button>--}}
                <button class="qs-btn qs-btn-primary qs-btn-sm qs-btn-table" style="width: 80px;"  data-opt="delete" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">撤单</button>
                {{--@{{# if(d.day >= 1 && d.is_top != 1) {  }}--}}
                <button class="qs-btn qs-btn-sm qs-btn-primary"  style="width: 80px;" data-opt="top" data-no="@{{ d.no }}">置顶</button>
                {{--@{{# }  }}--}}
            @{{# } else if (d.status == 13) {  }}
                <button class="qs-btn qs-btn-sm" style="width: 80px;"  data-opt="revoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">撤销</button>
                <button class="qs-btn qs-btn-primary qs-btn-sm qs-btn-table" style="width: 80px;"  data-opt="applyArbitration" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">申请仲裁</button>
            @{{# } else if (d.status == 14) {  }}
                <button class="qs-btn qs-btn-sm" style="width: 80px;"  data-opt="showPhoto" data-no="@{{ d.no }}">查看图片</button>
                <button class="qs-btn qs-btn-sm" style="width: 80px;"  data-opt="complete" data-no="@{{ d.no }}">完成验收</button>

                {{--<button class="qs-btn qs-btn-sm" style="width: 80px;"  data-opt="revoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">撤销</button>--}}
                {{--<button class="qs-btn qs-btn-primary qs-btn-sm qs-btn-table"  style="width: 80px;"  data-opt="applyArbitration" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">申请仲裁</button>--}}
            @{{# } else if (d.status == 15) {  }}

                @{{# if (d.consult == 1) {  }}
                    <button class="qs-btn qs-btn-sm" style="width: 80px;"  data-opt="cancelRevoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消撤销</button>
                @{{# } else if (d.consult == 2) {  }}
                    <button class="qs-btn qs-btn-sm" style="width: 80px;" data-no="@{{ d.no }}" data-opt="agreeRevoke" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}" data-api_amount="@{{ d.leveling_consult.api_amount }}" data-api_deposit="@{{ d.leveling_consult.api_deposit }}" data-api_service="@{{ d.leveling_consult.api_service }}" data-who="2" data-reason="@{{ d.leveling_consult.revoke_message  }}">同意撤销</button>
                @{{# } }}

                <button class="qs-btn qs-btn-primary qs-btn-sm qs-btn-table"  style="width: 80px;"  data-opt="applyArbitration" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">申请仲裁</button>

            @{{# } else if (d.status == 16) {  }}

                @{{# if (d.complain == 1) {  }}
                    <button class="qs-btn qs-btn-sm" style="width: 80px;"   data-opt="cancelArbitration" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消仲裁</button>
                @{{# } }}

                @{{# if (d.consult == 2) {  }}
                    <button class="qs-btn qs-btn-sm" style="width: 80px;"  data-opt="agreeRevoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}" data-api_amount="@{{ d.leveling_consult.api_amount }}" data-api_deposit="@{{ d.leveling_consult.api_deposit }}" data-api_service="@{{ d.leveling_consult.api_service }}" data-who="2" data-reason="@{{ d.leveling_consult.revoke_message  }}">同意撤销</button>
                @{{# }   }}

            @{{# } else if (d.status == 17) {  }}
                <button class="qs-btn qs-btn-sm" style="width: 80px;"  data-opt="lock" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">锁定</button>
                <button class="qs-btn qs-btn-primary qs-btn-sm qs-btn-table" style="width: 80px;"  data-opt="revoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">撤销</button>
            @{{# } else if (d.status == 18) {  }}
                <button class="qs-btn qs-btn-sm" style="width: 80px;"  data-opt="cancelLock" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消锁定</button>
                <button class="qs-btn qs-btn-primary qs-btn-sm qs-btn-table" style="width: 80px;"  data-opt="revoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">撤销</button>
            @{{# } else if (d.status == 19 || d.status == 20 || d.status == 21) {  }}
                <button class="qs-btn qs-btn-sm" data-opt="repeat" style="width: 80px;"  data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">重发</button>
            @{{# } else if (d.status == 22) {  }}
                <button class="qs-btn qs-btn-sm" style="width: 80px;"  data-opt="onSale" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">上架</button>
                <button class="qs-btn qs-btn-primary qs-btn-sm qs-btn-table" style="width: 80px;"  data-opt="delete" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">撤单</button>
            @{{# } else if (d.status == 23) {  }}
                <button class="qs-btn qs-btn-sm" style="width: 80px;"  data-opt="repeat" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">重发</button>
            @{{# } else if (d.status == 24) {  }}
                <button class="qs-btn qs-btn-sm" style="width: 80px;"  data-opt="repeat" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">重发</button>
            @{{# }  }}
        @{{# } else {  }}

            @{{# if (d.status == 1) {  }}
            {{--<button class="qs-btn qs-btn-sm" style="width: 80px;" data-opt="offSale" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">下架</button>--}}

            @{{# } else if (d.status == 13) {  }}
                <button  class="qs-btn opt-btn"  data-opt="applyComplete" data-no="@{{ d.no }}" data-image-upload="@{{ d.gainer_primary_user_id != 0 ? 1 : 0 }}">申请完成</button>
                <button  class="qs-btn qs-btn-primary qs-btn-sm qs-btn-table" style="width: 80px;"  data-opt="revoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}" data-who="2">协商撤销</button>
            @{{# } else if (d.status == 14) {  }}
                <button  class="qs-btn opt-btn"  data-opt="cancelComplete" data-no="@{{ d.no }}">取消验收</button>
                <button  class="qs-btn qs-btn-primary qs-btn-sm qs-btn-table"  style="width: 80px;"  data-opt="revoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}" data-who="2">协商撤销</button>
            @{{# } else if (d.status == 15) {  }}

                @{{# if (d.consult == 1) {  }}
                    <button  class="qs-btn opt-btn"  data-opt="agreeRevoke" data-no="@{{ d.no }}"  data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}" data-api_amount="@{{ d.leveling_consult.api_amount }}" data-api_deposit="@{{ d.leveling_consult.api_deposit }}" data-api_service="@{{ d.leveling_consult.api_service }}" data-who="1" data-reason="@{{ d.leveling_consult.revoke_message  }}">同意撤销</button>
                @{{# } else if (d.consult == 2) {  }}
                    <button class="qs-btn qs-btn-sm" style="width: 80px;"  data-opt="cancelRevoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消撤销</button>
                @{{# } }}

                <button class="qs-btn qs-btn-primary qs-btn-sm qs-btn-table"  style="width: 80px;"  data-opt="applyArbitration" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">申请仲裁</button>

            @{{# } else if (d.status == 16) {  }}

                @{{# if (d.complain == 1) {  }}
                <button class="qs-btn qs-btn-sm" style="width: 80px;"   data-opt="cancelArbitration" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">取消仲裁</button>
                @{{# } }}

                @{{# if (d.consult == 2) {  }}
                <button class="qs-btn qs-btn-sm" style="width: 80px;"  data-opt="agreeRevoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}" data-api_amount="@{{ d.leveling_consult.api_amount }}" data-api_deposit="@{{ d.leveling_consult.api_deposit }}" data-api_service="@{{ d.leveling_consult.api_service }}" data-who="1" data-reason="@{{ d.leveling_consult.revoke_message  }}">同意撤销</button>
                @{{# }   }}

            @{{# } else if (d.status == 17) {  }}
                <button class="qs-btn qs-btn-sm" style="width: 80px;"  data-opt="lock" data-no="@{{ d.no }}">锁定</button>
                <button class="qs-btn qs-btn-primary qs-btn-sm qs-btn-table" style="width: 80px;"  data-opt="revoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">撤销</button>
            @{{# } else if (d.status == 18) {  }}
                <button class="qs-btn qs-btn-sm" style="width: 80px;"  data-opt="cancelLock" data-no="@{{ d.no }}">取消锁定</button>
                <button class="qs-btn qs-btn-primary qs-btn-sm qs-btn-table" style="width: 80px;"  data-opt="revoke" data-no="@{{ d.no }}" data-safe="@{{ d.security_deposit }}" data-effect="@{{ d.efficiency_deposit }}" data-amount="@{{ d.amount }}">撤销</button>
            @{{# } else if (d.status == 19 || d.status == 20 || d.status == 21) {  }}
                <button class="qs-btn qs-btn-sm" data-opt="repeat" style="width: 80px;"  data-no="@{{ d.no }}">重发</button>
            @{{# }  }}

        @{{# }  }}
    </script>
    <script type="text/html" id="noTemplate">
        天猫：<a style="color:#1f93ff"  href="{{ route('frontend.workbench.leveling.detail') }}?no=@{{ d.no }}" target="_blank"> @{{ d.source_order_no }}</a> <br/>
        @{{# if(d.third_name) { }}  @{{ d.third_name }}：<a style="color:#1f93ff" href="{{ route('frontend.workbench.leveling.detail') }}?no=@{{ d.no }}" target="_blank"> @{{  d.third_order_no }} </a>  @{{#  } }}
    </script>
    <script type="text/html" id="wwTemplate">
        @{{# if(d.client_wang_wang) { }}
        <a  style="color:#1f93ff" href="http://www.taobao.com/webww/ww.php?ver=3&touid=@{{ d.client_wang_wang }}&siteid=cntaobao&status=1&charset=utf-8"  target="_blank" title="@{{ d.client_wang_wang }}">
            <img src="/frontend/images/ww.gif" width="20px">@{{ d.client_wang_wang }}</a><br>
        <div>@{{ d.seller_nick }}</div>
        @{{#  } }}
    </script>
    <script type="text/html" id="statusTemplate">
        @{{ d.status_text }}  <br>
        @{{# if(d.timeout == 1 && d.status == 13)  { }}
            <span style="color:#ff8500"> @{{ d.timeout_time }}</span>
        @{{# } else if(d.status == 13 || d.status == 1) { }}
            @{{ d.status_time }}
        @{{# } else {  }}
            @{{ d.status_time }}
        @{{# }  }}
    </script>
    <script type="text/html" id="gameTemplate">
        @{{ d.game_name }} <br>
        @{{ d.region }} / @{{ d.serve }}
    </script>
    <script type="text/html" id="accountPasswordTemplate">
        @{{ d.account }} <br/>
        @{{ d.password }}
    </script>
    <script type="text/html" id="efficiencyAndSecurityDeposit">
        @{{ d.efficiency_deposit }}/@{{ d.security_deposit }}
    </script>
    <script type="text/html" id="createdAtAndReceiving">
        @{{ d.created_at }}<br/>
        @{{ d.receiving_time }}
    </script>
    <script type="text/html" id="hatchetManQQAndPhone">
        @{{ d.hatchet_man_qq }}<br/>
        @{{ d.hatchet_man_phone }}
    </script>
    <script type="text/html" id="remarkTemplate">
        @{{# if(d.customer_service_remark) {  }}
            @{{ d.customer_service_remark }}
        @{{# } else {  }}
            <span style="color:#cccccc">点击输入</span>
        @{{# }  }}
    </script>
    <script type="text/html" id="titleTemplate">
        <span class="tips" lay-tips="@{{ d.game_leveling_title  }}">@{{ d.game_leveling_title }}</span>
    </script>
    <script type="text/html" id="changeStyleTemplate">
        <style>
            .layui-table-view .layui-table[lay-size=sm] td .laytable-cell-@{{ d  }}-no,
            .layui-table-view .layui-table[lay-size=sm] td .laytable-cell-@{{ d  }}-status_text,
            .layui-table-view .layui-table[lay-size=sm] td .laytable-cell-@{{ d  }}-game_name,
            .layui-table-view .layui-table[lay-size=sm] td .laytable-cell-@{{ d  }}-account_password,
            .layui-table-view .layui-table[lay-size=sm] td .laytable-cell-@{{ d  }}-receiving_time,
            .layui-table-view .layui-table[lay-size=sm] td .laytable-cell-@{{ d  }}-hatchet_man_qq,
            .layui-table-view .layui-table[lay-size=sm] td .laytable-cell-@{{ d  }}-seller_nick {
                height: 40px;
                line-height: 20px;
            }
            .layui-table-view .layui-table[lay-size=sm] td  .laytable-cell-@{{ d  }}-button{
                display: block;
                height: 40px;
                line-height: 40px;
                word-break: break-all;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                padding-left: 15px;
            }
            .layui-laypage .layui-laypage-curr .layui-laypage-em {
                background-color: #ff8500;
            }
        </style>
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
    <script>
        layui.use(['table', 'form', 'layedit', 'laydate', 'laytpl', 'element', 'carousel'], function () {
            var form = layui.form,
                    layer = layui.layer,
                    element = layui.element,
                    layTpl = layui.laytpl,
                    table = layui.table,
                    carousel =  layui.carousel;
            // 是否将天猫订单发货
            var delivery = 1;
            // 状态切换
            element.on('tab(order-list)', function () {
                $('form').append('<input name="status" type="hidden" value="' + this.getAttribute('lay-id')  + '">');
                reloadOrderList();
            });
            // 搜索
            form.on('submit(search)', function (data) {
                reloadOrderList(data.field);
                return false;
            });
            // 导出
            form.on('submit(export)', function (data) {
                var formCondition = $('form').serializeArray();
                var condition = '';
                $.each(formCondition, function() {
                    condition += this.name + '=' + this.value + '&';
                });
                console.log("{{ route('frontend.workbench.leveling.index')}}?export=1&" + condition);
                window.location.href = "{{ route('frontend.workbench.leveling.index', ['export' => 1])}}" + condition;
                return false;
            });
            // 选择游戏加载对应的代练类型
            form.on('select(game)', function (data) {
                $.post('{{ route('frontend.workbench.get-game-leveling-type') }}', {game_id: data.value}, function (result) {
                    var options = '<option value="">请选择代练类型</option>';
                    $.each(result.content, function(index, item) {
                       options += '<option value="' + item  + '">' + item  + '</option>';
                    });
                    $('select[name=game_leveling_type]').html(options);
                    layui.form.render();
                }, 'json')
            });
            // 备注编辑
            table.on('edit(order-list)', function(obj){
                var value = obj.value, field = obj.field; // 修改后的值, 修改的字段
                $.post('{{ route("frontend.workbench.leveling.remark") }}', {no:obj.data.no, field:field, value:value}, function (result) {
                }, 'json');
            });
            // 加载数据
            table.render({
                elem: '#order-list',
                url: '{{ route('frontend.workbench.leveling.order-list') }}',
                method: 'post',
                cols: [[
                    {field: 'no', title: '订单号', width: 260, templet: '#noTemplate', style:"height: 40px;line-height: 20px;"},
                    {field: 'status_text', title: '订单状态', width: 95, style:"height: 40px;line-height: 20;", templet:'#statusTemplate' },
                    {field: 'seller_nick', title: '玩家旺旺',  width: 140, templet:'#wwTemplate', style:"height: 40px;line-height: 20px;"},
                    {field: 'customer_service_remark', title: '客服备注', minWidth: 160,edit: 'text', templet:'#remarkTemplate'},
                    {field: 'game_leveling_title', title: '代练标题', width: 230, templet:'#titleTemplate'},
                    {field: 'game_name', title: '游戏/区/服', width: 140, templet:'#gameTemplate'},
                    {field: 'role', title: '角色名称', width: 100},
                    {field: 'account_password', title: '账号/密码', width: 120, templet:'#accountPasswordTemplate'},
                    {field: 'amount', title: '代练价格', width: 100},
                    {field: 'efficiency_deposit', title: '效率/安全保证金', width: 120, templet:'#efficiencyAndSecurityDeposit'},
                    {field: 'receiving_time', title: '发单/接单时间', width: 160, templet:'#createdAtAndReceiving'},
                    {field: 'leveling_time', title: '代练时间', width: 100},
                    {field: 'left_time', title: '剩余时间', width: 100},
                    {field: 'hatchet_man_qq', title: '打手QQ电话', width: 113, templet:'#hatchetManQQAndPhone'},
                    {field: 'client_phone', title: '号主电话', width: 100},
                    {field: 'source_price', title: '来源价格', width: 100},
                    {field: 'payment_amount', title: '支付代练费用', width: 100},
                    {field: 'get_amount', title: '获得赔偿金额', width: 100},
                    {field: 'poundage', title: '手续费', width: 100},
                    {field: 'profit', title: '最终支付金额', width: 100},
                    {field: 'customer_service_name', title: '发单客服', width: 100},
                    {field: 'button', title: '操作',width:195, fixed: 'right', style:"height: 40px;line-height: 20px;", toolbar: '#operation'}
                ]],
                height: 'full-245',
                size: 'sm',
                page: {
//                    layout: [ 'count', 'prev', 'page', 'next', 'skip'],
                    layout: ['prev', 'page', 'next'],
                    groups: 10,
                    prev: '«',
                    next: '»',
                    limit:50
                },
                done: function(res, curr, count){
                    changeStyle(layui.table.index);
                    setStatusNumber();
                }
            });
            // 订单表格重载
            function reloadOrderList(parameter) {
                var condition = {};
                if (parameter == undefined) {
                    var formCondition = $('form').serializeArray();
                    $.each(formCondition, function() {
                        condition[this.name] = this.value;
                    });
                } else {
                    condition = parameter;
                }
                //执行重载
                table.reload('order-list', {
                    where: condition,
                    height: 'full-245',
                    page: {
                        curr: 1
                    },
                    done: function(res, curr, count){
                        changeStyle(layui.table.index);
                        setStatusNumber();
                        layui.form.render();
                    }
                });
            }
            // 重新渲染后重写样式
            function changeStyle(index) {
                var getTpl = changeStyleTemplate.innerHTML, view = $('body');
                layTpl(getTpl).render(index, function(html){
                    view.append(html);
                });
            }
            // 设置订单状态数
            function setStatusNumber() {
                var condition = {};
                var parameter;
                var formCondition = $('form').serializeArray();
                $.each(formCondition, function() {
                    condition[this.name] = this.value;
                });
                $.post('{{ route('frontend.workbench.leveling.order-status-count') }}', condition, function (result) {
                    parameter = result.content;
                    if (parameter.length == 0){
                        $('.qs-badge').addClass('layui-hide');
                    }
                    $('.qs-badge').addClass('layui-hide');
                    $.each(parameter, function(key, val) {
                        var name = 'quantity-'  +  key;
                        if ($('span').hasClass(name) && val > 0) {
                            $('.' + name).html(val).removeClass('layui-hide');
                        }
                    });
                }, 'json');
            }
            // 备注
            $('.layui-card-body').on('click', '[data-field=customer_service_remark]', function () {
                if ($(this).text().trim() == '点击输入') {
                    $('.layui-table-edit').val('');
                }
            });
            // 对订单操作
            $('.layui-card-body').on('click', '.qs-btn', function () {
                var opt = $(this).attr("data-opt");
                var orderNo = $(this).attr("data-no");
                var orderAmount = $(this).attr("data-amount");
                var orderSafe = $(this).attr("data-safe");
                var orderEffect = $(this).attr("data-effect");
                var apiAmount = parseFloat($(this).attr("data-api_amount"));
                var apiDeposit = parseFloat($(this).attr("data-api_deposit"));
                var apiService = parseFloat($(this).attr("data-api_service"));
                var who=$(this).attr("data-who");
                var reason=$(this).attr("data-reason");

                if (!orderAmount) {
                    orderAmount = 0;
                }
                if (!orderSafe) {
                    orderSafe = 0;
                }
                if (!orderEffect) {
                    orderEffect = 0;
                }
                $('.order_amount').val(orderAmount);
                $('.safe').val(orderSafe);
                $('.effect').val(orderEffect);

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
                        content: who == 2 ? $('.consult2') : $('.consult')
                    });
                    form.on('submit(consult)', function (data) {
                        $.post("{{ route('frontend.workbench.leveling.consult') }}", {
                            orderNo: orderNo,
                            data: data.field
                        }, function (result) {
                            if (result.status == 1) {
                                    reloadOrderList();
                                    layer.closeAll();
                                    layer.msg(result.message, {icon: 6},function () {
                                    });
                            } else {
                                layer.msg(result.message, {icon: 5},function () {});
                            }

                        });
                        return false;
                    });
                    return false;
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
                            layer.msg('请至少上传一张图片', {icon: 5});
                            layer.close(complainLoad);
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
                                    reloadOrderList();
                                    layer.closeAll();
                                    layer.msg(result.message, {icon: 6},function () {
                                    });
                                } else {
                                    layer.msg(result.message, {icon: 5},function () {});
                                }
                            });
                        }
                        return false;
                    });
                    return false
                } else if (opt == 'delete') {
                    layer.confirm('确认删除吗？', {icon: 3, title: '提示'}, function (index) {
                        $.post("{{ route('frontend.workbench.leveling.status') }}", {
                            orderNo: orderNo,
                            keyWord: opt
                        }, function (result) {
                            if (result.status == 1) {
                                    reloadOrderList();
                                    layer.closeAll();
                                layer.msg(result.message, {icon: 6},function () {
                                });
                            } else {
                                layer.msg(result.message, {icon: 5}, function () {
                                    layer.closeAll();
                                });
                            }
                        });

                        layer.close(index);
                    });
                    return false
                } else if (opt == 'complete') {
                    layer.open({
                        type:6,
                        btn:false,
                        content: '<div style="padding:20px 60px;font-size: 14px">确定完成订单？<br/><input type="checkbox" checked id="delivery"> 同时提交淘宝/天猫订单发货</div><div style="text-align: center;margin-bottom: 20px">' +
                        '<button class="qs-btn delivery-confirm">确定</button>&nbsp;&nbsp;&nbsp;<button class="qs-btn qs-btn-primary  qs-btn-table cancel">取消</button></div>'
                    });

                    $('body').on('click', '.delivery-confirm', function () {
                        $.post("{{ route('frontend.workbench.leveling.status') }}", {
                            orderNo: orderNo,
                            keyWord: opt,
                            delivery: delivery
                        }, function (result) {
                            if (result.status == 1) {
                                reloadOrderList();
                                layer.closeAll();
                                layer.msg(result.message, {icon: 6},function () {});
                            } else {
                                layer.msg(result.message, {icon: 5});
                            }
                        }, 'json');
                    });
                    return false;
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
                                    reloadOrderList();
                                    layer.closeAll();
                                layer.msg(result.message, {icon: 6}, function () {
                                });
                            } else {
                                layer.msg(result.message, {icon: 5}, function () {});
                            }
                        });
                        layer.close(index);
                    });
                    return false
                } else if(opt == 'top') {
                    $.post("{{ route('frontend.workbench.leveling.set-top') }}", {
                        no: orderNo
                    }, function (result) {
                        if (result.status == 1) {
                                reloadOrderList();
                                layer.closeAll();
                            layer.msg(result.message, {icon: 6}, function () {
                            });
                        } else {
                            layer.msg(result.message, {icon: 5}, function () {
                                layer.closeAll();
                            });
                        }
                    });
                    return false
                } else if (opt == 'showPhoto') {
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
                    $.get("{{ route('frontend.workbench.leveling.leave-image') }}?order_no=" + orderNo, function (result) {
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
                                layer.msg('暂时没有图片', {icon: 5});
                            }
                        }
                    });
                } else if (opt == 'applyComplete'){
                    layer.open({
                        type: 1,
                        shade: 0.2,
                        title: '上传完成图片',
                        area: ['600px'],
                        content: $('.complete-upload')
                    });
                    form.on('submit(complete-upload)', function (data) {

                        var complainLoad = layer.load(2, {shade:[0.2, '#000']});
                        var pic1 = $('.pic-1 img').attr('src');
                        var pic2 = $('.pic-2 img').attr('src');
                        var pic3 = $('.pic-3 img').attr('src');

                        $.post("{{ route('frontend.workbench.leveling.status') }}", {
                            orderNo: orderNo,
                            keyWord: opt,
                            pic1: pic1,
                            pic2: pic2,
                            pic3: pic3
                        }, function (result) {
                            layer.close(complainLoad);
                            if (result.status == 1) {
                                reloadOrderList();
                                layer.closeAll();
                                layer.msg(result.message, {icon: 6},function () {
                                });
                            } else {
                                layer.msg(result.message, {icon: 5},function () {});
                            }
                        });
                        return false;
                    });
                    return false;
                } else {
                    $.post("{{ route('frontend.workbench.leveling.status') }}", {
                        orderNo: orderNo,
                        keyWord: opt
                    }, function (result) {
                        if (result.status == 1) {
                                reloadOrderList();
                                layer.closeAll();
                            layer.msg(result.message, {icon: 6}, function () {
                            });
                        } else {
                            layer.msg(result.message, {icon: 5}, function () {
                                layer.closeAll();
                            });
                        }
                    });
                    return false;
                }
            });
            // 弹窗的取消按钮
            $('.cancel').click(function () {
                layer.closeAll();
            });
            $('body').on('click', '.cancel', function () {
                layer.closeAll();
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