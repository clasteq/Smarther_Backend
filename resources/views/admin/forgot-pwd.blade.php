<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config("constants.site_name") }} | Forgot Password</title>

  <!-- Google Font: Source Sans Pro 
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">-->
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('/public/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{asset('/public/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('/public/dist/css/adminlte.min.css')}}">

  <link rel="stylesheet" href="{{asset('/public/css/sweetalert.css')}}">
</head>
<body class="hold-transition login-page">

<div class="row col-md-12">

  <?php 
    if(isset($login_image) && !empty($login_image)) {
      $image = $login_image;
    } else {
      $image = asset('/public/image/adminlogin.jpeg');
    }
  ?>
  <!-- <div class="col-md-6 float-left" style="background: url('<?php echo $image; ?>'">
  </div> -->
  <div class="col-md-8 float-left" >
  <img src="{{$image}}" alt='{{ config("constants.site_name") }}' class="brand-image mr-3" style="opacity: .8; height: 100%; width: 100%;">
  </div>

  <div class="col-md-4 float-left">
    <div class="login-box" style="margin-top:10%; margin-left: 5%;">
      <div class="login-logo" >
        <img src="{{asset('/public/image/logo.png')}}" alt='{{ config("constants.site_name") }} Logo' class="brand-image mr-3" style="opacity: .8; height: 200px; width: 200px;"><!-- <b>{{ config("constants.site_name") }}</b>  -->
      </div>
      <!-- /.login-logo -->
      <div class="card">
        <div class="card-body login-card-body">
          
            <p class="login-box-msg">Forgot Password</p>

            <form action="{{ url('admin/forgotpwd') }}" method="post" id="login-form">
              {{csrf_field()}}
              <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Mobile" name="mobile" id="mobile" required onkeypress="return isNumber(event);" minlength="10" maxlength="10">
                <div class="input-group-append">
                  <div class="input-group-text">
                    <span class="fas fa-mobile"></span>
                  </div>
                </div>
              </div> 
              <div class="row">
                <div class="col-md-12 float-left">
                  <small id="emailHelp" class="form-text text-m red"></small>
                  <small id="emailHelpS" class="form-text text-t"></small>
                </div>
                <!-- /.col -->
                <div class="col-md-4 float-left">
                  <button type="submit" class="btn btn-primary btn-block" id="login-id">Submit</button>
                </div>
                <div class="col-md-8 float-right">
                  <a href="{{ URL('/admin/login') }}"    class="fw-600 font-xsss text-grey-700 white-text mt-1 float-right">
                  Login?</a>
                </div>
                <!-- /.col -->
              </div>
            </form>  
        </div>
        <!-- /.login-card-body -->
      </div>
    </div>
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{asset('/public/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('/public/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('/public/dist/js/adminlte.min.js')}}"></script>
<!-- 
<script type="text/javascript" src="{{asset('/public/plugins/validation/form-validation.js')}}"></script> -->

  <script src="{{asset('/public/js/sweetalert.min.js') }}"></script>

  <script src="{{asset('/public/js/jquery-form.js') }}"></script>

  <!-- END PAGE LEVEL JS-->

  <script>
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    $('#login-id').on('click', function () {

        var options = {

            beforeSend: function (element) {

                $("#login-id").text('Processing..');

                $("#login-id").prop('disabled', true);

            },
            success: function (response) {

                $('#emailHelp').text('');

                $("#login-id").prop('disabled', false);

                $("#login-id").text('Submit');

                if (response.status == "SUCCESS") {

                    $('#emailHelpS').text(response.message);

                    window.location.href = "{{URL::to('admin/resetpwd/')}}?id="+response.userid;

                }
                else if (response.status == "FAILED") {

                    $('#emailHelp').text(response.message);

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {

                $("#login-id").prop('disabled', false);

                $("#login-id").text('Submit');

                swal("Oops!", 'Sorry could not process your request', "error");
            }
        };
        $("#login-form").ajaxForm(options);
    });
</script>
</body>
</html>
