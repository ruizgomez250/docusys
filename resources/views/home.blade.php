@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-12">
            <h1 class="m-0 custom-heading">Sistema de Trazabilidad Documental</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalDocumentos ?? '0' }}</h3>
                    <p>Documentos Registrados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <a href="{{ route('mesaentrada.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $documentosHoy ?? '0' }}</h3>
                    <p>Documentos Hoy</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <a href="{{ route('mesaentrada.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendientes ?? '0' }}</h3>
                    <p>Pendientes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('recepciondoc') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $finalizados ?? '0' }}</h3>
                    <p>Finalizados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('reenviado') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Acerca del Sistema</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-primary">
                                <i class="fas fa-landmark mr-2"></i>Período Legislativo 2023 - 2024
                            </h6>
                            <div class="bg-light p-3 rounded mb-3">
                                <p class="mb-1"><strong>Dr. Raúl Luis Latorre Martínez</strong></p>
                                <p class="mb-0 text-muted">Presidente</p>
                            </div>

                            <h6 class="font-weight-bold text-primary">
                                <i class="fas fa-clipboard-list mr-2"></i>Solicitud de Proyecto
                            </h6>
                            <div class="bg-light p-3 rounded mb-3">
                                <p class="mb-1"><strong>Secretario General Administrativo:</strong> Lic. Carlos Samudio</p>
                                <p class="mb-0"><strong>Secretario Administrativo:</strong> Abg. Julio Cesar Cabrera Guerrero</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-primary">
                                <i class="fas fa-code mr-2"></i>Construcción del Proyecto
                            </h6>
                            <div class="bg-light p-3 rounded mb-3">
                                <p class="mb-1"><strong>Dirección de Informática</strong></p>
                                <p class="mb-1"><strong>Director:</strong> Nahuel Ojeda</p>
                                <p class="mb-0 font-weight-bold">Desarrolladores:</p>
                                <p class="mb-1">Prog. Jorge Ruíz Gómez</p>
                                <p class="mb-1">Ing. Narciso Ramon Añazco</p>
                            </div>

                            <h6 class="font-weight-bold text-primary">
                                <i class="fas fa-chalkboard-teacher mr-2"></i>Capacitación
                            </h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">Lic. Laura Susana Martínez</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
