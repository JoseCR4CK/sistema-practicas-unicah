(() => {

    const STORAGE_KEY =
        'unicah_coord_documentos_v1';


    const initialDocuments = [

        {
            id: 1,

            estudiante: 'Andrea Martínez',
            cuenta: '0801-2022-00458',
            carrera: 'Ingeniería en Ciencias de la Computación',

            nombre: 'Documento inicial — ejemplo',
            categoria: 'Inicial',

            estado: 'Aprobado',

            version_actual: 2,

            archivo:
                'documento_inicial_andrea_v2.pdf',

            fecha_carga:
                '2026-09-01T10:30:00',

            observacion_actual:
                'Documento validado correctamente.',

            versiones: [

                {
                    version: 1,

                    archivo:
                        'documento_inicial_andrea_v1.pdf',

                    fecha:
                        '2026-08-29T09:15:00',

                    estado:
                        'Corrección requerida',

                    observacion:
                        'El documento requería una corrección antes de ser aprobado.'
                },

                {
                    version: 2,

                    archivo:
                        'documento_inicial_andrea_v2.pdf',

                    fecha:
                        '2026-09-01T10:30:00',

                    estado:
                        'Aprobado',

                    observacion:
                        'Corrección recibida y validada.'
                }

            ]
        },


        {
            id: 2,

            estudiante: 'Carlos Rodríguez',
            cuenta: '0801-2021-00231',
            carrera: 'Ingeniería Industrial',

            nombre: 'Documento inicial — ejemplo',
            categoria: 'Inicial',

            estado: 'Pendiente',

            version_actual: 1,

            archivo:
                'documento_inicial_carlos_v1.pdf',

            fecha_carga:
                '2026-10-27T11:25:00',

            observacion_actual: null,

            versiones: [

                {
                    version: 1,

                    archivo:
                        'documento_inicial_carlos_v1.pdf',

                    fecha:
                        '2026-10-27T11:25:00',

                    estado:
                        'Pendiente',

                    observacion: null
                }

            ]
        },


        {
            id: 3,

            estudiante: 'María López',
            cuenta: '0801-2022-00671',
            carrera: 'Administración de Empresas',

            nombre: 'Documento del proceso — ejemplo',
            categoria: 'Proceso',

            estado: 'Corrección requerida',

            version_actual: 1,

            archivo:
                'documento_proceso_maria_v1.pdf',

            fecha_carga:
                '2026-10-25T15:40:00',

            observacion_actual:
                'La información presentada requiere corrección.',

            versiones: [

                {
                    version: 1,

                    archivo:
                        'documento_proceso_maria_v1.pdf',

                    fecha:
                        '2026-10-25T15:40:00',

                    estado:
                        'Corrección requerida',

                    observacion:
                        'La información presentada requiere corrección.'
                }

            ]
        },


        {
            id: 4,

            estudiante: 'Luis Hernández',
            cuenta: '0801-2022-00714',
            carrera: 'Ingeniería Industrial',

            nombre: 'Documento del proceso — ejemplo',
            categoria: 'Proceso',

            estado: 'Pendiente',

            version_actual: 1,

            archivo:
                'documento_proceso_luis_v1.pdf',

            fecha_carga:
                '2026-10-28T08:20:00',

            observacion_actual: null,

            versiones: [

                {
                    version: 1,

                    archivo:
                        'documento_proceso_luis_v1.pdf',

                    fecha:
                        '2026-10-28T08:20:00',

                    estado:
                        'Pendiente',

                    observacion: null
                }

            ]
        }

    ];


    function initialize() {

        if (!localStorage.getItem(STORAGE_KEY)) {

            localStorage.setItem(
                STORAGE_KEY,
                JSON.stringify(initialDocuments)
            );

        }

    }


    function getAll() {

        initialize();

        return JSON.parse(
            localStorage.getItem(STORAGE_KEY)
        ) || [];

    }


    function saveAll(documents) {

        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify(documents)
        );

    }


    function getById(id) {

        return getAll().find(document =>
            String(document.id) === String(id)
        ) || null;

    }


    function saveDocument(document) {

        const documents =
            getAll();


        const index =
            documents.findIndex(item =>
                String(item.id) === String(document.id)
            );


        if (index < 0) {
            return false;
        }


        documents[index] =
            document;


        saveAll(documents);

        return true;

    }


    window.DocumentStore = {
        getAll,
        getById,
        saveAll,
        saveDocument
    };


    initialize();

})();