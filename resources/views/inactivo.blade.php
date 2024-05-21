@extends('layouts.guest')

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
        <div class="container-fluid">
            <div class="row">
                <div class="col-2"></div>
                <div class="col-8">
                    <h5>El usuario logeado esta inactivo. Favor de comunicarse con el administrador.</h5>
                </div>
                <div class="col-2"></div>

            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="{{ route('logout') }}" class="dropdown-item"
               onclick="event.preventDefault(); this.closest('form').submit();">
                <i class="mr-2 fas fa-sign-out-alt"></i>
                {{ __('Salir') }}
            </a>
        </form>


        <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection
