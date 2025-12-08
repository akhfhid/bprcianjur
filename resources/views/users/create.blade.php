@extends("layouts.global")
@section("title") Create New User @endsection
@section("content")


<div class="col-md-8">
	@if(session('status'))
		<div class="alert alert-success">
			{{session('status')}}
		</div>
		@endif

	<form
		enctype="multipart/form-data"
		class="bg-white shadow-sm p-3"
		action="{{route('users.store')}}"
		method="POST">
		@csrf

		<label for="name">Nama</label>
		<input
			Class="form-control"
			placeholder="Full Name"
			type="text"
			name="name"
			id="name"/>
		<br>
		<label for="name">Username</label>
		<input
			Class="form-control"
			placeholder="Username"
			type="text"
			name="username"
			id="username"/>
		<br>
		<label for="">Roles</label>
		<br>
		<SELECT name="roles" class="form-control">
			@foreach ($roles as $role => $name )
			<option value="{{$name}}">{{$role}}</option>
			@endforeach
		</SELECT>

		<br>
		<br>
		<label for="phone">Nomor Handphone</label>
		<br>
		<input
			type="text"
			name="phone"
			class="form-control">
		<br>
		<label for="address">Alamat</label>
		<textarea
			name="address"
			id="address"
			class="form-control"></textarea>
		<br>
		<label for="avatar">Avatar Image</label>
		<br>
		<input
			id="avatar"
			type="file"
			name="avatar"
			class="form-control">
		<hr class="my-3">

		<label for="email">Email</label>
		<input
			class="form-control"
			type="text"
			name="email"
			placeholder="user@mail.com"
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
        <label>Akses Log User</label>
        <select class="form-control" name="log">
            <option value="TIDAK">Tidak</option>
            <option value="YA">Ya</option>

        </select>
		<input
			class="btn btn-primary"
			type="submit"
			value="Save">
	</form>
</div>
</div>
</div>

@endsection
