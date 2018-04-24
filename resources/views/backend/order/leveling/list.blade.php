<form class="layui-form" action="">
    <table class="layui-table" lay-size="sm">
        <thead>
        <tr>
            <th>订单号</th>
            <th>千手状态</th>
            <th>外部状态</th>
            <th>接单平台</th>
            <th>接单方操作</th>
            <th>发单方操作</th>
            <th>发布时间</th>
            <th>操作时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @forelse($paginateOrderNotices as $paginateOrderNotice)
            @if ($paginateOrderNotice->third == 1) 
                <tr>
                    <td>{{ $paginateOrderNotice->order_no }}</td>
                    <td>{{ config('order.status_leveling')[$paginateOrderNotice->status] }}</td>
                    <td>
                    @if($paginateOrderNotice->child_third_status == 100)
                    {{ config('order.show91')[$paginateOrderNotice->third_status] }}
                    @else
                        @if (isset($paginateOrderNotice->third_status))
                        {{ config('order.show91')[$paginateOrderNotice->third_status].' ('.config('order.show91')[$paginateOrderNotice->child_third_status].')' }}
                        @else
                        ''
                        @endif
                    @endif
                    </td>
                    <td>{{ config('order.third')[$paginateOrderNotice->third] }}</td>
                    @if(substr($paginateOrderNotice->operate, -1) == '@')
                    <td style="color:green;">
                        {{ subOperate($paginateOrderNotice->operate) ?: '' }}
                    </td>
                    @else
                    <td style="color:red;">
                        {{ $paginateOrderNotice->operate ?: '' }}
                    </td>
                    @endif
                    <td style="color:red;">
                        {{ $paginateOrderNotice->our_operate ?? '' }}
                    </td>
                    <td>{{ $paginateOrderNotice->create_order_time }}</td>
                    <td>{{ $paginateOrderNotice->created_at }}</td>
                    <td>
                        <div class="form-group col-xs-4" style="margin: 10px 0 10px 0">
                            <select  style="background-color: #1E9FFF" name="status" lay-filter="change_status" data-amount="{{ $paginateOrderNotice->amount }}" data-safe="{{ $paginateOrderNotice->security_deposit }}"
                            data-effect="{{ $paginateOrderNotice->efficiency_deposit }}" lay-data="{{ $paginateOrderNotice->order_no }}">                
                                <option value="">修改状态</option>
                                @forelse($ourStatus as $key => $status)
                                    <option value="{{ $key }}" id="status{{ $key }}" data-status="{{ $status }}" >{{ $status }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <button class="layui-btn layui-btn-normal layui-btn" style="margin-top: 10px;" lay-submit="" lay-filter="delete" data-id="{{ $paginateOrderNotice->id }}">删除</button>
                    </td>
                </tr>
            @elseif ($paginateOrderNotice->third == 2)
                <tr>
                    <td>{{ $paginateOrderNotice->order_no }}</td>
                    <td>{{ config('order.status_leveling')[$paginateOrderNotice->status] }}</td>
                    <td>
                    @if($paginateOrderNotice->child_third_status == 100)
                        {{ $paginateOrderNotice->third_status }}
                    @else
                        {{ $paginateOrderNotice->third_status }}
                    @endif
                    </td>
                    <td>{{ config('order.third')[$paginateOrderNotice->third] }}</td>
                    @if(substr($paginateOrderNotice->operate, -1) == '@')
                    <td style="color:green;">
                        {{ subOperate($paginateOrderNotice->operate) ?: '' }}
                    </td>
                    @else
                    <td style="color:red;">
                        {{ $paginateOrderNotice->operate ?: '' }}
                    </td>
                    @endif
                    <td style="color:red;">
                        {{ $paginateOrderNotice->our_operate ?? '' }}
                    </td>
                    <td>{{ $paginateOrderNotice->create_order_time }}</td>
                    <td>{{ $paginateOrderNotice->created_at }}</td>
                    <td>
                        <div class="form-group col-xs-4" style="margin: 10px 0 10px 0">
                            <select  style="background-color: #1E9FFF" name="status" lay-filter="change_status" data-amount="{{ $paginateOrderNotice->amount }}" data-safe="{{ $paginateOrderNotice->security_deposit }}"
                            data-effect="{{ $paginateOrderNotice->efficiency_deposit }}" lay-data="{{ $paginateOrderNotice->order_no }}">                
                                <option value="">修改状态</option>
                                @forelse($ourStatus as $key => $status)
                                    <option value="{{ $key }}" id="status{{ $key }}" data-status="{{ $status }}" >{{ $status }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <button class="layui-btn layui-btn-normal layui-btn" style="margin-top: 10px;" lay-submit="" lay-filter="delete" data-id="{{ $paginateOrderNotice->id }}">删除</button>
                    </td>
                </tr>
            @endif
        @empty
        @endforelse
        </tbody>
    </table>
</form>
<div class="row">
    <div class="col-xs-3">
        总数：{{ $paginateOrderNotices->total() }}　本页显示：{{ $paginateOrderNotices->count() }}
    </div>
    <div class="col-xs-9">
        {{ $paginateOrderNotices->appends([
            'third' => $third,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->render()}}
    </div>
</div>