<head>
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config("constants.site_name") }} Teacher Console</title>
  
    <!-- Google Font: Source Sans Pro -->
    <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> -->
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{asset('/public/plugins/fontawesome-free/css/all.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('/public/dist/css/adminlte.min.css')}}">
    <link rel="stylesheet" href="{{asset('/public/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <!-- Datatable -->
    <link rel="stylesheet" href="{{asset('/public/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('/public/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('/public/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('/public/css/sweetalert.css')}}">
    <link rel="stylesheet" href="{{asset('/public/css/bootstrap-datepicker.css')}}">

    {{-- Flat Picker --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  </head>
  
    @yield('css')
  
      <style type="text/css">
        body {
          background-color: #fff !important;
        }
          .dataTables_filter {
              display: none;
          }
          tfoot {
              display: table-header-group;
          }
          .dt-buttons {
            margin-bottom: 1rem !important;
            margin-right: 3rem !important;
            float: left;
          }
          h4.card-title {
            width: 100%;
          }
          .container, .container-lg, .container-md, .container-sm, .container-xl {
              max-width: 100% !important;
          }
          ul.dropdown-menu.border-0.shadow.show {
            min-width: 11rem;
          }
  
          /*.navbar-blue {
            background-color: #005bac;
            color: #fff;
          }*/

        .navbar-blue {
          background-color: #FF6F61 !important;
          color: #fff;
        }

        .manstar {
          color: #f00;
        }

        .preloader{
          display: none !important;
        }

        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active, .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
            background-color: #FF03DAC5 !important;
            color: #fff;
        }

        [class*=sidebar-light-] .nav-treeview>.nav-item>.nav-link.active, [class*=sidebar-light-] .nav-treeview>.nav-item>.nav-link.active:hover {
            background-color: #FF6F61 !important;
            color: #fff;
        }

        .page-item.active .page-link {
            z-index: 3;
            color: #fff;
            background-color: #FF6F61 !important;
            border-color: #FF6F61 !important;
        }

        .nav-sidebar>.nav-item .nav-icon { 
            color: #FF03DAC5 !important;
        }

        .nav-sidebar>.nav-item .nav-link.active .nav-icon { 
            color: #ff6f61 !important;
        }

        [class*=sidebar-dark-] {
            background-color: #ff6f61 !important;
        }

        [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link {
            color: #FF03DAC5;
        }

        .content-wrapper {
            background-color: #ff6f6130 !important;
        }

        [class*=sidebar-dark-] .nav-sidebar>.nav-item>.nav-treeview {
            background-color: mistyrose !important;
        }

        [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link:focus, [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link:hover {
              background-color: rgba(255, 255, 255, .1);
              color: #ff6f61  !important;
          }
      </style>
  
  </head>