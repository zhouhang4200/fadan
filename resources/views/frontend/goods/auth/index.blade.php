@extends('frontend.layouts.app')

@section('title', '商品 - 接单权限')

@section('css')
    <style>
        .user-td td div{
            text-align: center;width: 320px;
        }
        .layui-table tr th {
            text-align: center;
        }
        .explanation {
            padding: 11px 13px;
            margin-bottom: 15px;
            color: #707070;
            border-radius: 5px;
            background-color: #f5faff;
            border: 1px dashed #62b3ff;
            width: calc(100% - 28px);
            height: 100%;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.goods.submenu')
@endsection

@section('main')
    <div class="">
        <div class="explanation" id="explanation">
            <div class="ex_tit" style="margin-bottom: 10px;"><i class="sc_icon"></i><h4>操作提示</h4><span id="explanationZoom" title="收起提示" class=""></span></div>
            <ul style="display: block;">
                <li>该功能可以控制，平台用户对您的订单的接单权限。</li>
                <li>控制方式：无表示不控制所有平台用户可接您的订单。白名单表示，只有白名单中的用户可接您的单。黑名单表示，在黑名单中用户无法接您的订单。(三种方式只会有一种生效)</li>
            </ul>
        </div>
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-form-item" pane>
                <label class="layui-form-label">控制方式</label>
                <div class="layui-input-block"  >
                    <input type="radio" name="control" value="no" title="不开启" checked lay-filter="control">
                    <input type="radio" name="control" value="whitelist" title="白名单" checked lay-filter="control">
                    <input type="radio" name="control" value="blacklist" title="黑名单"lay-filter="control" >
                </div>
            </div>
        </form>
    </div>
    <div class="layui-tab" lay-filter="whitelist" id="whitelist">
        <ul class="layui-tab-title">
            <li class="layui-this">站点接单白名单</li>
            <li>商品接单白名单</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">

            </div>
            <div class="layui-tab-item">
                <form class="layui-form" id="search-form">
                    <div class="layui-form-item">
                        <div class="layui-input-inline" style="width: 100px;">
                            <select name="service_id">
                                <option value="">所有类型</option>
                                @foreach ($services as $key => $value)
                                    <option value="{{ $key }}" {{ $key == $serviceId ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-input-inline" style="width: 100px;">
                            <select name="game_id">
                                <option value="">所有游戏</option>
                                @foreach ($games as $key => $value)
                                    <option value="{{ $key }}" {{ $key == $gameId ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-input-inline" style="width: 200px;">
                            <input type="text" class="layui-input" name="foreign_goods_id" placeholder="外部商品ID" value="{{ $foreignGoodsId  }}">
                        </div>
                        <div class="layui-input-inline" style="width: 200px;">
                            <button class="layui-btn layui-btn-normal" type="submit">查询</button>
                        </div>
                        <a  href="{{ route('frontend.goods.create') }}" class="layui-btn layui-btn-normal fr" >添加商品</a>
                    </div>
                </form>

                <table class="layui-table" lay-size="sm">
                    <thead>
                    <tr>
                        <th width="6%">商品ID</th>
                        <th>类型</th>
                        <th>游戏</th>
                        <th>商品名</th>
                        <th>单价</th>
                        <th>外部商品ID</th>
                        <th>添加时间</th>
                        <th>更新时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($goods as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->service->name }}</td>
                            <td>{{ $item->game->name }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->price }}</td>
                            <td>{{ $item->foreign_goods_id }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td>{{ $item->updated_at }}</td>
                            <td><button class="layui-btn layui-btn-normal layui-btn-small edit"><a href="{{ route('frontend.goods.edit', ['id' => $item->id]) }}" style="color: #fff">编辑</a></button>
                                <button class="layui-btn layui-btn-normal layui-btn-small delete" onclick="deletes({{ $item->id }})">删除</button></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">您还没有添加商品</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                {{ $goods->appends([
                'service_id' => $serviceId,
                'game_id' => $gameId,
                'foreign_goods_id' => $foreignGoodsId,
                ])->links() }}
            </div>
        </div>
    </div>
    <div class="layui-tab layui-hide" lay-filter="blacklist" id="blacklist">
        <ul class="layui-tab-title">
            <li class="layui-this">站点接单黑名单</li>
            <li>商品接单黑名单</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">

            </div>
            <div class="layui-tab-item">
                <form class="layui-form" id="search-form">
                    <div class="layui-form-item">
                        <div class="layui-input-inline" style="width: 100px;">
                            <select name="service_id">
                                <option value="">所有类型</option>
                                @foreach ($services as $key => $value)
                                    <option value="{{ $key }}" {{ $key == $serviceId ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-input-inline" style="width: 100px;">
                            <select name="game_id">
                                <option value="">所有游戏</option>
                                @foreach ($games as $key => $value)
                                    <option value="{{ $key }}" {{ $key == $gameId ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-input-inline" style="width: 200px;">
                            <input type="text" class="layui-input" name="foreign_goods_id" placeholder="外部商品ID" value="{{ $foreignGoodsId  }}">
                        </div>
                        <div class="layui-input-inline" style="width: 200px;">
                            <button class="layui-btn layui-btn-normal" type="submit">查询</button>
                        </div>
                        <a  href="{{ route('frontend.goods.create') }}" class="layui-btn layui-btn-normal fr" >添加商品</a>
                    </div>
                </form>

                <table class="layui-table" lay-size="sm">
                    <thead>
                    <tr>
                        <th width="6%">商品ID</th>
                        <th>类型</th>
                        <th>游戏</th>
                        <th>商品名</th>
                        <th>单价</th>
                        <th>外部商品ID</th>
                        <th>添加时间</th>
                        <th>更新时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($goods as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->service->name }}</td>
                            <td>{{ $item->game->name }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->price }}</td>
                            <td>{{ $item->foreign_goods_id }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td>{{ $item->updated_at }}</td>
                            <td><button class="layui-btn layui-btn-normal layui-btn-small edit"><a href="{{ route('frontend.goods.edit', ['id' => $item->id]) }}" style="color: #fff">编辑</a></button>
                                <button class="layui-btn layui-btn-normal layui-btn-small delete" onclick="deletes({{ $item->id }})">删除</button></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">您还没有添加商品</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                {{ $goods->appends([
                'service_id' => $serviceId,
                'game_id' => $gameId,
                'foreign_goods_id' => $foreignGoodsId,
                ])->links() }}
            </div>
        </div>
    </div>


@endsection

@section('js')
    <script>
        // 删除
        function deletes(id)
        {
             layui.use(['form', 'layedit', 'laydate', 'element'], function(){
                var form = layui.form
                ,layer = layui.layer;
                layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('frontend.goods.destroy') }}",
                        data:{id: id},
                        success: function (data) {
                            if (data.status.code == 1) {
                                layer.msg('删除成功', {icon: 6, time:1000},);
                                window.location.href = "{{ route('frontend.goods.index') }}";
                                
                            } else {
                                layer.msg('删除失败', {icon: 5, time:1000},);                  
                            }
                        }
                    });
                    layer.close(index);
                });

            });
        }
        layui.use(['form', 'layedit', 'laydate', 'element'], function(){
            var form = layui.form
                    ,layer = layui.layer
                    ,layedit = layui.layedit
                    ,laydate = layui.laydate;

            //监听控制方式
            form.on('radio(control)', function(data){

                if (data.value == 'no') {
                    layer.msg('已经关闭接单控制，平台所有用户可接您的订单');
                    $('#blacklist').addClass('layui-hide');
                    $('#whitelist').addClass('layui-hide');
                } else if(data.value == 'whitelist') {
                    layer.msg('已经切换为白名单模式');
                    $('#blacklist').addClass('layui-hide');
                    $('#whitelist').removeClass('layui-hide');
                } else {
                    layer.msg('已经切换为黑名单模式');
                    $('#whitelist').addClass('layui-hide');
                    $('#blacklist').removeClass('layui-hide');
                }
            });
            //监听指定开关
            form.on('switch(switchTest)', function(data){
                layer.msg('开关checked：'+ (this.checked ? 'true' : 'false'), {
                    offset: '6px'
                });
            });
        });
    </script>
@endsection