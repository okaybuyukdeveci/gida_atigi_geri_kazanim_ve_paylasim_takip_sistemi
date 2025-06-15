// custom.js
document.addEventListener('DOMContentLoaded', () => {
    const filterForm = document.querySelector('#filter-form');
    if (filterForm) {
        filterForm.addEventListener('submit', (e) => {
            e.preventDefault(); // Formun varsayılan submit davranışını engelle
            const locationInput = document.querySelector('#filter-location');
            const location = locationInput ? locationInput.value.trim() : '';
            const query = location ? `?location=${encodeURIComponent(location)}` : '';
            window.location.href = 'index.php' + query;
        });
    }
});
