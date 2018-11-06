@extends('frontend.v2.layouts.main')

@section('title', '店铺授权')

@section('content')
    <setting-authorize
            setting-authorize-data-list-api="{{ route('v2.setting.authorize-data-list') }}"
            setting-authorize-delete-api="{{ route('v2.setting.authorize-delete') }}"
            setting-authorize-url-api="{{ route('v2.setting.authorize-url') }}"
    >
    </setting-authorize>
@endsection