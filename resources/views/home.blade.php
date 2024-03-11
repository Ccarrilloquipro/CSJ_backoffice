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
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection