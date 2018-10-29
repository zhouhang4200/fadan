@extends('frontend.v2.layouts.main')

@section('title', '代练订单-下单')

@section('content')
    <game-leveling-order-show
            page-title="代练订单-订单详情"
            trade-no="{{ request('trade_no') }}"
            order-edit-api="{{ route('order.game-leveling.edit') }}"
            order-update-api="{{ route('order.game-leveling.update') }}"
            game-leveling-types-api="{{ route('game-leveling-types') }}"
            game-region-server-api="{{ route('game-region-server') }}">
    </game-leveling-order-show>
@endsection
