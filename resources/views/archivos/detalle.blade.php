@extends('layouts.app')

@section('content')
{{--        @dd($pagos)--}}
    <!-- Main content -->
    <br>
    <div class="content">
        <div class="container-fluid">
            {{--primera seccion datos--}}
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-info">
                        Relación de pagos en archivo {{$pagos[0]->archivo}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Cuenta</th>
                                    <th>Cobrador</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Cobro</th>
                                    <th>Recibo</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $numero=0;
                                @endphp
                                @foreach($pagos as $pago)
                                    @php
                                       $numero++;
                                    @endphp
                                    <tr>
                                        <td>{{ $numero }}</td>
                                        <td>{{ $pago->claveCuenta }}</td>
                                        <td>{{ $pago->nombreCobrador }}</td>
                                        <td>{{ $pago->nombreCliente }}</td>
                                        <td>{{ $pago->fechaDePago }}</td>
                                        <td>{{ $pago->montoCobradoEnVisita }}</td>
                                        <td>{{ $pago->cobrado }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection





