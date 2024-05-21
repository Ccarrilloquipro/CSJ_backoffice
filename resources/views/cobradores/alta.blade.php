@php
	$menuIdCobradores = session()->get('menuIdCobradores');
	$cobrador = session()->get('formularioCobrador');
@endphp

@extends('layouts.app')

@section('content')
	<!-- Main content -->
	<br>
	<div class="content">
		<div class="container-fluid">
{{--			@dd($cobrador)--}}
			<div class="row">
				<div class="col-lg-12">
					<form method="post" action="{{ route('cobrador.grabar') }}">
						<input type="hidden" name="id" value="nuevo">
						<input type="hidden" name="idUser" value="nuevo">
						<input type="hidden" name="idTipoUsuario" value="2">
						<div class="card bg-light mb-3">
							<div class="card-header p-3">
								<h4 class="m-0">Nuevo cobrador</h4>
							</div>
							<!-- /.card-header -->
							<div class="card-body p-4">
								<div class="row">
									{{-- nombres--}}
									<div class="col-4">
										<div class="form-group ">
											<label for="exampleInputBorder">Nombres:</label>
											<input type="text" class="form-control form-control-sm " name="nombres" value="{{$cobrador['nombre']}}">
											@if ($errors->has('nombres'))
												<span class="text-danger">{{ $errors->first('nombres') }}</span>
											@endif
										</div>
									</div>
									{{-- paterno--}}
									<div class="col-4">
										<div class="form-group ">
											<label for="exampleInputBorder">Apellido paterno:</label>
											<input type="text" class="form-control form-control-sm " name="paterno" value="{{$cobrador['paterno']}}">
											@if ($errors->has('paterno'))
												<span class="text-danger">{{ $errors->first('paterno') }}</span>
											@endif
										</div>
									</div>
									{{-- materno--}}
									<div class="col-4">
										<div class="form-group ">
											<label for="exampleInputBorder">Apellido materno:</label>
											<input type="text" class="form-control form-control-sm " name="materno"  value="{{$cobrador['materno']}}">
										</div>
									</div>
								</div>
								<div class="row">
									{{-- correo--}}
									<div class="col-4">
										<div class="form-group ">
											<label for="exampleInputBorder">Correo:</label>
											<input type="text" class="form-control form-control-sm " name="email"  value="{{$cobrador['email']}}">
											@if ($errors->has('email'))
												<span class="text-danger">{{ $errors->first('email') }}</span>
											@endif
										</div>
									</div>
									{{-- idPersona--}}
									<div class="col-4">
										<div class="form-group ">
											<label for="exampleInputBorder">Id persona San Juan:</label>
											<input type="text" class="form-control form-control-sm " name="idPersona"  value="{{$cobrador['idPersona']}}">
											@if ($errors->has('idPersona'))
												<span class="text-danger">{{ $errors->first('idPersona') }}</span>
											@endif
										</div>
									</div>
								</div>
								<div class="row">
									{{-- usuario--}}
									<div class="col-4">
										<div class="form-group ">
											<label for="exampleInputBorder">Usuario:</label>
											<input type="text" class="form-control form-control-sm " name="usuario"  value="{{$cobrador['usuario']}}">
											@if ($errors->has('usuario'))
												<span class="text-danger">{{ $errors->first('usuario') }}</span>
											@endif
										</div>
									</div>
									<div class="col-2">
										<div class="form-group ">
											<label for="exampleInputBorder">Password:</label>
											<input type="text" class="form-control form-control-sm " name="password" value="{{$cobrador['clave']}}">
											@if ($errors->has('password'))
												<span class="text-danger">{{ $errors->first('password') }}</span>
											@endif
										</div>
									</div>
									<div class="col-1">
										&nbsp;
									</div>
								</div>
								<div class="row">

									{{-- boton grabar--}}
									<div class="col-1">
										<button type="submit" name="accion" class="btn btn-xs bg-success" value="" >Grabar</button>
										@csrf
									</div>
								</div>
								<br>
								<div class="row">
									<div class="col-8">
										<table class="table">
											<tr>
												<th>Id en sistema San Juan</th>
												<th>Nombre</th>
											</tr>
											<tbody>
											@foreach($menuIdCobradores as $item )
												<tr>
													<td>{{ $item['id'] }}</td>
													<td>{{ $item['nombre'] }}</td>
												</tr>
											@endforeach
											<tr>

											</tr>
											</tbody>
										</table>
									</div>
								</div>
								<hr>
							</div>
						</div>
					</form>
				</div>
				<div class="col-lg-4">

				</div>
			</div>
			<!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
	<!-- /.content -->
@endsection
