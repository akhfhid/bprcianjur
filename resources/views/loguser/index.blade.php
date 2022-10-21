@extends('layouts.global')
@section('title') List Log Pegawai @endsection

@section('content')
<!--<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">-->
<link rel="stylesheet" href="{{asset('DataTables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('DataTables/Buttons-1.5.6/css/buttons.bootstrap4.min.css')}}">

  <!--Pencarian Tanggal 
   
   <div class="table-responsive">
    <br />
    <div class="row">
     <div class="input-daterange">
      <div class="col-md-4">
       <input type="text" name="start_date" id="start_date" class="form-control" />
      </div>
      <div class="col-md-4">
       <input type="text" name="end_date" id="end_date" class="form-control" />
      </div>      
     </div>
     <div class="col-md-4">
      <input type="button" name="search" id="search" value="Search" class="btn btn-info" />
     </div>
    </div>
    <br />
    Akhir Pencarian Tanggal -->

		<table class="table table-bordered table-stripped" id="table_log">
			<thead>
				<tr>
					<th><b>Nama Pegawai</b></th>
					<th><b>Jenis</b></th>
					<th><b>Keterangan</b></th>
					<th><b>Waktu Akses</b></th>
				</tr>
			</thead>
		</table>
	

<!--<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>-->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
    <script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>

    <!-- Datatables -->
    <script src="{{asset('DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('DataTables/DataTables-1.10.18/js/dataTables.bootstrap4.min.js')}}"></script>

    <script src="{{asset('DataTables/Buttons-1.5.6/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('DataTables/Buttons-1.5.6/js/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{asset('DataTables/JSZip-2.5.0/jszip.min.js')}}"></script>
    <script src="{{asset('DataTables/pdfmake-0.1.36/vfs_fonts.js')}}"></script>
    <script src="{{asset('DataTables/Buttons-1.5.6/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('DataTables/Buttons-1.5.6/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('DataTables/Buttons-1.5.6/js/buttons.colVis.min.js')}}"></script>
    
<script type="text/javascript">
	
 
$(document).ready(function(){
   var table = $('#table_log').dataTable({

        "buttons": [ 'copy','print', 'excel', 'colvis' ],
        "dom": 
        "<'row'<'col-md-3'l><'col-md-5'B><'col-md-4'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-5'i><'col-md-7'p>>",
        "lengthMenu":[
                     [25,50,100,-1],
                    [25,50,100,"All"]
                ],

        "processing": true,
        "serverSide": true,
        "ajax": {
        "url": "/Loguser",
        "type" : 'GET'
          },
          "columns": [
          //{data: 'DT_Row_Index', name:'DT_Row_Index' },
           {data: "nampeg"},
           {data: "jenis"},
           {data: "keterangan"},
           {data: "created_at"

       }],
       order : [
                  [3,'desc']
       ]
    });

    table.buttons().container()
                .appendTo( '#table_wrapper .col-md-5:eq(0)' );
});

</script>


@endsection