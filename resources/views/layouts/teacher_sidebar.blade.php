{{-- {{Auth::user()->profile_image}} --}}
<?php 
if(!empty(Auth::user()->profile_image)){
  $url = asset('/public/uploads/userdocs/'. Auth::user()->profile_image);
}else{
  $url =  asset('/public/image/default.png');
}

?>
  <style type="text/css">
    nav .menuicon {
      height: auto;
      width: 2.1rem;
      box-shadow: 0 3px 6px rgba(0, 0, 0, .16), 0 3px 6px rgba(0, 0, 0, .23) !important;
      border-radius: 25%;
    }
  </style>
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{URL('/teacher/home')}}" class="brand-link">
      <img src="{{asset('/public/image/logo.png')}}" alt='{{ config("constants.site_name") }} Logo' class="brand-image bg-white" style="opacity: .8">
      <span class="brand-text font-weight-light" style="white-space: break-spaces;">{{ config("constants.site_name") }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{$url}}" style="height:40px !important;width:40px !important;" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{Auth::user()->name}}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
              <a href="{{URL('/teacher/home')}}" class="nav-link @yield('dashboard')">
                <img src="{{asset('/public/menuicons/dashboard.png')}}" class="menuicon">
                <p> Dashboard </p>
              </a>
          </li>

          <li class="nav-item">
            <a href="{{URL('/teacher/profile')}}" class="nav-link @yield('profile ')">
              <img src="{{asset('/public/menuicons/staff.png')}}" class="menuicon">
              <p> Profile </p>
            </a>
        </li>
          <li class="nav-item @yield('menuopenm')">
            <a href="#" class="nav-link @yield('master_settings')">
              <img src="{{asset('/public/menuicons/scholars.png')}}" class="menuicon">
              <p>Scholars
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{URL('/teacher/student')}}" class="nav-link @yield('master_students')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Scholars List</p>
                </a>
              </li>

              {{-- <li class="nav-item">
                <a href="{{URL('/teacher/student_promotions')}}" class="nav-link @yield('master_promotions')">
                  <i class="fa fa-users nav-icon"></i>
                  <p>Promotions</p>
                </a>
              </li> --}}
              {{-- <li class="nav-item">
                <a href="{{URL('/teacher/student_academics')}}" class="nav-link @yield('master_student_academics')">
                  <i class="fa fa-users nav-icon"></i>
                  <p>Students Class Mappings</p>
                </a>
              </li> --}}
              <li class="nav-item">
                <a href="{{URL('/teacher/student_dailyattendance')}}" class="nav-link @yield('master_attendance')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Students Daily Attendance</p>
                </a>
              </li>
            </ul>
          </li> 

          
        <li class="nav-item @yield('menuopencomn')">
            <a href="#" class="nav-link @yield('communicationsettings')">
              <img src="{{asset('/public/menuicons/communication.png')}}" class="menuicon">
              <p>Communication
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview">  

              <li class="nav-item">
                <a href="{{URL('/teacher/categories')}}" class="nav-link @yield('master_categories')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Category</p>
                </a>
              </li> 

              <li class="nav-item">
                <a href="{{URL('/teacher/bthemes')}}" class="nav-link @yield('master_bthemes')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Background Themes</p>
                </a>
              </li> 

              <li class="nav-item">
                <a href="{{URL('/teacher/group')}}" class="nav-link @yield('master_group')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Group</p>
                </a>
              </li> 

              <li class="nav-item">
                <a href="{{URL('/teacher/posts')}}" class="nav-link @yield('master_posts')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Posts</p>
                </a>
              </li> 

              <li class="nav-item">
                <a href="{{URL('/teacher/postsms')}}" class="nav-link @yield('master_postsms')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>SMS</p>
                </a>
              </li> 

            </ul>
        </li>
          
        <li class="nav-item @yield('menuopenac')">
          <a href="#" class="nav-link @yield('actsettings')">
            <img src="{{asset('/public/menuicons/activities.png')}}" class="menuicon">
            <p>Activity & Assignment
              <i class="fas fa-angle-left right"></i>
              <span class="badge badge-info right"></span>
            </p>
          </a>

          <ul class="nav nav-treeview">  
            <li class="nav-item">
              <a href="{{URL('/teacher/homework')}}" class="nav-link @yield('master_homework')">
                <i class="fas fa-database nav-icon"></i>
                <p>Home Work</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
            <a href="{{URL('/teacher/timetable')}}" class="nav-link @yield('master_timetable')">
              <img src="{{asset('/public/menuicons/settings.png')}}" class="menuicon">
              <p> Time Table </p>
            </a>
        </li>

        <li class="nav-item">
          <a href="{{URL('/teacher/circulars')}}" class="nav-link @yield('master_circulars')">
            <img src="{{asset('/public/menuicons/2.png')}}" class="menuicon">
            <p> Circulars </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{URL('/teacher/events')}}" class="nav-link @yield('master_events')">
            <img src="{{asset('/public/menuicons/2.png')}}" class="menuicon">
            <p>Events</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{URL('/teacher/marks_entry')}}" class="nav-link @yield('master_marks_entry')">
            <img src="{{asset('/public/menuicons/Tests (1).png')}}" class="menuicon">
            <p> Marks Entry </p>
          </a>
        </li>
        <li class="nav-item @yield('menuopenq')">
          <a href="#" class="nav-link @yield('questionbank_settings')">
            <img src="{{asset('/public/menuicons/qbank.png')}}" class="menuicon">
            <p>Question Bank
              <i class="fas fa-angle-left right"></i>
              <span class="badge badge-info right"></span>
            </p>
          </a>

          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{URL('/teacher/questionbank')}}" class="nav-link @yield('master_questionbank')">
                <i class="fas fa-database nav-icon"></i>
                <p>Question Bank</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item @yield('menuopent')">
          <a href="#" class="nav-link @yield('test_settings')">
            <img src="{{asset('/public/menuicons/tests.png')}}" class="menuicon">
            <p>Test
              <i class="fas fa-angle-left right"></i>
              <span class="badge badge-info right"></span>
            </p>
          </a>
    
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{URL('/teacher/testlist')}}" class="nav-link @yield('master_testlist')">
                <i class="fas fa-database nav-icon"></i>
                <p>Test list</p>
              </a>
            </li>
    
            <li class="nav-item">
              <a href="{{URL('/teacher/add/testlist')}}" class="nav-link @yield('master_addtestlist')">
                <i class="fas fa-database nav-icon"></i>
                <p>Manual Test Creation</p>
              </a>
            </li>
    
            <li class="nav-item">
              <a href="{{URL('/teacher/auto/testlist')}}" class="nav-link @yield('master_autoaddtestlist')">
                <i class="fas fa-database nav-icon"></i>
                <p>Auto Test Creation</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{URL('/teacher/testlistpapers')}}" class="nav-link @yield('master_testlistpapers')">
                <i class="fas fa-database nav-icon"></i>
                <p>Test list Papers</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{URL('/teacher/auto/testlistpapers')}}" class="nav-link @yield('master_autoaddtestlistpapers')">
                <i class="fas fa-database nav-icon"></i>
                <p>Auto Test Papers Creation</p>
              </a>
            </li>
            
          </ul>
        </li>

    <li class="nav-item @yield('menuopenr')">
      <a href="#" class="nav-link @yield('report_settings')">
        <img src="{{asset('/public/menuicons/reports.png')}}" class="menuicon">
        <p>Reports
          <i class="fas fa-angle-left right"></i>
          <span class="badge badge-info right"></span>
        </p>
      </a>

      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="{{URL('/teacher/studentsleave')}}" class="nav-link @yield('master_studentleave')">
            <i class="fa fa-users nav-icon"></i>
            <p> Students Leave </p>
          </a>
      </li>
  
      <li class="nav-item">
        <a href="{{URL('/teacher/tleave')}}" class="nav-link @yield('master_teacherleave')">
          <i class="fa fa-users nav-icon"></i>
          <p> Teacher Leave </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{URL('/teacher/tstudentstestlist')}}" class="nav-link @yield('master_studentstest')">
          <i class="fas fa-database nav-icon"></i>
          <p>Students Test</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{URL('/teacher/studentattendancerep')}}" class="nav-link @yield('master_studentsatten')">
          <i class="fas fa-database nav-icon"></i>
          <p>Students Attendance</p>
        </a>
      </li>
      </ul>
    </li>

   

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
