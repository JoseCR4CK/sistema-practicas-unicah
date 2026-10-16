@extends('layouts.app')

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

        <div style="max-width: 520px;">

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

                <i class="bi bi-grid"></i>

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
                La interfaz de este módulo forma parte del
                prototipo navegable del sistema. Sus operaciones,
                integración con base de datos y funcionalidades
                completas serán incorporadas durante las siguientes
                etapas de desarrollo.
            </p>

        </div>

    </div>

</section>

@endsection