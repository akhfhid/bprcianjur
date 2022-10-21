@extends('layouts.global')
@section('footer-scripts')
<link href="\assets\select2\dist\css\select2.min.css" rel="stylesheet" />
<script src="\assets\select2\dist\js\select2.min.js"></script>
<script>
			$('#cabang').select2({ajax: { url: '/ajax/cabang/search',
processResults: function(data){ return {results: data.map(function(item){return {id: item.id, text:item.name} })
					}
				}
			}
		});
	</script>
@endsection
@section('title') Penghasilan Pegawai @endsection

@section('content')

<div class="row">
	<div class="col-md-6">
		<form action="{{route('penghasilan.index')}}">
			<div class="input-group">

			<select class="form-control select2" name="cabang" id="cabang">
				<option value="cabang"></option>
			</select>
				<div class="input-group-append">
					<input type="submit" value="Filter" class="btn btn-primary">
				</div>
			</div>
		</form>
	</div>
</div>

<hr class="my-3">

<div class="row">
	<div class="col-md-12">
@if(session('status'))
<div class="alert alert-success">
{{session('status')}}
</div>
@endif

<table class="table table-bordered table-stripped">
			<thead>
				<tr style="width: 350px">
					<th hidden>id</th>
					<th style="width: 350px;"><b>Nama</b></th>
					<th style="width: 350px;"><b>Kantor</b></th>
					<th style="width: 350px;"><b>Jabatan</b></th>
					<th style="width: 350px;"><b>Gaji Pokok</b></th>
					<th style="width: 350px;"><b>Tunjangan Istri</b></th>
					<th style="width: 350px;"><b>Tunjangan Anak</b></th>
					<th style="width: 350px;"><b>Tunjangan Pangan</b></th>
					<th style="width: 350px;"><b>Tunjangan Kinerja</b></th>
					<th style="width: 350px;"><b>BPJS Ketenagakerjaan</b></th>
					<th style="width: 350px;"><b>BPJS Kesehatan</b></th>
					<th style="width: 350px;"><b>Tunjangan Pensiun</b></th>
					<th style="width: 350px;"><b>Tunjangan Pph</b></th>
					<th style="width: 350px;"><b>Tunjangan Jabatan</b></th>
					<th style="width: 350px;"><b>Tunjangan Fungsional</b></var></th>
					
					
					
					
					<th style="width: 350px;"><b>Total Penghasilan</b></th>
				</tr>
			</thead>
			<tbody>
				@foreach($datapenghasilan as $data)
				<tr>
					<td>{{$data['nama']}}</td>
					<td>{{$data['cabang']}}</td>
					<td>{{$data['jabatan']}}</td>
					<td>@currency($data['gapok'])</td>
					<td>@currency($data['tunis'])</td>
					<td>@currency($data['tunak'])</td>
					<td>@currency($data['tunpang'])	</td>
					<td>@currency($data['tuncab'])</td>
					<td>@currency($data['bpjstk'])</td>
					<td>@currency($data['bpjsks'])</td>
					<td>@currency($data['pensiun'])</td>
					<td>@currency($data['pph'])</td>
					<td>@currency($data['tunjab'])</td>
					<td>@currency($data['fungsi'])</td>			
					<td>@currency($data['total'])</td>			
					
				</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
				<td colspan="10">
					{{$pegawai->appends(Request::all())->links()}}
				</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
</div>









@endsection
