@extends('backend.layouts.main')

@section('title', ' | 配置管理-标品下单-新增')

@section('css')
    <style>
        .layui-form-label {
            width:140px;
        }

        .layui-input, .layui-textarea {
            display: block;
            width:300px;
            /* width: 100%; */
            padding-left: 10px;
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
                    <li class="layui-this" lay-id="add">新增游戏</li>
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
                    <form class="form-horizontal layui-form" role="form" method="post" action="{{ route('game-leveling.channel.game.update', ['id' => $item->id]) }}" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label  class="col-lg-1 control-label">用户</label>
                            <div class="col-lg-10">
                                <select name="user_id" lay-filter="game" lay-verify="required" lay-search="">
                                    <option value="">请选择</option>
                                    @forelse($users as $uses)
                                        <option value="{{ $uses->id }}" @if($uses->id == $item->user_id) selected @endif>{{ $uses->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label  class="col-lg-1 control-label">游戏</label>
                            <div class="col-lg-10">
                                <select name="game_id" lay-filter="game" lay-verify="required" lay-search="">
                                    <option value="">请选择</option>
                                    @forelse($games as $game)
                                        <option value="{{ $game->id }}-{{ $game->name }}" @if($game->id == $item->game_id) selected @endif>{{ $game->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label  class="col-lg-1 control-label">代练类型</label>
                            <div class="col-lg-10">
                                <select name="game_leveling_type_id" lay-filter="" lay-verify="" lay-search="" id="type">
                                    @forelse($types as $type)
                                        <option value="{{ $type->id }}-{{ $type->name }}" @if($type->id == $item->game_leveling_type_id) selected @endif>{{ $type->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-lg-1 control-label">代练说明</label>
                            <div class="col-lg-10">
                                <textarea placeholder="请输入内容" name="explain"  lay-verify="required" class="layui-textarea">{{ $item->explain }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-lg-1 control-label">代练要求</label>
                            <div class="col-lg-10">
                                <textarea placeholder="请输入内容" name="requirement"  lay-verify="required" class="layui-textarea">{{ $item->requirement }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-lg-1 control-label">商户qq</label>
                            <div class="col-lg-10">
                                <input type="text" name="user_qq" lay-verify="required" value="{{ $item->user_qq }}" autocomplete="off" placeholder="请输入" class="layui-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-lg-1 control-label">发单价格固定比例</label>
                            <div class="col-lg-10">
                                <input type="text" name="rebate" lay-verify="required" value="{{ $item->rebate }}" autocomplete="off" placeholder="请输入" class="layui-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-offset-1 col-lg-10">
                                <button type="submit" class="btn btn-success">更新</button>
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
        $('#export').click(function () {
            var url = "?export=1&" + $('#search-flow').serialize();
            window.location.href = url;
        });
        layui.use(['layer', 'form'], function () {});
    </script>
@endsection