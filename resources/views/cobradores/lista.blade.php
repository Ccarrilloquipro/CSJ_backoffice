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

                    <div class="alert alert-info">
                        Lista
                    </div>

                    <div class="card">
                        <div class="card-body p-0">

                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Nombres</th>
                                    <th>Paterno</th>
                                    <th>Materno</th>
                                    <th>id Persona en San Juan</th>
                                    <th>Activo</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($arrCobradores as $cobrador)
                                    <tr>
                                        <td>{{ $cobrador->nombre }}</td>
                                        <td>{{ $cobrador->paterno }}</td>
                                        <td>{{ $cobrador->materno }}</td>
                                        <td>{{ $cobrador->idPersona }}</td>
                                        <td>{{ $cobrador->activo }}</td>
                                        <td><a href="{{ route('cobrador.ficha',[$cobrador->id]) }}" class="btn btn-xs bg-success">Ficha</a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer clearfix">
{{--                            {{ $users->links() }}--}}
                        </div>
                    </div>

                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection