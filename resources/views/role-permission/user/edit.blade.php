@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-12">
            <h1 class="m-0 text-dark">Editar Usuario</h1>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary float-right">Regresar</a>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('users.update', $user) }}" method="post">
                                @csrf
                                @method('put')
                                <div class="mb-3">
                                    <label for="">Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="">Email</label>
                                    <input type="text" name="email" readonly class="form-control"
                                        value="{{ $user->email }}">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="">Password</label>
                                    <input type="text" name="password" class="form-control">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="">Roles</label>
                                    <select name="roles[]" class="form-control" multiple>                                        
                                        @foreach ($roles as $item)
                                            <option value="{{ $item }}"
                                                {{ in_array($item, $userRoles) ? 'selected' : '' }}>{{ $item }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('roles')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary  float-right">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
