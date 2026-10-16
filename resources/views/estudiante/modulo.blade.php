@extends('layouts.student')

@section('title', $titulo . ' | UNICAH')

@section('content')

<div class="page-header">

    <div>

        <h1>
            {{ $titulo }}
        </h1>

        <p>
            {{ $descripcion }}
        </p>

    </div>

</div>


<section class="content-card">

    <div
        style="
            min-height: 340px;
            padding: 50px 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        "
    >

        <div style="max-width: 500px;">

            <div
                style="
                    width: 64px;
                    height: 64px;
                    margin: 0 auto 18px;
                    border-radius: 16px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: rgba(6,69,127,.10);
                    color: var(--unicah-blue);
                    font-size: 1.5rem;
                "
            >

                <i class="bi bi-person-workspace"></i>

            </div>


            <h2
                style="
                    margin: 0;
                    color: var(--unicah-blue-dark);
                    font-size: 1.1rem;
                "
            >
                {{ $titulo }}
            </h2>


            <p
                style="
                    margin: 10px 0 0;
                    color: var(--text-gray);
                    font-size: .74rem;
                    line-height: 1.6;
                "
            >
                Esta sección forma parte de la estructura
                del prototipo. Su integración completa
                será realizada en las siguientes etapas
                del proyecto.
            </p>

        </div>

    </div>

</section>

@endsection