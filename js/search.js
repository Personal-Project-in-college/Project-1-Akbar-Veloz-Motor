document.addEventListener("DOMContentLoaded", function() {
    const filterSidebar = document.getElementById('filterSidebar');
    const filterOverlay = document.getElementById('filterOverlay'); 

    if (filterToggleButton && filterSidebar && filterOverlay) {
        filterToggleButton.addEventListener('click', function() {
            filterSidebar.classList.toggle('show');
            filterOverlay.classList.toggle('show'); 
            document.body.classList.toggle('no-scroll'); 

            if (filterSidebar.classList.contains('show')) {
                filterToggleButton.innerHTML = '<i class="fas fa-times"></i> Tutup Filter';
            } else {
                filterToggleButton.innerHTML = '<i class="fas fa-filter"></i> Filter Kendaraan';
            }
        });

        filterOverlay.addEventListener('click', function() {
            filterSidebar.classList.remove('show');
            filterOverlay.classList.remove('show');
            document.body.classList.remove('no-scroll');
            filterToggleButton.innerHTML = '<i class="fas fa-filter"></i> Filter Kendaraan'; 
        });
    }

});