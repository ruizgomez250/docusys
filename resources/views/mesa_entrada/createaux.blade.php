@extends('adminlte::page')

@section('content_header')
    <h1 class="m-0 custom-heading">Registrar Nueva Mesa de Entrada Auxiliar</h1>
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
                    <form action="{{ route('mesaentradastoreaux') }}" method="post" enctype="multipart/form-data"
                        autocomplete="off" id="miFormulario">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="form-group">
                                <label for="fechaemision">FECHA DE EMISIÓN</label>
                                <input type="date" class="form-control" id="fechaemision" name="fechaemision"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="documento">Documento (PDF o DOC)</label>
                                <input type="file" name="documento" id="documento" accept=".pdf, .doc, .docx">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="documento">N° Mesa Entrada</label>
                                <input type="number" name="nromesaentrada" id="nromesaentrada" required>
                            </div>
                            <x-adminlte-select2 name="id_tipo_docr" label="Tipo de Documento" fgroup-class="col-md-3"
                                required>
                                @foreach ($tiposDoc as $tipoDoc)
                                    <option value="{{ $tipoDoc->id }}">{{ $tipoDoc->nombre }}</option>
                                @endforeach
                            </x-adminlte-select2>
                            {{-- <div class="col-md-5 form-group">
                                <label for="archivo">Archivo (ZIP o RAR)</label>
                                <input type="file" name="archivo" id="archivo" accept=".zip, .rar" required>
                            </div> --}}
                        </div>

                        <div class="row">
                            <x-adminlte-select2 name="id_origen" label="Origen" fgroup-class="col-md-4" required>
                                @foreach ($origenes as $origen)
                                    <option value="{{ $origen->id }}">
                                        {{ $origen->indice . '.' . $origen->subindice . ' - ' . $origen->nombre }}</option>
                                @endforeach
                            </x-adminlte-select2>

                            <x-adminlte-select2 name="id_tipo_doc" label="Tipo de Documento" fgroup-class="col-md-4"
                                required>
                                @foreach ($tiposDocR as $tipoDocR)
                                    <option value="{{ $tipoDocR->id }}">{{ $tipoDocR->nombre }}</option>
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
                        <div class="row">
                            <x-adminlte-textarea name="duplicado" label="Verificar Duplicación"
                                placeholder="Ingresar Duplicación"
                                fgroup-class="col-md-12">{{ old('duplicado') }}</x-adminlte-textarea>
                        </div>
                        <hr>

                        <div id="items">
                            <div class="item" style="background-color: #343A40;">
                                <div class="row ml-2">
                                    <label for="" class="col-2" style="color: white;">NUMERO</label>
                                    <label for="" class="col-2" style="color: white;">CEDULA</label>
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
@stop
@push('js')
    <script src="{{ asset('vendor/jquery-ui-1.13.2/jquery-ui.min.js') }}"></script>
    <script>
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
                                    <input type="number" name="item[]" class="codigo_id form-control col-2"
                                    placeholder="Código" value="` + itemn + `" required readonly>
                                    <input type="text" name="cedula[]" class="autocomplete-cedula form-control col-2">
                                    <input type="hidden" name="idfirmante[]" value="0" class="codigo_id form-control col-1" required>
                                    <input type="text" name="nombre[]" class="autocomplete-nombre form-control col-3"
                                      required>
                                    <input type="text" name="telefono[]" step="any" class="form-control col-2">
                                    <input type="text" name="email[]" class=" form-control col-2 " >                                    
                                     <button class="btn-remove btn btn-outline-danger ml-2" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button>                                           
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
                                    label: data[key].nombre,
                                    value: data[key].nombre,
                                    cedula: data[key].cedula,
                                    telefono: data[key].telefono,
                                    email: data[key].email,
                                    id: data[key].id,
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
                                    label: data[key].cedula,
                                    value: data[key].cedula,
                                    cedula: data[key].cedula,
                                    nombre: data[key].nombre,
                                    telefono: data[key].telefono,
                                    email: data[key].email,
                                    id: data[key].id,
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
                    $(this).closest('.row').find('input[name="nombre[]"]').val(ui.item.nombre);
                    $(this).closest('.row').find('input[name="telefono[]"]').val(ui.item.telefono);
                    $(this).closest('.row').find('input[name="email[]"]').val(ui.item.email);
                    $(this).closest('.row').find('input[name="idfirmante[]"]').val(ui.item.id);
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
    </script>
@endpush
