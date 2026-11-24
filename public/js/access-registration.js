document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-access-registration]');
    const subjects = form?.querySelector('#subjects');
    if (!subjects) return;
    const faculty = form.querySelector('#academic_career_id');
    const pending = form.querySelector('#has_pending_subjects');
    const catalog = JSON.parse(document.getElementById('accessCatalog').textContent);
    const message = form.querySelector('[data-subject-message]');
    const toggle = () => {
        const enabled = pending.value === '1';
        form.querySelector('[data-pending-selector]').hidden = !enabled;
        subjects.disabled = !enabled || !faculty.value;
        subjects.required = enabled;
        message.textContent = faculty.value ? '' : 'Selecciona primero tu facultad.';
    };
    faculty.addEventListener('change', () => {
        subjects.replaceChildren(...(catalog.find(unit => String(unit.id) === faculty.value)?.subjects ?? [])
            .map(subject => new Option(subject.name, subject.id)));
        toggle();
    });
    pending.addEventListener('change', toggle);
    toggle();
});
