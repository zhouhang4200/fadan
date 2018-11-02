@extends('frontend.v2.layouts.main')

@section('title', '资金流水')

@section('content')
    <finance-amount-flow
            amount-flow-api="{{ route('v2.finance.amount-flow.data-list') }}"
    >
    </finance-amount-flow>
@endsection
