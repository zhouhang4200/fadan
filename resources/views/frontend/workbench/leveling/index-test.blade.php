@extends('frontend.layouts.app')

@section('title', '工作台 - 代练')

@section('css')
    <link rel="stylesheet" href="/frontend/css/fixed-table.css">
    <style>
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track:vertical::-webkit-scrollbar-track:horizontal {
            background-color: #fff;
            -webkit-background-clip: padding-box;
            background-clip: padding-box;
        }

        ::-webkit-scrollbar-thumb {
            min-height: 28px;
            padding-top: 100;
            background-color: rgba(0, 0, 0, .2);
            -webkit-background-clip: padding-box;
            background-clip: padding-box;
            border-radius: 5px;
            -webkit-box-shadow: inset 1px 1px 0 rgba(0, 0, 0, .1), inset 0 -1px 0 rgba(0, 0, 0, .07);
        }

        .layui-laypage-em {
            background-color: #ff7a00 !important;
        }

        .layui-form-select .layui-input {
            padding-right: 0 !important;
        }

        .layui-table-fixed-r .layui-table-cell {
            overflow: inherit;
        }

        .layui-form-item .layui-inline {
            margin-bottom: 5px;
            margin-right: 5px;
        }

        .layui-form-mid {
            margin-right: 4px;
        }

        .layui-tab-title li {
            min-width: 42px;
        }

        .w-150 {
            width: 150px;
        }

        .w-100 {
            width: 100px;
        }


        .opt-btn {
            color: #1f93ff;
            padding: 0 2px;
            border: none;
            cursor:pointer;
        }

        .layui-form-item {
            margin-bottom: 0
        }

        .pagination > .active span {
            color: #fff;
            background: #ff7a00;
            border: 1px solid #ff7a00;
        }
    </style>
@endsection

@section('submenu')
    @include('frontend.workbench.submenu')
@endsection

@section('main')

@endsection

<!--START 底部-->
@section('js')