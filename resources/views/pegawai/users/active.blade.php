@extends('layouts.global')
@section('title') Aktive User @endsection
@section('content')

<div class="col-md-8">
		@if(session('status'))
		<div class="alert alert-success">
			{{session('status')}}
		</div>
		@endif

<form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('users.update',[$user->id])}}" method="POST">
	@csrf
	<input type="hidden" value="PUT" name="_method">
	<label for="name">Nama</label>
	<input 
		value="{{$user->username}}"
		class="form-control"
		placeholder="Full Name" 
		type="text"
		name="name"
		disabled="disabled" 
		id="name"/>
	<input type="hidden" name="pegawai_id" value="{{$pegawai->id}}">
	<br>
	<label for="username">Alamat Email</label>
	<input 
		value="{{$user->email}}"
		disabled="disabled"
		class="form-control"
		placeholder="email" 
		type="text" 
		name="email"
		id="email"/>
	<br>
	<label for="password">Password</label>
		<input 
			class="form-control" 
			placeholder="password" 
			type="password" 
			name="password"
			id="password"/>
		<br>
		<label for="password_confirmation">Konfirmasi Password</label>
		<input 
			class="form-control" 
			placeholder="password confirmation" 
			type="password" 
			name="password_confirmation"
			id="password_confirmation"/>
		<br>
		<label for="">Roles</label>
		<br>
		<SELECT name="roles" class="form-control">
			@foreach ($roles as $role => $name )
			<option value="{{$name}}">{{$role}}</option>
			@endforeach
		</SELECT>

		<br>
		<input type="hidden" name="status" value="ACTIVE">
		<br>
		<input 
			class="btn btn-primary" 
			type="submit"
			value="Save">
			
	</form>
</div>


@endsection