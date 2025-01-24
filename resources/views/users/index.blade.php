@extends('layouts.user_master')
@section('content')

<!-- ===========================
    =====>> Hero <<===== -->
    <section id="Home-area" class="header">
        <div class="fullslider owl-carousel owl-theme ">
            <!-- slider item -->
            <div class="item slider-bg-1 ">
                <div class="container ">
                    <div class="row ">
                        <div class="col-lg-8 text-left dis-tab ">
                            <div class="slider-all-text ">
                                <h1>SPORTS CONSULTANT</h1>
                                <p>Utilize our resources to discover the sports-related<br> job best suited for your skillset.</p>
                                <a class="video-play-button " href="#">
                                    <i class="flaticon-play-button "></i>
                                    <span>Take a Tour</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- slider item -->
            <div class="item slider-bg-6">
                <div class="container ">
                    <div class="row ">
                        <div class="col-lg-8 text-left dis-tab ">
                            <div class="slider-all-text ">
                                <h1>SPORTS CONSULTANT</h1>
                                <p>Utilize our resources to discover the sports-related<br> job best suited for your skillset.</p>
                                <a class="video-play-button " href="#">
                                    <i class="flaticon-play-button "></i>
                                    <span>Take a Tour</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- slider item -->
            <div class="item slider-bg-2">
                <div class="container ">
                    <div class="row ">
                        <div class="col-lg-8 text-left dis-tab ">
                            <div class="slider-all-text ">
                                <h1>SPORTS CONSULTANT</h1>
                                <p>Utilize our resources to discover the sports-related<br> job best suited for your skillset.</p>
                                <a class="video-play-button " href="#">
                                    <i class="flaticon-play-button "></i>
                                    <span>Take a Tour</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =====>> End Hero <<===== 
    =========================== -->

    <!-- ===========================
    =====>> About Box <<===== -->
    <section id="about-box">
        <div class="container-fluid ">
            <div class="row ">
                <!-- <div class="col-lg-3 about-box-left-bc p-0 ">
                    <div class="about-box-left-img "></div>
                    <div class="about-box-left ">
                        <h2>Membership starts from <span>$99.5</span> month </h2>
                        <p>Every member gets a free, personalized Get Started Plan when they join. </p>
                        <a href="# " class="btn btn-2">Get Started</a>
                    </div>
                </div> -->
                <div class="col-lg-12 p-0 ">
                    <!-- <div class="about-box-right media">
                        <div class="about-box-right-img "></div>
                        <h2>Try us for
                            <br> <span>7 days</span> FREE</h2>
                        <a href="# " class="btn btn-1">Get GYM PASS</a>
                    </div> -->
                    <div id="main">
                        <div class="slideFrame" id="slider-0" style="height: 245px;">
                            <ul class="slideGuide left ">
                                <li class="slideCell ">
                                    <img src="{{asset('assets/img/home/ab-1.jpg') }}" alt=" ">
                                    <div class="sliderCell-text ">
                                        <h2>Fit Plans </h2>
                                        <h3>For Every Goal</h3>
                                        <p>Let our knowledgeable team get you started with a one-on-one workout session</p>
                                    </div>
                                </li>
                                <li class="slideCell ">
                                    <img src="{{asset('assets/img/home/ab-2.jpg') }}" alt=" ">
                                    <div class="sliderCell-text ">
                                        <h2>Fit Plans </h2>
                                        <h3>For Every Goal</h3>
                                        <p>Let our knowledgeable team get you started with a one-on-one workout session</p>
                                    </div>
                                </li>
                                <li class="slideCell ">
                                    <img src="{{asset('assets/img/home/ab-3.jpg') }}" alt=" ">
                                    <div class="sliderCell-text ">
                                        <h2>Fit Plans </h2>
                                        <h3>For Every Goal</h3>
                                        <p>Let our knowledgeable team get you started with a one-on-one workout session</p>
                                    </div>
                                </li>
                                <li class="slideCell ">
                                    <img src="{{asset('assets/img/home/ab-4.jpg') }}" alt=" ">
                                    <div class="sliderCell-text ">
                                        <h2>Fit Plans </h2>
                                        <h3>For Every Goal</h3>
                                        <p>Let our knowledgeable team get you started with a one-on-one workout session</p>
                                    </div>
                                </li>
                                <li class="slideCell ">
                                    <img src="{{asset('assets/img/home/ab-5.jpg') }}" alt=" ">
                                    <div class="sliderCell-text ">
                                        <h2>Fit Plans </h2>
                                        <h3>For Every Goal</h3>
                                        <p>Let our knowledgeable team get you started with a one-on-one workout session</p>
                                    </div>
                                </li>

                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =====>> End About Box <<===== 
    =========================== -->

    <!-- ===========================
    =====>> Fitner <<===== -->
    <section id="fitner-area" class="pt-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="section-title text-center">
                        <h2>Welcome to <span>Sports Works</span> </h2>
                        <p>Kick your feet up! With a gym designed around you, we think
                            <br> you’ll love it here.</p>
                    </div>
                </div>
            </div>
            <div class="fitner-content pt-40 ">
                <div class="row ">
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <!-- fitner item -->
                        <div class="fitner-item fitner-border mt-40">
                            <img src="{{asset('assets/img/10935851.png') }}" alt=" ">
                            <h2> World Wide </h2>
                            <p>Over 50 group training sessions per week There is no one type or way in our diverse community. Come as you are!</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <!-- fitner item -->
                        <div class="fitner-item fitner-border mt-40">
                            <img src="{{asset('assets/img/Group75.png') }}" alt=" ">
                            <h2>Sports </h2>
                            <p>Over 50 group training sessions per week There is no one type or way in our diverse community. Come as you are!</p>
                        </div>
                    </div>
                    <div class="col-lg-4 offset-lg-0 col-md-6 offset-md-3 col-sm-6 offset-sm-3">
                        <!-- fitner item -->
                        <div class="fitner-item mt-40">
                            <img src="{{asset('assets/img/Football-Vector-Illustration-1536x15361.png') }}" alt=" ">
                            <h2>Navicate</h2>
                            <p>Over 50 group training sessions per week There is no one type or way in our diverse community. Come as you are!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =====>> End Fitner <<===== 
    =========================== -->

    <!-- ===========================
    =====>> Services <<===== -->
    <section id="services-area" class="pt-150">
        <div class="container ">
            <div class="row">
                <div class="col-lg-10 offset-lg-1 ">
                    <div class="section-title text-center ">
                        <h2>Our <span>services</span> </h2>
                        <p>Kick your feet up! With a gym designed around you, we think
                            <br> you’ll love it here.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="service-content pt-40 ">
            <div class="container-fluid ">
                <div class="row ">
                    <!-- Service Item -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                        <div class="services-box effect-item mt-40">
                            <div class="services-item content-overlay">
                                <img src="{{asset('assets/img/Career-in-Sports-011.png') }}" alt=" ">
                                <div class="content-details fadeIn-top">
                                    <i class="flaticon-male-silhouette-variant-showing-muscles "></i>
                                    <h3>fat burn</h3>
                                    <p>Over 50 group training sessions per week There is no one type or way in our diverse community.</p>
                                </div>
                            </div>
                            <div class="services-text text-center">
                                <i class="flaticon-male-silhouette-variant-showing-muscles "></i>
                                <h3>fat burn</h3>
                            </div>
                        </div>
                    </div>
                    <!-- Service Item -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                        <div class="services-box effect-item mt-40">
                            <div class="services-item content-overlay">
                                <img src="{{asset('assets/img/Income-Tax1.png') }}" alt=" ">
                                <div class="content-details fadeIn-top">
                                    <i class="flaticon-male-arm-muscles "></i>
                                    <h3>Muscle Sculpe</h3>
                                    <p>Over 50 group training sessions per week There is no one type or way in our diverse community.</p>
                                </div>
                            </div>
                            <div class="services-text text-center">
                                <i class="flaticon-male-arm-muscles "></i>
                                <h3>Muscle Sculpe</h3>
                            </div>
                        </div>
                    </div>
                    <!-- Service Item -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                        <div class="services-box effect-item mt-40">
                            <div class="services-item content-overlay">
                                <img src="{{asset('assets/img/unnamed(1)1.png') }}" alt=" ">
                                <div class="content-details fadeIn-top">
                                    <i class="flaticon-calories "></i>
                                    <h3>pilates & stretching </h3>
                                    <p>Over 50 group training sessions per week There is no one type or way in our diverse community. </p>
                                </div>
                            </div>
                            <div class="services-text text-center">
                                <i class="flaticon-calories "></i>
                                <h3>pilates & stretching </h3>
                            </div>
                        </div>
                    </div>
                    <!-- Service Item -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                        <div class="services-box effect-item mt-40">
                            <div class="services-item content-overlay">
                                <img src="{{asset('assets/img/205_SE-Easiest-Sports-to-get-a-Scholarship(1)1.png') }}" alt=" ">
                                <div class="content-details fadeIn-top">
                                    <i class="flaticon-treadmill "></i>
                                    <h3>Cycling</h3>
                                    <p>Over 50 group training sessions per week There is no one type or way in our diverse community.</p>
                                </div>
                            </div>
                            <div class="services-text text-center">
                                <i class="flaticon-treadmill "></i>
                                <h3>Cycling</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =====>> End Services <<===== 
    =========================== -->
    <div class="container" style="width: 100%;max-width: 100%;background: #339032;padding: 40px 36px;margin-top: 30px;margin-bottom: -50px;">
        <div class="row">
            <div class="col-lg-3">
                <img src="{{asset('assets/img/2.png') }}" alt="">
            </div>
            <div class="col-lg-3">
                <img src="{{asset('assets/img/4.png') }}" alt="">
            </div>
            <div class="col-lg-3">
                <img src="{{asset('assets/img/6.png') }}" alt="">
            </div>
            <div class="col-lg-3">
                <img src="{{asset('assets/img/21.png') }}" alt="">
            </div>
        </div>
    </div>
    


    <!-- ===========================
    =====>> Bmi <<===== -->
    <section id="bmi-area" class="bmi-bg parallax pt-150 pb-150 mt-50">
        <div class="container">
            <div class="row ">
                <div class="col-lg-8">
                    <div class="section-title text-left">
                        <h2>GET IN TOUCH</h2>
                        <p>JOIN OUR TRAINING CLUB AND RISE TO A NEW CHALLENGE</p>
                    </div>
                </div>
            </div>
            <div class="bmi-content pt-80">
                <div class="row">
                    <div class="col-lg-6">
                        <!-- bmi nav -->
                        <ul class="bmi-tabs bmi-tab-btn" style="display:none">
                            <li class="tab-link " data-tab="tabs-1">STANDARD</li>
                            <li class="tab-link current" data-tab="tabs-2">metric</li>
                        </ul>

        
                        <div id="tabs-2" class="tab-content2 current">
                            <form class="row">
                                <div class="col-12">
                                    <h3> Name</h3>
                                </div>
                                <div class="col-12">
                                    <input type="text" class="input" name="feet" placeholder="Your name"></div>

                                <div class="col-12 pt-30">
                                    <h3> Email</h3>
                                </div>
                                <div class="col-12">
                                    <input type="text" class="input" name="feet" placeholder="Your Email"></div>

                                <div class="col-12 pt-30">
                                    <h3> Mobile</h3>
                                </div>
                                <div class="col-12">
                                    <input type="text" class="input" name="feet" placeholder="Your Mobile Number">
                                </div>
                                <div class="col-12 pt-30">
                                    <h3>Message</h3>
                                </div>
                                <div class="col-12">
                                    <textarea type="text" class="input" name="feet" ></textarea>
                                </div>
                                
                                <div class="col-6"><a href="#" class="btn container-btn btn-1">Submit</a></div>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =====>> End Bmi <<===== 
    =========================== -->
@endsection