@extends('adminlte::page')

@section('content_header')
    <h1 class="m-0 custom-heading">Registrar Nueva Mesa de Entrada<button id="openBubble" class="btn btn-dark btn-sm">
            Ultimos 3 Docs.
        </button></h1>
@stop
@section('plugins.Sweetalert2', true)

@push('js')
    <script>
        $(document).ready(function() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
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
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('mesaentrada.store') }}" method="post" enctype="multipart/form-data"
                        autocomplete="off" id="miFormulario">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="form-group">
                                <label for="fechaemision">FECHA DEL DOCUMENTO</label>
                                <input type="date" class="form-control" id="fechaemision" name="fechaemision"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-5 form-group">
                                <label for="documento">Documento (PDF o DOC)</label>
                                <input type="file" name="documento" id="documento" accept=".pdf, .doc, .docx">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="documento">N° Mesa Entrada</label>
                                <input type="number" name="nromesaentrada" id="nromesaentrada" required>
                            </div>
                            {{-- <div class="col-md-5 form-group">
                                <label for="archivo">Archivo (ZIP o RAR)</label>
                                <input type="file" name="archivo" id="archivo" accept=".zip, .rar" required>
                            </div> --}}
                        </div>

                        <div class="row">
                            <x-adminlte-select2 name="id_origen" label="Origen" fgroup-class="col-md-4" required>
                                @foreach ($origenes as $origen)
                                    <option value="{{ $origen->id }}"
                                        {{ $origen->nombre == 'CAMARA DE DIPUTADOS' ? 'selected' : '' }}>
                                        {{ $origen->indice . '.' . $origen->subindice . ' - ' . $origen->nombre }}
                                    </option>
                                @endforeach

                            </x-adminlte-select2>

                            <x-adminlte-select2 name="id_tipo_doc" label="Tipo de Documento" fgroup-class="col-md-4"
                                required>
                                @foreach ($tiposDoc as $tipoDoc)
                                    <option value="{{ $tipoDoc->id }}">{{ $tipoDoc->nombre }}</option>
                                @endforeach
                            </x-adminlte-select2>

                            <x-adminlte-select2 name="id_destino" label="Destino" fgroup-class="col-md-4" required>
                                @foreach ($destinos as $destino)
                                    <option value="{{ $destino->id }}" {{ $destino->default == 1 ? 'selected' : '' }}>
                                        {{ $destino->nombre }}</option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>

                        <div class="row">
                            <x-adminlte-textarea name="observacion" label="Observación" placeholder="Ingresar Observación"
                                fgroup-class="col-md-12">{{ old('observacion') }}</x-adminlte-textarea>
                        </div>
                        <div class="row" style="display: none;">
                            <x-adminlte-textarea name="duplicado" label="Verificar Duplicación"
                                placeholder="Ingresar Duplicación"
                                fgroup-class="col-md-12">{{ old('duplicado') }}</x-adminlte-textarea>
                        </div>
                        <hr>

                        <div id="items">
                            <div class="item" style="background-color: #343A40;">
                                <div class="row ml-2">
                                    <label for="" class="col-1" style="color: white;">CODIGO</label>
                                    <label for="" class="col-1" style="color: white;">CEDULA</label>
                                    <label for="" class="col-3" style="color: white;">NOMBRE</label>
                                    <label for="" class="col-2" style="color: white;">TELEFONO</label>
                                    <label for="" class="col-2" style="color: white;">EMAIL</label>
                                </div>
                            </div>
                        </div>
                        <button onclick="addNewItem()" class="btn btn-primary mt-2" type="button">Agregar Ítem</button>
                        <hr>

                        <div class="row">
                            <div class="form-group col-md-12">
                                <a class="btn btn-danger" style="float: right;"
                                    href="{{ route('mesaentrada.index') }}">Cancelar</a>
                                <x-adminlte-button class="btn-group" style="float: right;" type="submit" label="Registrar"
                                    theme="primary" icon="fas fa-lg fa-save" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Burbuja flotante -->
    <div id="floating-bubble">
        <button type="button" onclick="closeBubble()">X</button>
        <p>Últimos 3 registros:</p>
        <ul>
            @foreach ($ultimos3 as $entrada)
                <li>
                    <strong>Número:</strong> {{ $entrada->nro_mentrada }} -
                    {{ $entrada->observacion }}
                </li>
            @endforeach
        </ul>
    </div>
@stop
@push('css')
    <style>
        #floating-bubble {
            position: fixed;
            top: 60px;
            right: 20px;
            width: 300px;
            max-height: 400px;
            /* Altura máxima para que no se agrande demasiado */
            background-color: rgba(255, 255, 255, 0.5);
            /* Fondo blanco al 50% de transparencia */
            border: 1px solid #ccc;
            padding: 10px;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            overflow-y: auto;
            /* Habilita el scroll vertical */
        }

        #floating-bubble button {
            float: right;
            border: none;
            background: transparent;
            font-size: 1.2rem;
            cursor: pointer;
        }
    </style>
@endpush
@push('js')
    <script src="{{ asset('vendor/jquery-ui-1.13.2/jquery-ui.min.js') }}"></script>
    <script>
        function seleccionarOrigen(idOrigen) {
            // Obtener el elemento select por su ID
            const selectElement = document.getElementById('id_origen');
            // Verificar si el elemento existe
            if (selectElement) {
                // Buscar el option con el valor deseado
                const optionToSelect = selectElement.querySelector(`option[value="${idOrigen}"]`);
                // Si el option existe, seleccionarlo
                if (optionToSelect) {
                    optionToSelect.selected = true; // Selecciona la opción en el DOM

                    // Disparar el evento 'change' para notificar a Select2
                    $(selectElement).trigger('change');

                    // Actualizar Select2 para reflejar el cambio en la vista
                    $(selectElement).select2();
                } else {
                    console.error('No se encontró el origen con el ID:', idOrigen);
                }
            } else {
                console.error('No se encontró el elemento select con ID "id_origen"');
            }
        }
        $(function() {
            // Hacer que la burbuja flotante sea arrastrable
            $("#floating-bubble").draggable();
            $("#openBubble").click(function() {
                $("#floating-bubble").show();
            });
        });

        function closeBubble() {
            $("#floating-bubble").hide();
        }

        function checkForDuplicateObservacion() {
            const duplicado = document.querySelector('textarea[name="duplicado"]').value;
            //console.log(observacion);

            // Realizar una llamada AJAX al servidor
            fetch('/docusys/public/verificar-duplicado', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        duplicado: duplicado
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.duplicado) {
                        Swal.fire({
                            title: "Desea Guardar de todos modos?",
                            text: "ya existe un documento con esos datos!",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Si, Guardar!"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                        return false; // Prevenir el envío del formulario
                    } else {
                        form.submit(); // Si no hay duplicados, continuar con el envío
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });

            return false; // Evitar el envío inmediato del formulario
        }

        const form = document.getElementById('miFormulario');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            checkForDuplicateObservacion(); // Verificar antes de enviar
        });
        itemn = 0;
        const itemsContainer = document.getElementById('items');
        addNewItem();
        // Función para agregar un nuevo ítem de compra
        function addNewItem() {
            itemn++;

            const newItem = document.createElement("div");
            newItem.classList.add("item");
            borrar =
                '<button class="btn-remove btn btn-outline-danger ml-2" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button>';


            newItem.innerHTML = `
            <div class="row ml-1">
                <input type="number" name="codigo[]" class="autocomplete-codigo form-control col-1"
                placeholder="Código" value="" onchange="buscarporcod(this)">
                
                <input type="text" name="cedula[]" class="autocomplete-cedula form-control col-1">
                
                <input type="hidden" name="idfirmante[]" value="0" class="codigo_id form-control col-1" required>
                
                <input type="text" name="nombre[]" class="autocomplete-nombre form-control col-3" required>
                
                <input type="text" name="telefono[]" step="any" class="form-control col-2">
                
                <input type="text" name="email[]" class="form-control col-2">
                
                <select name="tipo[]" class="form-control col-2">
                    <option value="FIRMANTE">FIRMANTE</option>
                    <option value="SOLICITANTE">SOLICITANTE</option>
                </select>
                
                <button class="btn-remove btn btn-outline-danger ml-2" type="button">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
            </div>
            `;



            const codigoInput = newItem.querySelector('input[name="cedula[]"]');
            codigoInput.addEventListener('keydown', function(e) {
                // Verifica si la tecla presionada es "Enter"
                if (e.key === 'Enter') {
                    // Evita el envío del formulario
                    e.preventDefault();
                    // Enfoca en el campo de producto
                    const cantidadInput = newItem.querySelector('input[name="nombre[]"]');
                    cantidadInput.focus();
                }
            });
            const cantidadInput = newItem.querySelector('input[name="nombre[]"]');
            cantidadInput.addEventListener('keydown', function(e) {
                // Verifica si la tecla presionada es "Enter"
                if (e.key === 'Enter') {
                    // Evita el envío del formulario
                    e.preventDefault();
                    // Enfoca en el campo de producto
                    const descripcionInput = newItem.querySelector('input[name="telefono[]"]');
                    descripcionInput.focus();
                }
            });
            const descripcionInput = newItem.querySelector('input[name="telefono[]"]');
            descripcionInput.addEventListener('keydown', function(e) {
                // Verifica si la tecla presionada es "Enter"
                if (e.key === 'Enter') {
                    // Evita el envío del formulario
                    e.preventDefault();

                    // Enfoca en el campo de producto
                    const precioInput = newItem.querySelector('input[name="email[]"]');
                    precioInput.focus();
                    precioInput.select();
                }
            });
            const emailInput = newItem.querySelector('input[name="email[]"]');
            emailInput.addEventListener('keydown', function(e) {
                // Verifica si la tecla presionada es "Enter"
                if (e.key === 'Enter') {
                    // Evita el envío del formulario
                    e.preventDefault();
                    addNewItem();
                    // Enfoca en el campo de producto
                    const precioInput = newItem.querySelector('input[name="cedula[]"]');
                    //this.precioInput.focus();
                    //this.precioInput.select();
                }
            });
            // Agregar el nuevo elemento al contenedor
            itemsContainer.appendChild(newItem);

            // Agregar el evento click para eliminar el ítem después de que se haya agregado al contenedor
            // btnAddItem.addEventListener("click", addNewItem);
            const btnRemove = newItem.querySelector(".btn-remove");
            btnRemove.addEventListener("click", function() {
                removeItem(newItem);
            });



            $('input[name="cedula[]"]').focus();


        }

        function removeItem(itemToRemove) {
            itemsContainer.removeChild(itemToRemove);
            itemn--;

        }
        $(document).on('focus', '.autocomplete-nombre', function() {
            $(this).autocomplete({
                minLength: 0, // Cambiamos a 0 para que se dispare el autocompletado sin escribir
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('obtenerfirmante') }}",
                        contentType: "application/json",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            var filteredData = Object.keys(data).map(function(key) {
                                return {
                                    label: data[key].nombre + ' - Cod. ' + data[key]
                                        .codigo,
                                    value: data[key].nombre,
                                    cedula: data[key].cedula,
                                    codigo: data[key].codigo,
                                    telefono: data[key].telefono,
                                    email: data[key].email,
                                    id: data[key].id,
                                    origen: data[key].origen,
                                };
                            });
                            response(filteredData);
                        }
                    });
                },
                select: function(event, ui) {
                    // Aquí puedes manejar lo que sucede cuando se selecciona un elemento
                    //traerCargarDatosProducto(ui.item.codigo, this);
                    //console.log(ui.item);
                    $(this).closest('.row').find('input[name="cedula[]"]').val(ui.item.cedula);
                    $(this).closest('.row').find('input[name="nombre[]"]').val(ui.item.value);
                    $(this).closest('.row').find('input[name="telefono[]"]').val(ui.item.telefono);
                    $(this).closest('.row').find('input[name="email[]"]').val(ui.item.email);
                    $(this).closest('.row').find('input[name="idfirmante[]"]').val(ui.item.id);
                    $(this).closest('.row').find('input[name="codigo[]"]').val(ui.item.codigo);
                    $('input[name="email[]"]').focus(); // Movemos el foco al campo de cantidad
                    console.log($(this).closest('.row').find('input[name="tipo[]"]').val());
                    seleccionarOrigen(ui.item.origen);

                },
                autoFocus: true, // Activamos el enfoque automático para facilitar la navegación con teclado
            }).keydown(function(event) {
                // Capturamos el evento keydown para verificar si se presionó Enter
                if (event.keyCode === 13 && !$(this).val()) {
                    // Si se presionó Enter y el campo está vacío
                    $('input[name="cantidad[]"]').focus(); // Movemos el foco al campo de cantidad
                }
            });
        });
        $(document).on('focus', '.autocomplete-codigo', function() {
            $(this).autocomplete({
                minLength: 0, // Cambiamos a 0 para que se dispare el autocompletado sin escribir
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('obtenerfirmante') }}",
                        contentType: "application/json",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {

                            var filteredData = Object.keys(data).map(function(key) {
                                return {
                                    label: data[key].nombre + ' - Cod. ' + data[key]
                                        .codigo,
                                    value: data[key].nombre,
                                    cedula: data[key].cedula,
                                    codigo: data[key].codigo,
                                    telefono: data[key].telefono,
                                    email: data[key].email,
                                    id: data[key].id,
                                    origen: data[key].origen,
                                };
                            });
                            response(filteredData);
                        }
                    });
                },
                select: function(event, ui) {
                    // Aquí puedes manejar lo que sucede cuando se selecciona un elemento
                    //traerCargarDatosProducto(ui.item.codigo, this);
                    //console.log(ui.item);
                    $(this).closest('.row').find('input[name="cedula[]"]').val(ui.item.cedula);
                    $(this).closest('.row').find('input[name="nombre[]"]').val(ui.item.value);
                    $(this).closest('.row').find('input[name="telefono[]"]').val(ui.item.telefono);
                    $(this).closest('.row').find('input[name="email[]"]').val(ui.item.email);
                    $(this).closest('.row').find('input[name="idfirmante[]"]').val(ui.item.id);
                    $(this).closest('.row').find('input[name="codigo[]"]').val(ui.item.codigo);
                    seleccionarOrigen(ui.item.origen);
                    $('input[name="email[]"]').focus(); // Movemos el foco al campo de cantidad

                },
                autoFocus: true, // Activamos el enfoque automático para facilitar la navegación con teclado
            }).keydown(function(event) {
                // Capturamos el evento keydown para verificar si se presionó Enter
                if (event.keyCode === 13 && !$(this).val()) {
                    // Si se presionó Enter y el campo está vacío
                    $('input[name="cantidad[]"]').focus(); // Movemos el foco al campo de cantidad
                }
            });
        });
        $(document).on('focus', '.autocomplete-cedula', function() {
            $(this).autocomplete({
                minLength: 0, // Se activa el autocompletado sin necesidad de escribir
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('obtenerfirmante') }}",
                        contentType: "application/json",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            var filteredData = Object.keys(data).map(function(key) {
                                return {
                                    label: data[key].cedula,
                                    value: data[key].cedula,
                                    cedula: data[key].cedula,
                                    nombre: data[key].nombre,
                                    codigo: data[key].codigo,
                                    telefono: data[key].telefono,
                                    email: data[key].email,
                                    id: data[key].id,
                                    origen: data[key].origen,
                                };
                            });
                            response(filteredData);
                        }
                    });
                },
                select: function(event, ui) {
                    // Completa los campos en la fila actual
                    $(this).closest('.row').find('input[name="cedula[]"]').val(ui.item.cedula);
                    $(this).closest('.row').find('input[name="nombre[]"]').val(ui.item.nombre);
                    $(this).closest('.row').find('input[name="telefono[]"]').val(ui.item.telefono);
                    $(this).closest('.row').find('input[name="email[]"]').val(ui.item.email);
                    $(this).closest('.row').find('input[name="idfirmante[]"]').val(ui.item.id);
                    $(this).closest('.row').find('input[name="codigo[]"]').val(ui.item.codigo);
                    seleccionarOrigen(ui.item.origen);
                    $('input[name="email[]"]').focus(); // Mueve el foco al siguiente campo


                    return false; // Previene el autocompletado en el campo original
                },
                autoFocus: true // Enfoque automático para facilitar la navegación con teclado
            }).keydown(function(event) {
                if (event.keyCode === 13) { // Si se presiona Enter
                    event.preventDefault(); // Evita el envío del formulario

                    // Si no se ha seleccionado nada, pero se presiona Enter, hace una acción por defecto
                    if (!$(this).data("ui-autocomplete").selectedItem) {
                        $(this).autocomplete("search", $(this).val());
                    }
                }
            });
        });

        function buscarporcod(inputElement) {
            const codigo = inputElement.value;
            // Llama a la ruta de búsqueda
            fetch(`/docusys/public/buscar-firmante/${codigo}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const firmante = data.data;

                        // Encuentra el contenedor de la fila actual
                        const parentRow = inputElement.closest('.row');

                        // Llena los campos correspondientes
                        parentRow.querySelector('input[name="cedula[]"]').value = firmante.cedula || '';
                        parentRow.querySelector('input[name="nombre[]"]').value = firmante.nombre || '';
                        parentRow.querySelector('input[name="telefono[]"]').value = firmante.telefono || '';
                        parentRow.querySelector('input[name="email[]"]').value = firmante.correo || '';
                        parentRow.querySelector('input[name="idfirmante[]"]').value = firmante.id || '';

                    } else {
                        console.log('Firmante no encontrado');
                    }
                })
                .catch(error => {
                    console.error('Error al buscar el firmante:', error);
                });
        }
    </script>
@endpush
