@extends('frontend.v2.layouts.main')

@section('title', '订单统计')

@section('content')
    <statistic-order
            statistic-order-data-list-api="{{ route('v2.statistic.order-data-list') }}"
    >
    </statistic-order>
@endsection