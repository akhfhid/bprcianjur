@extends('layouts.global')
@section('title')Detail Peraturan @endsection
@section ('content')

<hr class="my-3">
	<div class="row mb-3">
	<div class="col-md-12 text-right">
		<a href="{{route('supervisor.peraturan')}}" class="btn btn-primary btn-sm">Back</a>
	</div>
</div>

<div class="col-md-12">
<div class="card">
<div class="card-body">

	<div class="text-center">
	<div class="rounded mx-auto d-block">
		<small>{{Auth::user()->name}} - {{Auth::user()->email}}</small>
		{{$time}}
	</div>
	</div>
	<div class="row wrapper">
	<div class="col-sm-12 mt-3">
		{!! $peraturan->pdf !!}	
	</div>
</div>
	<div class="text-center">
	<div class="rounded mx-auto d-block">
		<small>{{Auth::user()->name}} - {{Auth::user()->email}}</small>
		{{$time}}
	</div>
	</div>
</div>
</div>
</div>
</div>
@endsection