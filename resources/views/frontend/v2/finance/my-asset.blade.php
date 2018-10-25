@extends('frontend.v2.layouts.main')

@section('title', '我的资产')

@section('content')
    <my-asset
            my-asset-api="{{ route('v2.finance.my-asset.data-list') }}"
    >
    </my-asset>
@endsection