@extends('layouts.teacher')

@section('title', 'Mis practicantes | UNICAH')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            DOCENTE DE SEGUIMIENTO
        </span>

        <h1>
            Mis practicantes
        </h1>

        <p>
            Consulta los estudiantes asignados para seguimiento
            durante el período académico.
        </p>

    </div>


    <div class="teacher-period-badge">

        <i class="bi bi-calendar3"></i>

        III Período 2026

    </div>

</div>



<!-- RESUMEN -->

<section class="row g-3 mb-4">

    <div class="col-12 col-md-4">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">

                <i class="bi bi-people"></i>

            </div>


            <div>

                <span>
                    Practicantes asignados
                </span>

                <strong id="assignedStudentsCount">
                    0
                </strong>

            </div>

        </div>

    </div>


    <div class="col-12 col-md-4">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">

                <i class="bi bi-briefcase"></i>

            </div>


            <div>

                <span>
                    Prácticas activas
                </span>

                <strong id="activePracticesCount">
                    0
                </strong>

            </div>

        </div>

    </div>


    <div class="col-12 col-md-4">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">

                <i class="bi bi-calendar-check"></i>

            </div>


            <div>

                <span>
                    Visitas por atender
                </span>

                <strong id="pendingVisitsCount">
                    0
                </strong>

            </div>

        </div>

    </div>

</section>



<!-- PRACTICANTES -->

<section class="content-card">

    <div class="card-header">

        <div>

            <h2>
                Estudiantes asignados
            </h2>

            <p>
                Practicantes bajo tu seguimiento académico.
            </p>

        </div>

    </div>


    <div id="teacherStudentsList"></div>

</section>


@endsection



@push('scripts')



<script>
const teacherPractices = {{ Illuminate\Support\Js::from($practiceData) }};
const teacherVisits = {{ Illuminate\Support\Js::from($visitData) }};
</script>


<script>



/*
|--------------------------------------------------------------------------
| OBTENER ID DEL ESTUDIANTE
|--------------------------------------------------------------------------
*/

function getPracticeStudentId(practice) {

    return (
        practice.student_id ??
        practice.estudiante_id ??
        practice.id
    );

}


/*
|--------------------------------------------------------------------------
| PRÁCTICAS DEL DOCENTE
|--------------------------------------------------------------------------
*/

function getTeacherPractices() { return teacherPractices; }


/*
|--------------------------------------------------------------------------
| VISITA DEL PRACTICANTE
|--------------------------------------------------------------------------
*/

function getStudentVisit(studentId) { return teacherVisits.find(visit => String(visit.student_id) === String(studentId)) || null; }


/*
|--------------------------------------------------------------------------
| ESTADO DE VISITA
|--------------------------------------------------------------------------
*/

function getVisitStatus(visit) {

    if (!visit) {
        return 'Sin registro';
    }


    if (visit.estado === 'Realizada') {
        return 'Realizada';
    }


    if (visit.estado === 'Programada') {
        return 'Programada';
    }


    if (visit.estado === 'Habilitada') {
        return 'Habilitada';
    }


    if (visit.estado === 'No habilitada') {
        return 'No habilitada';
    }


    if (visit.inicio_practica) {

        const start =
            new Date(
                visit.inicio_practica +
                'T00:00:00'
            );


        const today =
            new Date();

        today.setHours(
            0,
            0,
            0,
            0
        );


        const day =
            Math.floor(
                (today - start) /
                86400000
            ) + 1;


        return day >= 45
            ? 'Habilitada'
            : 'No habilitada';

    }


    return 'Sin registro';

}


/*
|--------------------------------------------------------------------------
| CLASE VISUAL DEL ESTADO
|--------------------------------------------------------------------------
*/

function getVisitStatusClass(status) {

    if (status === 'Realizada') {
        return 'completed';
    }

    if (status === 'Habilitada') {
        return 'enabled';
    }

    if (status === 'Programada') {
        return 'scheduled';
    }

    return 'neutral';

}


/*
|--------------------------------------------------------------------------
| ESTADÍSTICAS
|--------------------------------------------------------------------------
*/

function renderTeacherStudentStats(practices) {

    const assigned =
        practices.length;


    const active =
        practices.filter(practice =>

            practice.status === 'ACTIVE'

        ).length;


    const visitsToAttend =
        practices.filter(practice => {

            const studentId =
                getPracticeStudentId(
                    practice
                );


            const visit =
                getStudentVisit(
                    studentId
                );


            const status =
                getVisitStatus(
                    visit
                );


            return (
                status === 'Habilitada' ||
                status === 'Programada'
            );

        }).length;


    document.getElementById(
        'assignedStudentsCount'
    ).textContent =
        assigned;


    document.getElementById(
        'activePracticesCount'
    ).textContent =
        active;


    document.getElementById(
        'pendingVisitsCount'
    ).textContent =
        visitsToAttend;

}


/*
|--------------------------------------------------------------------------
| RENDERIZAR PRACTICANTES
|--------------------------------------------------------------------------
*/

function renderTeacherStudents() {

    const practices =
        getTeacherPractices();


    renderTeacherStudentStats(
        practices
    );


    const container =
        document.getElementById(
            'teacherStudentsList'
        );


    if (!practices.length) {

        container.innerHTML = `

            <div class="teacher-load-empty">

                <i class="bi bi-people"></i>

                <strong>
                    No tienes practicantes asignados
                </strong>

                <span>
                    Actualmente no existen estudiantes
                    asociados a tu usuario.
                </span>

            </div>

        `;

        return;

    }


    container.innerHTML =
        practices.map(practice => {

            const studentId =
                getPracticeStudentId(
                    practice
                );


            const visit =
                getStudentVisit(
                    studentId
                );


            const visitStatus =
                getVisitStatus(
                    visit
                );


            const initials =
                practice.estudiante
                    .split(' ')
                    .filter(Boolean)
                    .slice(0, 2)
                    .map(word =>
                        word.charAt(0)
                    )
                    .join('')
                    .toUpperCase();


            const progress =
                Number(
                    practice.progreso ?? 0
                );


            return `

                <article class="teacher-student-row">

                    <div class="teacher-student-main">

                        <div class="teacher-student-avatar">
                            ${initials}
                        </div>


                        <div>

                            <span>
                                ESTUDIANTE
                            </span>

                            <strong>
                                ${practice.estudiante}
                            </strong>

                            <small>
                                ${practice.cuenta ?? ''}
                            </small>

                        </div>

                    </div>


                    <div>

                        <span class="teacher-student-label">
                            Empresa
                        </span>

                        <strong class="teacher-student-value">
                            ${practice.empresa}
                        </strong>

                    </div>


                    <div>

                        <span class="teacher-student-label">
                            Etapa actual
                        </span>

                        <strong class="teacher-student-value">
                            ${practice.etapa}
                        </strong>

                    </div>


                    <div>

                        <span class="teacher-student-label">
                            Visita
                        </span>

                        <span
                            class="teacher-student-visit
                            ${getVisitStatusClass(
                                visitStatus
                            )}"
                        >
                            ${visitStatus}
                        </span>

                    </div>


                    <div class="teacher-student-progress">

                        <span>
                            Progreso
                        </span>

                        <strong>
                            ${progress}%
                        </strong>

                        <div>

                            <span
                                style="width: ${progress}%"
                            ></span>

                        </div>

                    </div>


                    <a
                        href="/docente/practicantes/${studentId}"
                        class="coord-teacher-view"
                    >

                        <i class="bi bi-eye"></i>

                        Ver seguimiento

                    </a>

                </article>

            `;

        }).join('');

}


renderTeacherStudents();

</script>

@endpush