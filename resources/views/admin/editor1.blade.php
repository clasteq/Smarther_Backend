@extends('layouts.admin_master')
@section('comn_settings', 'active')
@section('master_posts', 'active')
@section('menuopencomn', 'menu-is-opening menu-open')
<?php use App\Http\Controllers\AdminController;
$slug_name = (new AdminController())->school; ?>
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Sections', 'active' => 'active']];
?>
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>

  <!-- include libraries BS
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.css" />
  <script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.5/umd/popper.js"></script>
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.js"></script> -->

  <!-- include summernote 
  <script type="text/javascript" src="/summernote-bs4.js"></script>-->

  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

  <link rel="stylesheet" href="example.css">
  <script type="text/javascript">
    $(document).ready(function () {
      $('#summernote').summernote({
        placeholder: 'Hello stand alone ui',
        tabsize: 2,
        height: 120,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link']], 
        ]
      });
    });
  </script>
</head>
<body>
<section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Create Post for Scholars
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('admin/post_new_message') }}" method="post" id="post_communication"
                            class="post_communication">

                            @csrf
                            <div class="form-group">
                                <label for="title">Title:</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                                <span class="text-danger error-text title_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Message:</label>
                                <textarea type="text" id="summernote" name="summernote" class="form-control" rows="5"></textarea>
                                <span class="text-danger error-text message_error"></span>
                            </div> 
                          </form>
                    </div>
                </div>
            </div>
        </div>
</section>
@endsection

