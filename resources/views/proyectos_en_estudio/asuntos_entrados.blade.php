@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1 class="m-0 custom-heading">Asuntos Entrados</h1>
        </div>
        <div class="col-6">
            <a href="{{ route('proyectos-en-estudio.sesiones') }}" class="btn btn-primary btn-sm" style="float: right; margin-left: 5px;">
                <i class="fas fa-plus"></i> Nueva Sesión
            </a>
            <a href="{{ route('proyectos-en-estudio.listado') }}" class="btn btn-secondary btn-sm" style="float: right;">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('plugins.Sweetalert2', true)

@push('js')
    <script>
        $(document).ready(function() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                color: '#716add',
                showConfirmButton: false,
                timer: 3000
            });

            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: '<label style="font-size: 1.6rem !important;">Operación Exitosa!</label>',
                    text: '{{ session('success') }}',
                });
            @endif

            @if (session('error'))
                Toast.fire({
                    icon: 'error',
                    title: '<label style="font-size: 1.6rem !important;">Error Inesperado!</label>',
                    text: '{{ session('error') }}',
                });
            @endif

            $('.btn-delete-sesion').on('click', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: '¿Desasignar documentos?',
                    text: 'Los documentos volverán al listado general y la sesión se eliminará.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, desasignar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url("proyectos-en-estudio/sesiones") }}/' + id,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function() {
                                Swal.fire('Eliminado', 'La sesión fue eliminada.', 'success').then(() => {
                                    location.reload();
                                });
                            },
                            error: function() {
                                Swal.fire('Error', 'No se pudo eliminar la sesión.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-2">
                    <form method="GET" action="{{ route('proyectos-en-estudio.asuntos-entrados') }}" class="form-inline">
                        <div class="input-group" style="width: 100%; max-width: 500px;">
                            <input type="text" name="search" class="form-control"
                                placeholder="Buscar por tipo, nro de sesión o fecha..."
                                value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('proyectos-en-estudio.asuntos-entrados') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($sesiones->isEmpty())
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            @if(request('search'))
                                No se encontraron sesiones con "{{ request('search') }}".
                            @else
                                No hay sesiones creadas aún. Vaya a <strong>Generar PDF de Sesión</strong> para crear una.
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @foreach($sesiones as $sesion)
                @php
                    $carbon = $sesion->fecha_sesion;
                    $dias = ['DOMINGO', 'LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO'];
                    $meses = [1 => 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
                    $fechaTexto = $dias[$carbon->dayOfWeek] . ' ' . $carbon->format('d') . ' DE ' . $meses[$carbon->month] . ' DE ' . $carbon->format('Y');
                @endphp
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-gavel text-danger"></i>
                            <strong>SESIÓN {{ $sesion->tipo_sesion }} N° {{ $sesion->nro_sesion }}</strong>
                            <span class="text-muted" style="margin-left: 10px; font-size: 0.85em;">
                                | {{ $fechaTexto }} | {{ $sesion->proyectos->count() }} documento(s)
                            </span>
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('proyectos-en-estudio.asuntos-entrados.pdf', $sesion->id) }}"
                               class="btn btn-danger btn-sm" target="_blank" title="Descargar PDF">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                            <a href="{{ route('proyectos-en-estudio.edit-sesion', $sesion->id) }}"
                               class="btn btn-info btn-sm" title="Editar">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm btn-delete-sesion"
                                    data-id="{{ $sesion->id }}" title="Desasignar y eliminar sesión">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 8%">N°</th>
                                    <th style="width: 18%">Expediente</th>
                                    <th style="width: 44%">Acapite</th>
                                    <th style="width: 20%">Destino</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sesion->proyectos->sortBy('nro_expediente') as $p)
                                    @php
                                        $letra = '';
                                        if ($p->camara == 'Senado') $letra = 'S';
                                        elseif ($p->camara == 'Diputados') $letra = 'D';
                                        elseif ($p->camara == 'Congreso') $letra = 'C';
                                        $aa = substr($p->anho, -2);
                                        $expediente = $letra . '-' . $aa . $p->nro_expediente;

                                        $acapite = strip_tags($p->acapite ?? '');
                                        $acapite = html_entity_decode($acapite, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                        $acapite = preg_replace('/\s+/', ' ', trim($acapite));
                                        if (mb_strlen($acapite) > 100) {
                                            $acapite = mb_substr($acapite, 0, 100) . '...';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center"><strong>{{ $p->nro_expediente }}</strong></td>
                                        <td>{{ $expediente }}</td>
                                        <td>{{ $acapite }}</td>
                                        <td>{{ $p->destino ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop
