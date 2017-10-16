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
                        <a href="<?php echo e(url('/')); ?>">
                            <i class="fa fa-dashboard"></i>
                            <span>后台首页</span>
                        </a>
                    </li>
                    <li <?php if($currentOneLevelMenu == 'goods'): ?> class="open active" <?php endif; ?>>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>商品</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="<?php echo e(url('admin/order')); ?>" <?php if($currentRouteName == 'order.index'): ?> class="active" <?php endif; ?>>
                                    商品分类
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(url('admin/goods/template')); ?>" <?php if($currentRouteName == 'order.goods.template'): ?> class="active" <?php endif; ?>>
                                    商品模版
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li <?php if($currentOneLevelMenu == 'finance'): ?> class="open active" <?php endif; ?>>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>财务</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="<?php echo e(route('finance.platform.asset')); ?>" <?php if($currentRouteName == 'finance.platform.asset'): ?> class="active" <?php endif; ?>>
                                    平台资产
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('finance.platform.flow')); ?>" <?php if($currentRouteName == 'finance.platform.flow'): ?> class="active" <?php endif; ?>>
                                    资金流水
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li <?php if($currentOneLevelMenu == 'accounts.index' || $currentOneLevelMenu == 'admin-accounts.index'): ?> class="open active" <?php endif; ?>>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>账号</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="<?php echo e(route('accounts.index')); ?>" <?php if($currentRouteName == 'accounts.index'): ?> class="active" <?php endif; ?>>
                                    前端账号
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('admin-accounts.index')); ?>" <?php if($currentRouteName == 'admin-accounts.index'): ?> class="active" <?php endif; ?>>
                                    后端账号
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li <?php if($currentOneLevelMenu == 'roles' || $currentOneLevelMenu == 'permissions' || $currentOneLevelMenu == 'admin-roles' || $currentOneLevelMenu == 'admin-permissions' || $currentOneLevelMenu == 'admin-groups' || $currentOneLevelMenu == 'groups' ||$currentOneLevelMenu == 'admin-modules' || $currentOneLevelMenu == 'modules'): ?> class="open active" <?php endif; ?>>
                        <a href="#" class="dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span>权限管理</span>
                            <i class="fa fa-chevron-circle-right drop-icon"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="<?php echo e(route('roles.index')); ?>" <?php if($currentRouteName == 'roles.index'): ?> class="active" <?php endif; ?>>
                                    角色列表
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('roles.create')); ?>" <?php if($currentRouteName == 'roles.create'): ?> class="active" <?php endif; ?>>
                                    添加角色
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('permissions.index')); ?>" <?php if($currentRouteName == 'permissions.index'): ?> class="active" <?php endif; ?>>
                                    权限列表
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('permissions.create')); ?>" <?php if($currentRouteName == 'permissions.create'): ?> class="active" <?php endif; ?>>
                                    添加权限
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('admin-roles.index')); ?>" <?php if($currentRouteName == 'admin-roles.index'): ?> class="active" <?php endif; ?>>
                                    后台角色列表
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('admin-roles.create')); ?>" <?php if($currentRouteName == 'admin-roles.create'): ?> class="active" <?php endif; ?>>
                                    添加后台角色
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('admin-permissions.index')); ?>" <?php if($currentRouteName == 'admin-permissions.index'): ?> class="active" <?php endif; ?>>
                                    后台权限列表
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('admin-permissions.create')); ?>" <?php if($currentRouteName == 'admin-permissions.create'): ?> class="active" <?php endif; ?>>
                                    添加后台权限
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('groups.index')); ?>" <?php if($currentRouteName == 'groups.index'): ?> class="active" <?php endif; ?>>
                                    前台管理组列表
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('groups.create')); ?>" <?php if($currentRouteName == 'groups.create'): ?> class="active" <?php endif; ?>>
                                    添加前台管理组
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('admin-groups.index')); ?>" <?php if($currentRouteName == 'admin-groups.index'): ?> class="active" <?php endif; ?>>
                                    后台管理组列表
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('admin-groups.create')); ?>" <?php if($currentRouteName == 'admin-groups.create'): ?> class="active" <?php endif; ?>>
                                    添加后台管理组
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('modules.index')); ?>" <?php if($currentRouteName == 'modules.index'): ?> class="active" <?php endif; ?>>
                                    前台模块列表
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('modules.create')); ?>" <?php if($currentRouteName == 'modules.create'): ?> class="active" <?php endif; ?>>
                                    添加前台模块
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('admin-modules.index')); ?>" <?php if($currentRouteName == 'admin-modules.index'): ?> class="active" <?php endif; ?>>
                                    后台模块列表
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('admin-modules.create')); ?>" <?php if($currentRouteName == 'admin-modules.create'): ?> class="active" <?php endif; ?>>
                                    添加后台模块
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</div>