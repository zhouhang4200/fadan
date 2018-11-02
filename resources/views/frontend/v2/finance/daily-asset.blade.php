@extends('frontend.v2.layouts.main')

@section('title', '资产日报')

@section('content')
    <finance-daily-asset
            daily-asset-api="{{ route('v2.finance.daily-asset.data-list') }}"
    >
    </finance-daily-asset>
@endsection