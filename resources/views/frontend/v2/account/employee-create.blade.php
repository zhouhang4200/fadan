@extends('frontend.v2.layouts.main')

@section('title', '新增员工岗位')

@section('content')
    <account-employee-create
            account-employee-add-api="{{ route('v2.account.employee-add') }}"
            account-employee-station-api="{{ route('v2.account.employee-station') }}"
    >
    </account-employee-create>
@endsection