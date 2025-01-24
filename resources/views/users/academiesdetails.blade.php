@extends('layouts.user_master')
@section('content')

@if(!empty($academies))
    <!-- ===========================
    =====>> Page Hero <<===== -->
    <section id="page-hero" class="about-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title text-center">
                        <h1>{{$academies->name}} <span>academy</span></h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =====>> End Page Hero <<===== 
    =========================== -->

    <!-- ===========================
    =====>> Trainers Single <<===== -->
    <section id="trainers-single-area" class="pt-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-5">
                    <div class="trainers-single-img">
                        <img src="{{$academies->is_profile_image}}" alt="" style="width:350px; height:250px;">
                    </div>
                </div>
                <div class="col-lg-7 offset-lg-1 col-md-7 offset-md-0">
                    <div class="trainers-single-text">
                        <h2>{{$academies->name}}</h2>
                        <h6>Fitness & Body</h6>
                        <p>{{$academies->descriptiontext}}</p>

                        <div class="trainers-information">
                            <ul>
                                <li><span>Email</span> : {{$academies->email}}</li> 
                                <li><span>Phone</span> : +{{$academies->country_code}} {{$academies->mobile}}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =====>> End Trainers Single <<===== 
    =========================== -->

    <!-- ===========================
    =====>> Team <<===== -->
    <section id="team-area" class="pt-100 pb-150">
        <div class="container">
            <div class="team-content">
                <div class="row">@include('users.academies_list', ['academies' => $academieslist]) 
                </div>
            </div>
        </div>
    </section>
    <!-- =====>> End Team <<===== 
    =========================== -->

@else 
<!-- ===========================
    =====>> Page Hero <<===== -->
    <section id="page-hero" class="about-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title text-center">
                        <h1> <span>academy</span></h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =====>> End Page Hero <<===== 
    =========================== -->

    <!-- ===========================
    =====>> Trainers Single <<===== -->
    <section id="trainers-single-area" class="pt-150">
        <div class="container">
            <div class="row"> No Details found
            </div>
        </div>
    </section>
    <!-- =====>> End Team <<===== 
    =========================== -->
@endif
@endsection