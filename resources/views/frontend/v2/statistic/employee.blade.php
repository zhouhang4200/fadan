@extends('frontend.v2.layouts.main')

@section('title', '员工统计')

@section('content')
    <statistic-employee
            statistic-employee-data-list-api="{{ route('v2.statistic.employee-data-list') }}"
            statistic-employee-user-api="{{ route('v2.statistic.employee-user') }}"
    >
    </statistic-employee>
@endsection