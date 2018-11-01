@extends('frontend.v2.layouts.main')

@section('title', '代练订单-下单')

@section('content')
    <game-leveling-order-show
            page-title="代练订单-订单详情"
            trade-no="{{ request('trade_no') }}"
            order-repeat-api="{{ route('order.game-leveling.repeat') }}"
            order-edit-api="{{ route('order.game-leveling.edit') }}"
            order-update-api="{{ route('order.game-leveling.update') }}"
            order-add-amount-api="{{ route('order.game-leveling.add-amount') }}"
            order-log-api="{{ route('order.game-leveling.log') }}"
            order-add-day-hour-api="{{ route('order.game-leveling.add-day-hour') }}"
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
            complain-info-api="{{ route('order.game-leveling.complain-info') }}"
            add-complain-info-api="{{ route('order.game-leveling.add-complain-info') }}"
            complete-api="{{ route('order.game-leveling.complete') }}"
            lock-api="{{ route('order.game-leveling.lock') }}"
            cancel-lock-api="{{ route('order.game-leveling.cancel-lock') }}"
            anomaly-api="{{ route('order.game-leveling.anomaly') }}"
            cancel-anomaly-api="{{ route('order.game-leveling.cancel-anomaly') }}"
            message-api="{{ route('order.game-leveling.message') }}"
            send-message-api="{{ route('order.game-leveling.send-message') }}"
            game-region-server-api="{{ route('game-region-server') }}">
    </game-leveling-order-show>
@endsection
