@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">PERFILES</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">


                    <a href="{{ url('users') }}" class="btn btn-outline-secondary btn-sm btn-block">GESTIÓN DE USUARIOS</a>
                    <a href="{{ url('roles') }}" class="btn btn-outline-secondary btn-sm btn-block">GESTIÓN DE ROLES</a>
                    {{-- <a href="{{ url('permissions') }}" class="btn btn-outline-secondary btn-sm btn-block">GESTIÓN DE PERMISOS</a> --}}

                </div>
            </div>
        </div>
    </div>
@stop
