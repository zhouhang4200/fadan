@extends('frontend.v2.layouts.main')

@section('title', '我的账号')

@section('content')
    <account-mine
            account-mine-data-list-api="{{ route('v2.account.mine-data-list') }}"
    >
    </account-mine>
@endsection