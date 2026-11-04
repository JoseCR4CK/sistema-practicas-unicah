(() => {

    const STORAGE_KEY =
        'unicah_coord_practicas_v1';


    const initialPractices = [

        {
            id: 1,
            student_id: 1,

            estudiante: 'Andrea Martínez',
            cuenta: '0801-2022-00458',
            carrera: 'Ingeniería en Ciencias de la Computación',

            empresa: 'Grupo Tecnológico S.A.',
            responsable: 'Ing. Roberto Martínez',

            docente: 'Msc. Carlos Martínez',

            inicio: '2026-09-01',
            finalizacion: '2026-11-30',

            etapa: 'Práctica',
            progreso: 58,
            estado: 'Activa',

            visita_estado: 'Pendiente',
            monografia_estado: 'Pendiente',

            observaciones:
                'Práctica profesional actualmente en desarrollo.'
        },


        {
            id: 2,
            student_id: 2,

            estudiante: 'Carlos Rodríguez',
            cuenta: '0801-2021-00231',
            carrera: 'Ingeniería Industrial',

            empresa: 'Corporación Industrial HN',
            responsable: 'Lic. Ana López',

            docente: 'Ing. Ana Rodríguez',

            inicio: '2026-09-15',
            finalizacion: '2026-12-15',

            etapa: 'Documentación',
            progreso: 32,
            estado: 'Activa',

            visita_estado: 'No habilitada',
            monografia_estado: 'Pendiente',

            observaciones:
                'Proceso activo con documentación en seguimiento.'
        },


        {
            id: 3,
            student_id: 3,

            estudiante: 'María López',
            cuenta: '0801-2022-00671',
            carrera: 'Administración de Empresas',

            empresa: 'Inversiones del Valle S. de R.L.',
            responsable: 'Lic. José García',

            docente: 'Msc. Carlos Martínez',

            inicio: '2026-08-20',
            finalizacion: '2026-11-20',

            etapa: 'Monografía',
            progreso: 79,
            estado: 'Activa',

            visita_estado: 'Realizada',
            monografia_estado: 'En revisión',

            observaciones:
                'Visita realizada. Actualmente continúa el proceso de monografía.'
        },


        {
            id: 4,
            student_id: 4,

            estudiante: 'Luis Hernández',
            cuenta: '0801-2022-00714',
            carrera: 'Ingeniería Industrial',

            empresa: 'Grupo Tecnológico S.A.',
            responsable: 'Ing. Roberto Martínez',

            docente: 'Ing. María Fernández',

            inicio: '2026-09-08',
            finalizacion: '2026-12-08',

            etapa: 'Práctica',
            progreso: 45,
            estado: 'Activa',

            visita_estado: 'Pendiente',
            monografia_estado: 'Pendiente',

            observaciones:
                'Práctica profesional en desarrollo.'
        }

    ];


    function initialize() {

        if (!localStorage.getItem(STORAGE_KEY)) {

            localStorage.setItem(
                STORAGE_KEY,
                JSON.stringify(initialPractices)
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


    function saveAll(practices) {

        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify(practices)
        );

    }


    function savePractice(practice) {

        const practices =
            getAll();


        const index =
            practices.findIndex(item =>
                String(item.id) === String(practice.id)
            );


        if (index < 0) {
            return false;
        }


        practices[index] =
            practice;


        saveAll(practices);

        return true;

    }


    window.PracticeStore = {
        getAll,
        getById,
        saveAll,
        savePractice
    };


    initialize();

})();