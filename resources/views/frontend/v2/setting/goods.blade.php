@extends('frontend.v2.layouts.main')

@section('title', '抓取商品配置管理')

@section('content')
    <setting-goods
            setting-goods-delivery-api="{{ route('v2.setting.goods-delivery') }}"
            setting-goods-data-list-api="{{ route('v2.setting.goods-data-list') }}"
            setting-goods-game-api="{{ route('v2.setting.goods-game') }}"
            setting-goods-seller-nick-api="{{ route('v2.setting.goods-seller-nick') }}"
            setting-goods-add-api="{{ route('v2.setting.goods-add') }}"
            setting-goods-update-api="{{ route('v2.setting.goods-update') }}"
            setting-goods-delete-api="{{ route('v2.setting.goods-delete') }}"
    >
    </setting-goods>
@endsection