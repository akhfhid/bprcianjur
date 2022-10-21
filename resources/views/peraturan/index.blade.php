@extends('layouts.global')
@section('title')Data Peraturan @endsection
@section ('content')
<link rel="stylesheet" href="{{asset('DataTables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css')}}">



<div class="row">
	
</div>
<br>
<div class="row">
	<div class="col-md-12">
		@if(session('status'))
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-warning">
					{{session('status')}}
				</div>
			</div>
		</div>
		@endif
		<div class="col-md-12 text-right">
		<ul class="nav nav-pills card-header-pills">
			<li class="nav-item">
				<a class="nav-link active" href="{{route('peraturan.index')}}">Published</a>
			</li>
			<li class="nav item">
				<a class="nav-link" href="{{route('peraturan.trash')}}">Trash</a>
			</li>
			<li class="nav item">
				<a class="nav-link" href="{{route('peraturan.create')}}">Tambah Peraturan</a>
			</li>
	</div>
	<hr class="my-3">
	
		<table class="table table-bordered table-stripped" id="atur">
			<thead>
				<tr align="center">
					<th><b>Nama Peraturan</b></th>
					<th><b>Nomor Surat Keputusan</b></th>
					<th><b>Tanggal Surat Keputusan</b></th>
					<th><b>Action</b></th>
				</tr>
			</thead>
		</table>

</div>
<div id="confirmModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
                <h4 align="center" style="margin:0;">Apakah anda yakin akan menghapus data ini?</h4>
            </div>
            <div class="modal-footer">
                <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="{{asset('js/jquery-3.5.1.js')}}"></script>
    <script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>

    <!-- Datatables -->
    <script src="{{asset('DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('DataTables/DataTables-1.10.18/js/dataTables.bootstrap4.min.js')}}"></script>

    <script type="text/javascript">
$(document).ready(function(){
 $('#atur').dataTable({
        "lengthMenu":[
                     [25,50,100,-1],
                    [25,50,100,"All"]
                ],

        "processing": true,
        "serverSide": true,
        "ajax": {
        "url": "/peraturan",
        "type" : 'GET'
          },
          "columns": [
          //{data: 'DT_Row_Index', name:'DT_Row_Index' },
           {data: "name"},
           {data: "nosk"},
           {data: "tglsk"},
           
           {data: 'action', orderable: false, searchable: false}
       ],
       order : [
                  [2,'desc']
       ]
    });

 var atur_id; 

	$(document).on('click', '.delete', function(){
		atur_id = $(this).attr('id');
		$('#confirmModal').modal('show');
	});

	$('#ok_button').click(function(){
		$.ajax({
			url:"peraturan/destroy/"+atur_id,
			beforeSend:function(){
				$('#ok_button').text('Deleting...');
			},
			success:function(data)
			{
				setTimeout(function(){
					$('#confirmModal').modal('hide');
					$('#atur').DataTable().ajax.reload();
					alert('Data Deleted');
				}, 200);
			}
		})
	});

});
    
    </script>


@endsection