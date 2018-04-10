<div class="header">
    <div class="wrapper">
        <a href="" class="logo">
            <div class="t">
                <h1 style="text-align: center;">淘宝发单平台</h1>
            </div>
            <div class="en"><img src="/frontend/images/en.png"></div>
        </a>
        <div class="nav">
            <ul>
                <li class="{{ in_array(Route::currentRouteName(), ['frontend.index', 'users.persional']) ? 'current' : '' }}"><a href="{{ route('frontend.index') }}">首页</a><div class="arrow"></div></li>

                @if(Auth::user()->could('frontend.goods.index'))
                    <li class="{{ substr(Route::currentRouteName(), 0, 14) == 'frontend.goods' ? 'current' : '' }}"><a href="{{ route('frontend.goods.index') }}">商品</a><div class="arrow"></div></li>
                @endif

                @if(Auth::user()->could('frontend.order.receive'))
                    <li class="{{ substr(Route::currentRouteName(), 0, 14) == 'frontend.order' ? 'current' : '' }}"><a href="{{ route('frontend.order.receive') }}">订单</a><div class="arrow"></div></li>
                @endif

                @if($route = Auth::user()->could([
                              'frontend.workbench.leveling.index',
                              'frontend.workbench.leveling.create',
                              'frontend.workbench.leveling.wait',
                          ]))
                    <li class="{{ substr(Route::currentRouteName(), 0, 18) == 'frontend.workbench' ? 'current' : '' }}"><a href="{{ route($route) }}">工作台</a><div class="arrow"></div></li>
                @endif

                @if($route = Auth::user()->could([
                    'frontend.finance.asset',
                    'frontend.finance.asset-daily',
                    'frontend.finance.amount-flow',
                    'frontend.finance.withdraw-order',
                    'frontend.statistic.employee',
                    'frontend.statistic.order',
                    'frontend.statistic.sms',
                ]))
                    <li class="{{ substr(Route::currentRouteName(), 0, 16) == 'frontend.finance'  || substr(Route::currentRouteName(), 0, 18) == 'frontend.statistic' ? 'current' : '' }}"><a href="{{ route($route) }}">财务</a><div class="arrow"></div></li>
                @endif

                @if($route = Auth::user()->could([
                    'frontend.workbench.index',
                ]))
                    <li class="{{ substr(Route::currentRouteName(), 0, 18) == 'frontend.workbench' ? 'current' : '' }}"><a href="{{ route($route) }}">工作台</a><div class="arrow"></div></li>
                @endif

                    <li class="{{ 
                        substr(Route::currentRouteName(), 0, 16) == 'staff-management' ? 'current' : '' ||
                        substr(Route::currentRouteName(), 0, 7) == 'station' ? 'current' : '' ||
                        substr(Route::currentRouteName(), 0, 13) == 'home-accounts' ? 'current' : '' ||
                        substr(Route::currentRouteName(), 0, 6) == 'idents' ? 'current' : '' ||
                        Route::currentRouteName() == 'login.history' ? 'current' : '' 
                    }}"><a href="{{ route('home-accounts.index') }}">账号</a><div class="arrow"></div></li>
                @if($route = Auth::user()->could([
                    'frontend.setting.sending-control.index',
                    'frontend.setting.receiving-control.index',
                    'frontend.setting.api-risk-management.index',
                    'frontend.setting.skin.index',
                    'frontend.setting.sms.index',
                    'frontend.setting.tb-auth.store',
                    'frontend.setting.sending-assist.require',
                ]))
                    <li class="{{ substr(Route::currentRouteName(), 0, 16) == 'frontend.setting' ? 'current' : '' }}"><a href="{{ route($route) }}">设置</a><div class="arrow"></div></li>
                @endif
                {{--@if($route = Auth::user()->could([--}}
                    {{--'frontend.statistic.employee',--}}
                    {{--'frontend.statistic.order',--}}
                    {{--'frontend.statistic.sms',--}}
                {{--]))--}}
                    {{--<li class="{{ substr(Route::currentRouteName(), 0, 18) == 'frontend.statistic' ? 'current' : '' }}"><a href="{{ route($route) }}">统计</a><div class="arrow"></div></li>--}}
                {{--@endif--}}
                @if(Auth::user()->could('frontend.steam.goods.index'))
                    <li class="{{ substr(Route::currentRouteName(), 0, 14) == 'frontend.steam' ? 'current' : '' }}"><a href="{{ route('frontend.steam.goods.index') }}">Steam</a><div class="arrow"></div></li>
                @endif
            </ul>
        </div>
        <div class="user">
            <ul class="layui-nav layui-bg-blue" lay-filter="demo">
                <li class="layui-nav-item ">
                    <a href="#" id="leveling-message">
                        <i class="layui-icon" style="padding-right: 7px;">&#xe611;</i>代练留言<span class="layui-badge layui-bg-gray leveling-message-quantity @if(levelingMessageCount(auth()->user()->getPrimaryUserId(), 4) == 0) layui-hide  @endif" style="border-radius: 50%;margin-top:-15px">{{ levelingMessageCount(auth()->user()->getPrimaryUserId(), 4) }}</span>
                    </a>
                </li>
                <!--如果是代练商户则显示挂起-->
                @if(Auth::user()->could('frontend.workbench.recharge.index'))
                <li class="layui-nav-item">
                    <a href="#" class="current-status">{{ Auth::user()->online == 1 ? '在线' : '挂起' }}</a>
                    <dl class="layui-nav-child">
                        <dd><a href="#">在线</a></dd>
                        <dd><a href="#">挂起</a></dd>
                    </dl>
                </li>
                @endif
                <li class="layui-nav-item"><a href="#"><i class="iconfont icon-tuichu" style="padding-right: 7px;"></i>注销登录</a></li>
            </ul>
        </div>
    </div>
</div>