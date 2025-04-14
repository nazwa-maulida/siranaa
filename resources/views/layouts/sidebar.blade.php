<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
    @php
    $role = Auth::user()->role; // Pastikan user sudah login
@endphp

<li class="nav-item">
    @if ($role === 'admin')
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="icon-grid menu-icon"></i>
            <span class="menu-title">Dashboard Admin</span>
        </a>
    @elseif ($role === 'mitra')
        <a class="nav-link" href="{{ route('mitra.dashboard') }}">
            <i class="icon-grid menu-icon"></i>
            <span class="menu-title">Dashboard Mitra</span>
        </a>
    @elseif ($role === 'perusahaan')
        <a class="nav-link" href="{{ route('perusahaan.dashboard') }}">
            <i class="icon-grid menu-icon"></i>
            <span class="menu-title">Dashboard Perusahaan</span>
        </a>
    @else
        <a class="nav-link" href="#">
            <i class="icon-grid menu-icon"></i>
            <span class="menu-title">Dashboard Tidak Tersedia</span>
        </a>
    @endif
</li>


<li class="nav-item">
    <a class="nav-link">
        <i class="icon-grid-2 menu-icon"></i>
        <span class="menu-title">Forms</span>
    </a>
    <ul class="nav flex-column">
        @if(auth()->user()->role === 'mitra')
            <li class="nav-item flex-column sub-menu">
                <a class="nav-link" href="{{ route('proyeks.index') }}">Proyek</a>
            </li>
        @elseif(auth()->user()->role === 'perusahaan')
           
            <li class="nav-item">
                <a class="nav-link" href="{{ route('surveys.index') }}">Survey</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('rekomendasis.index') }}">Rekomendasi</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('realisasi_proyeks.index') }}">Realisasi Proyek</a>
            </li>
        @endif
    </ul>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('logout') }}"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="ti-power-off menu-icon"></i>
        <span class="menu-title">Logout</span>
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</li>
    </ul>
</nav>
