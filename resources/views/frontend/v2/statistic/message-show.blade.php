@extends('frontend.v2.layouts.main')

@section('title', '短信详情')

@section('content')
    <statistic-message-show
            statistic-message-show-data-list-api="{{ route('v2.statistic.message-show-data-list') }}"
            date-api="{{ $date }}"
    >
    </statistic-message-show>
@endsection