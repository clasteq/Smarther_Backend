<?php  use App\Models\User;
$username = Auth::User()->name;
$user_type = Auth::User()->user_type; 
$profile_image = User::getUserProfileImageAttribute(Auth::User()->id);
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
    <a href="{{URL('/admin/home')}}" class="brand-link">
      <img src="{{asset('/public/image/logo.png')}}" alt='{{ config("constants.site_name") }} Logo' class="brand-image bg-white" style="opacity: .8">
      <span class="brand-text font-weight-light" style="white-space: break-spaces;">{{ config("constants.site_name") }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{$profile_image}}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info" style="text-wrap: pretty;">
          <a href="#" class="d-block">{{$username}}</a>
        </div>
      </div>
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
              <a href="{{URL('/admin/home')}}" class="nav-link @yield('dashboard')">
                <img src="{{asset('/public/menuicons/dashboard.png')}}" class="menuicon">
                <p> Dashboard </p>
              </a>
          </li>

          @if($user_type == 'SCHOOL')
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
                <a href="{{URL('/admin/posts')}}" class="nav-link @yield('master_posts')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Posts</p>
                </a>
              </li> 

              <li class="nav-item">
                <a href="{{URL('/admin/posthomeworks')}}" class="nav-link @yield('master_posthomeworks')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Homeworks</p>
                </a>
              </li> 

              <li class="nav-item">
                <a href="{{URL('/admin/postsms')}}" class="nav-link @yield('master_postsms')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>SMS</p>
                </a>
              </li> 

              <li class="nav-item">
                <a href="{{URL('/admin/group')}}" class="nav-link @yield('master_group')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Group</p>
                </a>
              </li> 

              <li class="nav-item">
                <a href="{{URL('/admin/bthemes')}}" class="nav-link @yield('master_bthemes')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Background Themes</p>
                </a>
              </li> 

              <li class="nav-item">
                <a href="{{URL('/admin/categories')}}" class="nav-link @yield('master_categories')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Category</p>
                </a>
              </li> 

            </ul>
          </li>
          <li class="nav-item @yield('menuopenfee')">
              <a href="#" class="nav-link @yield('feessettings')">
                  <img src="{{asset('/public/menuicons/fee.png')}}" class="menuicon">
                  <p>Fees
                      <i class="fas fa-angle-left right"></i>
                      <span class="badge badge-info right"></span>
                  </p>
              </a>

              <ul class="nav nav-treeview">

                  <li class="nav-item">
                      <a href="{{ URL('/admin/receipt_head') }}" class="nav-link @yield('master_receipthead')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Receipt Head</p>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fees_account') }}" class="nav-link @yield('master_fees_account')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fee Account</p>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fee_category') }}" class="nav-link @yield('master_feecategory')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Category</p>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fee_items') }}" class="nav-link @yield('master_fee_items')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fee Items</p>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fee_terms') }}" class="nav-link @yield('master_fee_terms')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fee Terms</p>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a href="{{ URL('/admin/payment_mode') }}" class="nav-link @yield('master_paymentmode')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Payment Modes</p>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fee_cancel_reason') }}" class="nav-link @yield('master_fee_cancel')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fee Cancel Reason</p>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a href="{{ URL('/admin/concession_category') }}" class="nav-link @yield('master_concession_category')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Concession Category</p>
                      </a>
                  </li>
                  <li class="nav-item">
                  <li class="nav-item">
                    <a href="{{ URL('/admin/wavier_category') }}" class="nav-link @yield('master_waiver_category')">
                        <i class="fas fa-database nav-icon"></i>
                        <p>Waiver Category</p>
                    </a>
                </li>
                  <li class="nav-item">
                      <a href="{{ URL('/admin/bank_master') }}" class="nav-link @yield('master_bank_list')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>School Bank List</p>
                      </a>
                  </li> 

              </ul>
          </li>
          <li class="nav-item @yield('menuopenfeemod')">
              <a href="#" class="nav-link @yield('feesmod')">
                  <img src="{{asset('/public/menuicons/fee.png')}}" class="menuicon">
                  <p>Fees 
                      <i class="fas fa-angle-left right"></i>
                      <span class="badge badge-info right"></span>
                  </p>
              </a>

              <ul class="nav nav-treeview">

                  <li class="nav-item">
                      <a href="{{ URL('/admin/fee_collection') }}" class="nav-link @yield('fees_collection')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Collection</p>
                      </a>
                  </li>

                  <li class="nav-item">
                      <a href="{{ URL('/admin/fee_report/collection') }}" class="nav-link @yield('fee_collection_report')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Collection Report</p>
                      </a>
                  </li> 

                  <li class="nav-item d-none">
                      <a href="{{ URL('/admin/fee_summary') }}" class="nav-link @yield('fee_summary')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Summary</p>
                      </a>
                  </li>

                  <li class="nav-item">
                      <a href="{{ URL('/admin/fee_structure/list') }}" class="nav-link @yield('fees_structure')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Structure</p>
                      </a>
                  </li>
                  
              </ul>
          </li>
          <li class="nav-item @yield('menuopenfeereport')">
              <a href="#" class="nav-link @yield('feesreport')">
                  <img src="{{asset('/public/menuicons/fee.png')}}" class="menuicon">
                  <p>Fees Report
                      <i class="fas fa-angle-left right"></i>
                      <span class="badge badge-info right"></span>
                  </p>
              </a>

              <ul class="nav nav-treeview">
                  
              </ul>
          </li>
          @endif

          <li class="nav-item @yield('menuopenm')">
            <a href="#" class="nav-link @yield('master_settings')">
              <img src="{{asset('/public/menuicons/masters.png')}}" class="menuicon">
              <p>Master Settings
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview">

              @if($user_type == 'SUPER_ADMIN')
              <li class="nav-item">
                <a href="{{URL('/admin/countries')}}" class="nav-link @yield('master_countries')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Country</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/states')}}" class="nav-link @yield('master_states')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>State</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/districts')}}" class="nav-link @yield('master_districts')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>District</p>
                </a>
              </li> 
              <li class="nav-item d-none">
                <a href="{{URL('/admin/days')}}" class="nav-link @yield('master_days')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Days</p>
                </a>
              </li>
              @endif
              @if($user_type == 'SCHOOL')
              
              <li class="nav-item">
                <a href="{{URL('/admin/subjects')}}" class="nav-link @yield('master_subjects')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Subjects</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/grades')}}" class="nav-link @yield('master_grades')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Grades</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/mclasses')}}" class="nav-link @yield('master_mclasses')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Classes</p>
                </a>
              </li> 
              <li class="nav-item d-none">
                <a href="{{URL('/admin/classes')}}" class="nav-link @yield('master_classes')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Classes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/sections')}}" class="nav-link @yield('master_sections')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Sections</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/section_subjects')}}" class="nav-link @yield('master_section_subjects')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Section Subject Mappings</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/period_timing')}}" class="nav-link @yield('master_PeriodsTiming')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Periods Timing</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/circulars')}}" class="nav-link @yield('master_circulars')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Circulars</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/events')}}" class="nav-link @yield('master_events')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Events</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/holidays')}}" class="nav-link @yield('master_holidays')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Holidays</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/changeholidays')}}" class="nav-link @yield('master_changeholidays')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Change Holidays</p>
                </a>
              </li> 
              <li class="nav-item">
                <a href="{{URL('/admin/timetable')}}" class="nav-link @yield('master_timetable')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Timetable</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/chapters')}}" class="nav-link @yield('master_chapters')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Chapters</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/chaptertopics')}}" class="nav-link @yield('master_chapertopics')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Chapter Topics</p>
                </a>
              </li> 
              <li class="nav-item">
                <a href="{{URL('/admin/topics')}}" class="nav-link @yield('master_topics')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Books</p>
                </a>
              </li>
              @endif
            </ul>
          </li>
          
          @if($user_type == 'SCHOOL')
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
                <a href="{{URL('/admin/homework')}}" class="nav-link @yield('master_homework')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Home Work</p>
                </a>
              </li>
            </ul>
          </li>

          @endif
          @if($user_type == 'SUPER_ADMIN')
          <li class="nav-item">
              <a href="{{URL('/admin/schools')}}" class="nav-link @yield('master_schools')">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p> Schools </p>
              </a>
          </li> 

          <li class="nav-item">
            <a href="{{URL('/admin/smscredits')}}" class="nav-link @yield('master_smscredits')">
              <i class="fas fa-database nav-icon"></i>
              <p>SMS Credits</p>
            </a>
          </li> 
          @endif
          @if($user_type == 'SCHOOL')
          <li class="nav-item @yield('menuopensch')">
            <a href="#" class="nav-link @yield('schsettings')">
              <img src="{{asset('/public/menuicons/scholars.png')}}" class="menuicon">
              <p>Scholars
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview"> 
              
              <li class="nav-item">
                <a href="{{URL('/admin/student')}}" class="nav-link @yield('master_students')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Scholars List</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{URL('/admin/import_students')}}" class="nav-link @yield('master_import_students')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Import Scholars</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{URL('/admin/pre_student')}}" class="nav-link @yield('master_pre_student')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Pre Admission Scholars List</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{URL('/admin/alumnis')}}" class="nav-link @yield('master_alumnis')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Alumni Scholars List</p>
                </a>
              </li>

            </ul>
          </li>

          <li class="nav-item @yield('menuopensta')">
            <a href="#" class="nav-link @yield('stasettings')">
              <img src="{{asset('/public/menuicons/staff.png')}}" class="menuicon">
              <p>Staffs
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview"> 
              
              <li class="nav-item">
                <a href="{{URL('/admin/teachers')}}" class="nav-link @yield('master_teachers')">
                  <i class="fas fa-code-branch nav-icon"></i>
                  <p>Staffs List</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{URL('/admin/import_teachers')}}" class="nav-link @yield('master_import_teachers')">
                  <i class="fas fa-code-branch nav-icon"></i>
                  <p>Import Staffs</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item @yield('menuopenmap')">
            <a href="#" class="nav-link @yield('mapsettings')">
              <img src="{{asset('/public/menuicons/mapping.png')}}" class="menuicon">
              <p>Mapping
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview">     
              <li class="nav-item">
                <a href="{{URL('/admin/class_teachers')}}" class="nav-link @yield('master_class_teachers')">
                  <i class="fas fa-code-branch nav-icon"></i>
                  <p>Class Teacher Mapping</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/subject_mapping')}}" class="nav-link @yield('master_subject_mapping')">
                  <i class="fas fa-code-branch nav-icon"></i>
                  <p>Teachers Subject Mapping</p>
                </a>
              </li>
             
            </ul>
          </li>
          @endif   
          @if($user_type == 'SCHOOL')
          <li class="nav-item @yield('menuopena')">
            <a href="#" class="nav-link @yield('academicsettings')">
              <img src="{{asset('/public/menuicons/academics.png')}}" class="menuicon">
              <p>Academic Details
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{URL('/admin/exams')}}" class="nav-link @yield('master_exams')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Exams</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{URL('/admin/terms')}}" class="nav-link @yield('master_terms')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Terms</p>
                </a>
              </li>

              {{-- <li class="nav-item">
                <a href="{{URL('/admin/student_academics')}}" class="nav-link @yield('master_classmappings')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Students Class Mappings</p>
                </a>
              </li> --}}

              <li class="nav-item">
                <a href="{{URL('/admin/student_promotions')}}" class="nav-link @yield('master_promotions')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Promotions</p>
                </a>
              </li>

              {{-- <li class="nav-item">
                <a href="{{URL('/admin/student_attendance')}}" class="nav-link @yield('master_sattendance')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Students Attendance</p>
                </a>
              </li> --}}

              
              {{-- <li class="nav-item">
                <a href="{{URL('/admin/teacher_attendance')}}" class="nav-link @yield('master_tattendance')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Teachers Attendance</p>
                </a>
              </li> --}}

              <li class="nav-item">
                <a href="{{URL('/admin/marks_entry')}}" class="nav-link @yield('master_marksentry')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Marks Entry</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item @yield('menuopenatt')">
            <a href="#" class="nav-link @yield('attendancesettings')">
              <img src="{{asset('/public/menuicons/scholarattendance.png')}}" class="menuicon">
              <p>Scholar Attendance
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{URL('/admin/oa_student_attendance_approval')}}" class="nav-link @yield('master_oa_student_attendance_approval')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Overall Students Attendance Approval</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/student_daily_attendance')}}" class="nav-link @yield('master_sattendance')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Students Daily Attendance</p>
                </a>
              </li>
              <li class="nav-item d-none">
                <a href="{{URL('/admin/student_attendance_up_report')}}" class="nav-link @yield('master_sattendance_upreport')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Students Attendance Update Report</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{URL('/admin/student_dailyattendance')}}" class="nav-link @yield('master_sattendance')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Students Daily Attendance Table</p>
                </a>
              </li>

              <li class="nav-item d-none">
                <a href="{{URL('/admin/studentspresence')}}" class="nav-link @yield('master_studentspresence')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Students Attendance Report</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/studentsleavelist')}}" class="nav-link @yield('master_studentleave')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Student Leave</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/studentattendancerep')}}" class="nav-link @yield('master_studentsatten')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Students Attendance Report</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/studentleavereports')}}" class="nav-link @yield('master_studentsabsent')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Students Absence Report</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{URL('/admin/oa_student_attendance')}}" class="nav-link @yield('master_oa_student_attendance')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Overall Students Attendance Report</p>
                </a>
              </li>
            </ul>

          </li>

          <li class="nav-item @yield('menuopenstaatt')">
            <a href="#" class="nav-link @yield('staattendancesettings')">
              <img src="{{asset('/public/menuicons/staffattendance.png')}}" class="menuicon">
              <p>Staff Attendance
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{URL('/admin/teacher_dailyattendance')}}" class="nav-link @yield('master_tattendance')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Staff Daily Attendance</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{URL('/admin/teacherleavelist')}}" class="nav-link @yield('master_teacherleave')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Staff Leave</p>
                </a>
              </li>


              <li class="nav-item">
                <a href="{{URL('/admin/teacherattendancerep')}}" class="nav-link @yield('master_teachersatten')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Teachers Attendance Report</p>
                </a>
              </li>

            </ul>
          </li>

          <li class="nav-item @yield('menuopenq')">
            <a href="#" class="nav-link @yield('questionbanksettings')">
              <img src="{{asset('/public/menuicons/qbank.png')}}" class="menuicon">
              <p>Question Bank
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{URL('/admin/questionbank')}}" class="nav-link @yield('master_questionbank')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Question Bank</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item @yield('menuopent')">
            <a href="#" class="nav-link @yield('testsettings')">
              <img src="{{asset('/public/menuicons/tests.png')}}" class="menuicon">
              <p>Test
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{URL('/admin/testlist')}}" class="nav-link @yield('master_testlist')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Test list</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{URL('/admin/add/testlist')}}" class="nav-link @yield('master_addtestlist')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Manual Test Creation</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{URL('/admin/auto/testlist')}}" class="nav-link @yield('master_autoaddtestlist')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Auto Test Creation</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{URL('/admin/testlistpapers')}}" class="nav-link @yield('master_testlistpapers')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Test list Papers</p>
                </a>
              </li>

              <li class="nav-item d-none">
                <a href="{{URL('/admin/auto/testlistpapers')}}" class="nav-link @yield('master_autoaddtestlistpapers')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Auto Test Papers Creation</p>
                </a>
              </li>

            </ul>
          </li>

          <li class="nav-item @yield('menuopenr')">
            <a href="#" class="nav-link @yield('reportsettings')">
              <img src="{{asset('/public/menuicons/reports.png')}}" class="menuicon">
              <p>Reports
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{URL('/admin/studentstrength')}}" class="nav-link @yield('master_studentstrength')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Students Strength</p>
                </a>
              </li> 
              <li class="nav-item">
                <a href="{{URL('/admin/studentstestlist')}}" class="nav-link @yield('master_studentstest')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Students Test</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/studenttestattempts')}}" class="nav-link @yield('master_studenttestattempts')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Students Test Attempts</p>
                </a>
              </li> 
            </ul>
          </li>
          
          <li class="nav-item @yield('menuopen')">
            <a href="#" class="nav-link @yield('settings1')">
              <img src="{{asset('/public/menuicons/settings.png')}}" class="menuicon">
              <p>General Settings
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{URL('/admin/settings')}}" class="nav-link @yield('settings_admin')">
                  <i class="fas fa-cogs nav-icon"></i>
                  <p>Admin Settings</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/about')}}" class="nav-link @yield('settings_about')">
                  <i class="fas fa-info nav-icon"></i>
                  <p>About</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/privacypolicy')}}" class="nav-link @yield('settings_privacy')">
                  <i class="fas fa-clipboard-check nav-icon"></i>
                  <p>Privacy Policy</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/termscond')}}" class="nav-link @yield('settings_terms')">
                  <i class="fas fa-clipboard-check nav-icon"></i>
                  <p>Terms and Conditions</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/faq')}}" class="nav-link @yield('settings_faq')">
                  <i class="fas fa-question-circle nav-icon"></i>
                  <p>Faq</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/contacts_for')}}" class="nav-link @yield('settings_contacts_for')">
                  <i class="fas fa-question-circle nav-icon"></i>
                  <p>Contacts For </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/contacts_list')}}" class="nav-link @yield('settings_contacts_list')">
                  <i class="fas fa-question-circle nav-icon"></i>
                  <p>Contacts List </p>
                </a>
              </li>
            </ul>
          </li>
          @endif
        </ul>
      </nav>
      <!-- /.sidebar-menu -->

    </div>
    <!-- /.sidebar -->
  </aside>
