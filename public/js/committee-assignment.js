(() => {
    const catalogElement = document.getElementById('committeeTeacherCatalog');
    if (!catalogElement) return;
    const teachers = JSON.parse(catalogElement.textContent);
    const origin = JSON.parse(document.getElementById('committeeOriginPeriod').textContent);
    document.querySelectorAll('[data-committee-slot]').forEach(slot => {
        const area = slot.querySelector('[data-committee-area]');
        const select = slot.querySelector('[data-committee-teacher]');
        const load = slot.querySelector('[data-committee-load]');
        const describe = () => {
            const teacher = teachers.find(row => String(row.user_id) === select.value);
            if (!teacher) { load.textContent = ''; return; }
            const other = Object.entries(teacher.periods).filter(([period]) => period !== origin).map(([period, count]) => `${period}: ${count}`);
            load.textContent = `${teacher.faculty} · ${origin}: ${teacher.periods[origin] || 0}${other.length ? ' | ' + other.join(' | ') : ''} | Total activas: ${teacher.total}`;
        };
        const filter = selected => {
            select.replaceChildren(new Option(area.value ? 'Seleccione docente' : 'Seleccione primero facultad o área', ''));
            select.disabled = !area.value;
            teachers.filter(teacher => teacher.faculty_ids.some(id => String(id) === area.value)).forEach(teacher => {
                select.add(new Option(teacher.name, teacher.user_id));
            });
            select.value = selected || '';
            describe();
        };
        area.addEventListener('change', () => filter(''));
        select.addEventListener('change', describe);
        filter(select.dataset.selected);
    });
})();
