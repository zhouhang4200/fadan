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
                    <li @if($currentOneLevelMenu == 'goods') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
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
                            <li>
                                <a href="{{ url('admin/goods/template') }}" @if($currentRouteName == 'goods.template.index') class="active" @endif>
                                    模版
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li @if($currentOneLevelMenu == 'finance') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
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
                                    用户资产列表
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('finance.user-asset-daily') }}" @if($currentRouteName == 'finance.user-asset-daily') class="active" @endif>
                                    用户资产日报
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('finance.user-amount-flow') }}" @if($currentRouteName == 'finance.user-amount-flow') class="active" @endif>
                                    用户资金流水
                                </a>
                            </li>
                        </ul>
                    </li>
                    @hasanyrole('admin.super-manager|admin.manager')
                    <li @if($currentOneLevelMenu == 'login-record' || $currentOneLevelMenu == 'admin-idents') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>账号管理</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('login-record.index') }}" @if($currentRouteName == 'login-record.index') class="active" @endif>
                                    我的账号
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin-idents.index') }}" @if($currentRouteName == 'admin-idents.index') class="active" @endif>
                                    实名认证
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endhasanyrole
                    @role('admin.super-manager')
                    <li @if($currentOneLevelMenu == 'roles' || $currentOneLevelMenu == 'permissions' || $currentOneLevelMenu == 'admin-roles' || $currentOneLevelMenu == 'admin-permissions' || $currentOneLevelMenu == 'admin-groups' || $currentOneLevelMenu == 'groups' ||$currentOneLevelMenu == 'admin-modules' || $currentOneLevelMenu == 'modules' || $currentOneLevelMenu == 'accounts' || $currentOneLevelMenu == 'admin-accounts') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>权限管理</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('accounts.index') }}" @if($currentRouteName == 'accounts.index') class="active" @endif>
                                    前端账号
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin-accounts.index') }}" @if($currentRouteName == 'admin-accounts.index') class="active" @endif>
                                    后端账号
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('roles.index') }}" @if($currentRouteName == 'roles.index') class="active" @endif>
                                    前台角色列表
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('permissions.index') }}" @if($currentRouteName == 'permissions.index') class="active" @endif>
                                    前台权限列表
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('modules.index') }}" @if($currentRouteName == 'modules.index') class="active" @endif>
                                    前台模块列表
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('groups.index') }}" @if($currentRouteName == 'groups.index') class="active" @endif>
                                    前台管理组列表
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin-roles.index') }}" @if($currentRouteName == 'admin-roles.index') class="active" @endif>
                                    后台角色列表
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin-permissions.index') }}" @if($currentRouteName == 'admin-permissions.index') class="active" @endif>
                                    后台权限列表
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin-modules.index') }}" @if($currentRouteName == 'admin-modules.index') class="active" @endif>
                                    后台模块列表
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin-groups.index') }}" @if($currentRouteName == 'admin-groups.index') class="active" @endif>
                                    后台管理组列表
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endrole
                    @hasanyrole('admin.super-manager|admin.manager')
                    <li>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>系统日志</span>
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
                    @endhasanyrole

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