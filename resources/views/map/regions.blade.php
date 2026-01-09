<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Philippines Map</title>
<style>
    body {
        font-family: Arial, sans-serif;
        text-align: center;
        margin: 0;
        padding: 20px;
    }

    h1 {
        margin-bottom: 20px;
    }

    .ph-map {
        width: 100%;
        max-width: 1000px;
        height: auto;
        border: 1px solid #ccc;
        display: block;
        margin: 0 auto;
    }

    .region {
        fill: #e5e7eb;
        stroke: #ffffff;
        stroke-width: 1;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .region:hover {
        fill: #2563eb;
    }

    .tooltip {
        position: absolute;
        background: rgba(0,0,0,0.7);
        color: #fff;
        padding: 4px 8px;
        border-radius: 4px;
        pointer-events: none;
        font-size: 14px;
        display: none;
    }
</style>
</head>
<body>

<h1>Philippines Map</h1>

<div id="map-container">
    <svg viewBox="0 0 1000 1200" class="ph-map">
        {!! $svgPaths !!}
    </svg>
</div>

<div id="tooltip" class="tooltip"></div>

<script>
const tooltip = document.getElementById('tooltip');

document.querySelectorAll('.region').forEach(region => {
    region.addEventListener('mousemove', (e) => {
        tooltip.style.display = 'block';
        tooltip.style.left = e.pageX + 10 + 'px';
        tooltip.style.top = e.pageY + 10 + 'px';
        tooltip.innerText = region.dataset.name || region.dataset.region;
    });

    region.addEventListener('mouseleave', () => {
        tooltip.style.display = 'none';
    });

    region.addEventListener('click', () => {
        const regionCode = region.dataset.code;
        if(regionCode) {
            window.location.href = `/map/region/${regionCode}`;
        }
    });
});
</script>

</body>
</html>
