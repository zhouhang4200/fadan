@extends('frontend.v2.layouts.main')

@section('title', '代练订单')

@section('content')
    <game-leveling-order
            page-title="代练订单"
            game-leveling-order-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-delete-api="1">
    </game-leveling-order>
@endsection
