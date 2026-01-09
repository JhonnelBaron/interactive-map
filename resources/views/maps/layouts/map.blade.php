<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    @vite([
        'resources/css/app.css',
        'resources/css/map/base.css',
        'resources/css/map/regions.css',
        'resources/css/map/provinces.css',
        'resources/css/map/animations.css',
        'resources/js/app.js',
        'resources/js/map/map.js'
    ])
    @stack('styles')
</head>
<body>

<h1>@yield('heading')</h1>

<div id="map-container">
    <svg id="map-svg" viewBox="0 0 1000 1200" class="ph-map">
        @yield('map')
    </svg>
</div>

@include('maps.components.tooltip')

@stack('scripts')
</body>
</html>
