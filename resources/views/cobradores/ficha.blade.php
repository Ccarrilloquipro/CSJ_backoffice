@extends('layouts.app')

@section('content')
{{--    @dd($cliente)--}}

    <!-- Content Header (Page header) -->

    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
            @dd($cobrador)
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-12">
                    <form method="post" action="{{ route('cobrador.update') }}">
                        <input type="hidden" name="id" value="{{$cobrador->id}}">
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

                                    {{-- idPersona--}}
                                    <div class="col-2">
                                        <div class="form-group ">
                                            <label for="exampleInputBorder">Id persona:</label>
                                            <input type="text" class="form-control form-control-sm " name="materno"  value="{{$cobrador->idPersona}}">
                                        </div>
                                    </div>
                                    {{-- idPersona--}}
                                    <div class="col-2">
                                        <div class="form-group ">
                                            <label for="exampleInputBorder">Activo:</label>
                                            <input type="text" class="form-control form-control-sm " name="materno"  value="{{$cobrador->activo}}">
                                        </div>
                                    </div>

                                    {{-- boton grabar--}}
                                    <div class="col-2">
                                            <a href="{{ route('cobrador.update',[$cobrador->id]) }}" class="btn btn-xs bg-success">Grabar</a>
                                    </div>
                                </div>


















                                {{-- titulos--}}
{{--                                <div class="row">--}}
{{--                                    <div class="col-3">--}}
{{--                                        <label for="exampleInputBorder">Nombres</label>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-3">--}}
{{--                                        <label for="exampleInputBorder">Paterno</label>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-3">--}}
{{--                                        <label for="exampleInputBorder">PaternoMaterno</label>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-3">--}}
{{--                                        <label for="exampleInputBorder">id Persona</label>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                                {{-- datos--}}
{{--                                <div class="row">--}}
{{--                                    <div class="col-3">--}}
{{--                                        {{ $cobrador->nombre}}--}}
{{--                                    </div>--}}
{{--                                    <div class="col-3">--}}
{{--                                        {{ $cobrador->paterno}}--}}
{{--                                    </div>--}}
{{--                                    <div class="col-3">--}}
{{--                                        {{ $cobrador->materno}}--}}
{{--                                    </div>--}}
{{--                                    <div class="col-3">--}}
{{--                                        {{ $cobrador->idPersona}}--}}
{{--                                    </div>--}}
{{--                                </div>--}}
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
