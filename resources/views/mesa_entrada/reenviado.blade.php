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

                                <th></th> <!-- Columna para el botón de expansión -->
                                <th>Nro MEntrada</th>
                                <th>Año</th>
                                <th>Fecha Recepción</th>
                                <th>Origen</th>
                                <th>Tipo Doc</th>
                                <th>Firmantes</th>
                                <th>Observación</th>
                                <th>Estado</th>
                                <th>Usuario</th>
                                <th>Acciones</th>
                                <th>Ult. Act.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mesasEntrada as $row)
                                <tr data-child-id="{{ $row->id }}">
                                    <td class="details-control text-center">
                                        <i class="fa fa-plus-circle text-primary"></i> <!-- Ícono de expansión -->
                                    </td>
                                    <td>
                                        {{ $row->nro_mentrada }}
                                        @if ($row->nro_suplementario !== null)
                                            .{{ $row->nro_suplementario }}
                                        @endif
                                    </td>
                                    <td>{{ $row->anho }}</td>
                                    <td>{{ $row->fecha_recepcion }}</td>
                                    <td>{{ $row->origen->nombre ?? 'N/A' }}</td>
                                    <td>{{ $row->tipoDoc->nombre ?? 'N/A' }}</td>
                                    <td>{{ $row->nombres_firmantes ?? 'N/A' }}</td>
                                    <td>{{ $row->observacion }}</td>
                                    <td class="{{ $row->estado_recorrido == '2' ? 'text-danger' : 'text-success' }}">
                                        @if ($row->estado_recorrido == '2')
                                            Enviado
                                        @elseif ($row->estado_recorrido == '0')
                                            Aceptado
                                        @elseif ($row->estado_recorrido == '4')
                                            Redireccionado
                                        @elseif ($row->estado == '0')
                                            Trámite Finalizado
                                        @endif

                                    </td>
                                    <td>{{ $row->user->name ?? 'N/A' }}</td>
                                    <td>
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
                                        @if ($row->estado_recorrido > 0)
                                            <x-adminlte-button theme="outline-danger" data-toggle="modal"
                                                data-target="#modalDestinos" class="btn-sm " icon="fas fa-paper-plane"
                                                onclick="cargarmentrada({{ $row->id }})" />
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
                                    <td>{{ $row->fecha_creacion_recorrido }}</td>
                                </tr>
                            @endforeach


                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <!--Modal-->
    <div class="modal fade" id="modalDestinos">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Fijar nuevo destino</h4>
                </div>
                <div class="modal-body d-flex justify-content-center align-items-center">
                    <form action="{{ route('reenviardoc') }}" method="POST">
                        @csrf
                        <x-adminlte-select2 name="id_destino" label="Destino" fgroup-class="col-md-12" required>
                            @foreach ($destinos as $destino)
                                <option value="{{ $destino->id }}" {{ $destino->default == 1 ? 'selected' : '' }}>
                                    {{ $destino->nombre }}
                                </option>
                            @endforeach
                        </x-adminlte-select2>

                        <input type="hidden" name="idmentrada" id="idmentrada" required>

                        <!-- Campo hidden para masdestinos -->
                        <input type="hidden" name="masdestinos" id="masdestinos" value="0">

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-info" onclick="setMasDestinos(1)"
                                data-bs-dismiss="modal">Fijar Otro destino más</button>
                            <button type="submit" class="btn btn-primary" onclick="setMasDestinos(0)">Cambiar Destino
                                Enviado</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>
                </div>
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

    <!-- fin Modal-->
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
                    [11, 'desc']
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

            // Evento de clic en la flechita para mostrar/ocultar detalles
            $('#table1 tbody').on('click', 'td.details-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                var id = tr.data('child-id'); // Obtener el ID del elemento (Mesa de entrada)
                var iconElement = $(this).find('i'); // Guardar el elemento del ícono

                if (row.child.isShown()) {
                    // Si el detalle está visible, lo ocultamos
                    row.child.hide();
                    tr.removeClass('shown');
                    iconElement.removeClass('fa-minus-circle').addClass('fa-plus-circle');
                } else {
                    // Si el detalle está oculto, lo mostramos
                    $.ajax({
                        url: '{{ route('mesaentrada.firmantes', '') }}/' +
                            id, // Verificar si la ruta se forma correctamente
                        method: 'GET',
                        success: function(response) {

                            // Utilizar el objeto de respuesta directamente como array de detalles
                            var detalles =
                                response; // Aquí 'response' ya es un array de objetos, no 'response.detalles'
                            if (detalles.length > 0) {
                                // Mostramos el detalle
                                row.child(format(detalles)).show();
                                tr.addClass('shown');
                                iconElement.removeClass('fa-plus-circle').addClass(
                                    'fa-minus-circle');
                            } else {
                                console.log('No se encontraron detalles.');
                            }
                        },
                        error: function() {
                            console.log('Error al obtener detalles.');
                        }
                    });
                }
            });
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
