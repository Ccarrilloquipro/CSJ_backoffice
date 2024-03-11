@extends('layouts.app')

@section('content')
    {{--    @dd($arrCobradores)--}}
    <!-- Content Header (Page header) -->

    <!-- /.content-header -->

    <!-- Main content -->
    <br>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header  text-white  bg-gradient-olive">
                            <h3 class="card-title">Administradores</h3>
                        </div>


                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Usuario</th>
                                    <th>Activo</th>
                                    <th>
                                        <a href="{{ route('administrador.nuevo') }}" class="btn btn-xs bg-success">Agregar</a>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($administradores as $administrador)
                                    @php
                                    $valorActivo = ($administrador->activo == 1) ? "Si" : "No";
                                    @endphp
                                    <tr>
                                        <td>{{ $administrador->name }}</td>
                                        <td>{{ $administrador->email }}</td>
                                        <td>{{ $administrador->usuario }}</td>
                                        <td>{{ $valorActivo }}</td>
                                        <td><a href="{{ route('administrador.ficha',[$administrador->id]) }}" class="btn btn-xs bg-success">Ficha</a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer clearfix">

                        </div>
                    </div>

                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection
