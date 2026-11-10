document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-record-modal]').forEach(form => {
        const modal = form.closest('.modal');
        const title = modal.querySelector('.modal-title');
        const oldInput = JSON.parse(form.dataset.oldInput);
        const editId = oldInput._record || form.dataset.editId;
        let restore = form.dataset.validationFailed === '1';

        modal.addEventListener('show.bs.modal', event => {
            const trigger = event.relatedTarget;
            const fields = restore ? oldInput : JSON.parse(trigger?.dataset.fields || '{}');
            const action = trigger?.dataset.action;

            form.reset();
            form.action = action || form.dataset.createAction;
            form.elements._method.disabled = !action;
            form.elements._record.value = trigger?.dataset.recordId || '';
            title.textContent = action ? form.dataset.editTitle : form.dataset.createTitle;

            Object.entries(fields).forEach(([name, value]) => {
                if (name.startsWith('_')) return;
                const field = form.elements.namedItem(name);
                if (!field) return;
                if (field instanceof HTMLSelectElement && value && !Array.from(field.options).some(option => option.value === value)) {
                    field.add(new Option(value, value));
                }
                field.value = value ?? '';
            });

            restore = false;
        });

        if (editId || restore) {
            const trigger = Array.from(document.querySelectorAll('[data-record-id]'))
                .find(button => button.dataset.recordId === editId);
            if (trigger || !editId) bootstrap.Modal.getOrCreateInstance(modal).show(trigger);
        }
    });
});
