@extends('layouts.user_master')
@section('content')
    <!-- ===========================
    =====>> Page Hero <<===== -->
    <section id="page-hero" class="about-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title text-center">
                        <h1>fitness <span>academies</span></h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =====>> End Page Hero <<===== 
    =========================== -->

    <!-- ===========================
    =====>> Team <<===== -->
    <input type="hidden" name="filtercontent" id="filtercontent" value="academies">
    <section id="team-area" class="pt-50 pb-150">
        <div class="container">
            <div class="team-content" id="cpagelistnav">
                <div class="row">
                    @include('users.academies_list') 
                </div>
                <div class="row text-center pt-100 d-none">
                    <div class="col-lg-12 ">
                        <nav>
                            <ul class="pagination blog-pagination">
                                <li><a href="#"><i class="fas fa-chevron-left"></i></a></li>
                                <li><a href="#">1</a></li>
                                <li><a href="#">2</a></li>
                                <li><a href="#"><i class="fas fa-chevron-right"></i></a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =====>> End Team <<===== 
    =========================== -->
@endsection