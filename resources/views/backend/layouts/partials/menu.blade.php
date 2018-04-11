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
                        'frontend.user.oriented.index',
                        ])) class="open active" @endif>
                            <a href="#" class="dropdown-toggle">
                                <i class="fa fa-shopping-cart"></i>
                                <span>订单管理</span>
                                <i class="fa fa-chevron-circle-right drop-icon"></i>
                            </a>
                            <ul class="submenu">
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
                                <li>
                                    <a href="{{ route('order.leveling.index') }}" @if($currentRouteName == 'order.leveling.index') class="active" @endif>
                                        报警订单
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


                                <li>
                                    <a href="{{ route('modules.index') }}" @if($currentRouteName == 'modules.index') class="active" @endif>
                                        模块列表
                                    </a>
                                </li>


                                <li>
                                    <a href="{{ route('permissions.index') }}" @if($currentRouteName == 'permissions.index') class="active" @endif>
                                        权限列表
                                    </a>
                                </li>


                                <li>
                                    <a href="{{ route('roles.index') }}" @if($currentRouteName == 'roles.index') class="active" @endif>
                                        角色列表
                                    </a>
                                </li>


                                <li>
                                    <a href="{{ route('groups.index') }}" @if($currentRouteName == 'groups.index') class="active" @endif>
                                        管理员列表
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('frontend.user.weight.index') }}" @if($currentRouteName == 'frontend.user.weight.index') class="active" @endif>
                                        商户权重列表
                                    </a>
                                </li>



                            <li>
                                <a href="{{ route('frontend.user.oriented.index') }}" @if($currentRouteName == 'frontend.user.oriented.index') class="active" @endif>
                                    游戏指定分配
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
                                <a href="{{ route('finance.caution-money.index') }}" @if($currentRouteName == 'businessman.caution-money.index') class="active" @endif>
                                    商户保证金列表
                                </a>
                            </li>
                        </ul>
                    </li>


                    <li @if($currentOneLevelMenu == 'login-record' || $currentOneLevelMenu == 'admin-idents') class="open active" @endif>
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

                        </ul>
                    </li>
                    @if (Auth::user()->hasAnyPermission(['admin-accounts.index', 'admin-modules.index', 'admin-permissions.index', 'admin-roles.index', 'admin-groups.index']))
                    <li @if($currentOneLevelMenu == 'admin-roles' || $currentOneLevelMenu == 'admin-permissions' || $currentOneLevelMenu == 'admin-groups'  ||$currentOneLevelMenu == 'admin-modules' || $currentOneLevelMenu == 'admin-accounts') class="open active" @endif>
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

                        </ul>
                    </li>
                    @endif

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

                    <li @if($currentOneLevelMenu == 'app') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>App管理</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">

                            <li>
                                <a href="{{ route('app.version.index') }}" @if($currentRouteName == 'app.version.index') class="active" @endif>
                                    版本管理
                                </a>
                            </li>


                            <li>
                                <a href="{{ route('app.order-charge.index') }}" @if(substr($currentRouteName, 0, 16) == 'app.order-charge') class="active" @endif>
                                    充值记录
                                </a>
                            </li>

                        </ul>
                    </li>

                    <li @if($currentOneLevelMenu == 'backend') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>Steam管理</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('backend.steam.goods.index') }}" @if($currentRouteName == 'backend.steam.goods.index') class="active" @endif>
                                    商品列表
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('backend.steam.goods.getGameNameList') }}" @if($currentRouteName == 'backend.steam.goods.getGameNameList') class="active" @endif>
                                    Steam直充游戏名
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('backend.steam.store-price.index') }}" @if($currentRouteName == 'backend.steam.store-price.index') class="active" @endif>
                                    商户密价
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('backend.steam.order.index') }}" @if($currentRouteName == 'backend.steam.order.index') class="active" @endif>
                                    订单列表
                                </a>
                            </li>
                        </ul>
                    </li>

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
                    <li @if($currentOneLevelMenu == 'datas' || $currentOneLevelMenu == 'statistic') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>统计管理</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('datas.index') }}" @if($currentRouteName == 'datas.index') class="active" @endif>
                                    代充平台数据
                                </a>
                            </li>
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
                                <a href="{{ route('config.game') }}" @if($currentRouteName == 'datas.index') class="active" @endif>
                                    游戏配置
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('config.export') }}" @if($currentRouteName == 'datas.index') class="active" @endif>
                                    区服配置
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li @if($currentOneLevelMenu == 'home') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>商户权限系统</span>
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
                </ul>
            </div>
        </div>
    </section>
</div>
