@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1 class="m-0 custom-heading">Dirección de Proyectos en Estudio</h1>
        </div>
    </div>
@stop

@section('plugins.Sweetalert2', true)

@push('js')
    <script>
        function abrirModalCrear(id) {
            $('#id_mentrada_crear').val(id);
            $('#modalCrearProyecto').modal('show');
        }

        function confirmarCrear() {
            var id = $('#id_mentrada_crear').val();
            var camara = $('input[name="camara"]:checked').val();
            if (!camara) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Seleccione una cámara',
                    text: 'Debe elegir Senado, Diputados o Congreso para continuar.'
                });
                return;
            }
            window.location.href = '{{ route('proyectos-en-estudio.create', '') }}/' + id + '?camara=' + encodeURIComponent(
            camara);
        }

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

            var table = $('#table1').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('proyectos-en-estudio.data') }}',
                    type: 'GET',
                    error: function(xhr) {
                        console.error("Error:", xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudieron cargar los datos.'
                        });
                    }
                },
                responsive: true,
                autoWidth: false,
                columns: [
                    { data: 'nro_mentrada' },
                    { data: 'anho' },
                    { data: 'fecha_recepcion' },
                    { data: 'origen' },
                    { data: 'tipo_doc' },
                    { data: 'firmantes' },
                    { data: 'observacion' },
                    { data: 'usuario' },
                    { data: 'acciones', orderable: false, searchable: false },
                    { data: 'mapa_created_at', orderable: true, searchable: false }
                ],
                columnDefs: [
                    { orderable: false, searchable: false, targets: [8] },
                    { className: 'text-center', targets: [8] }
                ],
                order: [
                    [9, 'desc']
                ],
                language: {
                    processing: "Cargando...",
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "No hay registros disponibles",
                    infoFiltered: "(filtrado de _MAX_ registros)",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                    zeroRecords: "No se encontraron resultados"
                }
            });
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-inbox"></i> Documentos Recepcionados</span>
                </div>
                <div class="card-body">
                    <table id="table1" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Nro MEntrada</th>
                                <th>Año</th>
                                <th>Fecha Recepción</th>
                                <th>Origen</th>
                                <th>Descripción</th>
                                <th>Firmantes</th>
                                <th>Observación</th>
                                <th>Usuario</th>
                                <th>Acciones</th>
                                <th>Ult. Act.</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCrearProyecto" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Seleccionar Cámara de Origen</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_mentrada_crear" value="">
                    <p>Seleccione la cámara de origen para el proyecto:</p>
                    <div class="form-group">
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="camara_senado" name="camara" value="Senado" class="custom-control-input">
                            <label class="custom-control-label" for="camara_senado">Senado</label>
                        </div>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="camara_diputados" name="camara" value="Diputados" class="custom-control-input">
                            <label class="custom-control-label" for="camara_diputados">Diputados</label>
                        </div>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="camara_congreso" name="camara" value="Congreso" class="custom-control-input">
                            <label class="custom-control-label" for="camara_congreso">Congreso</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="confirmarCrear()">
                        <i class="fas fa-check"></i> Crear Proyecto
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop
