@extends('backend.layouts.main')

@section('title', ' | 用户资料')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li class=""><span>首页</span></li>
                <li class=""><a href="{{ route('orders.index') }}"><span>用户列表</span></a></li>
                <li class="active"><span>用户资料</span></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <div class="layui-tab layui-tab-brief" lay-filter="detail">
                            <ul class="layui-tab-title">
                                <li  class="layui-this"  lay-id="detail"><a href="{{ route('frontend.user.show', ['id' => Route::input('userId')])  }}">用户资料</a></li>
                                <li lay-id="authentication"><a href="{{ route('frontend.user.authentication', ['id' => Route::input('userId')])  }}">实名认证</a></li>
                            </ul>
                            <div class="layui-tab-content">
                                <div class="layui-tab-item layui-show detail"></div>
                                <div class="layui-tab-item authentication"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="/backend/js/jquery.magnific-popup.min.js"></script>
    <script>

    </script>
@endsection