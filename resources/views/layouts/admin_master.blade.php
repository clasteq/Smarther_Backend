<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
    @include('layouts.admin_head')

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{asset('/public/dist/img/AdminLTELogo.png')}}" alt="AdminLTELogo" height="60" width="60">
  </div>  

  @include('layouts.admin_header')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    
    @include('layouts.admin_topnavigation')

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
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      <!-- Anything you want -->
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; {{date('Y')}} <a href="javascript:void(0);">{{ config("constants.site_name") }}</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->
    <form method="post" name="filters" id="filters">
        <input type="hidden" name="filter_page" id="filter_page" value="0"/>
        <input type="hidden" name="filter_pagename" id="filter_pagename" value=""/>
        <input type="hidden" name="filter_input_id" id="filter_input_id" value=""/> 

        <input type="hidden" name="filter_from_date" id="filter_from_date" value=""/> 
        <input type="hidden" name="filter_to_date" id="filter_to_date" value=""/>   

        <input type="hidden" name="filter_category_id" id="filter_category_id" value=""/>  
        <input type="hidden" name="filter_search" id="filter_search" value=""/>

        <input type="hidden" name="filter_status_id" id="filter_status_id" value=""/>  
        <input type="hidden" name="filter_approval_status_id" id="filter_approval_status_id" value=""/>  
        <input type="hidden" name="filter_class_id" id="filter_class_id" value=""/> 
        <input type="hidden" name="filter_section_id" id="filter_section_id" value=""/> 
        <input type="hidden" name="filter_subject_id" id="filter_subject_id" value=""/> 

    </form>

    <input type="hidden" name="baseurl" id="baseurl" value="{{URL('/')}}/admin">

    @include('layouts.admin_footer')
    @yield('scripts')
    <script type="text/javascript">
      
        function loadClassSection(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#section_dropdown,#edit_section_dropdown").html('');
            $.ajax({
                url: "{{ url('admin/fetch-section') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {

                    $('#section_dropdown,#edit_section_dropdown').html(
                        '<option value="">-- Select Section --</option>');
                    /*if (selid != null && selval != null) {
                        $("#edit_section_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');
                    }*/
                    $.each(res.section, function(key, value) {
                      var selected = '';
                      if (selid != null && selval != null) {
                           if(selid == value.id) {
                            selected = ' selected ';
                           }
                      }
                        $("#section_dropdown,#edit_section_dropdown").append('<option value="' + value
                            .id + '" '+selected+'>' + value.section_name + '</option>');
                    });
                }
            });
        }

        function loadClassExams(val, selectedid, selectedval) {
 
            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#exam_id").html('');
            $.ajax({
                url: "{{ url('admin/fetch-exams') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {

                    $('#exam_id').html(
                        '<option value="">-- Select Exam --</option>');
                    /*if (selid != null && selval != null) {
                        $("#edit_section_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');
                    }*/
                    $.each(res.exams, function(key, value) {
                      var selected = '';
                      if (selid != null && selval != null) {
                           if(selid == value.id) {
                            selected = ' selected ';
                           }
                      }
                        $("#exam_id").append('<option value="' + value
                            .id + '" '+selected+' data-startdate="'+value.monthyear+'">' + value.exam_name + ' - ' +value.monthyear + '</option>');
                    });
                }
            });
        }

        function loadClassSubjects(val, selectedid, selectedval, isclass) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            isclass = isclass || 0; 
            var section_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#subject_id").html('');
            $.ajax({
                url: "{{ url('admin/fetch-subject') }}",
                type: "POST",
                async:true,
                data: {
                    section_id: section_id,isclass:isclass,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {

                    $('#subject_id').html(
                        '<option value="">-- Select Subject --</option>');
                    /*if (selid != null && selval != null) {
                        $("#edit_section_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');
                    }*/
                    $.each(res.subjects, function(key, value) {
                      var selected = '';
                      if (selid != null && selval != null) {
                           if(selid == value.id) {
                            selected = ' selected ';
                           }
                      }
                        $("#subject_id").append('<option value="' + value
                            .id + '" '+selected+'>' + value.subject_name + '</option>');
                    });
                }
            });
        }

        function loadClassTerms(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#term_id").html('');
            $.ajax({
                url: "{{ url('admin/fetch-terms') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: '{{ csrf_token() }}'
                },
                async:true,
                dataType: 'json',
                success: function(res) {

                    $('#term_id,#edit_term_id,.term_id').html(
                        '<option value="">-- Select Term --</option>');
                    /*if (selid != null && selval != null) {
                        $("#edit_section_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');
                    }*/
                    $.each(res.terms, function(key, value) {
                      var selected = '';
                      if (selid != null && selval != null) {
                           if(selid == value.id) {
                            selected = ' selected ';
                           }
                      }
                        $("#term_id,#edit_term_id,.term_id").append('<option value="' + value
                            .id + '" '+selected+'>' + value.term_name + '</option>');
                    });
                }
            });
        }

        /*$("#exam_id").change(function() {
              var selectedItem = $(this).val();
              var abc = $('option:selected',this).data("startdate");
              $('#monthyear').val(monthyear);
        });*/

        function loadmonthyear(){
            var monthyear = $('#exam_id').find(':selected').data('startdate');
            $('#monthyear').val(monthyear);
        }

        function isDecimal(evt, obj) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)  && (charCode != 46 || $(obj).val().indexOf('.') != -1)) {
                return false;
            }
            return true;
        }

        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }

        $(document).on('click', '.pagination_section .pagination a', function(event){
            event.preventDefault(); 
            var page = $(this).attr('href').split('page=')[1];
            $('#filter_pagename').val($('#pagename').val());
            $('#filter_page').val(page); 
            $('#filter_input_id').val($('#input_id').val());
            filterProducts();
            /*$('html, body').animate({
                scrollTop: $('.product_list_section').offset().top - 20 
            }, 'slow');*/
        });

        function filterProducts() {  
            $filterdata = $('#filters').serialize();

            var request = $.ajax({
                type: 'post',
                url: "{{URL::to('/admin/filter_things')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $filterdata,
                dataType: 'json',
                encode: true
            });
            request.done(function (response) {
                var data = response.data;
                var len = $('.pagination_section').length;
                if(len > 0) {
                    if(response.status == 1){
                        if (data == '') {
                            $('.pagination_section').html(response.message);   
                        }   else {
                            $('.pagination_section').html(data);   
                            var fpname = $('#filter_pagename').val();
                            if(fpname == 'vendor_calendar') { 
                                var bookarray = response.bookarray;
                                $.each( bookarray, function( key, value ) {
                                    $('#'+value).addClass('isbookings');
                                });
                            }
                        }
                    }else{
                        $('.pagination_section').html(response.message); 
                    } 
                }   else {
                    location.reload();
                }
                
            });
            request.fail(function (jqXHR, textStatus) {
                var len = $('.pagination_section').length;
                if(len > 0) {
                    $('.pagination_section').html(response.message);
                }   else {
                    location.reload();
                } 
                //$('#myLoaderModal').modal('hide'); 
            });
        }
    </script>
</body>
</html>
