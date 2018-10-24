@extends('frontend.v2.layouts.main')

@section('title', '代练订单')

@section('content')
    {{--<test></test>--}}
    <game-leveling-order
            page-title="代练订单"
            game-leveling-image-base64-api="{{ route('order.game-leveling.image-base64') }}"
            game-leveling-order-api="{{ route('order.game-leveling.data-list') }}"
            game-leveling-order-delete-api="{{ route('order.game-leveling.delete') }}"
            game-leveling-order-on-sale-api="{{ route('order.game-leveling.on-sale') }}"
            game-leveling-order-off-sale-api="{{ route('order.game-leveling.off-sale') }}"
            game-leveling-order-apply-consult-api="{{ route('order.game-leveling.apply-consult') }}"
            game-leveling-order-cancel-consult-api="{{ route('order.game-leveling.cancel-consult') }}"
            game-leveling-order-reject-consult-api="{{ route('order.game-leveling.reject-consult') }}"
            game-leveling-order-force-delete-api="{{ route('order.game-leveling.force-delete') }}"
            game-leveling-order-apply-complain-api="{{ route('order.game-leveling.apply-complain') }}"
            game-leveling-order-cancel-complain-api="{{ route('order.game-leveling.cancel-complain') }}"
            game-leveling-order-arbitration-api="{{ route('order.game-leveling.arbitration') }}"
            game-leveling-order-apply-complete-api="{{ route('order.game-leveling.apply-complete') }}"
            game-leveling-order-cancel-complete-api="{{ route('order.game-leveling.cancel-complete') }}"
            game-leveling-order-complete-api="{{ route('order.game-leveling.complete') }}"
            game-leveling-order-lock-api="{{ route('order.game-leveling.lock') }}"
            game-leveling-order-cancel-lock-api="{{ route('order.game-leveling.cancel-lock') }}"
            game-leveling-order-anomaly-api="{{ route('order.game-leveling.anomaly') }}"
            game-leveling-order-cancel-anomaly-api="{{ route('order.game-leveling.cancel-anomaly') }}">
    </game-leveling-order>
@endsection
