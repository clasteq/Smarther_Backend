<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
@include('layouts.teacher_head')

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{ asset('/public/dist/img/AdminLTELogo.png') }}" alt="AdminLTELogo"
                height="60" width="60">
        </div>

        @include('layouts.teacher_header')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            @include('layouts.teacher_topnavigation')

            <!-- Main content -->
            <div class="content">
                <div class="container">
                    @yield('content')
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        <footer class="main-footer" >
            <!-- To the right -->
            <div class="float-right d-none d-sm-inline">
                <!-- Anything you want -->
            </div>
            <!-- Default to the left -->
            <strong>Copyright &copy; {{ date('Y') }} <a
                    href="{{ URL('/') }}">{{ config('constants.site_name') }}</a>.</strong> All rights reserved.
        </footer>
    </div>
    <!-- ./wrapper -->

    @include('layouts.teacher_footer')
    @yield('scripts')
</body>

</html>
