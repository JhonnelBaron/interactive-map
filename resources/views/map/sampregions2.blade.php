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
    stroke: #ffffff;
    stroke-width: 1;
    cursor: pointer;
    transition: fill 0.25s ease;
}

.region:hover { /* hover fill will be handled by JS */ }
.province:hover { fill: #f59e0b; }

.selected-region {
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

<script src="{{ asset('storage/maps/map_settings.js') }}"></script>


<script>
const tooltip = document.getElementById('tooltip');
const svg = document.querySelector('.ph-map');
const resetBtn = document.getElementById('reset-zoom');
const originalViewBoxAttr = svg.getAttribute('viewBox').split(' ').map(Number);
const originalViewBox = {
    x: originalViewBoxAttr[0],
    y: originalViewBoxAttr[1],
    width: originalViewBoxAttr[2],
    height: originalViewBoxAttr[3]
};

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

// Animate viewBox (zoom)
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

// Zoom to region
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
    const startViewBox = { x: viewBoxAttr[0], y: viewBoxAttr[1], width: viewBoxAttr[2], height: viewBoxAttr[3] };
    animateViewBox(svg, startViewBox, endViewBox, 600);
    resetBtn.style.display = 'block';
}

// ================================
// APPLY map_settings.js DESIGN
// ================================
document.addEventListener('DOMContentLoaded', () => {
    if (!window.simplemaps_countrymap_mapdata) {
        console.warn('map_settings.js not loaded');
        return;
    }

    const mapData = window.simplemaps_countrymap_mapdata;
    const defaults = mapData.main_settings;

    // Apply colors & hover
    document.querySelectorAll('.region').forEach(region => {
        const code = region.dataset.region;
        const config = mapData.state_specific?.[code];

        if (!config) return;

        // Base color
        region.style.fill = config.color || defaults.state_color;

        // Tooltip name
        region.dataset.name = config.name || code;

        // Hover color
        region.addEventListener('mouseenter', () => {
            region.style.fill = config.hover_color || defaults.state_hover_color;
        });
        region.addEventListener('mouseleave', () => {
            if (!region.classList.contains('selected-region')) {
                region.style.fill = config.color || defaults.state_color;
            }
        });

        // Click to zoom
        region.addEventListener('click', () => {
                const mapData = window.simplemaps_countrymap_mapdata;
                const defaults = mapData.main_settings;

                const code = region.dataset.region;

                // 1️⃣ Reset all regions
                document.querySelectorAll('.region').forEach(r => {
                    const cfg = mapData.state_specific?.[r.dataset.region];
                    r.style.fill = cfg?.color || defaults.state_color;
                    r.classList.remove('selected-region');
                });

                // 2️⃣ Highlight clicked region
                region.classList.add('selected-region');
                const cfg = mapData.state_specific?.[code];
                region.style.fill = cfg?.hover_color || '#1d4ed8';

                // 3️⃣ Reset/hide all provinces
                document.querySelectorAll(`.province[data-region="${code}"]`).forEach(p => {
                    p.style.display = 'none';
                });

                // 4️⃣ Show provinces belonging to this region
                document.querySelectorAll(`.province[data-region="${code}"]`).forEach(p => {
                    p.style.display = 'block';
                });

                // 5️⃣ Zoom to region
                zoomToRegion(region);
            });
        // Show tooltip
        region.addEventListener('mousemove', e => showTooltip(e, region.dataset.name));
        region.addEventListener('mouseleave', hideTooltip);
    });

    // Province tooltip & click
    document.querySelectorAll('.province').forEach(province => {
        province.addEventListener('mousemove', e => showTooltip(e, province.dataset.name));
        province.addEventListener('mouseleave', hideTooltip);
        province.addEventListener('click', () => {
            const code = province.dataset.province;
            if (code) window.location.href = `/map/province/${code}`;
        });
    });
});

// Exit zoom
resetBtn.addEventListener('click', () => {
    document.querySelectorAll('.region').forEach(region => {
        const code = region.dataset.region;
        const cfg = simplemaps_countrymap_mapdata.state_specific?.[code];
        region.style.fill = cfg?.color || simplemaps_countrymap_mapdata.main_settings.state_color;
        region.classList.remove('selected-region');
    });
    animateViewBox(svg, {
        x: +svg.getAttribute('viewBox').split(' ')[0],
        y: +svg.getAttribute('viewBox').split(' ')[1],
        width: +svg.getAttribute('viewBox').split(' ')[2],
        height: +svg.getAttribute('viewBox').split(' ')[3]
    }, originalViewBox, 600);
    resetBtn.style.display = 'none';
});
</script>

</body>
</html>
