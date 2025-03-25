@extends('layouts.admin_master')
@section('settings', 'active')
@section('settings_admin', 'active')
@section('menuopen', 'menu-is-opening menu-open')
@section('content') 
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
        <!-- Exportable Table -->
        <div class="content container-fluid">

            <div class="panel"> 
            <div class="panel-body">
 
            <div class="row">

                <div class="col-xs-12 col-md-12">
            
                <div class="card">
                    <!-- <div class="card-header">General Settings
                    </div> -->
 
                </div>
            </div> 
        </div>
    </div>
</div>
</div>
</section>
@endsection

@section('scripts')  
@endsection

