@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1 class="m-0 custom-heading">Fechas de Recepción</h1>
        </div>
        <div class="col-6">
            <a href="{{ route('proyectos-en-estudio.index') }}" class="btn btn-secondary" style="float: right;">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('plugins.Sweetalert2', true)

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt"></i> Seleccione una fecha para generar el PDF
                    </h3>
                </div>
                <div class="card-body">
                    @if($fechas->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No hay fechas de recepción disponibles. Primero debe crear proyectos en estudio.
                        </div>
                    @else
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 60%">Fecha de Recepción</th>
                                    <th style="width: 20%">Proyectos</th>
                                    <th style="width: 20%">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fechas as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ strtoupper($item->fecha_texto) }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-primary">{{ $item->cantidad }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('proyectos-en-estudio.fechas-pdf', $item->fecha_recepcion) }}"
                                               class="btn btn-danger btn-sm" target="_blank">
                                                <i class="fas fa-file-pdf"></i> Descargar PDF
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
