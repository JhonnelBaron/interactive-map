{{-- <!DOCTYPE html>
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
        const regionCode = region.dataset.region;
        if(regionCode) {
            window.location.href = `/map/region/${regionCode}`;
        }
    });
});
</script>

</body>
</html> --}}


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Philippines Map</title>
<style>
body { font-family: Arial; text-align: center; margin: 0; padding: 20px; }
h1 { margin-bottom: 20px; }

.ph-map { width: 100%; max-width: 1000px; height: auto; border: 1px solid #ccc; display: block; margin: 0 auto; }

.region, .province {
    fill: #e5e7eb;
    stroke: #ffffff;
    stroke-width: 1;
    cursor: pointer;
    transition: 0.2s ease;
}

.region:hover { fill: #2563eb; }
.province:hover { fill: #f59e0b; }

.selected-region {
    fill: #1d4ed8 !important;
    transition: fill 0.3s ease;
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
<button id="reset-zoom" style="
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    padding: 10px 15px;
    background-color: #1d4ed8;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: none;
">Exit Zoom</button>



<div id="map-container">
    <svg viewBox="0 0 1000 1200" class="ph-map" id="ph-map">
        {!! $svgPaths !!}
    </svg>
</div>

<div id="tooltip" class="tooltip"></div>










<script>
const tooltip = document.getElementById('tooltip');
let selectedRegion = null;
const svg = document.querySelector('.ph-map');
const resetBtn = document.getElementById('reset-zoom');
const originalViewBoxAttr = svg.getAttribute('viewBox').split(' ').map(Number);
const originalViewBox = {
    x: originalViewBoxAttr[0],
    y: originalViewBoxAttr[1],
    width: originalViewBoxAttr[2],
    height: originalViewBoxAttr[3]
};

// Reset all regions & hide provinces
function resetMap() {
    document.querySelectorAll('.region').forEach(r => r.classList.remove('selected-region'));
    document.querySelectorAll('.province').forEach(p => p.style.display = 'none');
}

// Show tooltip
function showTooltip(e, text) {
    tooltip.style.display = 'block';
    tooltip.style.left = e.pageX + 10 + 'px';
    tooltip.style.top = e.pageY + 10 + 'px';
    tooltip.innerText = text;
}

// Hide tooltip
function hideTooltip() {
    tooltip.style.display = 'none';
}
function zoomToRegion(region) {
    const bbox = region.getBBox();
    const padding = 50;

    const endViewBox = {
        x: bbox.x - padding,
        y: bbox.y - padding,
        width: bbox.width + padding * 2,
        height: bbox.height + padding * 2
    };

    const viewBoxAttr = svg.getAttribute('viewBox').split(' ').map(Number);
    const startViewBox = {
        x: viewBoxAttr[0],
        y: viewBoxAttr[1],
        width: viewBoxAttr[2],
        height: viewBoxAttr[3]
    };

    animateViewBox(svg, startViewBox, endViewBox, 600);

    // Show exit button
    resetBtn.style.display = 'block';
}

// Reset / zoom out
function resetZoom() {
    const viewBoxAttr = svg.getAttribute('viewBox').split(' ').map(Number);
    const currentViewBox = {
        x: viewBoxAttr[0],
        y: viewBoxAttr[1],
        width: viewBoxAttr[2],
        height: viewBoxAttr[3]
    };

    animateViewBox(svg, currentViewBox, originalViewBox, 600);

    // Hide exit button
    resetBtn.style.display = 'none';

    // Remove selected highlight
    document.querySelectorAll('.region').forEach(r => r.classList.remove('selected-region'));
}
// Region events
document.querySelectorAll('.region').forEach(region => {
    // Hover tooltip
    region.addEventListener('mousemove', e => showTooltip(e, region.dataset.name || region.dataset.region));
    region.addEventListener('mouseleave', hideTooltip);

    // Click to zoom
    region.addEventListener('click', () => {
        document.querySelectorAll('.region').forEach(r => r.classList.remove('selected-region'));
        region.classList.add('selected-region');
        zoomToRegion(region);

        // TODO: Load provinces here if needed
    });
});

// Exit button click
resetBtn.addEventListener('click', resetZoom);

function animateViewBox(svg, start, end, duration = 600) {
    const startTime = performance.now();

    function animate(now) {
        const elapsed = now - startTime;
        const t = Math.min(elapsed / duration, 1);

        const currentX = start.x + (end.x - start.x) * t;
        const currentY = start.y + (end.y - start.y) * t;
        const currentWidth = start.width + (end.width - start.width) * t;
        const currentHeight = start.height + (end.height - start.height) * t;

        svg.setAttribute('viewBox', `${currentX} ${currentY} ${currentWidth} ${currentHeight}`);

        if (t < 1) requestAnimationFrame(animate);
    }

    requestAnimationFrame(animate);
}

// Province events
document.querySelectorAll('.province').forEach(province => {
    province.addEventListener('mousemove', e => showTooltip(e, province.dataset.name));
    province.addEventListener('mouseleave', hideTooltip);

    province.addEventListener('click', () => {
        const provinceCode = province.dataset.province;
        if(provinceCode) window.location.href = `/map/province/${provinceCode}`;
    });
});
</script>

</body>
</html>
