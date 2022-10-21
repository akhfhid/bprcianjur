@extends('layouts.global')
@section('title') Edit User @endsection
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
		value="{{$user->name}}"
		class="form-control"
		placeholder="Full Name" 
		type="text"
		name="name"
		id="name"/>
	<br>
	<label for="username">Username</label>
	<input 
		value="{{$user->username}}"
		disabled
		class="form-control"
		placeholder="username" 
		type="text" 
		name="username"
		id="username"/>
	<br>
	<label for="">Status</label>
	<br>
	<input 
		value="ACTIVE"
		type="radio"
		class="form-control" 
		name="status"
		id="active"
		{{$user->status == "ACTIVE" ? "checked" : ""}}>
		<label for="active">Active</label>

	<input 
		value="INACTIVE"
		type="radio"
		class="form-control" 
		name="status"
		id="active"
		{{$user->status == "INACTIVE" ? "checked" : ""}}>
		<label for="inactive">Inactive</label>
		<br><br>

	<label for="">Roles</label>
	<br>
	<input 
		type="checkbox" {{in_array("ADMIN",json_decode($user->roles)) ? "checked" : ""}}
		name="roles[]"
		id="ADMIN"
		value="ADMIN">
		<label for="ADMIN">Administrator</label>

	<input 
		type="checkbox" {{in_array("SUPERVISOR",json_decode($user->roles)) ? "checked" : ""}}
		name="roles[]"
		id="SUPERVISOR"
		value="SUPERVISOR">
		<label for="SUPERVISOR">Supervisor</label>

	<input 
		type="checkbox" {{in_array("USER",json_decode($user->roles)) ? "checked" : ""}}
		name="roles[]"
		id="USER"
		value="USER">
		<label for="USER">User</label>
	<br>
	<label for="phone">Nomor Handphone</label>
	<br>
	<input 
		type="text"
		name="phone"
		class="form-control" 
		value="{{$user->phone}}" 
		>
	<br>
	<label for="address">Address</label>
	<textarea
		name="address"
		id="address"
		class="form-control">{{$user->address}}</textarea>
	<br>
	<label for="avatar">Avatar Image</label>
	<br>
	current avatar: <br>
	@if($user->avatar)
		<img src="{{asset('storage/'.$user->avatar)}}" width="120px">
	<br>
	@else
		No Avatar
	@endif
	<br>
	<input 
		id="avatar" 
		type="file"
		name="avatar"
		class="form-control">
	<small class="text-muted"> Kosongkan jika tidak ingin mengubah avatar </small>

	<hr class="my-3">
	<label for="email">Email</label>
	<input 
		value="{{$user->email}}"
		disabled 
		class="form-control" 
		placeholder="user@mail.com" 
		type="text"
		name="email"
		id="email">
	<br>
	<input 
		class="btn btn-primary"
		type="submit"
		value="Save">
	</form>
</div>

@endsection