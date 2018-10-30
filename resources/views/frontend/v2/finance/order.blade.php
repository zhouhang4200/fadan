@extends('frontend.v2.layouts.main')

@section('title', '财务订单列表')

@section('content')
    <finance-order
            finance-order-data-list-api="{{ route('v2.finance.order-data-list') }}"
            finance-game-api="{{ route('v2.finance.game') }}"
    >
    </finance-order>
@endsection
