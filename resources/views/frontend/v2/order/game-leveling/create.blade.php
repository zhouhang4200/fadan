@extends('frontend.v2.layouts.main')

@section('title', '代练订单-下单')

@section('content')
    <game-leveling-order-create
            page-title="代练订单-下单"
            order-create-api="{{ route('order.game-leveling.create') }}"
            game-leveling-types-api="{{ route('game-leveling-types') }}"
            game-region-server-api="{{ route('game-region-server') }}">
    </game-leveling-order-create>
@endsection
