@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-12">
            <h1 class="m-0 text-dark">Editar Rol</h1>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary float-right">Regresar</a>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('roles.update',$role) }}" method="post">
                                @csrf
                                @method('put')
                                <div class="mb-3">
                                    <label for="">Rol Name</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{$role->name}}">
                                </div>
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
