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
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="table1" class="table table-bordered table-hover" theme="light">
                        <thead>
                            <tr>
                                <th>Nro MEntrada</th>
                                <th>Año</th>
                                <th>Fecha Del Documento</th>
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
                        <tbody>
                            @foreach ($mesasEntrada as $row)
                                <tr data-child-id="{{ $row->id }}">
                                    
                                    <td>{{ $row->nro_mentrada }}</td>
                                    <td>{{ $row->anho }}</td>
                                    <td>{{ $row->fecha_recepcion }}</td>
                                    <td>{{ $row->origen->nombre ?? 'N/A' }}</td>
                                    <td>{{ $row->tipoDoc->nombre ?? 'N/A' }}</td>
                                    <td>{{ $row->tipoDocR->nombre ?? 'N/A' }}</td>
                                    <td>{{ $row->nombres_firmantes ?? 'N/A' }}</td>
                                    <td>{{ $row->observacion }}</td>
                                    <td class="{{ $row->estado == '2' ? 'text-danger' : 'text-success' }}">
                                        @if ($row->estado == '2')
                                            Recepcionado
                                        @elseif ($row->estado == '3')
                                            Aceptado
                                        @else
                                            Redireccionado
                                        @endif
                                    </td>
                                    <td>{{ $row->user->name ?? 'N/A' }}</td>
                                    <td style="float:right;">
                                        @if ($row->estado == 2 && $row->mapa_estado == 1)
                                            <form action="{{ route('mesaentrada.aceptar', $row->id) }}" method="post"
                                                class="d-inline enviar-form">
                                                @csrf
                                                <button type="button"
                                                    class="btn btn-sm  btn-outline-secondary enviar-button">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @elseif($row->estado == 2 && $row->mapa_estado == 2)
                                            {{-- <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                                            data-bs-target="#modalDestinos">
                                            <i class="fas fa-paper-plane"></i>
                                        </button> --}}
                                            <x-adminlte-button theme="outline-danger" data-toggle="modal"
                                                data-target="#modalDestinos" class="btn-sm " icon="fas fa-paper-plane"
                                                onclick="cargarmentrada({{ $row->id }})" />
                                            <x-adminlte-button theme="outline-danger" data-toggle="modal"
                                                data-target="#modalCargaDocs" onclick="cargarmentrada({{ $row->id }})"
                                                class="btn-sm " icon="fas fa-file-upload" />
                                            <form action="{{ route('mesaentrada.finalizar', $row->id) }}" method="post"
                                                class="d-inline enviar-form">
                                                @csrf
                                                <button type="button"
                                                    class="btn btn-sm  btn-outline-secondary enviar-button">
                                                    <i class="fas fa-flag-checkered"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('reporte.recorrido', $row) }}" target="_blank"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="fa fa-file-pdf"></i>
                                        </a>
                                        @if ($row->tiene_documentos)
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                onclick="openDocumentosModal({{ $row->id }})">
                                                <i class="fa fa-sm fa-fw fa-print"></i>
                                            </button>
                                        @endif

                                        @if ($usuario->autorizar_modif == 1 && $row->modificar == 0)
                                            <form action="{{ route('mesaentrada.autorizarmodif', $row->id) }}"
                                                method="post" class="d-inline enviar-form">
                                                @csrf
                                                <button type="button"
                                                    class="btn btn-sm  btn-outline-secondary enviar-button">
                                                    <i class="fas fa-unlock"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
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
                responsive: true,
                autoWidth: false,
                columnDefs: [{
                        className: 'details-control', // Agrega clase de control de detalles
                        orderable: false, // No se puede ordenar por esta columna
                        targets: 0 // Índice de la columna de flechita
                    },
                    {
                        orderable: false,
                        targets: -1 // Última columna (acciones)
                    }
                ],
                order: [
                    [2, 'desc']
                ], // Ordenar por el ID (columna 1)
            });

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