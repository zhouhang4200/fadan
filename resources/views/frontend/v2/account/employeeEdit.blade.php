@extends('frontend.v2.layouts.main')

@section('title', '编辑员工岗位')

@section('content')
    <account-employee-edit
            account-employee-update-api="{{ route('v2.account.employee-update') }}"
            account-employee-station-api="{{ route('v2.account.employee-station') }}"
    >
    </account-employee-edit>
@endsection