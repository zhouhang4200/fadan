<div class="tab-pane active" id="tab-user">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div class="main-box clearfix" style="border: 1px solid #ddd">
                        <header class="main-box-header clearfix">
                            <h2 class="pull-left">订单号: {{ $content->no }} &nbsp;&nbsp;
                                状态: {{ $content->status }}</h2>
                        </header>
                        <div style=" border-bottom: 1px solid #ddd"></div>
                        <div class="main-box-body clearfix">
                            <div style="margin-top: 15px;"></div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <button class="md-trigger btn btn-primary" data-modal="refund-application">
                                        <i class="fa fa-plus-circle fa-lg"></i> 申请退款
                                    </button>
                                </div>
                                <div class="col-lg-2">
                                    <select class="form-control change-status">
                                        <option value="0">修改状态</option>
                                        <option value="success">成功</option>
                                        <option value="fail">失败</option>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control change-reason" id="exampleTooltip" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="输入手动更改状态原因">
                                </div>
                                <div class="col-lg-2">
                                    <button class="btn btn-primary change-button">
                                        <i class="fa fa-exchange"></i> 确定
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="main-box-body clearfix">
                            <div class="invoice-summary row">
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="invoice-summary-item">
                                        <span>服务</span>
                                        <div>{{ $content->service_name }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="invoice-summary-item">
                                        <span>游戏</span>
                                        <div>{{ $content->game_name }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="invoice-summary-item">
                                        <span>成交价</span>
                                        <div>{{ $content->amount }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="invoice-summary-item">
                                        <span>成交价</span>
                                        <div>{{ $content->amount }}</div>
                                    </div>
                                </div>

                            </div>
                            <div id="invoice-companies" class="row">
                                <div class="col-sm-6 invoice-box">
                                    <div class="invoice-company">
                                        <h4>发单商户</h4>
                                        <ul class="fa-ul">
                                            <li><i class="fa-li fa fa-truck"></i>商家ID:
                                                <span>{{ $content->creator_primary_user_id }}</span></li>
                                            <li><i class="fa-li fa fa-comment"></i>手机号:
                                                <span>{{ $content->creator_primary_user_id }}</span></li>
                                            <li><i class="fa-li fa fa-tasks"></i>QQ号: <span>{{ $content->creator_primary_user_id }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-sm-6 invoice-box">
                                    <div class="invoice-company">
                                        <h4>接单商户</h4>
                                            <ul class="fa-ul">
                                                <li><i class="fa-li fa fa-truck"></i>商家ID:
                                                    <span>{{ $content->gainer_primary_user_id }}</span></li>
                                                <li><i class="fa-li fa fa-comment"></i>手机号:
                                                    <span>{{ $content->gainer_primary_user_id }}</span></li>
                                                <li><i class="fa-li fa fa-tasks"></i>QQ号:
                                                    <span>{{ $content->gainer_primary_user_id }}</span></li>
                                            </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <h2></h2>
                                <table class="table table-striped table-hover">
                                    <tbody>
                                    @forelse($content->detail as $item)
                                        @if($item->field_name != 'quantity')
                                            <tr>
                                                <td class="text-left" width="20%">
                                                    {{ $item->field_display_name }}
                                                </td>
                                                <td class="text-left" style="font-size: 14px">
                                                    {{ $item->field_value }}
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td>没有数据</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>