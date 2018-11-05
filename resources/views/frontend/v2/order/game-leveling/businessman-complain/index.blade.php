@extends('frontend.v2.layouts.main')

@section('title', '代练待发')

@section('content')
    <game-leveling-businessman-complain
            page-title="代练待发"
            order-api="{{ route('order.game-leveling.businessman-complain.data-list') }}"
            status-quantity-api="{{ route('order.game-leveling.businessman-complain.status-quantity') }}"
            images-api="{{ route('order.game-leveling.businessman-complain.images') }}"
            businessman-complain-cancel-api="{{ route('order.game-leveling.businessman-complain.cancel') }}"
            games-api="{{ route('games') }}"
            show-api="{{ route('order.game-leveling.show') }}"
            game-leveling-types-api="{{ route('game-leveling-types') }}">
    </game-leveling-businessman-complain>
@endsection
