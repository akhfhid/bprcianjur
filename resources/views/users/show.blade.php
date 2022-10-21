@extends('layouts.global')
@section('title') Detail User @endsection
@section ('content')


<div class="col-md-8">
	<div class="card">
		<div class="card-body">
			<b>Nama :</b><br>
			{{$user->name}}
			<br><br>

			@if($user->avatar)
				<img src="{{asset('storage/'.$user->avatar)}}" width="128px"/>
			@else
				No Avatar
			@endif

			<br><br>
			<b>Username :</b><br>
			{{$user->username}}
			<br><br>
			<b>Email :</b>
			{{$user->email}}
			<br><br>
			<b>Nomor Handphone :</b>
			{{$user->phone}}
			<br><br>
			<b>Alamat :</b>
			{{$user->address}}
			<br><br>
			<b>Roles:</b><br>
			{{$user->roles}}
		</div>
	</div>
</div>


@endsection