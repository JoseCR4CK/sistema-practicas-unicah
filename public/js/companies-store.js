(() => {

    const STORAGE_KEY = 'unicah_coord_empresas_v1';


    const initialCompanies = [

        {
            id: 1,

            nombre_oficial: 'Grupo Tecnológico S.A.',
            nombre_comercial: 'GT Solutions',
            rtn: '08019000000000',

            sector: 'Tecnología',

            direccion: 'Barrio El Centro',
            municipio: 'Danlí',
            departamento: 'El Paraíso',

            telefono: '2763-0000',
            correo: 'info@empresa.demo',

            estado: 'Activa',

            responsables: [
                {
                    id: 101,
                    nombre: 'Ing. Roberto Martínez',
                    cargo: 'Jefe inmediato',
                    correo: 'responsable@empresa.demo',
                    telefono: '9999-0001',
                    estado: 'Activo'
                }
            ],

            practicantes: [
                {
                    id: 1,
                    nombre: 'Andrea Martínez',
                    cuenta: '0801-2022-00458',
                    carrera: 'Ingeniería en Ciencias de la Computación',
                    etapa: 'Práctica',
                    progreso: 58,
                    responsable_id: 101
                },

                {
                    id: 4,
                    nombre: 'Luis Hernández',
                    cuenta: '0801-2022-00714',
                    carrera: 'Ingeniería Industrial',
                    etapa: 'Práctica',
                    progreso: 45,
                    responsable_id: 101
                }
            ]
        },


        {
            id: 2,

            nombre_oficial: 'Corporación Industrial HN',
            nombre_comercial: 'CIH',
            rtn: '08019000000001',

            sector: 'Industrial',

            direccion: 'Zona Industrial',
            municipio: 'Danlí',
            departamento: 'El Paraíso',

            telefono: '2763-0001',
            correo: 'info@cih.demo',

            estado: 'Activa',

            responsables: [
                {
                    id: 201,
                    nombre: 'Lic. Ana López',
                    cargo: 'Supervisora de prácticas',
                    correo: 'supervision@empresa.demo',
                    telefono: '9999-0002',
                    estado: 'Activo'
                }
            ],

            practicantes: [
                {
                    id: 2,
                    nombre: 'Carlos Rodríguez',
                    cuenta: '0801-2021-00231',
                    carrera: 'Ingeniería Industrial',
                    etapa: 'Documentación',
                    progreso: 32,
                    responsable_id: 201
                }
            ]
        },


        {
            id: 3,

            nombre_oficial: 'Inversiones del Valle S. de R.L.',
            nombre_comercial: 'Inversiones del Valle',
            rtn: '08019000000002',

            sector: 'Servicios',

            direccion: 'Barrio La Reforma',
            municipio: 'Danlí',
            departamento: 'El Paraíso',

            telefono: '2763-0002',
            correo: 'info@inversiones.demo',

            estado: 'Activa',

            responsables: [
                {
                    id: 301,
                    nombre: 'Lic. José García',
                    cargo: 'Administrador',
                    correo: 'administracion@empresa.demo',
                    telefono: '9999-0003',
                    estado: 'Activo'
                }
            ],

            practicantes: [
                {
                    id: 3,
                    nombre: 'María López',
                    cuenta: '0801-2022-00671',
                    carrera: 'Administración de Empresas',
                    etapa: 'Monografía',
                    progreso: 67,
                    responsable_id: 301
                }
            ]
        }

    ];


    function initialize() {

        const current =
            localStorage.getItem(STORAGE_KEY);


        if (!current) {

            localStorage.setItem(
                STORAGE_KEY,
                JSON.stringify(initialCompanies)
            );

        }

    }


    function getAll() {

        initialize();

        return JSON.parse(
            localStorage.getItem(STORAGE_KEY)
        ) || [];

    }


    function saveAll(companies) {

        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify(companies)
        );

    }


    function getById(id) {

        return getAll().find(company =>
            String(company.id) === String(id)
        ) || null;

    }


    function saveCompany(company) {

        const companies =
            getAll();


        const index =
            companies.findIndex(item =>
                String(item.id) === String(company.id)
            );


        if (index >= 0) {

            companies[index] = company;

        } else {

            companies.push(company);

        }


        saveAll(companies);

        return company;

    }


    function createId() {

        return Date.now();

    }


    window.CompanyStore = {

        getAll,
        getById,
        saveAll,
        saveCompany,
        createId

    };


    initialize();

})();