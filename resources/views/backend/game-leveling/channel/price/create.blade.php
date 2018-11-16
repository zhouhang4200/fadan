@extends('backend.layouts.main')

@section('title', ' | 配置管理-标品下单-新增')

@section('css')
    <style>
        .layui-form-label {
            width:140px;
        }

        .layui-form-select dl {
            position: relative; 
            min-width: 0px; 
            top:0px;
            width: 300px;
        }

        .layui-edge {
            display: none;
        }

        .layui-input, .layui-textarea {
            display: block;
            width:300px;
            /* width: 100%; */
            padding-left: 10px;
        }
        .tips {
            position: absolute;
            width: 10%;
            height: 30px;
            right: -130px;
            top: 5px;
            text-align: center
        }

        .tips .iconfont {
            left: -5px;
            font-size: 25px;
        }
    </style>
@endsection

@section('content')
    <div class="main-box">
        <div class="main-box-body clearfix">
            <div class="layui-tab layui-tab-brief" lay-filter="widgetTab">
                <ul class="layui-tab-title">
                    <li class="layui-this" lay-id="add">新增价格</li>
                </ul>

                <div class="row">
                    @if(Session::has('success'))
                        <div class="col-lg-12">
                            <div class="alert alert-block alert-success fade in">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h4>{{ \Session::get('success', 'default') }}</h4>
                            </div>
                        </div>
                    @endif

                    @if(Session::has('fail'))
                        <div class="col-lg-12">
                            <div class="alert alert-block alert-danger fade in">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h4>{{ \Session::get('fail', 'default') }}</h4>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="layui-tab-content">
                    <div class="col-lg-12"></div>
                    <form class="form-horizontal layui-form" role="form" method="post" action="{{ route('game-leveling.channel.price.store') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <input type="hidden" name="game_leveling_channel_game_id" value="{{ request('game_leveling_channel_game_id') }}">
                        <div class="form-group">
                            <label  class="col-lg-1 control-label">序号</label>
                            <div class="col-lg-10">
                                <input type="text" name="sort" lay-verify="required" value="{{ old('sort') }}" autocomplete="off" placeholder="请输入" class="layui-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label  class="col-lg-1 control-label">层级</label>
                            <div class="col-lg-10">
                                <input type="text" name="level" lay-verify="required" value="{{ old('level') }}" autocomplete="off" placeholder="请输入" class="layui-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label  class="col-lg-1 control-label">到下一层级价格</label>
                            <div class="col-lg-10">
                                <input type="text" name="price" lay-verify="required" value="{{ old('price') }}" autocomplete="off" placeholder="请输入" class="layui-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label  class="col-lg-1 control-label">到下一层级耗时</label>
                            <div class="col-lg-10">
                                <input type="text" name="hour" lay-verify="required" value="{{ old('hour') }}" autocomplete="off" placeholder="请输入" class="layui-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label  class="col-lg-1 control-label">该层级安全保证金</label>
                            <div class="col-lg-10">
                                <input type="text" name="security_deposit" lay-verify="required" value="{{ old('security_deposit') }}" autocomplete="off" placeholder="请输入" class="layui-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label  class="col-lg-1 control-label">该层级效率保证金</label>
                            <div class="col-lg-10">
                                <input type="text" name="efficiency_deposit" lay-verify="required" value="{{ old('efficiency_deposit') }}" autocomplete="off" placeholder="请输入" class="layui-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-offset-1 col-lg-10">
                                <button type="submit" class="btn btn-success">添加</button>
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
        layui.use(['form', 'laydate', 'element'], function(){});
    </script>
@endsection