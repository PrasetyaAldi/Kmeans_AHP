<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://kit.fontawesome.com/e427dc0a75.js" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="{{ asset('assets/img/himasi.png') }}" type="image/x-icon">
    <link
        href="https://fonts.googleapis.com/css2?family=Expletus+Sans:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <title>{{ config('app.name') }} | @yield('title')</title>
    <style>
        body {
            background-color: #f4f8fb;
        }

        .bg-grey-200 {
            background-color: #eef2f5;
        }

        .card {
            border: 1px solid #eef2f5;
        }

        .table th,
        .table td {
            min-width: 90px !important;
            max-width: 90px !important;
            /* Ubah nilai lebar kolom sesuai kebutuhan */
        }

        .table tr {
            height: 30px !important;
        }

        .card-header,
        .card-footer {
            background-color: #ffffff;
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .nav-criteria:hover {
            background-color: #CFF4FC !important;
        }

        .active-criteria {
            background-color: #CFF4FC !important;
        }

        /* width scroll */
        ::-webkit-scrollbar {
            cursor: pointer;
            width: 0.75rem;
            height: 0.75rem;
        }

        /* Track  scroll */
        ::-webkit-scrollbar-track {
            background: #ffffff;
        }

        /* Handle scroll*/
        ::-webkit-scrollbar-thumb {
            background: #E0E3E7;
            border-radius: 0.5rem;
            border: 0.25rem solid #ffffff;
        }

        /* Handle on hover scroll */
        ::-webkit-scrollbar-thumb:hover {
            background: #C0C3C9;
        }

        .headcol {
            position: sticky;
            left: 0;
            z-index: 1;
            background-color: #ffffff;
        }
    </style>
</head>

<body>
    <x-nav />
    <div class="container mt-4">
        @if (Session::get('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <i class="fa-solid fa-triangle-exclamation"></i> &nbsp; {{ Session::get('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @elseif(Session::get('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <i class="fa-solid fa-triangle-exclamation"></i> &nbsp; {{ Session::get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <main>
            <div class="container">
                @yield('content')
            </div>
        </main>
        @yield('modal')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous">
    </script>
    @yield('scripts')
</body>

</html>
