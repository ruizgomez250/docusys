@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1 class="m-0 custom-heading">Proyectos Creados</h1>
        </div>
        <div class="col-6 text-right">
            <a href="{{ route('proyectos-en-estudio.index') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Proyecto
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
                    title: '<label style="font-size: 1.6rem !important;">Error!</label>',
                    text: '{{ session('error') }}',
                });
            @endif

            var table = $('#proyectos-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('proyectos-en-estudio.listado-data') }}',
                columnDefs: [
                    { targets: 0, orderable: false },
                    { targets: 4, orderable: false },
                    { targets: 6, orderable: false, searchable: false },
                    { targets: 7, visible: false }
                ],
                columns: [
                    { data: 'expediente', title: 'Expediente' },
                    { data: 'presentado_por', name: 'presentado_por', title: 'Presentado Por' },
                    { data: 'fecha_recepcion_texto', name: 'fecha_recepcion_texto', title: 'Fecha Recepción' },
                    { data: 'camara', name: 'camara', title: 'Cámara' },
                    { data: 'recepcion_nro', title: 'N° Recepción' },
                    { data: 'cantidad_observaciones', name: 'cantidad_observaciones', title: 'Obs.' },
                    { data: 'acciones', title: 'Acciones' },
                    { data: 'nro_expediente', name: 'nro_expediente', title: '' }
                ],
                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
                order: [[7, 'desc']]
            });

            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Eliminar proyecto',
                    text: '¿Está seguro de eliminar este proyecto? Solo puede eliminar el último de la lista.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('proyectos-en-estudio') }}/' + id,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                table.ajax.reload();
                                Toast.fire({
                                    icon: 'success',
                                    title: '<label style="font-size: 1.6rem !important;">Eliminado</label>',
                                    text: 'Proyecto eliminado correctamente.'
                                });
                            },
                            error: function(xhr) {
                                Toast.fire({
                                    icon: 'error',
                                    title: '<label style="font-size: 1.6rem !important;">Error</label>',
                                    text: xhr.responseJSON?.message || 'No se pudo eliminar el proyecto.'
                                });
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
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Todos los proyectos creados
                    </h3>
                </div>
                <div class="card-body">
                    <table id="proyectos-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Expediente</th>
                                <th>Presentado Por</th>
                                <th>Fecha Recepción</th>
                                <th>Cámara</th>
                                <th>N° Recepción</th>
                                <th>Obs.</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
