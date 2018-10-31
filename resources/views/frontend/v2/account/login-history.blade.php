@extends('frontend.v2.layouts.main')

@section('title', '登录记录')

@section('content')
    <account-login-history
            account-login-history-data-list-api="{{ route('v2.account.login-history-data-list') }}"
            account-user-arr-api="{{ route('v2.account.login-history-user') }}"
    >
    </account-login-history>
@endsection