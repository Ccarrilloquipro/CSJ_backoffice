@extends('layouts.app')

@section('content')
{{--    @dd($menuIdCobradores)--}}

    <!-- Content Header (Page header) -->

    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
{{--            @dd($cobrador)--}}
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-12">
                    <form method="post" action="{{ route('cobrador.grabar') }}">
                        <input type="hidden" name="id" value="{{$cobrador->id}}">
                        <input type="hidden" name="idUser" value="{{$cobrador->idUser}}">
                        <input type="hidden" name="idTipoUsuario" value="2">
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
                                            <label for="exampleInputBorder">Nombres:</label>
                                            <input type="text" class="form-control form-control-sm " name="nombres" value="{{$cobrador->nombre}}">
                                        </div>
                                    </div>
                                    {{-- paterno--}}
                                    <div class="col-2">
                                        <div class="form-group ">
                                            <label for="exampleInputBorder">Apellido paterno:</label>
                                            <input type="text" class="form-control form-control-sm " name="paterno" value="{{$cobrador->paterno}}">
                                        </div>
                                    </div>
                                    {{-- materno--}}
                                    <div class="col-2">
                                        <div class="form-group ">
                                            <label for="exampleInputBorder">Apellido materno:</label>
                                            <input type="text" class="form-control form-control-sm " name="materno"  value="{{$cobrador->materno}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    {{-- correo--}}
                                    <div class="col-2">
                                        <div class="form-group ">
                                            <label for="exampleInputBorder">Correo:</label>
                                            <input type="text" class="form-control form-control-sm " name="email"  value="{{$cobrador->email}}">
                                        </div>
                                    </div>
                                    {{-- idPersona--}}
                                    <div class="col-2">
                                        <div class="form-group ">
                                            <label for="exampleInputBorder">Id persona San Juan:</label>
                                            <input type="text" class="form-control form-control-sm " name="idPersona"  value="{{$cobrador->idPersona}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    {{-- usuario--}}
                                    <div class="col-2">
                                        <div class="form-group ">
                                            <label for="exampleInputBorder">Usuario:</label>
                                            <input type="text" class="form-control form-control-sm " name="usuario"  value="{{$cobrador->usuario}}">
                                        </div>
                                    </div>
                                    {{-- password--}}
                                    @php if($cobrador->id == null) { @endphp
                                    <div class="col-2">
                                        <div class="form-group ">
                                            <label for="exampleInputBorder">Password:</label>
                                            <input type="text" class="form-control form-control-sm " name="password" value="">
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        &nbsp;
                                    </div>
                                    @php } else { @endphp
                                    {{-- activo--}}
                                    <div class="col-2">
                                        <div class="form-group ">
                                            <label for="exampleInputBorder">Cambiar password:</label>
                                            <input type="text" class="form-control form-control-sm " name="password" value="">
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="activo" value="1" {{ ($cobrador->activo == 1 ? 'checked=1' : '') }} >
                                                <label class="form-check-label">Activo</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"  name="activo"  value="0" {{ ($cobrador->activo == 0 ? 'checked=0' : '') }} >
                                                <label class="form-check-label">Inactivo</label>
                                            </div>
                                        </div>
                                    </div>
                                    @php } @endphp
                                </div>
                                <div class="row">

                                    {{-- boton grabar--}}
                                    <div class="col-1">
                                        <button type="submit" name="accion" class="btn btn-xs bg-success" value="{{ $cobrador->id }}" >Grabar</button>
                                        @csrf
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <table class="table">
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Id en sistema San Juan</th>
                                            </tr>
                                            <tbody>
                                            @foreach($menuIdCobradores as $item )
                                            <tr>
                                                <td>{{ $item['id'] }}</td>
                                                <td>{{ $item['nombre'] }}</td>
                                            </tr>
                                            @endforeach
                                            <tr>

                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4">

                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection
