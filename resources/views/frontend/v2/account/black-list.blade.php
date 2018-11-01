@extends('frontend.v2.layouts.main')

@section('title', '员工管理')

@section('content')
    <account-black-list
            account-black-list-data-list-api="{{ route('v2.account.black-list-data-list') }}"
            account-black-list-name-api="{{ route('v2.account.black-list-name') }}"
            account-black-list-delete-api="{{ route('v2.account.black-list-delete') }}"
            account-black-list-add-api="{{ route('v2.account.black-list-add') }}"
            account-black-list-update-api="{{ route('v2.account.black-list-update') }}"
    >
    </account-black-list>
@endsection