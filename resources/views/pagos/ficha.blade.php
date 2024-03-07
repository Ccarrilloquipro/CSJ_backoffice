@extends('layouts.app')

@section('content')
{{--        @dd($pago)--}}

    <!-- Content Header (Page header) -->
    {{--    <div class="content-header">--}}
    {{--        <div class="container-fluid">--}}
    {{--            <div class="row mb-2">--}}
    {{--                <div class="col-sm-6">--}}
    {{--                    <h1 class="m-0">{{ $cliente->nombre }}</h1>--}}
    {{--                </div><!-- /.col -->--}}
    {{--            </div><!-- /.row -->--}}
    {{--        </div><!-- /.container-fluid -->--}}
    {{--    </div>--}}
    <br/>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <a href="{{ route('pago.regresarLista',[$pago->id]) }}" class="">
                        <img src="{{ asset('images/flechaIzq.jpg') }}" alt="" width="15px">
                    </a>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-6">
                    <div class="card bg-light mb-3">
                        <div class="card-header p-3">
                            <h4 class="m-0">Datos de pago</h4>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body p-2">
                            <div class="row">
                                <div class="col-6 font-weight-bold">
                                    Cobrador
                                </div>
                                <div class="col-6 font-weight-normal">
                                    {{$pago->nombreCobrador}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 font-weight-bold">
                                    Fecha de registro
                                </div>
                                <div class="col-6 font-weight-normal">
                                    {{$pago->fechaRegistroH}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 font-weight-bold">
                                    Cliente
                                </div>
                                <div class="col-6 font-weight-normal">
                                    {{$pago->nombreCliente}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 font-weight-bold">
                                    Cuenta
                                </div>
                                <div class="col-6 font-weight-normal">
                                    {{$pago->claveCuenta}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 font-weight-bold">
                                    Monto cobrado / acordado
                                </div>
                                <div class="col-6 font-weight-normal">
                                    ${{number_format($pago->montoCobradoEnVisita,2,'.',',')}} / ${{number_format($pago->montoAbonoAcordado,2,'.',',')}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 font-weight-bold">
                                    Fecha siguiente pago
                                </div>
                                <div class="col-6 font-weight-normal">
                                    {{$pago->fechaSiguientePagoH}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 font-weight-bold">
                                    Dirección
                                </div>
                                <div class="col-6 font-weight-normal">
                                    {{$pago->calle}} {{$pago->noExt}} {{$pago->noInt}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 font-weight-bold">
                                    &nbsp;
                                </div>
                                <div class="col-6 font-weight-normal">
                                    {{$pago->colonia}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 font-weight-bold">
                                    &nbsp;
                                </div>
                                <div class="col-6 font-weight-normal">
                                    {{$pago->delegacion}} {{$pago->cp}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 font-weight-bold">
                                    &nbsp;
                                </div>
                                <div class="col-6 font-weight-normal">
                                    {{$pago->municipio}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 font-weight-bold">
                                    Notas
                                </div>
                                <div class="col-6 font-weight-normal">
                                    {{$pago->nota}}
                                </div>
                            </div>
                           {{-- <div class="col-2">
                                <div class="form-group ">
                                    <label for="exampleInputBorder">Fecha de registro:</label>
                                    <input type="text" class="form-control form-control-sm " name="nombres" value="{{$pago->fechaRegistro}}">
                                </div>
                            </div>

                            <div class="col-2">
                                <div class="form-group ">
                                    <label for="exampleInputBorder">Apellido paterno:</label>
                                    <input type="text" class="form-control form-control-sm " name="paterno" value="{{$pago->paterno}}">
                                </div>
                            </div>

                            <div class="col-2">
                                <div class="form-group ">
                                    <label for="exampleInputBorder">Apellido materno:</label>
                                    <input type="text" class="form-control form-control-sm " name="materno"  value="{{$pago->materno}}">
                                </div>
                            </div>


                            <div class="col-2">
                                <div class="form-group ">
                                    <label for="exampleInputBorder">Id persona:</label>
                                    <input type="text" class="form-control form-control-sm " name="materno"  value="{{$pago->idPersona}}">
                                </div>
                            </div>

                            <div class="col-2">
                                <div class="form-group ">
                                    <label for="exampleInputBorder">Activo:</label>
                                    <input type="text" class="form-control form-control-sm " name="materno"  value="{{$pago->activo}}">
                                </div>
                            </div>

                             boton grabar
                            <div class="col-2">
                                <a href="{{ route('cobrador.update',[$pago->id]) }}" class="btn btn-xs bg-success">Grabar</a>

                            </div>--}}
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <form method="post" action="{{ route('pago.updateProximaFecha') }}">
                        <input type="hidden" name="id" value="{{$pago->id}}">
                        <div class="card bg-light mb-3">
                            <div class="card-header p-3">
                                <h4 class="m-0">Modificaciones</h4>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-2">
                                <div class="row">
                                    {{--<div class="col-6 font-weight-bold">
                                        Cobrador
                                    </div>
                                    <div class="col-6 font-weight-normal">
                                        {{$pago->nombreCobrador}}
                                    </div>--}}
                                    <div class="col-12">
                                        <div class="form-group ">
                                            <label for="nuevaFecha">Fecha siguiente pago:</label>
                                            <input type="text" class="form-control form-control-sm " name="nuevaFecha"  value="{{$pago->fechaSiguientePagoH}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-xs bg-success" value="Update" >Actualizar</button>
                                        @csrf
{{--                                        <a href="{{ route('pago.updateProximaFecha',[$pago->id]) }}" class="btn btn-xs bg-success">Grabar</a>--}}
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

