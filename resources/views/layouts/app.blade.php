<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>
    DSS Pemilihan Jenis Usaha - Metode VIKOR
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('/soft-ui-dashboard-main/assets/css/soft-ui-dashboard.css?v=1.1.0') }}" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-100">

  @include('layouts.sidebar')

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

    @include('layouts.navigation')

    <div class="container-fluid py-4">
        @yield('content')
    </div>

  </main>

  <!--   Core JS Files   -->
  <script src="{{ asset('/soft-ui-dashboard-main/assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('/soft-ui-dashboard-main/assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('/soft-ui-dashboard-main/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('/soft-ui-dashboard-main/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>

  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('/soft-ui-dashboard-main/assets/js/soft-ui-dashboard.min.js?v=1.1.0') }}"></script>

  @stack('scripts')
</body>

</html>
