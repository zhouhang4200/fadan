@extends('frontend.v2.layouts.main')

@section('title', '我的提现')

@section('content')
    <finance-my-withdraw
            my-withdraw-api="{{ route('v2.finance.my-withdraw.data-list') }}"
            can-withdraw-api="{{ route('v2.finance.can-withdraw') }}"
            create-withdraw-api="{{ route('v2.finance.create-withdraw') }}"
    >
    </finance-my-withdraw>
@endsection