@extends('layouts.teacher')

@section('title', $titulo . ' | UNICAH')

@section('content')

<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            SEGUIMIENTO
        </span>

        <h1>
            {{ $titulo }}
        </h1>

        <p>
            {{ $descripcion }}
        </p>

    </div>

</div>


<section class="content-card">

    <div style="padding: 55px 25px; text-align: center;">

        <div
            style="
                width: 64px;
                height: 64px;
                margin: 0 auto 15px;
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(6,69,127,.10);
                color: var(--unicah-blue);
                font-size: 1.4rem;
            "
        >

            <i class="bi {{ $icono }}"></i>

        </div>


        <h2
            style="
                margin: 0;
                color: var(--unicah-blue-dark);
                font-size: 1rem;
            "
        >
            {{ $titulo }}
        </h2>


        <p
            style="
                max-width: 480px;
                margin: 8px auto 0;
                color: var(--text-gray);
                font-size: .65rem;
                line-height: 1.6;
            "
        >
            Este módulo forma parte del seguimiento de prácticas profesionales.
            Su información funcional será integrada con los registros
            correspondientes del sistema.
        </p>

    </div>

</section>

@endsection