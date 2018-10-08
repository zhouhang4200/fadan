@extends('backend.layouts.main')

@section('title', ' | 商户投诉')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class="active"><span>商户投诉</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header clearfix">
                    <div class="filter-block pull-left">
                        <form class="layui-form" id="user-form">
                            <input type="hidden" name="status" value="{{ Request::input('status') }}">
                            <div class="layui-form-item">
                                <div class="layui-input-inline">
                                    <input type="text" class="layui-input" name="order_no"  placeholder="订单号" value="{{ Request::input('order_no') }}">
                                </div>
                                <div class="layui-input-inline">
                                    <input type="text" name="start_date" id="startDate" autocomplete="off" class="layui-input" placeholder="开始时间" value="{{ Request::input('start_date') }}">
                                </div>
                                <div class="layui-input-inline">
                                    <input type="text" name="end_date" id="endDate" autocomplete="off" class="layui-input" placeholder="结束时间" value="{{ Request::input('end_date') }}">
                                </div>
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-normal" type="submit" lay-submit="" lay-filter="user-search">查询</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="filter-block pull-right">
                        <a href="{{ route('businessman.complaint.create') }}" class="layui-btn layui-btn-samll layui-btn-normal">添加</a>
                    </div>
                </header>
                <div class="main-box-body clearfix">
                    <div class="layui-tab-item layui-show">
                        <div class="layui-tab"  lay-filter="status">
                            <ul class="layui-tab-title">
                                <li class="{{ Request::input('status') == 0 ? 'layui-this' : '' }}"  lay-id="0">全部
                                    <span  class="qs-badge quantity-9 layui-hide"></span>
                                </li>
                                <li class="{{ Request::input('status') == 1 ? 'layui-this' : '' }}" lay-id="1">投诉
                                    <span class="qs-badge quantity-1 layui-hide"></span>
                                </li>
                                <li class="{{ Request::input('status') == 2 ? 'layui-this' : '' }}"  lay-id="2">已取消
                                    <span class="qs-badge quantity-2 layui-hide"></span>
                                </li>
                                <li class="{{ Request::input('status') == 3 ? 'layui-this' : '' }}"  lay-id="3">投诉成功
                                    <span class="qs-badge quantity-3 layui-hide"></span>
                                </li>
                                <li class="{{ Request::input('status') == 4 ? 'layui-this' : '' }}"  lay-id="4">投诉失败
                                    <span class="qs-badge quantity-4 layui-hide"></span>
                                </li>
                            </ul>
                            <div class="layui-tab-content">
                            </div>
                        </div>

                        <table class="layui-table" lay-size="sm" >
                            <thead>
                            <tr>
                                <th width="6%">投诉方呢称/ID</th>
                                <th width="6%">被投诉方呢称/ID</th>
                                <th>投诉订单号</th>
                                <th>要求赔偿金额</th>
                                <th>投诉原因</th>
                                <th>备注</th>
                                <th>状态</th>
                                <th>申请时间</th>
                                <th width="16%">处理时间</th>
                                <th width="16%">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($complaint as $item)
                                <?php $detail = $item->orderDetail->pluck('field_value', 'field_name')->toArray() ?>
                                <tr>
                                    <td>{{ optional($item->complaintPrimaryUser)->nickname }}<br/>{{ $item->complaint_primary_user_id }}</td>
                                    <td>{{ optional($item->beComplaintPrimaryUser)->nickname }}<br/>{{ $item->be_complaint_primary_user_id }}</td>
                                    <td>天猫:{{$item->foreign_order_no }}<br/>{{ config('order.third')[(int)$detail['third']] }}:{{ $detail['third_order_no'] ?? '' }}</td>
                                    <td>{{ $item->amount }}</td>
                                    <td>{{ $item->remark }}</td>
                                    <td>{{ $item->result }}</td>
                                    <td>{{ $item->statusText[$item->status] }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->status != 1 ? $item->updated_at : '' }}</td>
                                    <td>
                                        <button class="layui-btn layui-btn layui-btn-normal layui-btn-mini screenshot" data-id="{{ $item->id }}">查看截图</button>
                                        @if($item->status == 1)
                                            <button class="layui-btn layui-btn layui-btn-normal layui-btn-mini operation" data-id="{{ $item->id }}" data-action="agree">同意</button>
                                            <button class="layui-btn layui-btn layui-btn-normal layui-btn-mini operation" data-id="{{ $item->id }}" data-action="refuse">拒绝</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">没有搜索到相关数据</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        {{ $complaint->appends(Request::all())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-carousel" id="carousel" style="display: none"></div>
@endsection
@section('js')
    <script id="images" type="text/html">
        <div carousel-item="" id="">
            @{{# var i = 0; layui.each(d, function(index, item){ }}
            <div  style="background: url(@{{ d[index]  }}) no-repeat center/contain;"  @{{# if(i == 0){ }} class="layui-this" @{{# } }} >
            </div>
            @{{# if(i == 0){   i = 1;  } }}
            @{{# }); }}
        </div>
    </script>
    <script>
        //注意：选项卡 依赖 element 模块，否则无法进行功能性操作
        layui.use(['form', 'layedit', 'laydate', 'laytpl', 'element', 'carousel'], function(){
            var form = layui.form, layer = layui.layer, layTpl = layui.laytpl, element = layui.element, carousel =  layui.carousel;

            //监听Tab切换，以改变地址hash值
            element.on('tab(status)', function(){
                window.location = '{{ route('businessman.complaint.index') }}?status=' + this.getAttribute('lay-id');
            });

            // 订单操作
            $('body').on('click', '.operation', function () {
                var id = $(this).attr('data-id');
                var action = $(this).attr('data-action');
                layer.prompt({
                    formType: 2
                },function(value, index, elem){
                    $.post('{{ route('businessman.complaint.operation') }}', {id:id,action:action, result:value}, function (result) {
                        layer.msg(result.message);
                        window.reload();
                    }, 'json');
                });
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
            $('body').on('click', '.screenshot', function () {
                var id = $(this).attr('data-id');
                $.post("{{ route('businessman.complaint.images') }}", {id:id}, function (result) {
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
            });
        });

    </script>
@endsection
