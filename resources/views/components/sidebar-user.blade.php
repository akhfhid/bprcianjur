<div>

	<li><a href="/home"><span class="oi oi-home"></span>Home </a></li>
	<li>
	<a href="{{route('staff.profile')}}"><span class="oi oi-people"></span>Profile</a>
	
	<li><a href="{{route('staff.cuti')}}"><span class="oi oi-inbox"></span>Permohonan Cuti</a></li>
	<li><a href="{{route('staff.peraturan')}}"><span class="oi oi-book"></span>Peraturan</a></li>
	@if(auth()->user()->loguser =='YA')
	<li><a href="{{route('Loguser.index')}}"><span class="oi oi-inbox"></span>Log Akses</a></li>
	@endif					
</div>