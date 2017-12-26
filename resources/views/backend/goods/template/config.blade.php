@extends('backend.layouts.main')

@section('title', '| 模版配置')

@section('css')
    <style>
        .layui-tab {
            margin: 0;
        }
        .layui-tab-content {
            padding: 25px;
        }
        .layui-tab-content{
            padding: 25px 0;
        }
    </style>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="main-box clearfix">
            <header class="main-box-header clearfix">
                <h2></h2>
            </header>
            <div class="main-box-body clearfix">
                <div id="nestable-menu">
                    <button type="button" class="btn btn-primary" data-action="expand-all">
                        Expand All
                    </button>
                    <button type="button" class="btn btn-danger" data-action="collapse-all">
                        Collapse All
                    </button>
                </div>
                <div class="row cf nestable-lists">
                    <div class="col-md-6 dd" id="nestable">
                        <ol class="dd-list dd-nodrag">

                            <li class="dd-item dd-nodrag" data-id="2">
                                <div class="dd-handle">
                                    版本
                                </div>
                                <ol class="dd-list">
                                    <li class="dd-item" data-id="5">
                                        <div class="dd-handle">
                                             QQ
                                            <div class="nested-links">
                                                <a href="#" class="nested-link"><i
                                                            class="fa fa-pencil"></i></a>
                                                <a href="#" class="nested-link"><i
                                                            class="fa fa-cog"></i></a>
                                                <a href="#" class="nested-link"><i
                                                            class="fa fa-trash"></i></a>
                                            </div>
                                        </div>
                                        <ol class="dd-list">
                                            <li class="dd-item" data-id="6">
                                                <div class="dd-handle">
                                                   2
                                                </div>
                                            </li>
                                            <li class="dd-item" data-id="7">
                                                <div class="dd-handle">
                                                 3
                                                </div>
                                            </li>

                                        </ol>
                                    </li>
                                    <li class="dd-item" data-id="5">
                                        <div class="dd-handle">
                                            wx
                                            <div class="nested-links">
                                                <a href="#" class="nested-link"><i
                                                            class="fa fa-pencil"></i></a>
                                                <a href="#" class="nested-link"><i
                                                            class="fa fa-cog"></i></a>
                                                <a href="#" class="nested-link"><i
                                                            class="fa fa-trash"></i></a>
                                            </div>
                                        </div>
                                        <ol class="dd-list">
                                            <li class="dd-item" data-id="6">
                                                <div class="dd-handle">
                                                    1
                                                </div>
                                            </li>
                                            <li class="dd-item" data-id="7">
                                                <div class="dd-handle">
                                                    2
                                                </div>
                                            </li>

                                        </ol>
                                    </li>
                                </ol>
                            </li>
                            <li class="dd-item" data-id="11">
                                <div class="dd-handle">
                                    Item 11
                                </div>
                            </li>
                            <li class="dd-item" data-id="12">
                                <div class="dd-handle">
                                    Item 12
                                    <div class="nested-links">
                                        <a href="#" class="nested-link"><i
                                                    class="fa fa-pencil"></i></a>
                                        <a href="#" class="nested-link"><i
                                                    class="fa fa-cog"></i></a>
                                        <a href="#" class="nested-link"><i
                                                    class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                            </li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('js')
    <script src="/backend/js/jquery.nestable.js"></script>
    <script>
        // activate Nestable for list 1
        $('#nestable').nestable({
            expandAll:true
        });
        $('.dd').nestable('collapseAll');

        $('#nestable-menu').on('click', function (e) {
            var target = $(e.target),
                    action = target.data('action');
            if (action === 'expand-all') {
                $('.dd').nestable('expandAll');
            }
            if (action === 'collapse-all') {
                $('.dd').nestable('collapseAll');
            }
        });
    </script>
@endsection