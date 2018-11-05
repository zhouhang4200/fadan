@extends('frontend.v2.layouts.main')

@section('title', '员工管理')

@section('content')
    <account-employee
            account-employee-data-list-api="{{ route('v2.account.employee-data-list') }}"
            account-employee-user-api="{{ route('v2.account.employee-user') }}"
            account-employee-station-api="{{ route('v2.account.employee-station') }}"
            account-employee-switch-api="{{ route('v2.account.employee-switch') }}"
            account-employee-delete-api="{{ route('v2.account.employee-delete') }}"
            account-employee-create-api="{{ route('v2.account.employee-create') }}"
            account-employee-update-api="{{ route('v2.account.employee-update') }}"
            account-employee-add-api="{{ route('v2.account.employee-add') }}"
    >
    </account-employee>
@endsection