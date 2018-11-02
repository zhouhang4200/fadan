@extends('frontend.v2.layouts.main')

@section('title', '我的资产')

@section('content')
    <finance-my-asset
            my-asset-api="{{ route('v2.finance.my-asset.data-list') }}"
    >
    </finance-my-asset>
@endsection