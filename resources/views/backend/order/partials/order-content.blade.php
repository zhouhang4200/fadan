<div class="tab-pane active" id="tab-user">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div class="main-box clearfix" style="border: 1px solid #ddd">
                        <header class="main-box-header clearfix">
                                <div class="col-lg-5" style="font-size: 15px">
                                        <p>集市订单号：{{ $content->no }} </p>&nbsp;
                                        <p>外部订单号：{{ $content->no }} </p>
                                </div>
                                <div class="col-lg-5" style="font-size: 15px">
                                        <p>状态：{{ config('order.status')[$content->status] }}</p>
                                </div>
                        </header>
                        <div style=" border-bottom: 1px solid #ddd"></div>

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
                                        <span>商品</span>
                                        <div>{{ $content->goods_name }}</div>
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
                                    <tr>
                                        <td>服务</td>
                                        <td>{{ $content->service_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>游戏</td>
                                        <td>{{ $content->game_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>商品</td>
                                        <td>{{ $content->goods_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>原单价</td>
                                        <td>{{ $content->original_price }}</td>
                                    </tr>
                                    <tr>
                                        <td>原总价</td>
                                        <td>{{ $content->original_amount }}</td>
                                    </tr>
                                    <tr>
                                        <td>集市单价</td>
                                        <td>{{ $content->price }}</td>
                                    </tr>
                                    <tr>
                                        <td>集市总价</td>
                                        <td>{{ $content->amount }}</td>
                                    </tr>
                                    <tr>
                                        <td>数量</td>
                                        <td>{{ $content->quantity }}</td>
                                    </tr>
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