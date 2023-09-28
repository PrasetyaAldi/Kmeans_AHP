<nav class="navbar navbar-expand-lg" style="background-color: #fff">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/">
            <span class="fw-bold text-primary">SKR<span
                    style="background: linear-gradient(120deg, var(--bs-primary) 50%, var(--bs-info) 50%);-webkit-background-clip: text;-webkit-text-fill-color: transparent;">I</span><span
                    style="color: var(--bs-info)">PSI</span></span>
            <img src="{{ asset('assets/img/logo.jpg') }}" style="width:40px" alt="logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
            aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item"><a class="nav-link{{ request()->is('home') ? ' active fw-semibold' : '' }}"
                            href="{{ route('home.index') }}"><i class="fa-solid fa-house"></i> Home</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle{{ request()->is('k-means') || request()->is('cluster') || request()->is('k-means/*') || request()->is('optimasi-cluster') || request()->is('presentase') ? ' active fw-semibold' : '' }}"
                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-database"></i> Data
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item {{ request()->is('k-means') ? ' active' : '' }}"
                                    href="{{ route('k-means.index') }}">Data</a></li>
                            <li><a class="dropdown-item {{ request()->is('k-means/transformation') ? ' active' : '' }}"
                                    href="{{ route('k-means.transformation') }}">Transformasi</a></li>
                            <li><a class="dropdown-item {{ request()->is('k-means/normalization') ? ' active' : '' }}"
                                    href="{{ route('k-means.normalization') }}">Normalisasi</a></li>
                            <li><a class="dropdown-item {{ request()->is('optimasi-cluster') ? ' active' : '' }}"
                                    href="{{ route('optimasi-cluster') }}">Optimasi Cluster</a></li>
                            <li><a class="dropdown-item {{ request()->is('cluster') ? ' active' : '' }}"
                                    href="{{ route('cluster') }}">Cluster</a></li>
                            <li><a class="dropdown-item {{ request()->is('presentase') ? ' active' : '' }}"
                                    href="{{ route('presentase') }}">Presentase Cluster</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle{{ request()->is('ahps') || request()->is('ahps/*') ? ' active fw-semibold' : '' }}"
                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-bookmark"></i> Rekomendasi
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item{{ request()->is('ahps') ? ' active' : '' }}"
                                    href="{{ route('ahps.index') }}">Bobot Kriteria</a></li>
                            <li><a class="dropdown-item{{ request()->is('ahps/weight-alternatif') ? ' active' : '' }}"
                                    href="{{ route('ahps.weight-alternatif') }}">Bobot Alternatif</a>
                            </li>
                            <li><a class="dropdown-item{{ request()->is('ahps/final-calculate') ? ' active' : '' }}"
                                    href="{{ route('ahps.final-calculate') }}">Peringkat</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('logout') }}"><i
                                class="fa-solid fa-right-from-bracket"></i></a></li>
                @endauth
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}"><i
                                class="fa-solid fa-right-to-bracket"></i> Login</a></li>
                @endguest
            </div>
        </div>
    </div>
</nav>
