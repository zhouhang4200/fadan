@extends('frontend.v2.layouts.main')

@section('title', '我的账号')

@section('content')
    <account-mine
            account-mine-form-api="{{ route('v2.account.mine-form') }}"
            account-mine-update-api="{{ route('v2.account.mine-update') }}"
    >
    </account-mine>
@endsection