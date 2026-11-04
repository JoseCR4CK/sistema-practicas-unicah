(() => {

    const STORAGE_KEY =
        'unicah_coord_visitas_v1';


    const initialVisits = [

        {
            id: 1,
            practice_id: 1,
            student_id: 1,

            estudiante: 'Andrea Martínez',
            cuenta: '0801-2022-00458',

            empresa: 'Grupo Tecnológico S.A.',
            responsable: 'Ing. Roberto Martínez',

            docente: 'Msc. Carlos Martínez',

            inicio_practica: '2026-09-01',

            estado: 'Habilitada',

            fecha_programada: null,
            fecha_realizada: null,

            observacion: null
        },


        {
            id: 2,
            practice_id: 2,
            student_id: 2,

            estudiante: 'Carlos Rodríguez',
            cuenta: '0801-2021-00231',

            empresa: 'Corporación Industrial HN',
            responsable: 'Lic. Ana López',

            docente: 'Ing. Ana Rodríguez',

            inicio_practica: '2026-09-15',

            estado: 'No habilitada',

            fecha_programada: null,
            fecha_realizada: null,

            observacion: null
        },


        {
            id: 3,
            practice_id: 3,
            student_id: 3,

            estudiante: 'María López',
            cuenta: '0801-2022-00671',

            empresa: 'Inversiones del Valle S. de R.L.',
            responsable: 'Lic. José García',

            docente: 'Msc. Carlos Martínez',

            inicio_practica: '2026-08-20',

            estado: 'Realizada',

            fecha_programada: '2026-10-10',
            fecha_realizada: '2026-10-10',

            observacion:
                'Visita registrada como realizada.'
        },


        {
            id: 4,
            practice_id: 4,
            student_id: 4,

            estudiante: 'Luis Hernández',
            cuenta: '0801-2022-00714',

            empresa: 'Grupo Tecnológico S.A.',
            responsable: 'Ing. Roberto Martínez',

            docente: 'Ing. María Fernández',

            inicio_practica: '2026-09-08',

            estado: 'Habilitada',

            fecha_programada: null,
            fecha_realizada: null,

            observacion: null
        }

    ];


    function initialize() {

        if (!localStorage.getItem(STORAGE_KEY)) {

            localStorage.setItem(
                STORAGE_KEY,
                JSON.stringify(initialVisits)
            );

        }

    }


    function getAll() {

        initialize();

        return JSON.parse(
            localStorage.getItem(STORAGE_KEY)
        ) || [];

    }


    function getById(id) {

        return getAll().find(item =>
            String(item.id) === String(id)
        ) || null;

    }


    function saveAll(visits) {

        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify(visits)
        );

    }


    function saveVisit(visit) {

        const visits =
            getAll();


        const index =
            visits.findIndex(item =>
                String(item.id) === String(visit.id)
            );


        if (index < 0) {
            return false;
        }


        visits[index] = visit;

        saveAll(visits);

        return true;

    }


    window.VisitStore = {
        getAll,
        getById,
        saveAll,
        saveVisit
    };


    initialize();

})();