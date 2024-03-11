@extends('layouts.app')

@section('content')
    {{--    @dd($arrCobradores)--}}
    <!-- Content Header (Page header) -->
{{--    <div class="content-header">--}}
{{--        <div class="container-fluid">--}}
{{--            <div class="row mb-2">--}}
{{--                <div class="col-sm-6">--}}
{{--                    <h1 class="m-0">{{ __('Archivos') }}</h1>--}}
{{--                </div><!-- /.col -->--}}
{{--            </div><!-- /.row -->--}}
{{--        </div><!-- /.container-fluid -->--}}
{{--    </div>--}}
    <!-- /.content-header -->

    <!-- Main content -->
    <br>
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
                            <table class="table">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Fecha</th>
                                    <th>Archivo</th>
                                    <th>Generado por</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($archivos as $archivo)
                                    <tr>
                                        <td></td>
                                        <td>{{ $archivo->fechaCreacion }}</td>
                                        <td>{{ $archivo->archivo }}</td>
                                        <td>{{ $archivo->generador }}</td>
                                        <td><a href="{{ route('archivos.detalle',[$archivo->id]) }}" class="btn btn-xs bg-success">Detalles</a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->


                    </div>

                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection
