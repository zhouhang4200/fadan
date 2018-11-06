@extends('frontend.v2.layouts.main')

@section('title', '代练发单辅助')

@section('content')
    <setting-auxiliary
            setting-markup-data-list-api="{{ route('v2.setting.markup-data-list') }}"
            setting-markup-add-api="{{ route('v2.setting.markup-add') }}"
            setting-markup-update-api="{{ route('v2.setting.markup-update') }}"
            setting-markup-delete-api="{{ route('v2.setting.markup-delete') }}"
            setting-markup-time-api="{{ route('v2.setting.markup-time') }}"
            setting-channel-data-list-api="{{ route('v2.setting.channel-data-list') }}"
            setting-channel-switch-api="{{ route('v2.setting.channel-switch') }}"
    >
    </setting-auxiliary>
@endsection