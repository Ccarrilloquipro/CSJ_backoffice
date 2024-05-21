@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->

    <!-- /.content-header -->

    <!-- Main content -->

    <div class="content">
{{--        @dd($administrador)--}}
        <div class="container-fluid">
            <br>
            <div class="row">
                <div class="col-lg-12">
                    <form method="post" action="{{ route('administrador.grabar') }}">
                        <input type="hidden" name="id" value="{{$administrador->id}}">
                        <input type="hidden" name="idTipoUsuario" value="1">
                        <div class="card bg-light mb-3">
                            <div class="card-header p-3">
                                <h4 class="m-0">Datos</h4>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-2">
                                <div class="row">
                                    {{-- nombres--}}
                                    <div class="col-2">
                                        <div class="form-group ">
                                            <label for="exampleInputBorder">Nombre:</label>
                                            <input type="text" class="form-control form-control-sm " name="name" value="{{$administrador->name}}">
                                            @if ($errors->has('name'))
                                                <span class="text-danger">{{ $errors->first('name') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- correo--}}
                                    <div class="col-2">
                                        <div class="form-group ">
                                            <label for="exampleInputBorder">correo:</label>
                                            <input type="text" class="form-control form-control-sm " name="email" value="{{$administrador->email}}">
                                            @if ($errors->has('email'))
                                                <span class="text-danger">{{ $errors->first('email') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- ususario--}}
                                    <div class="col-2">
                                        <div class="form-group ">
                                            <label for="exampleInputBorder">Usuario:</label>
                                            <input type="text" class="form-control form-control-sm " name="usuario"  value="{{$administrador->usuario}}">
                                            @if ($errors->has('usuario'))
                                                <span class="text-danger">{{ $errors->first('usuario') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- clave--}}
                                    @php if($administrador->id == null) { @endphp
                                    <div class="col-2">
                                        <div class="form-group ">
                                            <label for="exampleInputBorder">clave:</label>
                                            <input type="text" class="form-control form-control-sm " name="password" value="">
                                            @if ($errors->has('password'))
                                                <span class="text-danger">{{ $errors->first('password') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @php } @endphp
                                    {{-- activo--}}
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="activo" value="1" {{ ($administrador->activo == 1 ? 'checked=1' : '') }} >
                                                <label class="form-check-label">Activo</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"  name="activo"  value="0" {{ ($administrador->activo == 0 ? 'checked=0' : '') }} >
                                                <label class="form-check-label">Inactivo</label>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- boton grabar--}}
                                    <div class="col-2">
                                        <button type="submit" name="accion" class="btn btn-xs bg-success" value="{{ $administrador->id }}" >Grabar</button>
                                        @csrf
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection
