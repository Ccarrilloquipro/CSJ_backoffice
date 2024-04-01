@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
{{--    <div class="content-header">--}}
{{--        <div class="container-fluid">--}}
{{--            <div class="row mb-2">--}}
{{--                <div class="col-sm-6">--}}
{{--                    <h1 class="m-0">{{ __('jom') }}</h1>--}}
{{--                </div><!-- /.col -->--}}
{{--            </div><!-- /.row -->--}}
{{--        </div><!-- /.container-fluid -->--}}
{{--    </div>--}}
    <!-- /.content-header -->

    <!-- Main content -->
    <br>
    <div class="content">
{{--        @dd($arrCobradores)--}}
        <div class="container-fluid">
            <div class="row">
                @foreach($arrCobradores as $cobrador)
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header  text-white  bg-gradient-olive">
                            <h3 class="card-title"> {{ $cobrador->cobrador }}</h3>
                        </div>
                        <div class="card-body">
                           <div class="row">
                               <div class="col-6">Cobros realizados</div>
                               <div class="col-6 text-right">{{ $cobrador->cuantos }}</div>
                           </div>
                            <div class="row">
                                <div class="col-6">Monto cobrado</div>
                                <div class="col-6 text-right">{{ number_format($cobrador->monto,2,'.',',') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <!-- /.row -->
{{--            <div class="row">--}}
{{--                <div class="col-12">--}}
{{--                    <form method="post" action="{{ route('pagos.traerCobros') }}">--}}
{{--                        <input type="hidden" name="idCobrador" value="45449">--}}
{{--                        <button type="submit" name="accion" class="btn btn-xs bg-success" value="" >Cobros</button>--}}
{{--                        @csrf--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="row">--}}
{{--                <div class="col-12">--}}
{{--                    <form method="post" action="{{ route('pagos.traerFoto') }}">--}}
{{--                        <input type="hidden" name="idCliente" value="23117">--}}
{{--                        <button type="submit" name="accion" class="btn btn-xs bg-success" value="" >Foto</button>--}}
{{--                        @csrf--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="row">--}}
{{--                <div class="col-12">--}}
{{--                    <form method="post" action="{{ route('pagos.traerOrden') }}">--}}
{{--                        <input type="hidden" name="idCuenta" value="9569">--}}
{{--                        <button type="submit" name="accion" class="btn btn-xs bg-success" value="" >orden</button>--}}
{{--                        @csrf--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="row">--}}
{{--                <div class="col-12">--}}
{{--                    <form method="post" action="{{ route('pagos.buscarCobros') }}">--}}
{{--                        <input type="hidden" name="nombre" value="aquino">--}}
{{--                        <input type="hidden" name="direccion" value="">--}}
{{--                        <input type="hidden" name="cuenta" value="9635">--}}
{{--                        <button type="submit" name="accion" class="btn btn-xs bg-success" value="" >buscar</button>--}}
{{--                        @csrf--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection