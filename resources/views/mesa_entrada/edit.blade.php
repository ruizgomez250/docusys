@extends('adminlte::page')

@section('content_header')
    <h1 class="m-0 custom-heading">Editar Mesa de Entrada</h1>
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
                    <form action="{{ route('mesaentrada.update', $mesaEntrada->id) }}" method="post" autocomplete="off">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group">
                                <label for="fechaemision">FECHA DE EMISIÓN</label>
                                <input type="date" class="form-control" id="fechaemision" name="fechaemision"
                                    value="{{ $mesaEntrada->fechaemision }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <x-adminlte-select2 name="id_origen" label="Origen" fgroup-class="col-md-4" required>
                                @foreach ($origenes as $origen)
                                    <option value="{{ $origen->id }}"
                                        {{ $mesaEntrada->id_origen == $origen->id ? 'selected' : '' }}>{{ $origen->nombre }}
                                    </option>
                                @endforeach
                            </x-adminlte-select2>

                            <x-adminlte-select2 name="id_tipo_doc" label="Tipo de Documento" fgroup-class="col-md-4"
                                required>
                                @foreach ($tiposDoc as $tipoDoc)
                                    <option value="{{ $tipoDoc->id }}"
                                        {{ $mesaEntrada->id_tipo_doc == $tipoDoc->id ? 'selected' : '' }}>
                                        {{ $tipoDoc->nombre }}</option>
                                @endforeach
                            </x-adminlte-select2>

                            <x-adminlte-select2 name="id_destino" label="Destino" fgroup-class="col-md-4" required>
                                @foreach ($destinos as $destino)
                                    <option value="{{ $destino->id }}"
                                        {{ $mesaEntrada->id_destino == $destino->id ? 'selected' : '' }}>
                                        {{ $destino->nombre }}</option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>

                        <div class="row">
                            <x-adminlte-textarea name="observacion" label="Observación" placeholder="Ingresar Observación"
                                fgroup-class="col-md-12">{{ old('observacion', $mesaEntrada->observacion) }}</x-adminlte-textarea>
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
                            @foreach ($firmantes as $index => $firmante)
                                <div class="item">
                                    <div class="row ml-1">
                                        <input type="number" name="item[]" class="codigo_id form-control col-2"
                                            placeholder="Código" value="{{ $index + 1 }}" required readonly>
                                        <input type="text" name="cedula[]" class="autocomplete-cedula form-control col-2"
                                            value="{{ $firmante->cedula }}" required>
                                        <input type="hidden" name="idfirmante[]" value="{{ $firmante->id }}"
                                            class="codigo_id form-control col-1" required>
                                        <input type="text" name="nombre[]" class="autocomplete-nombre form-control col-3"
                                            value="{{ $firmante->nombre }}" required>
                                        <input type="text" name="telefono[]" class="form-control col-2"
                                            value="{{ $firmante->telefono }}">
                                        <input type="text" name="email[]" class="form-control col-2"
                                            value="{{ $firmante->email }}">
                                        <button class="btn-remove btn btn-outline-danger ml-2" type="button"><i
                                                class="fa fa-trash" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button onclick="addNewItem()" class="btn btn-primary mt-2" type="button">Agregar Ítem</button>

                        <hr>

                        <div class="row">
                            <div class="form-group col-md-12">
                                <a class="btn btn-danger" style="float: right;"
                                    href="{{ route('mesaentrada.index') }}">Cancelar</a>
                                <x-adminlte-button class="btn-group" style="float: right;" type="submit" label="Actualizar"
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
        document.querySelectorAll('.btn-remove').forEach(btn => {
            btn.addEventListener('click', function() {
                const itemToRemove = btn.closest('.item'); // Obtiene el contenedor más cercano
                removeItem(itemToRemove);
            });
        });
        itemn = {{ count($firmantes) }};
        const itemsContainer = document.getElementById('items');

        // Función para agregar un nuevo ítem de compra
        function addNewItem() {
            itemn++;
            const newItem = document.createElement("div");
            newItem.classList.add("item");

            newItem.innerHTML = `
            <div class="row ml-1">
                <input type="number" name="item[]" class="codigo_id form-control col-2" placeholder="Código" value="${itemn}" required readonly>
                <input type="text" name="cedula[]" class="autocomplete-cedula form-control col-2" value="" required>
                <input type="hidden" name="idfirmante[]" value="0" class="codigo_id form-control col-1" required>
                <input type="text" name="nombre[]" class="autocomplete-nombre form-control col-3" required>
                <input type="text" name="telefono[]" step="any" class="form-control col-2" required>
                <input type="text" name="email[]" class="form-control col-2">
                <button class="btn-remove btn btn-outline-danger ml-2" type="button"><i class="fa fa-trash" aria-hidden="true"></i></button>
            </div>
        `;

            const codigoInput = newItem.querySelector('input[name="cedula[]"]');
            codigoInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const cantidadInput = newItem.querySelector('input[name="nombre[]"]');
                    cantidadInput.focus();
                }
            });

            const cantidadInput = newItem.querySelector('input[name="nombre[]"]');
            cantidadInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const descripcionInput = newItem.querySelector('input[name="telefono[]"]');
                    descripcionInput.focus();
                }
            });

            const descripcionInput = newItem.querySelector('input[name="telefono[]"]');
            descripcionInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const precioInput = newItem.querySelector('input[name="email[]"]');
                    precioInput.focus();
                    precioInput.select();
                }
            });

            const emailInput = newItem.querySelector('input[name="email[]"]');
            emailInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addNewItem();
                    const precioInput = newItem.querySelector('input[name="cedula[]"]');
                }
            });

            itemsContainer.appendChild(newItem);

            const btnRemove = newItem.querySelector(".btn-remove");
            btnRemove.addEventListener("click", function() {
                console.log("Eliminando item:", newItem);
                removeItem(newItem);
            });

            $('input[name="cedula[]"]').focus();
        }

        function removeItem(itemToRemove) {
            if (itemToRemove) {
                itemsContainer.removeChild(itemToRemove);
                itemn--; // Decrementa si es necesario
            }
        }

        $(document).on('focus', '.autocomplete-nombre', function() {
            $(this).autocomplete({
                minLength: 0,
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('obtenerfirmante') }}",
                        contentType: "application/json; charset=utf-8",
                        data: {
                            term: request.term,
                        },
                        success: function(data) {
                            response(data);
                        },
                        error: function(result) {
                            response([{
                                label: 'No se encontraron resultados.',
                                value: ''
                            }]);
                        },
                    });
                },
                select: function(event, ui) {
                    var name = ui.item.value;
                    $(this).val(name);
                    $(this).siblings('.autocomplete-cedula').val(ui.item.cedula);
                    $(this).siblings('.autocomplete-idfirmante').val(ui.item.id);
                    $(this).siblings('.autocomplete-telefono').val(ui.item.telefono);
                    $(this).siblings('.autocomplete-email').val(ui.item.email);
                    return false;
                },
            });
        });

        $(document).on('focus', '.autocomplete-cedula', function() {
            $(this).autocomplete({
                minLength: 0,
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('obtenerfirmante') }}",
                        contentType: "application/json; charset=utf-8",
                        data: {
                            term: request.term,
                        },
                        success: function(data) {
                            response(data);
                        },
                        error: function(result) {
                            response([{
                                label: 'No se encontraron resultados.',
                                value: ''
                            }]);
                        },
                    });
                },
                select: function(event, ui) {
                    var cedula = ui.item.value;
                    $(this).val(cedula);
                    $(this).siblings('.autocomplete-nombre').val(ui.item.nombre);
                    $(this).siblings('.autocomplete-idfirmante').val(ui.item.id);
                    $(this).siblings('.autocomplete-telefono').val(ui.item.telefono);
                    $(this).siblings('.autocomplete-email').val(ui.item.email);
                    return false;
                },
            });
        });
    </script>
@endpush
