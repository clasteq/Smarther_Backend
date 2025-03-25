<?php  use App\Models\Countries; 
use App\Models\User;

if(isset(Auth::User()->id))  { 
  } else {  header('Location: '.URL('/admin'));exit;?>
<?php }
$username = Auth::User()->name;
$user_type = Auth::User()->user_type; 

if(!empty(Auth::User()->profile_image)) {
  $profile_image = User::getUserProfileImageAttribute(Auth::User()->id);
} else {
  $profile_image = User::getUserProfileImageAttribute(Auth::User()->id);  //'';
}

if($user_type == 'SCHOOL') {
  $schoolname = $username;
  $username = 'School Admin';
  $profileurl = URL('/admin/profile');
} else {
  $schoolname = DB::table('users')->where('id', Auth::User()->school_college_id)->value('name');
  $profileurl = URL('/admin/view_staff?id='.Auth::User()->id);
} 

$countries = Countries::where('status', 'ACTIVE')->orderby('position', 'asc')->get(); 
$session_country = Session::get('session_country');


?>
 <style type="text/css">
   .user-panel img {
        height: 2.5rem;
        width: 2.5rem;
    }
    .dropdown-menu {
      border-radius: 1.25rem;
    }
 </style>

 <style>
    /* Make sure the dropdown is always visible */
    /* Notification Menu */
    #notificationMenu {
        position: absolute;
        right: 0;
        left: -200px;
        top: 100%;
        min-width: 285px;
        max-width: 300px;
        background: white;
        border-radius: 5px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 1050 !important;
        display: none;
        padding: 10px;
    }
    /* Make the scroll bar look nicer */
    .notification-list::-webkit-scrollbar {
        width: 5px;
    }
    .notification-list::-webkit-scrollbar-thumb {
        background-color: #aaa;
        border-radius: 5px;
    }
    .notification-list::-webkit-scrollbar-track {
        background: transparent;
    }
    /* Badge styling */
    .notification-badge {
        position: absolute;
        top: 2px;
        right: 2px;
        background: red;
        color: white;
        font-size: 12px;
        font-weight: bold;
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    /* Ensure navbar does not interfere */
    .navbar {
        overflow: visible !important;
        position: sticky;
        z-index: 1040;
        /* Lower than the notification dropdown */
    }
    /* Notification List - Enable scrolling when more than 7 items */
    .notification-list {
        max-height: 300px;
        /* Set height limit */
        overflow-y: auto;
        /* Enable scrolling */
        display: flex;
        flex-direction: column;
    }
    .notification-item {
        display: flex;
        align-items: center;
        padding: 10px;
        text-decoration: none;
        color: black;
        border-radius: 5px;
        transition: background 0.3s ease;
    }
    .notification-item i {
        width: 20px;
        height: 20px;
        margin-right: 10px;
    }
    .notification-item:hover {
        background-color: #F1F1F1;
    }
</style> 

<style>
    /* User Menu Dropdown */
    .user-menu>a {
        display: flex;
        align-items: center;
        text-decoration: none;
        padding: 10px;
        color: #333;
    }
    .user-image {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-right: 8px;
    }
    .user-menu .dropdown-menu {
        width: 250px;
        border-radius: 5px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }
    .user-header {
        text-align: center;
        padding: 15px;
        background: #ff6f61;
        color: white;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }
    .user-header img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 2px solid white;
    }
    .user-header p {
        margin: 10px 0 5px;
        font-size: 14px;
    }
    .user-header small {
        font-size: 12px;
        opacity: 0.8;
    }
    .user-body {
        padding: 10px;
        background: #F9F9F9;
        text-align: center;
    }
    .user-body .row {
        display: flex;
        justify-content: space-between;
        padding: 5px 15px;
    }
    .user-body a {
        text-decoration: none;
        font-size: 14px;
        color: #555;
    }
    .user-footer .btn:hover {
        background: #bbb;
    } 
</style>

<!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-blue" style="  position: sticky; top: 0;  overflow: hidden; padding-bottom:3px;">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars" style="color:white;"></i></a>
      </li> 
      <li class="nav-item">  
        <a class="nav-link" data-widget="pushmenu" href="#" role="button" style="color:white;text-wrap: pretty;">{{ $schoolname }}</a>
      </li> 
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown  d-none">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <span class="badge badge-danger navbar-badge">3</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Brad Diesel
                  <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">Call me whenever you can...</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  John Pierce
                  <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">I got your message bro</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Nora Silvester
                  <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">The subject goes here</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
        </div>
      </li>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown  d-none">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">15 Notifications</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 new messages
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li> 
      <li class="nav-item  d-none">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>  


      <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <span class="hidden-xs mr-1">{{ $username }}</span>
                @if(empty($profile_image))
                  <?php $shortcode = $username; ?>
                  <svg class="col-md-2" height="100" width="100">
                    <defs>
                      <linearGradient id="grad1">
                        <stop offset="0%" stop-color="#FF6F61" />
                        <stop offset="100%" stop-color="#FF6F61" />
                      </linearGradient>
                    </defs>
                    <ellipse cx="60" cy="40" rx="40" ry="40" fill="url(#grad1)" />
                    <text fill="#ffffff" font-size="35" font-family="Verdana" x="35" y="55">{{$shortcode}}</text>
                    Sorry, your browser does not support inline SVG.
                  </svg>
                @else 
                  <img src="{{ $profile_image }}" class="user-image" alt="User Image">
                @endif 
            </a>
            <ul class="dropdown-menu">
                <!-- User image -->
                <li class="user-header">
                    @if(empty($profile_image))
                      <?php $shortcode = $username; ?>
                      <svg class="col-md-2" height="100" width="100">
                        <defs>
                          <linearGradient id="grad1">
                            <stop offset="0%" stop-color="#FF6F61" />
                            <stop offset="100%" stop-color="#FF6F61" />
                          </linearGradient>
                        </defs>
                        <ellipse cx="60" cy="40" rx="40" ry="40" fill="url(#grad1)" />
                        <text fill="#ffffff" font-size="35" font-family="Verdana" x="35" y="55">{{$shortcode}}</text>
                        Sorry, your browser does not support inline SVG.
                      </svg>
                    @else 
                    <img src="{{ $profile_image }}" class="img-circle" alt="User Image">
                    @endif  
                    <p>{{ $username }}
                        <small><!-- Member since Jan. 2020 --></small>
                    </p>
                </li>
                <!-- Menu Footer -->
                <li class="user-footer">
                    <div class="text-center">
                        <a href="{{$profileurl}}" class="btn btn-default btn-flat btn-block">My Profile</a>
                        <a href="{{URL('/admin/changepwd')}}" class="btn btn-default btn-flat btn-block">Change Password</a>
                    </div>
                </li>
            </ul>
        </li>

      

      @if($user_type == 'SCHOOL')
      <li class="nav-item dropdown ">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i> 
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <div class="dropdown-divider m-3"></div>

            <!-- Message Start -->
            <div class="media"> 
              <div class="media-body text-center">
                <a href="{{URL('/admin/posts')}}"> <img src="{{asset('/public/menuicons/communication.png')}}" class="menuicon">
                <p class="text mt-2">Posts</p> </a> 
              </div>
              <div class="media-body text-center">
                <a href="{{URL('/admin/postsms')}}"> <img src="{{asset('/public/menuicons/communication.png')}}" class="menuicon">
                <p class="text mt-2">SMS</p> </a> 
              </div>
              <div class="media-body text-center">
                <a href="{{URL('/admin/posthomeworks')}}"> <img src="{{asset('/public/menuicons/communication.png')}}" class="menuicon">
                <p class="text mt-2">Homework</p> </a> 
              </div>
            </div>

            <div class="dropdown-divider m-3"></div>

            <!-- Message Start -->
            <div class="media"> 
              <div class="media-body text-center">
                <a href="{{URL('/admin/student_daily_attendance')}}"> <img src="{{asset('/public/menuicons/scholarattendance.png')}}" class="menuicon"> 
                <p class="text mt-2"> Scholar Attendance  </p></a>
              </div>
              <div class="media-body text-center">
                <a href="{{URL('/admin/oa_student_attendance')}}"> <img src="{{asset('/public/menuicons/scholarattendance.png')}}" class="menuicon">
                <p class="text mt-2"> Overall Scholar  </p> </a>
              </div>
              <div class="media-body text-center">
                <a href="{{URL('/admin/staff_dailyattendance')}}"> <img src="{{asset('/public/menuicons/Scholar Attendance (3).png')}}" class="menuicon">
                <p class="text mt-2"> Staff Attendance </p> </a> 
              </div>
            </div>
            <!-- Message End -->

            <div class="dropdown-divider m-3"></div>

            <div class="media"> 
              <div class="media-body text-center">
                <a href="{{URL('/admin/fee_collection')}}"> <img src="{{asset('/public/menuicons/fee.png')}}" class="menuicon">
                <p class="text mt-2"> Fees  </p> </a>
              </div>
              <div class="media-body text-center">
                <a href="{{URL('/admin/fees_report')}}"> <img src="{{asset('/public/menuicons/fee.png')}}" class="menuicon"> 
                <p class="text mt-2"> Report  </p></a>
              </div>
              <div class="media-body text-center">
                <a href="{{URL('/admin/fees_summary_report')}}"> <img src="{{asset('/public/menuicons/fee.png')}}" class="menuicon"> 
                <p class="text mt-2"> Summary  </p></a>
              </div>
            </div>
            <!-- Message End -->
           
          <div class="dropdown-divider m-3"></div>
            <!-- Message Start -->
            <div class="media"> 
              <div class="media-body text-center">
                <a href="{{URL('/admin/student')}}"> <img src="{{asset('/public/menuicons/scholars.png')}}" class="menuicon"> 
                <p class="text mt-2"> Scholars  </p></a>
              </div>
              <div class="media-body text-center">
                <a href="{{URL('/admin/staffs')}}"> <img src="{{asset('/public/menuicons/staff.png')}}" class="menuicon">
                <p class="text mt-2"> Staffs </p> </a> 
              </div>
              <div class="media-body text-center">
                <a href="{{URL('/admin/ctutors')}}"> <img src="{{asset('/public/menuicons/staff.png')}}" class="menuicon">
                <p class="text mt-2"> Tutors  </p> </a>
              </div>
            </div>
            <!-- Message End -->

          <div class="dropdown-divider m-3"></div>
          
          <!-- Message Start -->
            <div class="media"> 
              <div class="media-body text-center">
                <a href="{{URL('/admin/examinations')}}"> <img src="{{asset('/public/menuicons/tests.png')}}" class="menuicon"> 
                <p class="text mt-2"> Exams  </p></a>
              </div>
              <div class="media-body text-center">
                <a href="{{URL('/admin/exam_marksentry')}}"> <img src="{{asset('/public/menuicons/Tests (1).png')}}" class="menuicon">
                <p class="text mt-2"> Mark Entry </p> </a> 
              </div>
              <div class="media-body text-center">
                <a href="{{URL('/admin/exam_results')}}"> <img src="{{asset('/public/menuicons/qbank.png')}}" class="menuicon">
                <p class="text mt-2"> Exam Results  </p> </a>
              </div>
            </div>
            <!-- Message End -->

           <div class="dropdown-divider m-3"></div>
        </div>
      </li>
      @endif
      <li class="nav-item">
        <a class="nav-link" href="{{URL('/admin/home')}}" role="button">
          <i class="fas fa-home"></i>
        </a>
      </li>  

      <!-- Notification Bell Dropdown -->
      <li class="nav-item position-relative" style="padding-right: 10px;">
          <!-- Bell Icon with Badge -->
          <a href="#" class="nav-link position-relative" id="notificationToggle">
              <i class="fas fa-bell"></i>
              <span class="notification-badge" id="notificationCount">0</span> <!-- Dynamic Count -->
          </a>
          <!-- Dropdown Menu -->
          <div id="notificationMenu" class="dropdown-menu p-3 shadow">
              <h6 class="dropdown-header">Notifications</h6>
              <div class="notification-list" id="notificationList">
                  <!-- Notifications will be added dynamically -->
              </div>
          </div>
      </li>  

      <li class="nav-item">
        <a class=" btn btn-danger"  href="{{URL('/admin/logout')}}" > <!-- Logout -->
           <i class="fas fa-power-off" style="color:white;"></i> 
        </a>
      </li>
    </ul>

  </nav>

  @include('layouts.admin_sidebar')
  <!-- /.navbar -->