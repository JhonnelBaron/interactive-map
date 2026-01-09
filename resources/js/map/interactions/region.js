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
        if (regionCode) {
            window.location.href = `/map/region/${regionCode}`;
        }
    });

});
