@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-4">
            <h1 class="m-0 custom-heading">LISTA MESA DE ENTRADA</h1>
        </div>
        <div class="col-5">
            <a href="{{ route('mesaentrada.create') }}" class="btn btn-primary" style="float: right;">Registrar Nueva Mesa de
                Entrada</a>
        </div>
        <div class="col-3 text-right">
            <a href="#" id="generateReport" class="btn btn-success">Generar Reporte</a>
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

            // Agregar confirmación de eliminación



        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <table id="table1" class="table table-bordered table-hover" theme="light">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nro MEntrada</th>
                                <th>Año</th>
                                <th>Fecha Del Documento</th>
                                <th>Origen</th>
                                <th>Descripcion</th>
                                <th>Tipo Doc</th>
                                <th>Firmantes</th>
                                <th>Destino</th>
                                <th>Observación</th>
                                <th>Estado</th>
                                <th>Usuario</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables llenará esta sección -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="documentosModal" tabindex="-1" role="dialog" aria-labelledby="documentosModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="documentosModalLabel">Documentos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <ul id="documentosList"></ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@push('js')
    <script>
        $(document).ready(function() {
            // Inicialización de DataTables
            var table = $('#table1').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('mesas-entrada.data') }}',
                    type: 'GET',
                    dataSrc: function(json) {
                        console.log("✅ Respuesta del servidor:", json); // Ver el JSON recibido
                        return json.data;
                    },
                    error: function(xhr, error, code) {
                        console.error("❌ Error en la carga de datos:", xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al cargar los datos de la tabla. Revisa la consola para más detalles.'
                        });
                    }
                },
                responsive: true,
                autoWidth: false,
                columns: [{
                        data: 'checkbox',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nro_mentrada'
                    },
                    {
                        data: 'anho'
                    },
                    {
                        data: 'fecha_recepcion'
                    },
                    {
                        data: 'origen'
                    },
                    {
                        data: 'tipo_doc'
                    },
                    {
                        data: 'tipo_docr'
                    },
                    {
                        data: 'firmantes'
                    },
                    {
                        data: 'destino'
                    },
                    {
                        data: 'observacion'
                    },
                    {
                        data: 'estado'
                    },
                    {
                        data: 'usuario'
                    },
                    {
                        data: 'acciones',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                        orderable: false,
                        searchable: false,
                        targets: [0, 12]
                    },
                    {
                        className: 'text-center',
                        targets: [0, 12]
                    } // centra checkbox y acciones
                ],
                order: [
                    [1, 'desc']
                ], // Ordenar por nro_mentrada (columna 1)
                language: {
                    processing: "Cargando...",
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "No hay registros disponibles",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                    zeroRecords: "No se encontraron resultados"
                }
            });

            // Confirmación antes de enviar formularios (enviar)
            $(document).on('submit', '.enviar-form', function(e) {
                e.preventDefault();
                var form = this;

                Swal.fire({
                    title: 'Confirmar envío',
                    text: '¿Deseas enviar este formulario?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, enviar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Confirmación antes de eliminar registros
            $(document).on('submit', '.delete-form', function(e) {
                e.preventDefault();
                var form = this;

                Swal.fire({
                    title: 'Eliminar registro',
                    text: '¿Estás seguro de eliminar este registro?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });




        function openDocumentosModal(id) {
            $.ajax({
                url: '{{ route('mesaentrada.documentos', '') }}/' + id,
                method: 'GET',
                success: function(data) {
                    $('#documentosList').empty();
                    if (data.length > 0) {
                        data.forEach(function(doc) {
                            if (doc.link) {
                                var linkHtml = '<a href="' + doc.link +
                                    '" target="_blank"><i class="fa fa-3x fa-link"></i></a>';

                                var observacionHtml = doc.observacion ? '<span class="observacion">' +
                                    doc.observacion + '</span>' : '';

                                $('#documentosList').append('<li>' + linkHtml + ' ' + observacionHtml +
                                    '</li>');
                            }
                            if (doc.nombre_archivo) { // Verificación añadida
                                var extension = doc.nombre_archivo.split('.').pop().toLowerCase();
                                var folder = (extension === 'pdf' || extension === 'docx' ||
                                    extension === 'doc') ? 'documentos' : 'archivos';
                                var url = 'http://localhost/docusys/public/' + folder + '/' + doc
                                    .nombre_archivo;

                                var iconClass = '';
                                if (extension === 'pdf') {
                                    iconClass = 'fa-file-pdf';
                                } else if (extension === 'docx' || extension === 'doc') {
                                    iconClass = 'fa-file-word';
                                } else {
                                    iconClass =
                                        'fa-file-archive'; // Puedes ajustar este icono según el tipo de archivo
                                }

                                var iconHtml = '<i class="fa fa-3x ' + iconClass +
                                    '"></i>'; // Tamaño grande del icono (fa-3x)
                                var linkHtml = '<a href="' + url + '" target="_blank">' + iconHtml +
                                    '</a>';
                                var observacionHtml = doc.observacion ? '<span class="observacion">' +
                                    doc.observacion + '</span>' : '';

                                $('#documentosList').append('<li>' + linkHtml + ' ' + observacionHtml +
                                    '</li>');
                            }
                        });
                    } else {
                        $('#documentosList').append('<li>No hay documentos disponibles.</li>');
                    }
                    $('#documentosModal').modal('show');
                },
                error: function(e) {
                    console.log(e);
                    alert('Error al obtener los documentos.');
                }
            });
        }
        $('#generateReport').on('click', function() {
            // Recoge los IDs de los checkboxes marcados
            var selectedIds = $('.select-row:checked').map(function() {
                return $(this).val(); // devuelve el value (ID)
            }).get(); // convierte el resultado a un array

            if (selectedIds.length === 0) {
                Swal.fire('Atención', 'Debe seleccionar al menos un registro.', 'warning');
                return;
            }

            // Enviar IDs al servidor
            var url = '{{ route('reporte.multiple') }}?ids=' + selectedIds.join(',');
            window.open(url, '_blank');
        });


        function toggleGenerateButton() {
            if ($('.select-row:checked').length > 0) {
                $('#generateReport').fadeIn();
            } else {
                $('#generateReport').fadeOut();
            }
        }

        // Llamar al cambiar cualquier checkbox
        $('#table1 tbody').on('change', 'input.select-row', toggleGenerateButton);
    </script>
@endpush
