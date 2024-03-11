@extends('layouts.app')

@section('content')

{{--@dd($menuCobradores)--}}
    <!-- Content Header (Page header) -->
{{--    <div class="content-header">--}}
{{--        <div class="container-fluid">--}}
{{--            <div class="row mb-2">--}}
{{--                <div class="col-sm-6">--}}
{{--                    <h1 class="m-0">{{ __('Pagos') }}</h1>--}}
{{--                </div><!-- /.col -->--}}
{{--            </div><!-- /.row -->--}}
{{--        </div><!-- /.container-fluid -->--}}
{{--    </div>--}}
    <!-- /.content-header -->

    <!-- Main content -->
    <br>
    <div class="content">
        <div class="container-fluid">
            {{--primera seccion filtro--}}
            <div class="row">
                <div class="col-lg-12">
                    <div class="card  mb-3">
                        <div class="card-header  text-white bg-gradient-olive">
                            <h3 class="card-title">Filtros</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" >
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <form method="post" action="{{ route('pago.filtrarLista') }}">
                                <div class="row">
                                    <div class="col-12 text-sm-left">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Fecha inicial:</th>
                                                    <th>Fecha final:</th>
                                                    <th>Cliente</th>
                                                    <th>Cobrador</th>
                                                    <th>En archivo</th>
                                                    <th>Archivo</th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td><input type="text" class="form-control form-control-sm " name="fechaInicial"  value="{{ $arrFiltros['fechaInicial'] }}"></td>
                                                <td><input type="text" class="form-control form-control-sm " name="fechaFinal"  value="{{ $arrFiltros['fechaFinal'] }}"></td>
                                                <td><input type="text" class="form-control form-control-sm " name="cliente"  value="{{ $arrFiltros['cliente'] }}"></td>
                                                <td>
                                                    <select class="custom-select rounded-0 input-xs form-control-sm"  name="cobrador">
                                                        @foreach($menuCobradores as $cobrador )
                                                            @if($cobrador['id'] == $arrFiltros['cobrador'])
                                                                <option value="{{ $cobrador['id'] }}" selected>{{ $cobrador['nombre'] }}</option>
                                                            @else
                                                                <option value="{{ $cobrador['id'] }}">{{ $cobrador['nombre'] }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </td>

                                                <td>
                                                    <select class="custom-select rounded-0 input-xs"  name="enArchivo">
                                                        @foreach($menuEnArchivo as $enArchivo )
                                                            @if($enArchivo['id'] == $arrFiltros['enExcel'])
                                                                <option class="input-sm" value="{{ $enArchivo['id']}}" selected>{{ $enArchivo['nombre'] }}</option>
                                                            @else
                                                                <option class="input-sm" value="{{ $enArchivo['id']}}">{{ $enArchivo['nombre'] }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm " name="archivo"  value=""></td>
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
                                    <th>No</th>
                                    <th>Cuenta</th>
                                    <th>Cobrador</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Cobro</th>
                                    <th>Recibo</th>
                                    <th>En archivo</th>
{{--                                    <th style="width: 30px">Archivo</th>--}}
                                    <th>
                                        <button type="submit"  name="accion" class="btn btn-outline-light" value="excel" >
                                            <img src="{{ asset('images/excel.jpg') }}" alt="" style="opacity: .8" width="25px" border="0">
                                        </button>
                                        @csrf
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                $numero=0;
                                @endphp
                                @foreach($arrPagos as $pago)
                                    @php
                                     $nombreChBx= "chBx_".$pago->id;
                                    $marcado = ($pago->marcado == 1) ? ' checked ' : "";
									$enArchivo = ($pago->enExcel == 1) ? 'Si' : "No";
									$numero++;
                                    @endphp
                                    <tr>
                                        <td style="align-content: center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="{{$nombreChBx}}" value="1" {{$marcado}}>
                                                <label class="form-check-label"></label>
                                            </div>
                                        </td>
                                        <td>{{ $numero }}</td>
                                        <td>{{ $pago->claveCuenta }}</td>
                                        <td>{{ $pago->nombreCobrador }}</td>
                                        <td>{{ $pago->nombreCliente }}</td>
                                        <td>{{ $pago->fechaDePago }}</td>
                                        <td>{{ $pago->montoCobradoEnVisita }}</td>
                                        <td>{{ $pago->cobrado }}</td>
                                        <td>{{ $enArchivo }}</td>
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
    <!-- /.content -->
@endsection