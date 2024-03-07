@extends('layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0">{{ __('Clientes') }}</h1>
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

{{--				<div class="alert alert-info">--}}
{{--					Alta--}}
{{--				</div>--}}

				<form method="post" action="{{ route('clientes.grabar') }}">
					<div class="card bg-light mb-3">
						<div class="card-header p-3">
							<h3>Alta</h3>
						</div>
						<!-- /.card-header -->

						<div class="card-body p-3">
							<div class="row">
								{{-- nombres--}}
								<div class="col-4">
									<div class="form-group ">
										<label for="exampleInputBorder">Nombres:</label>
										<input type="text" class="form-control form-control-sm " name="nombres" >
									</div>
								</div>

								{{-- paterno--}}
								<div class="col-4">
									<div class="form-group ">
										<label for="exampleInputBorder">Apellido paterno:</label>
										<input type="text" class="form-control form-control-sm " name="paterno" >
									</div>
								</div>

								{{-- materno--}}
								<div class="col-4">
									<div class="form-group ">
										<label for="exampleInputBorder">Apellido materno:</label>
										<input type="text" class="form-control form-control-sm " name="materno" >
									</div>
								</div>
							</div>
							<div class="row">
								{{-- fechaNacimiento--}}
								<div class="col-4">
									<div class="form-group ">
										<label for="exampleInputBorder">Fecha de nacimiento:</label>
										<input type="text" class="form-control form-control-sm " name="fechaNacimiento" >
									</div>
								</div>
								{{-- curp--}}
								<div class="col-4">
									<div class="form-group ">
										<label for="exampleInputBorder">CURP:</label>
										<input type="text" class="form-control form-control-sm " name="curp" >
									</div>
								</div>
								{{-- nss--}}
								<div class="col-4">
									<div class="form-group ">
										<label for="exampleInputBorder">NSS:</label>
										<input type="text" class="form-control form-control-sm " name="nss" >
									</div>
								</div>
							</div>
						</div>
						<!-- /.card-body -->

						<div class="card-footer clearfix">
							{{-- boton--}}
							<div class="col-12">
								<div class="form-group ">
									<label for="exampleInputBorder">&nbsp;</label>
									<input type="submit" class="btn bg-gradient-success btn-rounded waves-effect waves-light" value="Agregar">
									@csrf
								</div>
							</div>
						</div>
						<!-- /.card-footer -->
					</div>
				</form>
			</div>

		</div>
		<!-- /.row -->
	</div><!-- /.container-fluid -->
</div>
<!-- /.content -->
@endsection