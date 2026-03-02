@extends('layouts.global')
@section('title') Data Peraturan @endsection

@section('content')

<link rel="stylesheet" href="{{asset('DataTables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css')}}">

<div class="container-fluid">

```
@if(session('status'))
    <div class="alert alert-success">
        {{session('status')}}
    </div>
@endif

<!-- CARD PILIH KATEGORI -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <h3 class="mb-2">Manajemen Peraturan</h3>
                <p class="text-muted mb-4">Silakan pilih kategori peraturan terlebih dahulu</p>

                <button class="btn btn-primary btn-lg mr-3 kategoriBtn" data-kategori="internal">
                     Peraturan Internal
                </button>

                <button class="btn btn-success btn-lg kategoriBtn" data-kategori="external">
                     Peraturan External
                </button>
            </div>
        </div>
    </div>
</div>
<div class="row mb-4" id="statistikRow" style="display:none;">

    <div class="col-md-3">
        <div class="card bg-primary text-white shadow">
            <div class="card-body">
                <h6>Total Peraturan</h6>
                <h3 id="statTotal">0</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-info text-white shadow">
            <div class="card-body">
                <h6>Total SK</h6>
                <h3 id="statSK">0</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white shadow">
            <div class="card-body">
                <h6>Total SE</h6>
                <h3 id="statSE">0</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-dark text-white shadow">
            <div class="card-body">
                <h6>Tahun {{date('Y')}}</h6>
                <h3 id="statTahun">0</h3>
            </div>
        </div>
    </div>

</div>
<div id="tableWrapper" style="display:none;">

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <h5 class="mb-0">Filter Jenis Surat</h5>
                </div>
                <div class="col-md-3">
                    <select id="filterJenis" class="form-control">
                        <option value="all">Semua</option>
                        <option value="SK">Surat Keputusan (SK)</option>
                        <option value="SE">Surat Edaran (SE)</option>
                    </select>
                </div>

                <div class="col-md-6 text-right">
                    <a href="{{route('peraturan.create')}}" class="btn btn-dark">
                        + Tambah Peraturan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-bordered table-striped" id="atur">
        <thead class="thead-dark">
            <tr align="center">
                <th>Nama Peraturan</th>
                <th>Nomor SK</th>
                <th>Tanggal SK</th>
                <th width="200">Action</th>
            </tr>
        </thead>
    </table>

</div>
```

</div>

<!-- MODAL DELETE -->

<div id="confirmModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                Apakah anda yakin akan menghapus data ini?
            </div>
            <div class="modal-footer">
                <button id="ok_button" class="btn btn-danger">Hapus</button>
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('js/jquery-3.5.1.js')}}"></script>

<script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>

<script src="{{asset('DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js')}}"></script>

<script src="{{asset('DataTables/DataTables-1.10.18/js/dataTables.bootstrap4.min.js')}}"></script>

<script>
	function loadStatistik(){
    $.get('/peraturan/statistik', function(res){
        $('#statistikRow').fadeIn();
        $('#statTotal').text(res.total);
        $('#statSK').text(res.sk);
        $('#statSE').text(res.se);
        $('#statTahun').text(res.tahun_ini);
    });
}
let kategoriDipilih = null;
let jenisDipilih = 'all';
let table = null;

$('.kategoriBtn').click(function(){
    kategoriDipilih = $(this).data('kategori');
    $('#tableWrapper').fadeIn();
	loadStatistik();
    loadTable();
});

$('#filterJenis').change(function(){
    jenisDipilih = $(this).val();
    table.ajax.reload();
});

function loadTable(){
    if(table !== null){
        table.destroy();
    }

    table = $('#atur').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/peraturan",
            data: function(d){
                d.kategori = kategoriDipilih;
                d.jenis_surat = jenisDipilih;
            }
        },
        columns: [
            {data: "name"},
            {data: "nosk"},
            {data: "tglsk"},
            {data: "action", orderable:false, searchable:false}
        ],
        order:[[2,'desc']]
    });
}

let atur_id;
$(document).on('click','.delete',function(){
    atur_id = $(this).attr('id');
    $('#confirmModal').modal('show');
});

$('#ok_button').click(function(){
    $.ajax({
        url:"peraturan/destroy/"+atur_id,
        beforeSend:function(){
            $('#ok_button').text('Deleting...');
        },
        success:function(){
            $('#confirmModal').modal('hide');
            table.ajax.reload();
            alert('Data Deleted');
        }
    });
});
</script>

@endsection
