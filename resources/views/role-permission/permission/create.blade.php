@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-12">
            <h1 class="m-0 text-dark">Permisos</h1>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ url('permissions/index') }}" class="btn btn-secondary float-right">Regresar</a>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('permissions') }}" method="post">
                                @csrf
                                @method('post')
                                <div class="mb-3">
                                    <label for="">Permission Name</label>
                                    <input type="text" name="name" id="name" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
