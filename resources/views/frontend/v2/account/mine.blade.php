@extends('frontend.v2.layouts.main')

@section('title', '我的账号')

@section('content')
    <my-asset
            account-mine-data-list-api="{{ route('v2.account.mine-data-list') }}"
    >
    </my-asset>
@endsection