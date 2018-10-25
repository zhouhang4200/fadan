@extends('frontend.v2.layouts.main')

@section('title', '代练订单')

@section('content')
    <game-leveling-order
            page-title="代练订单"
            order-api="{{ route('order.game-leveling.data-list') }}"
            games-api="{{ route('games') }}"
            game-leveling-types-api="{{ route('game-leveling-types') }}"
            delete-api="{{ route('order.game-leveling.delete') }}"
            on-sale-api="{{ route('order.game-leveling.on-sale') }}"
            off-sale-api="{{ route('order.game-leveling.off-sale') }}"
            apply-consult-api="{{ route('order.game-leveling.apply-consult') }}"
            cancel-consult-api="{{ route('order.game-leveling.cancel-consult') }}"
            reject-consult-api="{{ route('order.game-leveling.reject-consult') }}"
            agree-consult-api="{{ route('order.game-leveling.agree-consult') }}"
            apply-complain-api="{{ route('order.game-leveling.apply-complain') }}"
            cancel-complain-api="{{ route('order.game-leveling.cancel-complain') }}"
            arbitration-api="{{ route('order.game-leveling.arbitration') }}"
            complete-api="{{ route('order.game-leveling.complete') }}"
            lock-api="{{ route('order.game-leveling.lock') }}"
            cancel-lock-api="{{ route('order.game-leveling.cancel-lock') }}"
            anomaly-api="{{ route('order.game-leveling.anomaly') }}"
            cancel-anomaly-api="{{ route('order.game-leveling.cancel-anomaly') }}">
    </game-leveling-order>
@endsection
