@extends('frontend.v2.layouts.main')

@section('title', '岗位管理')

@section('content')
    <account-station
            account-station-form-api="{{ route('v2.account.station-form') }}"
            account-station-data-list-api="{{ route('v2.account.station-data-list') }}"
            account-station-add-api="{{ route('v2.account.station-add') }}"
            account-station-update-api="{{ route('v2.account.station-update') }}"
            account-station-permission-api="{{ route('v2.account.station-permission') }}"
            account-station-delete-api="{{ route('v2.account.station-delete') }}"
    >
    </account-station>
@endsection