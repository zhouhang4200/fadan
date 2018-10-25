@extends('frontend.v2.layouts.main')

@section('title', '代练订单')

@section('content')
    <amount-flow
            amount-flow-api="{{ route('v2.finance.amount-flow.data-list') }}"
    >
    </amount-flow>
@endsection
