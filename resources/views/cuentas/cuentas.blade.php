@extends('layouts.app')

@section('content')
    {{--@dd($arrPagos)--}}
    <!-- Content Header (Page header) -->

    <!-- /.content-header -->

    <!-- Main content -->
    <br>
    <div class="content">
        <div class="container-fluid">
{{--            @dd($listaCuentas)--}}
            {{--primera seccion filtro--}}
            <div class="row">
                <div class="col-lg-12">
                    <div class="card  mb-3">
                        <div class="card-header  text-white bg-gradient-olive">
                            <h3 class="card-title">Filtros</h3>
                        </div>
                        <div class="card-body p-0 table-responsive p-0">
                            <form method="post" action="{{ route('cuentas.filtrar') }}">
                                <div class="row">
                                    <div class="col-8 text-sm-left">
                                        <table class="table table-hover text-nowrap">
                                            <thead>
                                            <tr>
                                                <th>Cobrador</th>
                                                <th>Cuenta</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <select class="custom-select rounded-0 input-xs form-control-sm"  name="cobrador">
                                                        @foreach($menuCobradores as $cobrador )
                                                            <option value="{{ $cobrador['id'] }}" >{{ $cobrador['nombre'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm " name="cuenta"  value=""></td>
                                                <td>
                                                    <button type="submit"  name="accion" class="btn btn-xs btn-success" value="buscar" >
                                                        Buscar
                                                    </button>
                                                    @csrf
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {{--segunda seccion datos--}}
            @if(isset($error) )
                <div class="row">
                    <div class="col-3">&nbsp;</div>
                    <div class="col-6">
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-sm-left">
                                        {{ $error }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">&nbsp;</div>
                </div>
            @endif
            @if(isset($listaCuentas))
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert alert-info">
                            Relación de pagos
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body p-0 table-responsive p-0">
                                <form method="post" action="{{ route('cuentas.actualizarFecha') }}">
                                    <table class="table table-hover text-nowrap">
                                        <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Cliente</th>
                                            <th>Cuenta</th>
                                            <th>Clave cuenta</th>
                                            <th>No. contrato</th>
                                            <th>Fecha venta</th>
                                            <th>Monto total</th>
                                            <th>Abono acordado</th>
                                            <th>Fecha proximo pago</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $numero=0;
                                        @endphp

                                        @foreach($listaCuentas as $cuenta)
                                            @php
                                                $nombreCuenta = "proximoPago_".$cuenta->idCuenta;
                                            @endphp
                                        <tr>
                                            <td>{{ $numero }}</td>
                                            <td>{{ $cuenta->nombreCliente }}</td>
                                            <td>{{ $cuenta->idCuenta }}</td>
                                            <td>{{ $cuenta->claveCuenta }}</td>
                                            <td>{{ $cuenta->idContrato }}</td>
                                            <td>{{ $cuenta->fechaVenta }}</td>
                                            <td>{{ $cuenta->montoTotal }}</td>
                                            <td>{{ $cuenta->montoAbonoAcordado }}</td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm " name="{{ $nombreCuenta }}"  value="{{ $cuenta->fechaProximoPago }}">
                                            </td>
                                            <td>
                                                <button type="submit" name="accion" class="btn btn-xs bg-success" value="{{$cuenta->idCuenta}}" >
                                                    Actualizar
                                                </button>@csrf
{{--                                                                                            <a href="{{ route('pago.ficha',[$pago->id]) }}" class="btn btn-xs bg-success">Ficha</a>--}}
                                            </td>
                                        </tr>
                                    @endforeach

                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
            @endif
@endsection

