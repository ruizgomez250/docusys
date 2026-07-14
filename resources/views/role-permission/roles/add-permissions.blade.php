@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-12">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <h1 class="m-0 text-dark">Role: {{ $role->name }}</h1>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary float-right">Regresar</a>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('roles.updatepermissionrole', $role->id) }}" method="post">
                                @csrf
                                @method('put')
                                <div class="mb-3">
                                    @error('permission')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <label for="">Permissions</label>
                                    <div class="row">
                                        @foreach ($permissions as $item)
                                            <div class="col-md-3 d-flex align-items-left justify-content-left">
                                                <label>
                                                    <input type="checkbox" name="permission[]" class="form-check-input"
                                                        value="{{ $item->name }}"
                                                        {{ in_array($item->id, $rolePermissions) ? 'checked' : '' }} />
                                                    {{ $item->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

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
