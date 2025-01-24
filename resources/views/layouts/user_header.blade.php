
    <!-- ===========================
    =====>> Top Menu <<===== -->

    <header class="top-nav">
        <!-- Top Address -->
        <div class="top-address">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="top-address-ditels">
                            <ul>
                                <!-- <li>
                                    <i class="fas fa-map-marker-alt"></i>
                                    <a target="_blank" href="https://www.google.com/maps/place/New+York,+NY+10036,+USA/@40.7462126,-74.0089606,14z/data=!3m1!4b1!4m5!3m4!1s0x89c2585393f82307:0xf7d56896de1566ed!8m2!3d40.7602619!4d-73.9932872">Brooklyn, NY 10036</a>
                                </li> -->
                                
                                <li>
                                    <i class="fas fa-phone"></i>
                                    <a href="tel:9789999751" target="_blank">+91 9789999751</a>
                                </li>
                                <li>
                                    <i class="far fa-envelope"></i>
                                    <a href="mailto:info@sportsworks.com" target="_blank"><span>info@sportsworks.com</span></a>
                                </li>
                            </ul>
                        </div>
                        <div class="top-social">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Top Address -->

        <!-- Top Menu -->
        <nav id="cssmenu">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-2">
                        <div class="logo">
                            <a href="{{URL('/')}}"><img src="{{asset('assets/img/11.png') }}" alt="logo"></a>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-12">
                        <div id="head-mobile"></div>
                        <div class="button"></div>
                        <ul class="navbar-nav">
                            <li><a href="#">Coaches</a> </li>
                            <li><a href="#">Academy</a> </li>
                            <li><a href="#">School</a> </li>
                            <li><a href="#">Student</a> </li>
                            <?php if(Auth::check()) { if(Auth::User()->user_type == 'SCHOOL') {  ?>
                            <li><a href="javascript:void(0);"><i class="fas fa-user"> </i> Profile</a>
                                <ul>
                                    <li><a href="{{URL('/')}}/classes">Classes </a> </li> 
                                    <li><a href="{{URL('/')}}/sections">Sections </a> </li> 
                                    <li><a href="{{URL('/')}}/classtests">Tests </a> </li> 
                                    <li><a href="{{URL('/')}}/questions">Questions </a> </li> 
                                    <li><a href="{{URL('/')}}/students">Students </a> </li> 
                                    <li><a href="{{URL('/')}}/logout">Logout</a> </li>
                                </ul>
                            </li>
                        <?php } if(Auth::User()->user_type == 'ACADEMY') {  ?>
                            <li><a href="javascript:void(0);"><i class="fas fa-user"> </i> Profile</a>
                                <ul>
                                    <li><a href="{{URL('/')}}/trainers">Trainers </a> </li> 
                                    <li><a href="{{URL('/')}}/players">Players </a> </li>  
                                    <li><a href="{{URL('/')}}/logout">Logout</a> </li>
                                </ul>
                            </li>
                        <?php }  if(Auth::User()->user_type == 'USER' && Auth::User()->is_player == 1) {  ?>
                            <li><a href="javascript:void(0);"><i class="fas fa-user"> </i> Profile</a>
                                <ul>
                                    <li><a href="{{URL('/')}}/academies">Academies </a> </li>  
                                    <li><a href="{{URL('/')}}/logout">Logout</a> </li>
                                </ul>
                            </li>
                        <?php }    if(Auth::User()->user_type == 'USER' && Auth::User()->is_student == 1) {  ?>
                            <li><a href="javascript:void(0);"><i class="fas fa-user"> </i> Profile</a>
                                <ul>
                                    <li><a href="{{URL('/')}}/academies">Academies </a> </li>  
                                    <li><a href="{{URL('/')}}/logout">Logout</a> </li>
                                </ul>
                            </li>
                        <?php }  } ?>
                            <?php if(Auth::check()) { ?> 
                            <!-- <li><a href="{{URL('/')}}/logout">Logout</a> </li>  -->
                            <?php }   else { ?>
                                <li><a href="javascript:void(0);">Login</a>
                                    <ul>
                                        <li><a href="{{URL('/')}}/login">As School </a> </li> 
                                        <li><a href="{{URL('/')}}/academylogin">As Academy </a> </li> 
                                        <li><a href="{{URL('/')}}/studentlogin">As Student </a> </li> 
                                        <li><a href="{{URL('/')}}/playerlogin">As Player </a> </li>  
                                    </ul>
                                </li> 
                            <?php }  ?>

                            
                        </ul>
                    </div>
                    <div class="col-lg-1 text-right p-0 nobile-position"> 
                        <div class="search-dropdown">
                            <button type="button" class="icon-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search"></i>       
                            </button>
                            <form class="dropdown-menu dropdown-menu-right">
                                <input class="search-input " name="search" placeholder="Search " aria-label="Search ">
                                <button class="search-btn " type="submit">   </button>
                            </form>
                        </div>
                        <div class="become-member ">
                           <!--  <a href="#">BECOME A MEMBER</a> -->
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <!-- End Top Menu -->
    </header>
    <!-- =====>> End Top Menu <<===== 
    =========================== -->

