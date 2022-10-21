@extends('layouts.global')
@section('title')Detail Peraturan @endsection
@section ('content')

<hr class="my-3">
	<div class="row mb-3">
	<div class="col-md-12 text-right">
		<a href="{{route('peraturan.show_pdf',$peraturan->id)}}" class="btn btn-primary" target="_blank">Print</a>
	</div>
</div>

<div class="col-md-12">
<div class="card">
<div class="card-body">

	<div class="text-center">
	<div class="rounded mx-auto d-block">
		<small>{{Auth::user()->name}} - {{Auth::user()->email}} - {{$time}}</small><br>
	</div>
	</div>
	<div class="text-left">
		<div class="rounded mx-auto d-block">
		<b>{{$peraturan->uraian}}</b>
	</div>
	</div>
	<div class="row wrapper">
	<div class="col-sm-12 mt-3">
		{!! $peraturan->pdf !!}	
	</div>
</div>
	
</div>
</div>
</div>
</div>
@endsection