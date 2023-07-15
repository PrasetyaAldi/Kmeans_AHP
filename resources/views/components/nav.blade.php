<nav class="navbar navbar-expand-lg" style="background-color: #fff">
    <div class="container">
        <a class="navbar-brand" href="#">
            <strong>SKRIPSI</strong>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
            aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('home.index') }}">Home</a></li>
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Data
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('k-means.index') }}">Data</a></li>
                            <li><a class="dropdown-item" href="{{ route('cluster') }}">Cluster</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Rekomendasi
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('ahps.index') }}">Bobot Kriteria</a></li>
                            <li><a class="dropdown-item" href="{{ route('ahps.weight-alternatif') }}">Bobot Alternatif</a>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('ahps.final-calculate') }}">Peringkat</a>
                            </li>
                        </ul>
                    </li>
                @endauth
            </div>
        </div>
    </div>
</nav>
