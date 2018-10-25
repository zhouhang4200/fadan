@extends('frontend.v2.layouts.main')

@section('title', '资金流水')

@section('content')
    <amount-flow
            amount-flow-api="{{ route('v2.finance.amount-flow.data-list') }}"
    >
    </amount-flow>
@endsection
