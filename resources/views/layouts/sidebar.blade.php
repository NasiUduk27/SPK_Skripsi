<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 " id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="{{ route('home') }}">
        <span class="ms-1 font-weight-bold">DSS VIKOR</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link {{ Request::routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-home text-dark text-gradient text-lg"></i>
                </div>
                <span class="nav-link-text ms-1">Dashboard</span>
            </a>
        </li>

        <li class="nav-item mt-3">
            <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Alur Kerja</h6>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::routeIs('kriteria.*') ? 'active' : '' }}" href="{{ route('kriteria.index') }}">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                     <i class="fas fa-tasks text-dark text-gradient text-lg"></i>
                </div>
                <span class="nav-link-text ms-1">1. Kelola Kriteria</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::routeIs('alternatif.*') ? 'active' : '' }}" href="{{ route('alternatif.index') }}">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-list-ol text-dark text-gradient text-lg"></i>
                </div>
                <span class="nav-link-text ms-1">2. Kelola Alternatif</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::routeIs('vikor.pilih') ? 'active' : '' }}" href="{{ route('vikor.pilih') }}">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-calculator text-dark text-gradient text-lg"></i>
                </div>
                <span class="nav-link-text ms-1">3. Mulai Perhitungan</span>
            </a>
        </li>

      </ul>
    </div>
</aside>
