const flowSearch = document.getElementById('flowSearch');
const flowStatus = document.getElementById('flowStatus');
const flowCareer = document.getElementById('flowCareer');
function filterAcademicRows() {
    const search = (flowSearch?.value || '').toLocaleLowerCase().trim();
    document.querySelectorAll('[data-flow-row]').forEach(row => {
        const matches = row.textContent.toLocaleLowerCase().includes(search)
            && (!flowStatus?.value || row.dataset.status === flowStatus.value)
            && (!flowCareer?.value || row.dataset.career === flowCareer.value);
        row.classList.toggle('d-none', !matches);
    });
}
[flowSearch, flowStatus, flowCareer].filter(Boolean).forEach(field => {
    field.addEventListener('input', filterAcademicRows);
});
