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
                        <a href="{{ url('/') }}">
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
                                <a href="{{ url('admin/order') }}" @if($currentRouteName == 'order.index') class="active" @endif>
                                    商品分类
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('admin/goods/template') }}" @if($currentRouteName == 'order.goods.template') class="active" @endif>
                                    商品模版
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
                                <a href="{{ route('finance.platform.asset') }}" @if($currentRouteName == 'finance.platform.asset') class="active" @endif>
                                    平台资产
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('finance.platform.flow') }}" @if($currentRouteName == 'finance.platform.flow') class="active" @endif>
                                    资金流水
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li @if($currentOneLevelMenu == 'accounts.index' || $currentOneLevelMenu == 'admin-accounts.index') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>账号</span>
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
                        </ul>
                    </li>
                    <li @if($currentOneLevelMenu == 'roles' || $currentOneLevelMenu == 'permissions' || $currentOneLevelMenu == 'admin-roles' || $currentOneLevelMenu == 'admin-permissions' || $currentOneLevelMenu == 'admin-groups' || $currentOneLevelMenu == 'groups' ||$currentOneLevelMenu == 'admin-modules' || $currentOneLevelMenu == 'modules') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>权限管理</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
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
                </ul>
            </div>
        </div>
    </section>
</div>