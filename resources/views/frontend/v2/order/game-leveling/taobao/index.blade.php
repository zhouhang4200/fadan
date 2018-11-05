@extends('frontend.v2.layouts.main')

@section('title', '代练待发')

@section('content')
    <game-leveling-taobao
            page-title="代练待发"
            order-api="{{ route('order.game-leveling.taobao.data-list') }}"
            status-quantity-api="{{ route('order.game-leveling.taobao.status-quantity') }}"
            games-api="{{ route('games') }}"
            game-leveling-types-api="{{ route('game-leveling-types') }}">
    </game-leveling-taobao>
@endsection
