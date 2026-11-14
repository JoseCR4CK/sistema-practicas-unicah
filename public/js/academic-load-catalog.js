document.addEventListener('DOMContentLoaded', () => {
    const source = document.getElementById('academicCatalogData');
    if (!source) return;
    const catalog = JSON.parse(source.textContent);

    document.querySelectorAll('[data-academic-catalog]').forEach(root => {
        const career = root.querySelector('[data-catalog-career]');
        const subject = root.querySelector('[data-catalog-subject]');
        const credits = root.querySelector('[data-catalog-credits]');
        const hours = root.querySelector('[data-catalog-hours]');
        const message = root.querySelector('[data-catalog-message]');
        const budget = root.querySelector('[data-catalog-budget]');
        const start = root.querySelector('[name="starts_at"]');
        const end = root.querySelector('[name="ends_at"]');
        const subjects = () => catalog.find(item => String(item.id) === career.value)?.subjects ?? [];

        function updateBudget() {
            const selected = subjects().find(item => String(item.id) === subject.value);
            credits.value = selected?.credits ?? '';
            hours.value = selected?.weekly_class_hours ?? '';
            end.setCustomValidity('');
            if (!selected || selected.allowed_minutes === null) {
                budget.textContent = 'La asignatura debe contar con créditos y equivalencia confirmados.';
                return;
            }
            const count = root.querySelectorAll('[name="weekdays[]"]:checked').length;
            const minutes = value => { const [h,m] = value.split(':').map(Number); return h*60+m; };
            if (!start.value || !end.value || !count || minutes(end.value) <= minutes(start.value)) {
                budget.textContent = `Esta asignatura permite ${selected.weekly_class_hours} horas clase semanales (${selected.allowed_minutes} minutos). Completa los días y el horario.`;
                return;
            }
            const scheduled = (minutes(end.value)-minutes(start.value))*count;
            const text = `Esta asignatura permite ${selected.weekly_class_hours} horas clase semanales (${selected.allowed_minutes} minutos). El horario seleccionado suma ${scheduled} minutos.`;
            budget.textContent = text + (scheduled < selected.allowed_minutes ? ' Horario parcial.' : '');
            if (scheduled > selected.allowed_minutes) end.setCustomValidity(text);
        }

        function populate(selectedId = '') {
            subject.replaceChildren(new Option(career.value ? 'Seleccione una asignatura' : 'Seleccione primero una carrera', ''));
            const available = subjects();
            subject.disabled = !career.value || available.length === 0;
            message.textContent = career.value && !available.length ? 'No hay asignaturas disponibles para esta carrera. El catálogo está pendiente de ampliación.' : '';
            for (const item of available) {
                const option = new Option(`${item.code ? item.code+' · ' : ''}${item.name}${item.allowed_minutes === null ? ' · Información académica pendiente' : ''}`, String(item.id));
                option.disabled = item.allowed_minutes === null;
                subject.add(option);
            }
            subject.value = available.some(item => String(item.id) === String(selectedId) && item.allowed_minutes !== null) ? String(selectedId) : '';
            updateBudget();
        }
        career.addEventListener('change', () => populate());
        subject.addEventListener('change', updateBudget);
        root.querySelectorAll('[name="weekdays[]"], [name="starts_at"], [name="ends_at"]').forEach(input => input.addEventListener('input', updateBudget));
        populate(subject.dataset.selectedSubject);
    });
});
