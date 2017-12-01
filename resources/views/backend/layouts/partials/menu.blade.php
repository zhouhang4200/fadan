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
                    @can('order.platform.index')
                        <li @if(in_array($currentRouteName, [
                        'order.platform.index',
                        'order.platform.content',
                        'order.platform.record',
                        'order.foreign.index',
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
                            </ul>
                        </li>
                    @endcan
                    <li @if($currentOneLevelMenu == 'frontend' || $currentOneLevelMenu == 'groups' || $currentOneLevelMenu == 'roles' || $currentOneLevelMenu == 'permissions' || $currentOneLevelMenu == 'modules' ) class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-users"></i>
                            <span>商户</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            @can('frontend.user.index')
                                <li>
                                    <a href="{{ route('frontend.user.index')}}" @if($currentRouteName == 'frontend.user.index') class="active" @endif>
                                        商户列表
                                    </a>
                                </li>
                            @endcan
                            @can('modules.index')
                                <li>
                                    <a href="{{ route('modules.index') }}" @if($currentRouteName == 'modules.index') class="active" @endif>
                                        模块列表
                                    </a>
                                </li>
                            @endcan
                            @can('permissions.index')
                                <li>
                                    <a href="{{ route('permissions.index') }}" @if($currentRouteName == 'permissions.index') class="active" @endif>
                                        权限列表
                                    </a>
                                </li>
                            @endcan
                            @can('roles.index')
                                <li>
                                    <a href="{{ route('roles.index') }}" @if($currentRouteName == 'roles.index') class="active" @endif>
                                        角色列表
                                    </a>
                                </li>
                            @endcan
                            @can('groups.index')
                                <li>
                                    <a href="{{ route('groups.index') }}" @if($currentRouteName == 'groups.index') class="active" @endif>
                                        商户权限列表
                                    </a>
                                </li>
                            @endcan
                            @can('frontend.user.weight.index')
                                <li>
                                    <a href="{{ route('frontend.user.weight.index') }}" @if($currentRouteName == 'frontend.user.weight.index') class="active" @endif>
                                        商户权重列表
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                    <li @if($currentOneLevelMenu == 'goods') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-copy"></i>
                            <span>商品</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                        @can('goods.service.index')
                            <li>
                                <a href="{{ url('admin/goods/service') }}" @if($currentRouteName == 'goods.service.index') class="active" @endif>
                                    服务
                                </a>
                            </li>
                        @endcan
                        @can('goods.game.index')
                            <li>
                                <a href="{{ url('admin/goods/game') }}" @if($currentRouteName == 'goods.game.index') class="active" @endif>
                                    游戏
                                </a>
                            </li>
                        @endcan
                        @can('goods.template.index')
                            <li>
                                <a href="{{ url('admin/goods/template') }}" @if($currentRouteName == 'goods.template.index') class="active" @endif>
                                    模版
                                </a>
                            </li>
                        @endcan
                        </ul>
                    </li>

                    <li @if($currentOneLevelMenu == 'finance') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa  fa-money"></i>
                            <span>财务</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                        @can('finance.platform-asset')
                            <li>
                                <a href="{{ route('finance.platform-asset') }}" @if($currentRouteName == 'finance.platform-asset') class="active" @endif>
                                    平台当前资产
                                </a>
                            </li>
                        @endcan
                        @can('finance.platform-asset-daily')
                            <li>
                                <a href="{{ route('finance.platform-asset-daily') }}" @if($currentRouteName == 'finance.platform-asset-daily') class="active" @endif>
                                    平台资产日报
                                </a>
                            </li>
                        @endcan
                        @can('finance.platform-amount-flow')
                            <li>
                                <a href="{{ route('finance.platform-amount-flow') }}" @if($currentRouteName == 'finance.platform-amount-flow') class="active" @endif>
                                    平台资金流水
                                </a>
                            </li>
                        @endcan
                        @can('finance.user-asset')
                            <li>
                                <a href="{{ route('finance.user-asset') }}" @if($currentRouteName == 'finance.user-asset') class="active" @endif>
                                    用户资产列表
                                </a>
                            </li>
                        @endcan
                        @can('finance.user-asset-daily')
                            <li>
                                <a href="{{ route('finance.user-asset-daily') }}" @if($currentRouteName == 'finance.user-asset-daily') class="active" @endif>
                                    用户资产日报
                                </a>
                            </li>
                        @endcan
                        @can('finance.user-amount-flow')
                            <li>
                                <a href="{{ route('finance.user-amount-flow') }}" @if($currentRouteName == 'finance.user-amount-flow') class="active" @endif>
                                    用户资金流水
                                </a>
                            </li>
                        @endcan
                        @can('finance.user-widthdraw-order')
                            <li>
                                <a href="{{ route('finance.user-widthdraw-order') }}" @if($currentRouteName == 'finance.user-widthdraw-order') class="active" @endif>
                                    用户提现管理
                                </a>
                            </li>
                        @endcan
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
                        @can('admin-idents.index')
                            <li>
                                <a href="{{ route('admin-idents.index') }}" @if($currentRouteName == 'admin-idents.index') class="active" @endif>
                                    实名认证
                                </a>
                            </li>
                        @endcan
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
                        @can('admin-accounts.index')
                            <li>
                                <a href="{{ route('admin-accounts.index') }}" @if($currentRouteName == 'admin-accounts.index') class="active" @endif>
                                    账号
                                </a>
                            </li>
                        @endcan
                        @can('admin-modules.index')
                            <li>
                                <a href="{{ route('admin-modules.index') }}" @if($currentRouteName == 'admin-modules.index') class="active" @endif>
                                    模块列表
                                </a>
                            </li>
                        @endcan
                        @can('admin-permissions.index')
                            <li>
                                <a href="{{ route('admin-permissions.index') }}" @if($currentRouteName == 'admin-permissions.index') class="active" @endif>
                                    权限列表
                                </a>
                            </li>
                        @endcan
                        @can('admin-roles.index')
                            <li>
                                <a href="{{ route('admin-roles.index') }}" @if($currentRouteName == 'admin-roles.index') class="active" @endif>
                                    角色列表
                                </a>
                            </li>
                        @endcan
                        @can('admin-groups.index')
                            <li>
                                <a href="{{ route('admin-groups.index') }}" @if($currentRouteName == 'admin-groups.index') class="active" @endif>
                                    账号权限组列表
                                </a>
                            </li>
                        @endcan
                        </ul>
                    </li>
                    @endif
                    @can('system-logs.index')
                    <li @if($currentOneLevelMenu == 'system-logs') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>系统日志</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            @can('system-logs.index')
                            <li>
                                <a href="{{ route('system-logs.index') }}" @if($currentRouteName == 'system-logs.index') class="active" @endif>
                                    系统日志
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                    @endcan
                    @can('punishes.index')
                    <li @if($currentOneLevelMenu == 'punishes' || $currentOneLevelMenu == 'punish-types') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>奖惩管理</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                        @can('punishes.index')
                            <li>
                                <a href="{{ route('punishes.index') }}" @if($currentRouteName == 'punishes.index') class="active" @endif>
                                    奖惩列表
                                </a>
                            </li>
                        @endcan
                        </ul>
                    </li>
                    @endcan
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
                    
                </ul>
            </div>
        </div>
    </section>
</div>