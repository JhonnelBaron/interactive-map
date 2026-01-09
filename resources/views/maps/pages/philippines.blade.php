@extends('maps.layouts.map')

@section('title', 'Philippines Map')
@section('heading', 'Philippines')

@section('map')
    @include('maps.components.svg.philippines')
@endsection

@push('scripts')
<script src="/js/map/interactions/region.js"></script>
@endpush
