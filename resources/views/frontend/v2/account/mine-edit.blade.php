@extends('frontend.v2.layouts.main')

@section('title', '我的账号修改')

@section('content')
    <my-asset
            account-mine-edit-api="{{ route('v2.account.mine-edit') }}"
    >
    </my-asset>
@endsection