@extends('frontend.v2.layouts.main')

@section('title', '代练订单')

@section('content')
    <game-leveling-order
            page-title="代练订单"
            game-leveling-order-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-delete-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-on-sale-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-off-sale-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-apply-consult-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-cancel-consult-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-reject-consult-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-force-delete-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-apply-complain-consult-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-cancel-complain-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-arbitration-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-apply-complete-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-cancel-complete-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-complete-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-lock-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-cancel-lock-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-anomaly-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-cancel-anomaly-api="{{ route('order.game-leveling.data-list') }}">
    </game-leveling-order>
@endsection
