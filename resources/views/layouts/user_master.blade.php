<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from raistheme.com/html/fitner/index-1.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 07 Sep 2022 06:22:36 GMT -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config("constants.site_name") }}</title>

    <!-- favicon icon -->

    <link rel="icon" type="image/x-icon" href="{{asset('assets/img/11.png') }}">

    <link rel="stylesheet" href="{{asset('assets/font/flaticon.css') }}">
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{asset('assets/css/responsive.css') }}">

      <link rel="stylesheet" href="{{asset('/public/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
      <link rel="stylesheet" href="{{asset('/public/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
      <link rel="stylesheet" href="{{asset('/public/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
      <link rel="stylesheet" href="{{asset('/public/css/sweetalert.css')}}">

    <style>
        @media (min-width: 320px) and (max-width: 991px){
            .logo img {
                width: 100px !important;
            }
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

        .bg-green {
            background: #A3D10C !important;
        }

        .text-green {
            color: #A3D10C !important;
        }

        .pagediv nav {
            float: right;
        }

        .logo img {
            width: 80%;
            margin-top: -40px;
        }
           
        #cssmenu.sticky .logo img {
            margin-top: -9px !important;
            width: 54%;
        }
        .top-address::after {
            background: linear-gradient(90deg, rgba(243, 10, 70, 0) 25%, rgb(52 146 50) 100%) !important;
        }
        .icon-btn{
            margin-right: 0px !important;
        }
    </style>
</head>

<body>
    <!-- ===========================
    =====>> Top Preloader <<===== -->
    <div id="preloader">
        <div class="lds-css">
            <div class="preloader-3">
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <!-- =====>> End Top Preloader <<===== 
    =========================== -->

    @include('layouts.user_header')

    @yield('content')

    @include('layouts.user_footer')

    <form method="post" name="filters" id="filters">
        <input type="hidden" name="filter_page" id="filter_page" value="0"/>
        <input type="hidden" name="filter_content" id="filter_content" value=""/>
    </form>

    <div class="modal fade loader-modal" id="myLoaderModal">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content" style="    width: 32%;    background: none; margin-left: 45%;">
               <div class="modal-body">
                    <div class="login-modal">
                        <div class="row">
                            <div class="col-md-12 pad-left-0">
                                <img id="loader" src="{{asset('assets/img/loader1.gif') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <input type="hidden" name="loadstates" id="loadstates" value="{!! URL('loadstates') !!}">
    <input type="hidden" name="loaddistricts" id="loaddistricts" value="{!! URL('loaddistricts') !!}">
    <input type="hidden" name="loadclasses" id="loadclasses" value="{!! URL('loadclasses') !!}">
    
    <script src="{{asset('assets/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{asset('assets/js/popper.min.js') }}"></script>
    <script src="{{asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{asset('assets/js/plugins.js') }}"></script>
    <script src="{{asset('assets/js/menu.js') }}"></script>
    <script src="{{asset('assets/js/scroll-slider.js') }}"></script>
    <script src="{{asset('assets/js/jquery.parallax-1.1.3.js') }}"></script>
    <script src="{{asset('assets/js/typing.js') }}"></script>
    <script src="{{asset('assets/js/contact.js') }}"></script>
    <script src="{{asset('assets/js/script.js') }}"></script>
    <script src="{{asset('public/js/functions.js') }}"></script> 
    <script src="{{asset('/public/js/sweetalert.min.js') }}"></script> 
    <script src="{{asset('/public/js/jquery-form.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{asset('/public/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('/public/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('/public/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('/public/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
    <script src="{{asset('/public/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('/public/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{asset('/public/plugins/jszip/jszip.min.js')}}"></script>
    <script src="{{asset('/public/plugins/pdfmake/pdfmake.min.js')}}"></script>
    <script src="{{asset('/public/plugins/pdfmake/vfs_fonts.js')}}"></script>
    <script src="{{asset('/public/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('/public/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('/public/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('/public/js/bootstrap-datepicker.min.js')}}"></script>

    @yield('scripts')

    <script type="text/javascript">
        $(document).on('click', '#cpagelistnav .pagination a', function(event){
            event.preventDefault(); 
            var page = $(this).attr('href').split('page=')[1];
            $('#filter_page').val(page);
            $('#filter_content').val($('#filtercontent').val());
            filterProducts();
            $('html, body').animate({
                scrollTop: $('#cpagelistnav').offset().top - 20 
            }, 'slow');
        });

        function filterProducts() {
            //$('#myLoaderModal').modal('show');
            $filterdata = $('#filters').serialize();

            var request = $.ajax({
                type: 'post',
                url: "{{URL::to('/filter-products')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $filterdata,
                dataType: 'json',
                encode: true
            });
            request.done(function (response) {
                var data = response.data;
                if(response.status == 1){
                    if (data == '') {
                        $('#cpagelistnav').html('No Details Found');
                    }   else {
                        $('#cpagelistnav').html(data);
                        //$('#myLoaderModal').modal('hide'); 
                        //$('#myLoaderModal').removeClass('show'); 
                        //$('.product-body span').css('color','#00b05f'); 
                    }
                }else{
                    $('#cpagelistnav').html('No Details Found');
                    //$('#myLoaderModal').modal('hide'); 
                }
                //$('#myLoaderModal').modal('hide'); 
            });
            request.fail(function (jqXHR, textStatus) {
                $('#cpagelistnav').html('No Details Found');
                //$('#myLoaderModal').modal('hide'); 
            });
        }
    </script>
</body>


<!-- Mirrored from raistheme.com/html/fitner/index-1.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 07 Sep 2022 06:22:49 GMT -->
</html>