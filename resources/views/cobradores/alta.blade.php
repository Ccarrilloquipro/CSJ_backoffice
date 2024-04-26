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
<br>
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

							@if($errors->any())

								{{ implode('', $errors->all('<div>:message</div>')) }}
							@endif
{{--							@if(isset($errors) )--}}
{{--								<div class="row">--}}
{{--									<div class="col-3">&nbsp;</div>--}}
{{--									<div class="col-6">--}}
{{--										<div class="alert alert-warning alert-dismissible fade show" role="alert">--}}
{{--											<button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
{{--												<span aria-hidden="true">&times;</span>--}}
{{--											</button>--}}
{{--											<div class="row">--}}
{{--												<div class="col-12">--}}
{{--													<h5 class="text-sm-left">--}}
{{--														Los siguientes pagos ya han sido incluidos en anteriores archivos.--}}
{{--													</h5>--}}
{{--												</div>--}}
{{--											</div>--}}
{{--											@for($x=0;$x<count($errores);$x++)--}}
{{--												<div class="row">--}}
{{--													<div class="col-2">&nbsp;</div>--}}
{{--													<div class="col-10">--}}
{{--														{{$errores[$x]}}--}}
{{--													</div>--}}
{{--												</div>--}}
{{--											@endfor--}}
{{--											<div class="row">--}}
{{--												<div class="col-12">--}}
{{--													<h5 class="text-sm-left">--}}
{{--														¿Desea incluirlos en este archivo?--}}
{{--													</h5>--}}
{{--												</div>--}}
{{--											</div>--}}
{{--											<div class="row">--}}
{{--												<div class="col-6">--}}
{{--													<button type="button" class="btn btn-xs btn-danger" data-dismiss="alert" aria-label="Close">--}}
{{--														<span aria-hidden="true">Cancelar</span>--}}
{{--													</button>--}}
{{--												</div>--}}
{{--												<div class="col-6">--}}
{{--													<button type="submit"  name="accion" class="btn btn-xs btn-success" value="excelValidado" >--}}
{{--														Generar excel--}}
{{--													</button>--}}
{{--												</div>--}}
{{--											</div>--}}
{{--										</div>--}}
{{--									</div>--}}
{{--									<div class="col-3">&nbsp;</div>--}}
{{--								</div>--}}
{{--							@endif--}}

							<div class="row">
								{{-- nombres--}}
								<div class="col-4">
									<div class="form-group ">
										<label for="exampleInputBorder">Nombres:</label>
										<input type="text" class="form-control form-control-sm " name="nombres" >
										@if ($errors->has('nombres'))
											<span class="text-danger">{{ $errors->first('nombres') }}</span>
										@endif
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