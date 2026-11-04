<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Informe de estudiantes | UNICAH
    </title>


    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
    >


    <style>

        body {
            margin: 0;
            background: #eef2f6;
            color: #16334d;
            font-family: Arial, Helvetica, sans-serif;
        }


        .report-toolbar {
            max-width: 1100px;
            margin: 22px auto 14px;
            padding: 0 15px;

            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }


        .report-toolbar a,
        .report-toolbar button {
            border: 0;
            border-radius: 7px;
            padding: 9px 14px;

            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }


        .report-back {
            background: white;
            color: #06457f;
            border: 1px solid #d9e1e8 !important;
        }


        .report-print {
            background: #06457f;
            color: white;
        }


        .report-page {
            width: 100%;
            max-width: 1100px;

            margin: 0 auto 30px;
            padding: 45px;

            background: white;

            box-shadow:
                0 5px 25px rgba(0,0,0,.06);
        }


        .report-header {
            padding-bottom: 22px;
            border-bottom: 3px solid #06457f;

            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 25px;
        }


        .report-logo {
            width: 190px;
            max-width: 100%;
        }


        .report-header-text {
            text-align: right;
        }


        .report-header-text h1 {
            margin: 0;

            color: #06457f;
            font-size: 21px;
            font-weight: 800;
        }


        .report-header-text p {
            margin: 6px 0 0;

            color: #66798b;
            font-size: 12px;
        }


        .report-meta {
            margin: 25px 0;

            padding: 15px;

            border: 1px solid #dce3e9;
            border-radius: 8px;

            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;

            background: #f8fafc;
        }


        .report-meta span {
            display: block;

            margin-bottom: 3px;

            color: #718292;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
        }


        .report-meta strong {
            color: #16334d;
            font-size: 12px;
        }


        .report-summary {
            margin-bottom: 25px;

            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }


        .report-stat {
            padding: 14px;

            border: 1px solid #dde4ea;
            border-radius: 8px;

            background: white;
        }


        .report-stat span {
            display: block;

            color: #718292;
            font-size: 10px;
        }


        .report-stat strong {
            display: block;

            margin-top: 4px;

            color: #06457f;
            font-size: 20px;
        }


        .report-section-title {
            margin: 25px 0 12px;

            color: #06457f;
            font-size: 15px;
            font-weight: 800;
        }


        .report-table {
            width: 100%;

            border-collapse: collapse;

            font-size: 10.5px;
        }


        .report-table th {
            padding: 9px 8px;

            border: 1px solid #d9e1e8;

            background: #f2f6f9;

            color: #486175;

            text-align: left;
            text-transform: uppercase;
            font-size: 9px;
        }


        .report-table td {
            padding: 10px 8px;

            border: 1px solid #d9e1e8;

            vertical-align: top;
        }


        .report-table strong {
            color: #0b355b;
        }


        .report-empty {
            padding: 35px;

            border: 1px dashed #cad5df;
            border-radius: 8px;

            color: #718292;
            text-align: center;
            font-size: 12px;
        }


        .report-footer {
            margin-top: 35px;
            padding-top: 15px;

            border-top: 1px solid #dde4ea;

            display: flex;
            justify-content: space-between;

            color: #778999;
            font-size: 9px;
        }


        @media print {

            body {
                background: white;
            }


            .report-toolbar {
                display: none !important;
            }


            .report-page {
                max-width: none;

                margin: 0;
                padding: 20px;

                box-shadow: none;
            }


            @page {
                size: letter landscape;
                margin: 10mm;
            }

        }

    </style>

</head>


<body>


<div class="report-toolbar">

    <a
        href="{{ route('coordinacion.estudiantes') }}"
        class="report-back"
    >
        ← Volver a estudiantes
    </a>


    <button
        type="button"
        class="report-print"
        onclick="window.print()"
    >
        Imprimir / Guardar PDF
    </button>

</div>



<main class="report-page">


    <header class="report-header">

        <img
            src="{{ asset('assets/img/logo-unicah.png') }}"
            alt="UNICAH"
            class="report-logo"
        >


        <div class="report-header-text">

            <h1>
                Informe de Prácticas Profesionales
            </h1>

            <p>
                Sistema de Gestión y Seguimiento de Prácticas Profesionales
            </p>

        </div>

    </header>



    <section class="report-meta">

        <div>

            <span>Período</span>

            <strong id="metaPeriod">
                III Período 2026
            </strong>

        </div>


        <div>

            <span>Tipo de informe</span>

            <strong id="metaType">
                Todos los estudiantes
            </strong>

        </div>


        <div>

            <span>Fecha de generación</span>

            <strong id="metaDate"></strong>

        </div>

    </section>



    <section class="report-summary">

        <div class="report-stat">

            <span>
                Registros incluidos
            </span>

            <strong id="summaryTotal">
                0
            </strong>

        </div>


        <div class="report-stat">

            <span>
                Prácticas activas
            </span>

            <strong id="summaryActive">
                0
            </strong>

        </div>


        <div class="report-stat">

            <span>
                En documentación
            </span>

            <strong id="summaryDocuments">
                0
            </strong>

        </div>


        <div class="report-stat">

            <span>
                En visita
            </span>

            <strong id="summaryVisits">
                0
            </strong>

        </div>

    </section>



    <h2 class="report-section-title">
        Detalle de estudiantes
    </h2>


    <div id="reportTableContainer"></div>



    <footer class="report-footer">

        <span>
            Universidad Católica de Honduras
        </span>

        <span>
            Coordinación Académica · Prácticas Profesionales
        </span>

    </footer>

</main>



<script>

/*
|--------------------------------------------------------------------------
| DATOS TEMPORALES
|--------------------------------------------------------------------------
|
| Serán reemplazados por consultas a la base de datos.
|
*/

const students = [

    {
        account: '0801-2022-00458',
        name: 'Andrea Martínez',
        career: 'Ingeniería en Ciencias de la Computación',
        company: 'Grupo Tecnológico S.A.',
        stage: 'Práctica',
        status: 'Activa',
        progress: 58,
        period: 'III-2026'
    },

    {
        account: '0801-2021-00231',
        name: 'Carlos Rodríguez',
        career: 'Ingeniería Industrial',
        company: 'Industrias del Norte',
        stage: 'Documentación',
        status: 'Activa',
        progress: 32,
        period: 'III-2026'
    },

    {
        account: '0801-2022-00671',
        name: 'María López',
        career: 'Ingeniería en Ciencias de la Computación',
        company: 'Soluciones Digitales HN',
        stage: 'Visita',
        status: 'Activa',
        progress: 67,
        period: 'III-2026'
    },

    {
        account: '0801-2021-00882',
        name: 'José Hernández',
        career: 'Ingeniería Industrial',
        company: 'Grupo Industrial Centroamericano',
        stage: 'Monografía',
        status: 'Activa',
        progress: 79,
        period: 'III-2026'
    }

];


const params =
    new URLSearchParams(
        window.location.search
    );


const type =
    params.get('tipo') || 'todos';

const period =
    params.get('periodo') || 'III-2026';

const stage =
    params.get('etapa') || '';

const career =
    params.get('carrera') || '';

const company =
    params.get('empresa') || '';


let filtered =
    students.filter(student =>
        student.period === period
    );


if (type === 'activas') {

    filtered =
        filtered.filter(student =>
            student.status === 'Activa'
        );

}


if (type === 'etapa' && stage) {

    filtered =
        filtered.filter(student =>
            student.stage === stage
        );

}


if (type === 'carrera' && career) {

    filtered =
        filtered.filter(student =>
            student.career === career
        );

}


if (type === 'empresa' && company) {

    filtered =
        filtered.filter(student =>
            student.company === company
        );

}


const typeNames = {

    todos:
        'Todos los estudiantes',

    activas:
        'Prácticas activas',

    etapa:
        stage
            ? `Etapa: ${stage}`
            : 'Por etapa',

    carrera:
        career
            ? `Carrera: ${career}`
            : 'Por carrera',

    empresa:
        company
            ? `Empresa: ${company}`
            : 'Por empresa'

};


document.getElementById('metaType')
    .textContent =
        typeNames[type] || 'Informe';


document.getElementById('metaPeriod')
    .textContent =
        period === 'III-2026'
            ? 'III Período 2026'
            : period === 'II-2026'
                ? 'II Período 2026'
                : 'I Período 2026';


document.getElementById('metaDate')
    .textContent =
        new Date().toLocaleDateString(
            'es-HN',
            {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            }
        );


document.getElementById('summaryTotal')
    .textContent =
        filtered.length;


document.getElementById('summaryActive')
    .textContent =
        filtered.filter(student =>
            student.status === 'Activa'
        ).length;


document.getElementById('summaryDocuments')
    .textContent =
        filtered.filter(student =>
            student.stage === 'Documentación'
        ).length;


document.getElementById('summaryVisits')
    .textContent =
        filtered.filter(student =>
            student.stage === 'Visita'
        ).length;



const container =
    document.getElementById(
        'reportTableContainer'
    );


if (filtered.length === 0) {

    container.innerHTML = `

        <div class="report-empty">
            No existen estudiantes que coincidan
            con los criterios seleccionados.
        </div>

    `;

} else {

    container.innerHTML = `

        <table class="report-table">

            <thead>

                <tr>

                    <th>Cuenta</th>
                    <th>Estudiante</th>
                    <th>Carrera</th>
                    <th>Empresa</th>
                    <th>Etapa</th>
                    <th>Estado</th>
                    <th>Progreso</th>

                </tr>

            </thead>


            <tbody>

                ${filtered.map(student => `

                    <tr>

                        <td>
                            ${student.account}
                        </td>

                        <td>
                            <strong>
                                ${student.name}
                            </strong>
                        </td>

                        <td>
                            ${student.career}
                        </td>

                        <td>
                            ${student.company}
                        </td>

                        <td>
                            ${student.stage}
                        </td>

                        <td>
                            ${student.status}
                        </td>

                        <td>
                            ${student.progress}%
                        </td>

                    </tr>

                `).join('')}

            </tbody>

        </table>

    `;

}

</script>


</body>

</html>