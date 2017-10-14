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
                    <li @if($currentOneLevelMenu == 'accounts' || $currentOneLevelMenu == 'admin-accounts') class="open active" @endif>
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
                    <li @if($currentOneLevelMenu == 'roles' || $currentOneLevelMenu == 'permissions' || $currentOneLevelMenu == 'admin-roles' || $currentOneLevelMenu == 'admin-permissions' || $currentOneLevelMenu == 'admin-groups' || $currentOneLevelMenu == 'groups') class="open active" @endif>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>权限管理</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('roles.index') }}" @if($currentRouteName == 'roles.index') class="active" @endif>
                                    角色列表
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('roles.create') }}" @if($currentRouteName == 'roles.create') class="active" @endif>
                                    添加角色
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('permissions.index') }}" @if($currentRouteName == 'permissions.index') class="active" @endif>
                                    权限列表
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('permissions.create') }}" @if($currentRouteName == 'permissions.create') class="active" @endif>
                                    添加权限
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin-roles.index') }}" @if($currentRouteName == 'admin-roles.index') class="active" @endif>
                                    后台角色列表
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin-roles.create') }}" @if($currentRouteName == 'admin-roles.create') class="active" @endif>
                                    添加后台角色
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin-permissions.index') }}" @if($currentRouteName == 'admin-permissions.index') class="active" @endif>
                                    后台权限列表
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin-permissions.create') }}" @if($currentRouteName == 'admin-permissions.create') class="active" @endif>
                                    添加后台权限
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('groups.index') }}" @if($currentRouteName == 'groups.index') class="active" @endif>
                                    前台管理组列表
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('groups.create') }}" @if($currentRouteName == 'groups.create') class="active" @endif>
                                    添加前台管理组
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin-groups.index') }}" @if($currentRouteName == 'admin-groups.index') class="active" @endif>
                                    后台管理组列表
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin-groups.create') }}" @if($currentRouteName == 'admin-groups.create') class="active" @endif>
                                    添加后台管理组
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</div>