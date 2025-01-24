<!-- Content Header (Page header) -->
<?php //echo "<pre>"; print_r($breadcrumb); exit; ?>
<div class="content-header">
  <div class="container d-none">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"> @yield('pagetitle') <small>@yield('sub_pagetitle')</small></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          @if(isset($breadcrumb) && is_array($breadcrumb) && count($breadcrumb)>0)
            @foreach($breadcrumb as $bc)
              <li class="breadcrumb-item {{$bc['active']}}">
                @if($bc['active'] != 'active')
                  <a href="{{$bc['url']}}">{{$bc['name']}}</a>
                @else 
                  {{$bc['name']}}
                @endif
              </li> 
            @endforeach
          @endif
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->