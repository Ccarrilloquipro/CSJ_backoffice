@extends('layouts.app')

@section('content')
    {{--    @dd($arrCobradores)--}}
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Cobradores') }}</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-header  text-white  bg-gradient-olive">
                            <h3 class="card-title">Archivos</h3>
                            {{--                            <div class="card-tools">--}}
                            {{--                                <button type="button" class="btn btn-tool" data-card-widget="collapse" >--}}
                            {{--                                    <i class="fas fa-plus"></i>--}}
                            {{--                                </button>--}}
                            {{--                            </div>--}}
                        </div>


                        <div class="card-body p-0">
{{--                            <table class="table">--}}
{{--                                <thead>--}}
{{--                                <tr>--}}
{{--                                    <th>Nombres</th>--}}
{{--                                    <th>Paterno</th>--}}
{{--                                    <th>Materno</th>--}}
{{--                                    <th>id Persona en San Juan</th>--}}
{{--                                    <th>Activo</th>--}}
{{--                                    <th>--}}
{{--                                        <a href="{{ route('cobrador.nuevo') }}" class="btn btn-xs bg-success">Agregar</a>--}}
{{--                                    </th>--}}
{{--                                </tr>--}}
{{--                                </thead>--}}
{{--                                <tbody>--}}
{{--                                @foreach($arrCobradores as $cobrador)--}}
{{--                                    <tr>--}}
{{--                                        <td>{{ $cobrador->nombre }}</td>--}}
{{--                                        <td>{{ $cobrador->paterno }}</td>--}}
{{--                                        <td>{{ $cobrador->materno }}</td>--}}
{{--                                        <td>{{ $cobrador->idPersona }}</td>--}}
{{--                                        <td>{{ $cobrador->activo }}</td>--}}
{{--                                        <td><a href="{{ route('cobrador.ficha',[$cobrador->id]) }}" class="btn btn-xs bg-success">Ficha</a></td>--}}
{{--                                    </tr>--}}
{{--                                @endforeach--}}
{{--                                </tbody>--}}
{{--                            </table>--}}
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
