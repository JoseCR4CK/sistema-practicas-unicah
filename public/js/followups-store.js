(() => {

    const STORAGE_KEY =
        'unicah_teacher_followups_v1';


    const initialData = [

        {
            id: 1,
            student_id: 1,
            estudiante: 'Andrea Martínez',
            cuenta: '0801-2022-00458',
            docente: 'Msc. Carlos Martínez',

            fecha: '2026-10-22',
            tipo: 'Revisión',
            titulo: 'Revisión general del avance',

            observacion:
                'Se revisó el estado general del proceso de práctica profesional.',

            estado: 'Realizado'
        },

        {
            id: 2,
            student_id: 3,
            estudiante: 'María López',
            cuenta: '0801-2022-00671',
            docente: 'Msc. Carlos Martínez',

            fecha: '2026-10-25',
            tipo: 'Orientación',
            titulo: 'Seguimiento de monografía',

            observacion:
                'Se verificó el avance correspondiente a la etapa de monografía.',

            estado: 'Realizado'
        },

        {
            id: 3,
            student_id: 1,
            estudiante: 'Andrea Martínez',
            cuenta: '0801-2022-00458',
            docente: 'Msc. Carlos Martínez',

            fecha: '2026-10-30',
            tipo: 'Seguimiento',
            titulo: 'Seguimiento general del proceso',

            observacion:
                'Actividad de seguimiento pendiente de realizar.',

            estado: 'Pendiente'
        }

    ];


    function initialize() {

        if (!localStorage.getItem(STORAGE_KEY)) {

            localStorage.setItem(
                STORAGE_KEY,
                JSON.stringify(initialData)
            );

        }

    }


    function getAll() {

        initialize();

        return JSON.parse(
            localStorage.getItem(STORAGE_KEY)
        ) || [];

    }


    function saveAll(records) {

        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify(records)
        );

    }


    function create(record) {

        const records = getAll();

        const maxId =
            records.length
                ? Math.max(...records.map(item => item.id))
                : 0;

        record.id = maxId + 1;

        records.push(record);

        saveAll(records);

        return record;

    }


    function update(id, data) {

        const records = getAll();

        const index =
            records.findIndex(item =>
                String(item.id) === String(id)
            );

        if (index < 0) {
            return false;
        }

        records[index] = {
            ...records[index],
            ...data
        };

        saveAll(records);

        return true;

    }


    function remove(id) {

        const records =
            getAll().filter(item =>
                String(item.id) !== String(id)
            );

        saveAll(records);

    }


    window.FollowupStore = {
        getAll,
        create,
        update,
        remove
    };


    initialize();

})();