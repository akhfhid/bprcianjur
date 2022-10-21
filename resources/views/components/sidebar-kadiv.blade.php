<div>
   <li><a href="/home"><span class="oi oi-home"></span>Home </a></li>
	<li>
	<!-- 
	<li><a href="{{route('kadiv.pegawairotasi')}}"><span class="oi oi-inbox"></span>Permohonan Rotasi Pegawai</a></li> -->
	<a href="{{route('kadiv.profile')}}"><span class="oi oi-people"></span>Profile</a>
	</li>
	<li>
	<a href="{{route('kadiv.indexpegawai')}}"><span class="oi oi-people"></span>Data Pegawai</a>
	</li>
	
	<li><a href="{{route('kadiv.cutikadiv')}}"><span class="oi oi-inbox"></span>Permohonan Cuti</a></li>
	<li><a href="{{route('kadiv.cutiindex')}}"><span class="oi oi-inbox"></span>Otorisasi Cuti Pegawai</a></li>
	
	<li><a href="{{route('kadiv.peraturan')}}"><span class="oi oi-book"></span>Peraturan</a></li>
	@if(auth()->user()->loguser =='YA')
	<li><a href="{{route('Loguser.index')}}"><span class="oi oi-inbox"></span>Log Akses</a></li>
	@endif
						
</div>