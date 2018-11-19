<?php
$currentRouteName = Route::currentRouteName();
$currentOneLevelMenu = explode('.', Route::currentRouteName())[0];

?>
<div id="nav-col">
    <section id="col-left" class="col-left-nano">
        <div id="col-left-inner" class="col-left-nano-content">
            <div class="collapse navbar-collapse navbar-ex1-collapse" id="sidebar-nav">
                <ul class="nav nav-pills nav-stacked">
                    <li>
                        <a href="{{ url('admin') }}">
                            <i class="fa fa-dashboard"></i>
                            <span>后台首页</span>
                        </a>
                    </li>

                        <li @if(in_array($currentRouteName, [
                            'order.platform.index',
                            'order.platform.content',
                            'order.platform.record',
                            'order.foreign.index',
                            'order.after-service.index',
                            'order.leveling.index',
                            'order.leveling.abnormal',
                            'frontend.user.oriented.index',
                            'order.notice.index',
                            'order.platform.history',
                        ])) class="open active" @endif>
                            <a href="#" class="dropdown-toggle">
                                <i class="fa fa-shopping-cart"></i>
                                <span>订单管理</span>
                                <i class="fa fa-chevron-circle-right drop-icon"></i>
                            </a>
                            <ul class="submenu">
                                @if(auth("admin")->user()->name != '淘宝发单平台')
                                <li>
                                    <a href="{{ route('order.platform.index') }}" @if($currentRouteName == 'order.platform.index') class="active" @endif>
                                        平台订单
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('order.foreign.index') }}" @if($currentRouteName == 'order.foreign.index') class="active" @endif>
                                        外部订单
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <a href="{{ route('order.leveling.index') }}" @if($currentRouteName == 'order.leveling.index') class="active" @endif>
                                        代练订单
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('order.platform.history') }}" @if($currentRouteName == 'order.platform.history') class="active" @endif>
                                        订单操作记录
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('order.notice.index') }}" @if($currentRouteName == 'order.notice.index') class="active" @endif>
                                        接口失败报警
                                    </a>
                                </li>
                            </ul>
                        </li>



                    <li @if($currentOneLevelMenu == 'frontend' || $currentOneLevelMenu == 'groups' || $currentOneLevelMenu == 'roles' || $currentOneLevelMenu == 'permissions' || $currentOneLevelMenu == 'modules' ) class="open active" @endif>
                    <li @if(in_array($currentOneLevelMenu, [ 'frontend', 'groups','roles', 'permissions', 'modules', 'businessman'])) class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-users"></i>
                            <span>商户</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">

                                <li>
                                    <a href="{{ route('frontend.user.index')}}" @if($currentRouteName == 'frontend.user.index') class="active" @endif>
                                        商户列表
                                    </a>
                                </li>
@if(auth("admin")->user()->name != '淘宝发单平台')

                                <li>
                                    <a href="{{ route('frontend.user.weight.index') }}" @if($currentRouteName == 'frontend.user.weight.index') class="active" @endif>
                                        商户权重列表
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('businessman.complaint.index') }}" @if($currentRouteName == 'businessman.complaint.index') class="active" @endif>
                                        商户投诉
                                    </a>
                                </li>


                            <li>
                                <a href="{{ route('frontend.user.oriented.index') }}" @if($currentRouteName == 'frontend.user.oriented.index') class="active" @endif>
                                    游戏指定分配
                                </a>
                            </li>
@endif
                            <li>
                                <a href="{{ route('businessman.taobao-shop-auth.index') }}" @if($currentRouteName == 'businessman.taobao-shop-auth.index') class="active" @endif>
                                    店铺授权管理
                                </a>
                            </li>
                        </ul>
                    </li>

                    @if(auth("admin")->user()->name != '淘宝发单平台')

                    <li @if($currentOneLevelMenu == 'home') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>商户权限</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('home.module.index') }}" @if($currentRouteName == 'home.module.index') class="active" @endif>
                                    模块列表
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('home.permission.index') }}" @if($currentRouteName == 'home.permission.index') class="active" @endif>
                                    权限列表
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('home.role.index') }}" @if($currentRouteName == 'home.role.index') class="active" @endif>
                                    角色列表
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('home.user.index') }}" @if($currentRouteName == 'home.user.index') class="active" @endif>
                                    用户角色列表
                                </a>
                            </li>
                        </ul>
                    </li>


                    <li @if($currentOneLevelMenu == 'customer') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>用户</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('customer.wang-wang-blacklist.index') }}" @if($currentRouteName == 'customer.wang-wang-blacklist.index') class="active" @endif>
                                    旺旺黑名单
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li @if($currentOneLevelMenu == 'goods') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-copy"></i>
                            <span>商品</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">

                            <li>
                                <a href="{{ url('admin/goods/service') }}" @if($currentRouteName == 'goods.service.index') class="active" @endif>
                                    服务
                                </a>
                            </li>


                            <li>
                                <a href="{{ url('admin/goods/game') }}" @if($currentRouteName == 'goods.game.index') class="active" @endif>
                                    游戏
                                </a>
                            </li>


                            <li>
                                <a href="{{ url('admin/goods/template') }}" @if($currentRouteName == 'goods.template.index') class="active" @endif>
                                    模版
                                </a>
                            </li>

                            <li>
                                <a href="{{ url('admin/goods/template/widget/type') }}" @if($currentRouteName == 'goods.template.widget.type') class="active" @endif>
                                    组件类型
                                </a>
                            </li>

                        </ul>
                    </li>

                    <li @if($currentOneLevelMenu == 'finance') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa  fa-money"></i>
                            <span>财务</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">

                            <li>
                                <a href="{{ route('finance.platform-asset') }}" @if($currentRouteName == 'finance.platform-asset') class="active" @endif>
                                    平台当前资产
                                </a>
                            </li>


                            <li>
                                <a href="{{ route('finance.platform-asset-daily') }}" @if($currentRouteName == 'finance.platform-asset-daily') class="active" @endif>
                                    平台资产日报
                                </a>
                            </li>


                            <li>
                                <a href="{{ route('finance.platform-amount-flow') }}" @if($currentRouteName == 'finance.platform-amount-flow') class="active" @endif>
                                    平台资金流水
                                </a>
                            </li>


                            <li>
                                <a href="{{ route('finance.user-asset') }}" @if($currentRouteName == 'finance.user-asset') class="active" @endif>
                                    商户资产列表
                                </a>
                            </li>


                            <li>
                                <a href="{{ route('finance.user-asset-daily') }}" @if($currentRouteName == 'finance.user-asset-daily') class="active" @endif>
                                    商户资产日报
                                </a>
                            </li>


                            <li>
                                <a href="{{ route('finance.user-amount-flow') }}" @if($currentRouteName == 'finance.user-amount-flow') class="active" @endif>
                                    商户资金流水
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('finance.process-order.index') }}" @if($currentRouteName == 'finance.process-order.index') class="active" @endif>
                                    托管资金明细
                                </a>
                            </li>


                            <li>
                                <a href="{{ route('finance.user-widthdraw-order') }}" @if($currentRouteName == 'finance.user-widthdraw-order') class="active" @endif>
                                    用户提现管理
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('finance.user-recharge-order.index') }}" @if($currentRouteName == 'finance.user-recharge-order.index') class="active" @endif>
                                    用户加款单
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('finance.deposit.index') }}" @if($currentRouteName == 'finance.deposit.index') class="active" @endif>
                                    商户押金管理
                                </a>
                            </li>
                        </ul>
                    </li>


                    <li @if($currentOneLevelMenu == 'login-record' || $currentOneLevelMenu == 'admin-idents' || $currentOneLevelMenu == 'admin.leveling-blacklist.index') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>账号管理</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('login-record.index') }}" @if($currentRouteName == 'login-record.index') class="active" @endif>
                                    登录记录
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin-idents.index') }}" @if($currentRouteName == 'admin-idents.index') class="active" @endif>
                                    实名认证
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.leveling-blacklist.index') }}" @if($currentRouteName == 'admin.leveling-blacklist.index') class="active" @endif>
                                    黑名单
                                </a>
                            </li>
                        </ul>
                    </li>
                    {{--@if (Auth::user()->hasAnyPermission(['admin-accounts.index', 'admin-modules.index', 'admin-permissions.index', 'admin-roles.index', 'admin-groups.index']))--}}
                    <li @if($currentOneLevelMenu == 'admin-roles' || $currentOneLevelMenu == 'admin-permissions' || $currentOneLevelMenu == 'admin-groups'  || $currentOneLevelMenu == 'admin-modules' || $currentOneLevelMenu == 'admin-accounts' || $currentRouteName == 'backend.order-send-channel.index') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>权限管理</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">

                            <li>
                                <a href="{{ route('admin-accounts.index') }}" @if($currentRouteName == 'admin-accounts.index') class="active" @endif>
                                    后台账号
                                </a>
                            </li>


                            <li>
                                <a href="{{ route('admin-modules.index') }}" @if($currentRouteName == 'admin-modules.index') class="active" @endif>
                                    模块列表
                                </a>
                            </li>


                            <li>
                                <a href="{{ route('admin-permissions.index') }}" @if($currentRouteName == 'admin-permissions.index') class="active" @endif>
                                    权限列表
                                </a>
                            </li>


                            <li>
                                <a href="{{ route('admin-roles.index') }}" @if($currentRouteName == 'admin-roles.index') class="active" @endif>
                                    角色列表
                                </a>
                            </li>


                            <li>
                                <a href="{{ route('admin-groups.index') }}" @if($currentRouteName == 'admin-groups.index') class="active" @endif>
                                    管理员列表
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('backend.order-send-channel.index') }}" @if($currentRouteName == 'backend.order-send-channel.index') class="active" @endif>
                                    发单渠道设置
                                </a>
                            </li>

                        </ul>
                    </li>
                    {{--@endif--}}

                    <li @if($currentOneLevelMenu == 'system-logs') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>系统管理</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">

                            <li>
                                <a href="{{ route('system-logs.index') }}" @if($currentRouteName == 'system-logs.index') class="active" @endif>
                                    系统日志
                                </a>
                            </li>

                        </ul>
                    </li>


                    <li @if($currentOneLevelMenu == 'punishes' || $currentOneLevelMenu == 'punish-types') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>奖惩管理</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">

                            <li>
                                <a href="{{ route('punishes.index') }}" @if($currentRouteName == 'punishes.index') class="active" @endif>
                                    奖惩列表
                                </a>
                            </li>


                            <li>
                                <a href="{{ route('punishes.record') }}" @if($currentRouteName == 'punishes.record') class="active" @endif>
                                    奖惩日志
                                </a>
                            </li>

                        </ul>
                    </li>

                    {{--<li @if($currentOneLevelMenu == 'app') class="open active" @endif>--}}
                        {{--<a href="#" class="dropdown-toggle">--}}
                            {{--<i class="fa fa-shopping-cart"></i>--}}
                            {{--<span>App管理</span>--}}
                            {{--<i class="fa fa-chevron-circle-right drop-icon"></i>--}}
                        {{--</a>--}}
                        {{--<ul class="submenu">--}}

                            {{--<li>--}}
                                {{--<a href="{{ route('app.version.index') }}" @if($currentRouteName == 'app.version.index') class="active" @endif>--}}
                                    {{--版本管理--}}
                                {{--</a>--}}
                            {{--</li>--}}


                            {{--<li>--}}
                                {{--<a href="{{ route('app.order-charge.index') }}" @if(substr($currentRouteName, 0, 16) == 'app.order-charge') class="active" @endif>--}}
                                    {{--充值记录--}}
                                {{--</a>--}}
                            {{--</li>--}}

                        {{--</ul>--}}
                    {{--</li>--}}

                    {{--<li @if($currentOneLevelMenu == 'backend') class="open active" @endif>--}}
                        {{--<a href="#" class="dropdown-toggle">--}}
                            {{--<i class="fa fa-shopping-cart"></i>--}}
                            {{--<span>Steam管理</span>--}}
                            {{--<i class="fa fa-chevron-circle-right drop-icon"></i>--}}
                        {{--</a>--}}
                        {{--<ul class="submenu">--}}
                            {{--<li>--}}
                                {{--<a href="{{ route('backend.steam.goods.index') }}" @if($currentRouteName == 'backend.steam.goods.index') class="active" @endif>--}}
                                    {{--商品列表--}}
                                {{--</a>--}}
                            {{--</li>--}}

                            {{--<li>--}}
                                {{--<a href="{{ route('backend.steam.goods.getGameNameList') }}" @if($currentRouteName == 'backend.steam.goods.getGameNameList') class="active" @endif>--}}
                                    {{--Steam直充游戏名--}}
                                {{--</a>--}}
                            {{--</li>--}}

                            {{--<li>--}}
                                {{--<a href="{{ route('backend.steam.store-price.index') }}" @if($currentRouteName == 'backend.steam.store-price.index') class="active" @endif>--}}
                                    {{--商户密价--}}
                                {{--</a>--}}
                            {{--</li>--}}

                            {{--<li>--}}
                                {{--<a href="{{ route('backend.steam.order.index') }}" @if($currentRouteName == 'backend.steam.order.index') class="active" @endif>--}}
                                    {{--订单列表--}}
                                {{--</a>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    {{--</li>--}}

                    <li>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>模版</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('template.form') }}" @if($currentRouteName == 'from') class="active" @endif>
                                    表单
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('template.icons1') }}" @if($currentRouteName == 'icons1') class="active" @endif>
                                    图标1
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('template.icons2') }}" @if($currentRouteName == 'icons2') class="active" @endif>
                                    图标2
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif

                    <li @if($currentOneLevelMenu == 'datas' || $currentOneLevelMenu == 'statistic') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>统计管理</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            @if(auth("admin")->user()->name != '淘宝发单平台')
                            <li>
                                <a href="{{ route('datas.index') }}" @if($currentRouteName == 'datas.index') class="active" @endif>
                                    代充平台数据
                                </a>
                            </li>
                            @endif
                            <li>
                                <a href="{{ route('statistic.platform') }}" @if($currentRouteName == 'statistic.platform') class="active" @endif>
                                    代练平台统计
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li @if($currentOneLevelMenu == 'config') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>配置管理</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('config.game') }}" @if($currentRouteName == 'config.game') class="active" @endif>
                                    游戏配置
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('config.export') }}" @if($currentRouteName == 'config.export') class="active" @endif>
                                    区服配置
                                </a>
                            </li>
                            <?php $levelingConfigRoutes = [
                                'config.leveling.index',
                                'config.leveling.create',
                                'config.leveling.edit',
                                'config.leveling.price.index',
                                'config.leveling.price.create',
                                'config.leveling.price.edit',
                                'config.leveling.rebate.index',
                                'config.leveling.rebate.create',
                                'config.leveling.rebate.edit',
                                'game-leveling.channel.game.index',
                                'game-leveling.channel.game.edit',
                                'game-leveling.channel.game.create',
                                'game-leveling.channel.price.index',
                                'game-leveling.channel.price.edit',
                                'game-leveling.channel.price.create',
                                'game-leveling.channel.discount.index',
                                'game-leveling.channel.discount.edit',
                                'game-leveling.channel.discount.create',

                            ]; ?>
                            <li>
                                <a href="{{ route('game-leveling.channel.game.index') }}" @if(in_array($currentRouteName, $levelingConfigRoutes)) class="active" @endif>
                                    标品下单
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li @if(in_array(explode('.', Route::currentRouteName())[1], ['game', 'server', 'region', 'leveling'])) class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>游戏管理</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('admin.game.index') }}" @if($currentRouteName == 'admin.game.index') class="active" @endif>
                                    游戏列表
                                </a>
                                <a href="{{ route('admin.region.index') }}" @if($currentRouteName == 'admin.region.index') class="active" @endif>
                                    区列表
                                </a>
                                <a href="{{ route('admin.server.index') }}" @if($currentRouteName == 'admin.server.index') class="active" @endif>
                                    服列表
                                </a>
                                <a href="{{ route('admin.leveling.index') }}" @if($currentRouteName == 'admin.leveling.index') class="active" @endif>
                                    代练类型列表
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</div>
