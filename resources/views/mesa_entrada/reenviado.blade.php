@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1 class="m-0 custom-heading">Lista de Documentos</h1>
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
            $('.delete-button').on('click', function() {
                var form = $(this).closest('.delete-form');
                Swal.fire({
                    title: 'Confirmar eliminación',
                    text: '¿Estás seguro de que deseas eliminar esta mesa de entrada?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            var table = $('#table1').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("mesaentrada.finalizado.data") }}',
            type: 'GET',
            dataSrc: function(json) {
                console.log("✅ Respuesta:", json);
                return json.data;
            },
            error: function(xhr) {
                console.error("❌ Error:", xhr.responseText);
                Swal.fire('Error', 'No se pudieron cargar los datos.', 'error');
            }
        },

        responsive: true,
        autoWidth: false,

        columns: [
            { data: 'nro_mentrada' },
            { data: 'anho' },
            { data: 'fecha' },
            { data: 'origen' },
            { data: 'descripcion' },
            { data: 'tipo_doc' },
            { data: 'firmantes' },
            { data: 'observacion' },
            { data: 'estado' },
            { data: 'usuario' },
            { data: 'acciones', orderable:false, searchable:false }
        ],

        order: [[0,'desc']],

        language: {
            processing: "Cargando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "No hay registros",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            }
        }
    });

    // ✅ CONFIRMACIÓN DE FORMULARIOS
    $(document).on('click', '.enviar-button', function() {
        let form = $(this).closest('form');

        Swal.fire({
            title: 'Confirmar',
            text: '¿Deseas continuar?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
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
                <div class="card-body">
                    <table id="table1" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Nro MEntrada</th>
                                <th>Año</th>
                                <th>Fecha</th>
                                <th>Origen</th>
                                <th>Descripcion</th>
                                <th>Tipo Doc.</th>
                                <th>Firmantes</th>
                                <th>Observación</th>
                                <th>Estado</th>
                                <th>Usuario</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
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
            

            // Función para generar HTML de detalles adicionales
            function format(details) {
                var detalleHTML = '<table class="table table-bordered table-hover table-sm">' +
                    '<thead>' +
                    '<tr>' +
                    '<th>N</th>' +
                    '<th>Cedula</th>' +
                    '<th>Nombre</th>' +
                    '<th>Telefono</th>' +
                    '<th>Email</th>' +
                    '</tr>' +
                    '</thead>' +
                    '<tbody>';
                details.forEach(function(detalle, index) {
                    detalleHTML += '<tr>' +
                        '<td>' + (index + 1) + '</td>' + // N
                        '<td>' + (detalle.firmante.cedula || '-') + '</td>' + // Cedula
                        '<td>' + (detalle.firmante.nombre || '-') + '</td>' + // Nombre
                        '<td>' + (detalle.firmante.telefono || '-') + '</td>' + // Telefono
                        '<td>' + (detalle.firmante.correo || '-') + '</td>' + // Email
                        '</tr>';
                });
                detalleHTML += '</tbody></table>';
                return detalleHTML;
            }


        });

        function setMasDestinos(value) {
            // Cambia el valor del campo hidden a 0 o 1
            document.getElementById('masdestinos').value = value;
        }
        $('.enviar-button').on('click', function() {
            var form = $(this).closest('.enviar-form');
            Swal.fire({
                title: 'Confirmar',
                text: '¿Estás seguro de que ya recepciono el formulario?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, aceptar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        function cargarmentrada(id) {
            document.getElementById('idmentrada').value = id;
        }

        function openDocumentosModal(id) {
            var baseUrl = "{{ asset('') }}";
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
                                var url = baseUrl + folder + '/' + doc
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
    </script>
@endpush
