@extends('layouts.user_master')
@section('content')
    <!-- ===========================
    =====>> Page Hero <<===== -->
    <section id="page-hero" class="about-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title text-center">
                        <h1>fitner <span>trainers</span></h1>
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
                        <img src="{{asset('assets/img/team/1.jpg')}}" alt="">
                    </div>
                </div>
                <div class="col-lg-7 offset-lg-1 col-md-7 offset-md-0">
                    <div class="trainers-single-text">
                        <h2>Arial hedger</h2>
                        <h6>Fitness & Body</h6>
                        <p>Over 50 group training sessions per week There is no one type or way in our diverse community Alienum phaedrum torquatos nec eu. Sed non mauris vitae erat consequat auctor eu in elit. Class aptent taciti sociosqu ad litora torquent
                            per conubia nostra, per inceptos himenaeos. Mauris in erat justo. Nullam ac urna eu felis dapibus condimentum</p>

                        <div class="trainers-information">
                            <ul>
                                <li><span>Experience</span> : 5 Years</li>
                                <li><span>Age</span> : 35</li>
                                <li><span>Weight</span> : 60 kg</li>
                                <li><span>Phone</span> : +1 234 45456 654</li>
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
                <div class="row">
                    <!-- team item -->
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="team-box effect-item mt-40">
                            <div class="team-item content-overlay">
                                <img src="{{asset('assets/img/team/4.jpg')}}" alt="">
                                <div class="content-details fadeIn-top">
                                    <ul>
                                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="#"><i class="fab fa-google-plus-g"></i></a></li>
                                        <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                                    </ul>
                                    <p>Over 50 group training sessions per week There is no one type or way in our diverse community.</p>
                                </div>
                            </div>
                            <div class="team-title text-center">
                                <h3><a href="#">Arial hedger</a></h3>
                                <p>Fitness & Body</p>
                            </div>
                        </div>
                    </div>
                    <!-- team item -->
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="team-box effect-item mt-40">
                            <div class="team-item content-overlay">
                                <img src="{{asset('assets/img/team/2.jpg')}}" alt="">
                                <div class="content-details fadeIn-top">
                                    <ul>
                                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="#"><i class="fab fa-google-plus-g"></i></a></li>
                                        <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                                    </ul>
                                    <p>Over 50 group training sessions per week There is no one type or way in our diverse community.</p>
                                </div>
                            </div>
                            <div class="team-title text-center">
                                <h3><a href="#">marina aring</a></h3>
                                <p>Fitness & Body</p>
                            </div>
                        </div>
                    </div>
                    <!-- team item -->
                    <div class="col-lg-4 offset-lg-0 col-md-6 offset-md-3 col-sm-6 offset-sm-3">
                        <div class="team-box effect-item mt-40">
                            <div class="team-item content-overlay">
                                <img src="{{asset('assets/img/team/3.jpg')}}" alt="">
                                <div class="content-details fadeIn-top">
                                    <ul>
                                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="#"><i class="fab fa-google-plus-g"></i></a></li>
                                        <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                                    </ul>
                                    <p>Over 50 group training sessions per week There is no one type or way in our diverse community.</p>
                                </div>
                            </div>
                            <div class="team-title text-center">
                                <h3><a href="#">alan lynda</a></h3>
                                <p>Fitness & Body</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- =====>> End Team <<===== 
    =========================== -->
@endsection