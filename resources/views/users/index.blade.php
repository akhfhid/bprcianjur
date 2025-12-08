@extends("layouts.global")
@section("title") Users List @endsection
@section("content")


<div class="row">
	<div class="col-md-6">
		<form action="{{route('users.index')}}">
			<div class="row">
				<div class="col-md-6">
				<input
					value="{{Request::get('keyword')}}"
					type="text"
					name="keyword"
					class="form-control"
					placeholder="Filter Berdasarkan Nama">
				</div>
				<div class="col-md-6">
					<input {{Request::get('status')=='ACTIVE' ? 'checked' :''}}
						value="ACTIVE"
						name="status"
						type="radio"
						class="form-control"
						id="active">
						<label for="active">Active</label>
					<input {{Request::get('status')=='INACTIVE' ? 'checked' :''}}
						value="INACTIVE"
						name="status"
						type="radio"
						class="form-control"
						id="inactive">
						<label for="inactive">Inactive</label>
						<input type="submit" value="Filter" class="btn btn-primary">
					</div>
				</div>
			</form>
		</div>
	</div>
<br>
@if(session('status'))
<div class="alert alert-success">
	{{session('status')}}
</div>
@endif
	<div class="row">
		<div class="col-md-12 text-right">
			<a href="{{route('users.create')}}" class="btn btn-primary"> Create User</a>
		</div>
	</div>
	<br>
<table class="table table-bordered">
	<thead>
		<tr style="text-align: center;">
			<th><b>User ID</b></th>
			<th><b>Username</b></th>
			<th><b>Pegawai ID</b></th>
			<th><b>Email</b></th>
			<th><b>Avatar</b></th>
			<th><b>Status</b></th>
			<th><b>Action</b></th>
		</tr>
	</thead>
	<tbody >
		@foreach($users as $user)
		<tr style="text-align: center;">
			<td>{{$user->id}}</td>
			<td>{{$user->username}}</td>
			<td>{{$user->pegawai_id}}</td>						
			<td>{{$user->email}}</td>
			<td>
				@if($user->avatar)
					<img src="{{asset('storage/'.$user->avatar)}}" width="70px">
				@else
					N/A
				@endif
			</td>
			<td>
				@if($user->status == "ACTIVE")
				<span class="badge badge-success">
					{{$user->status}}
				</span>
				@else
				<span class="badge bade-danger">
					{{$user->status}}
				</span>
				@endif
			</td>
			<td>
				<form
					onsubmit="return confirm('Delete this user permanently')"
					class="d-inline"
					action="{{route('users.destroy',[$user->id])}}"
					method="POST">
					@csrf
					<input type="hidden" name="_method" value="DELETE">
					<input type="submit" value="Delete" class="btn btn-danger btn-sm">
					</form>
				<a href="{{route('users.show',[$user->id])}}" class="btn btn-primary btn-sm">Detail</a>
                <a href="{{route('users.edit',[$user->id])}}" class="btn btn-info btn-sm"> Edit </a>
                @if($user->status =="INACTIVE")
				<a href="{{route('users.active',[$user->id])}}" class="btn btn-success btn-sm">Aktivasi</a>
				@endif
			</td>
		</tr>
		@endforeach
    </tbody>
		<tfoot>
			<tr>
				<td colspan="10">
					{{$users->appends(Request::all())->links()}}
			</tr>
		</tfoot>

</table>

@endsection
