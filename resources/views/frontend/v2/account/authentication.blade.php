@extends('frontend.v2.layouts.main')

@section('title', '实名认证')

@section('content')
    <account-authentication
            account-authentication-form-api="{{ route('v2.account.authentication-form') }}"
            account-authentication-update-api="{{ route('v2.account.authentication-update') }}"
            account-authentication-add-api="{{ route('v2.account.authentication-add') }}"
            account-authentication-upload-api="{{ route('v2.account.authentication-upload') }}"
    >
    </account-authentication>
@endsection