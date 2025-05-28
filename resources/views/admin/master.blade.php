<!DOCTYPE html>
<html lang="en">

<head>

  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />


    <!-- Fonts and icons -->
    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
      WebFont.load({
        google: {
          families: ["Public Sans:300,400,500,600,700"]
        },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons"
          ],
          urls: ["{{ asset('assets/css/fonts.min.css') }}"]
        },
        active: function() {
          sessionStorage.fonts = true;
        }
      });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}">

    @yield('styles')
  </head>

<body>
  <div class="wrapper">
    @include('admin.layouts.sidebar')
    @include('admin.layouts.header')

    <div class="container">
      <div class="page-inner">

        <!-- {{-- Only show page header if NOT on the homepage or "exclusion-list" --}}
        @if (Request::path() !== '/' && Request::path() !== 'exclusion-list')
        <div class="page-header">
          <h3 class="fw-bold mb-3">Dashboard</h3>
          <ul class="breadcrumbs mb-3">
            <li class="nav-home">
              <a href="#"><i class="icon-home"></i></a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">@yield('manage')</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">@yield('compaign')</a></li>
          </ul>
        </div>
        @endif -->

        <div class="container">
          @include('inc.messages')
          @yield('content')
        </div>

        @include('admin.layouts.footer')

      </div>
    </div>
  </div>

  <!-- JS Files -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
  <!-- <script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script> -->
  <script src="{{ asset('assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/jsvectormap/world.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>
  <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>
  <script src="{{ asset('assets/js/setting-demo.js') }}"></script>
  <script src="{{ asset('assets/js/demo.js') }}"></script>

  <script>
    // Example charts
    $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
      type: "line",
      height: "70",
      width: "100%",
      lineWidth: "2",
      lineColor: "#177dff",
      fillColor: "rgba(23, 125, 255, 0.14)"
    });

    $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
      type: "line",
      height: "70",
      width: "100%",
      lineWidth: "2",
      lineColor: "#f3545d",
      fillColor: "rgba(243, 84, 93, .14)"
    });

    $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
      type: "line",
      height: "70",
      width: "100%",
      lineWidth: "2",
      lineColor: "#ffa534",
      fillColor: "rgba(255, 165, 52, .14)"
    });
  </script>

  @yield('scripts')
</body>

</html>