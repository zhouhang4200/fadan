@extends('frontend.v2.layouts.main')

@section('title', '代练订单-重新下单')

@section('content')
    <game-leveling-order-repeat
            page-title="代练订单-重新下单"
            trade-no="{{ request('trade_no') }}"
            order-edit-api="{{ route('order.game-leveling.edit') }}"
            order-create-api="{{ route('order.game-leveling.create') }}"
            taobao-order-api="{{ route('order.game-leveling.taobao-order') }}"
            game-leveling-types-api="{{ route('game-leveling-types') }}"
            game-region-server-api="{{ route('game-region-server') }}">
    </game-leveling-order-repeat>
@endsection
