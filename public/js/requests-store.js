(() => {

    const STORAGE_KEY =
        'unicah_coord_solicitudes_v1';


    const initialRequests = [

        {
            id: 1,

            estudiante: 'Daniela Hernández',
            cuenta: '0801-2022-00521',
            correo: 'dhernandez@unicah.edu',
            carrera: 'Ingeniería en Ciencias de la Computación',

            empresa: 'Tech Solutions Honduras',

            fecha_solicitud: '2026-10-22',
            fecha_inicio_propuesta: '2026-11-02',

            estado: 'Pendiente',

            observacion_estudiante:
                'Solicitud para iniciar proceso de práctica profesional.',

            motivo_rechazo: null,

            revisado_por: null,
            fecha_revision: null,

            historial: [
                {
                    tipo: 'Creación',
                    descripcion:
                        'Solicitud enviada por el estudiante.',
                    fecha: '2026-10-22T14:20:00'
                }
            ]
        },


        {
            id: 2,

            estudiante: 'Kevin Martínez',
            cuenta: '0801-2022-00387',
            correo: 'kmartinez@unicah.edu',
            carrera: 'Ingeniería Industrial',

            empresa: 'Industria del Oriente S.A.',

            fecha_solicitud: '2026-10-23',
            fecha_inicio_propuesta: '2026-11-03',

            estado: 'Pendiente',

            observacion_estudiante:
                'Solicitud de autorización para comenzar práctica profesional.',

            motivo_rechazo: null,

            revisado_por: null,
            fecha_revision: null,

            historial: [
                {
                    tipo: 'Creación',
                    descripcion:
                        'Solicitud enviada por el estudiante.',
                    fecha: '2026-10-23T09:30:00'
                }
            ]
        },


        {
            id: 3,

            estudiante: 'Fernanda López',
            cuenta: '0801-2021-00614',
            correo: 'flopez@unicah.edu',
            carrera: 'Administración de Empresas',

            empresa: 'Servicios Corporativos del Valle',

            fecha_solicitud: '2026-10-20',
            fecha_inicio_propuesta: '2026-11-01',

            estado: 'Aprobada',

            observacion_estudiante:
                'Solicitud de práctica profesional.',

            motivo_rechazo: null,

            revisado_por:
                'Coordinación Académica',

            fecha_revision:
                '2026-10-21T10:20:00',

            historial: [
                {
                    tipo: 'Creación',
                    descripcion:
                        'Solicitud enviada por el estudiante.',
                    fecha: '2026-10-20T11:15:00'
                },

                {
                    tipo: 'Aprobación',
                    descripcion:
                        'Solicitud aprobada por Coordinación Académica.',
                    fecha: '2026-10-21T10:20:00'
                }
            ]
        },


        {
            id: 4,

            estudiante: 'José Rivera',
            cuenta: '0801-2022-00198',
            correo: 'jrivera@unicah.edu',
            carrera: 'Ingeniería en Ciencias de la Computación',

            empresa: 'Soluciones Integrales HN',

            fecha_solicitud: '2026-10-24',
            fecha_inicio_propuesta: '2026-11-05',

            estado: 'Pendiente',

            observacion_estudiante:
                'Solicitud para iniciar el proceso correspondiente.',

            motivo_rechazo: null,

            revisado_por: null,
            fecha_revision: null,

            historial: [
                {
                    tipo: 'Creación',
                    descripcion:
                        'Solicitud enviada por el estudiante.',
                    fecha: '2026-10-24T16:40:00'
                }
            ]
        },


        {
            id: 5,

            estudiante: 'Melissa García',
            cuenta: '0801-2021-00723',
            correo: 'mgarcia@unicah.edu',
            carrera: 'Ingeniería Industrial',

            empresa: 'Manufacturas Centroamericanas',

            fecha_solicitud: '2026-10-19',
            fecha_inicio_propuesta: '2026-11-01',

            estado: 'Rechazada',

            observacion_estudiante:
                'Solicitud de autorización de práctica.',

            motivo_rechazo:
                'La información proporcionada en la solicitud requiere corrección.',

            revisado_por:
                'Coordinación Académica',

            fecha_revision:
                '2026-10-20T08:45:00',

            historial: [
                {
                    tipo: 'Creación',
                    descripcion:
                        'Solicitud enviada por el estudiante.',
                    fecha: '2026-10-19T13:10:00'
                },

                {
                    tipo: 'Rechazo',
                    descripcion:
                        'Solicitud rechazada. Se solicitó corrección de información.',
                    fecha: '2026-10-20T08:45:00'
                }
            ]
        }

    ];


    function initialize() {

        if (!localStorage.getItem(STORAGE_KEY)) {

            localStorage.setItem(
                STORAGE_KEY,
                JSON.stringify(initialRequests)
            );

        }

    }


    function getAll() {

        initialize();

        return JSON.parse(
            localStorage.getItem(STORAGE_KEY)
        ) || [];

    }


    function saveAll(requests) {

        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify(requests)
        );

    }


    function getById(id) {

        return getAll().find(request =>
            String(request.id) === String(id)
        ) || null;

    }


    function saveRequest(request) {

        const requests =
            getAll();


        const index =
            requests.findIndex(item =>
                String(item.id) === String(request.id)
            );


        if (index === -1) {
            return false;
        }


        requests[index] =
            request;


        saveAll(requests);

        return true;

    }


    window.RequestStore = {
        getAll,
        getById,
        saveAll,
        saveRequest
    };


    initialize();

})();