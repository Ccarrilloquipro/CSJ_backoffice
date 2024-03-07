@extends('layouts.app')

@section('content')

{{--@dd($menuCobradores)--}}
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Pagos') }}</h1>
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
                    <div class="alert alert-warning">
                        Filtros
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body p-0">
{{--                            <form method="post" action="{{ route('pago.submitLista') }}">--}}
                                <div class="row">
                                    <div class="col-2 text-sm-left">
                                        <div class="form-group text-sm-left">
                                            <div class="text-sm-left">Fecha inicial: (dd-mm-yyy)</div>
                                            <input type="text" class="form-control form-control-sm " name="fechaInicial"  value="">
                                        </div>
                                    </div>
                                    <div class="col-2 text-sm-left">
                                        <div class="form-group text-sm-left">
                                            <div class="text-sm-left">Fecha inicial: (dd-mm-yyy)</div>
                                            <input type="text" class="form-control form-control-sm " name="fechaInicial"  value="">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group ">
                                            <div class="text-sm-left">Cliente</div>
                                            <input type="text" class="form-control form-control-sm " name="materno"  value="">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <div class="text-sm-left">Cobrador</div>
                                            <select class="custom-select rounded-0 input-xs"  name="cobrador">
                                                @foreach($menuCobradores as $cobrador )
                                                    <option class="input-sm" value="{{ $cobrador->id }}">{{ $cobrador->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

{{--                            </form>--}}
                        </div>
                    </div>
                </div>
            </div>






            {{--segunda seccion datos--}}
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
                        <div class="card-body p-0">
                            <form method="post" action="{{ route('pago.submitLista') }}">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th style="horiz-align: left;vertical-align: bottom">
                                        <a href="{{ route('pago.marcarTodos') }}">
                                            <img src="{{ asset('images/chBoxMarcada.png') }}" alt="" style="opacity: .8" width="25px"></a>
                                         /
                                        <a href="{{ route('pago.quitarTodos') }}">
                                            <img src="{{ asset('images/chBoxNoMarcada.png') }}" alt="" style="opacity: .8" width="22px"></a>
                                    </th>
                                    <th>Cuenta</th>
                                    <th>Cobrador</th>
                                    <th>Cliente</th>
                                    <th>Fecha de abono</th>
                                    <th>Cobro</th>
                                    <th>Archivo</th>
                                    <th>
                                        <button type="submit"  name="accion" class="btn btn-outline-light" value="excel" >
                                            <img src="{{ asset('images/excel.jpg') }}" alt="" style="opacity: .8" width="25px" border="0">
                                        </button>
                                        @csrf
                                    </th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($arrPagos as $pago)
                                    @php
                                     $nombreChBx= "chBx_".$pago->id;
                                    $marcado = ($pago->marcado == 1) ? ' checked ' : "";
                                    @endphp
                                    <tr>
                                        <td style="align-content: center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="{{$nombreChBx}}" value="1" {{$marcado}}>
                                                <label class="form-check-label"></label>
                                            </div>
                                        </td>
                                        <td>{{ $pago->claveCuenta }}</td>
                                        <td>{{ $pago->nombreCobrador }}</td>
                                        <td>{{ $pago->nombreCliente }}</td>
                                        <td>{{ $pago->fechaDePago }}</td>
                                        <td>{{ $pago->montoCobradoEnVisita }}</td>
                                        <td>{{ $pago->archivo }}</td>
                                        <td>
                                            <button type="submit" name="accion" class="btn btn-xs bg-success" value="{{$pago->id}}" >
                                                Ficha
                                            </button>
{{--                                            <a href="{{ route('pago.ficha',[$pago->id]) }}" class="btn btn-xs bg-success">Ficha</a>--}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            </form>
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
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>--}}

{{--<script type={"text/javascript"}>--}}
{{--    $(function(){--}}
{{--    $('#datetimepicker').datetimepicker()});--}}
{{--</script>--}}
    <!-- /.content -->
@endsection