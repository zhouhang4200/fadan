@extends('frontend.v2.layouts.main')

@section('title', '短信统计')

@section('content')
    <statistic-message
            statistic-message-data-list-api="{{ route('v2.statistic.message-data-list') }}"
            statistic-message-show-api="{{ route('v2.statistic.message-show') }}"
    >
    </statistic-message>
@endsection