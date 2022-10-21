<div>

	<li><a href="/home"><span class="oi oi-home"></span>Home </a></li>
	<!--<li>
		<li><a href="{{route('supervisor.pegawairotasi')}}"><span class="oi oi-inbox"></span>Permohonan Rotasi Pegawai</a></li>-->
		<li>
	<a href="{{route('supervisor.profile')}}"><span class="oi oi-people"></span>Profile</a>
	</li>
	<li>
	<a href="{{route('supervisor.indexpegawai')}}"><span class="oi oi-people"></span>Data Pegawai</a>
	</li>
	
	<li><a href="{{route('supervisor.cutisupervisor')}}"><span class="oi oi-inbox"></span>Permohonan Cuti</a></li>
	
	<li><a href="{{route('supervisor.cutiindex')}}"><span class="oi oi-inbox"></span>Otorisasi Cuti Pegawai</a></li>
	<li><a href="{{route('supervisor.peraturan')}}"><span class="oi oi-book"></span>Peraturan</a></li>
	@if(auth()->user()->loguser =='YA')
	<li><a href="{{route('Loguser.index')}}"><span class="oi oi-inbox"></span>Log Akses</a></li>
	@endif					
</div>