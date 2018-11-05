@extends('frontend.v2.layouts.main')

@section('title', '短信管理')

@section('content')
    <setting-message
            setting-message-status-api="{{ route('v2.setting.message-status') }}"
            setting-message-data-list-api="{{ route('v2.setting.message-data-list') }}"
            setting-message-update-api="{{ route('v2.setting.message-update') }}"
    >
    </setting-message>
@endsection