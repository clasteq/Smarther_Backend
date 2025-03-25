<?php  use App\Models\User;
$username = Auth::User()->name;
$user_type = Auth::User()->user_type; 
$profile_image = User::getUserProfileImageAttribute(Auth::User()->id);
$session_module = session()->get('module');
if($user_type == 'TEACHER') {
  //echo "<pre>"; print_r($session_module); exit; 
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
    <a href="{{URL('/admin/home')}}" class="brand-link">
      <img src="{{asset('/public/image/logo.png')}}" alt='{{ config("constants.site_name") }} Logo' class="brand-image bg-white" style="opacity: .8">
      <span class="brand-text font-weight-light" style="white-space: break-spaces;">{{ config("constants.site_name") }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <!-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{$profile_image}}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info" style="text-wrap: pretty;">
          <a href="#" class="d-block">{{$username}}</a>
        </div>
      </div> -->
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          @if(($user_type == 'SCHOOL' || $user_type == 'SUPER_ADMIN') || (isset($session_module['Home'])) && $session_module['Home']['view'] == 1)
          <li class="nav-item">
              <a href="{{URL('/admin/home')}}" class="nav-link @yield('dashboard')">
                <img src="{{asset('/public/menuicons/dashboard.png')}}" class="menuicon">
                <p> Dashboard </p>
              </a>
          </li>
          @endif

          <!-- if($user_type == 'SCHOOL') -->
          @if (!in_array($user_type, ['GUESTUSER', 'STUDENT', 'SUPER_ADMIN']) && (isset($session_module['Communication']) || ($user_type == 'SCHOOL')))

          <li class="nav-item @yield('menuopencomn')">
            <a href="#" class="nav-link @yield('comn_settings')">
              <img src="{{asset('/public/menuicons/communication.png')}}" class="menuicon">
              <p>Communication
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview">  
              @if((isset($session_module['Posts'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/posts')}}" class="nav-link @yield('master_posts')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Posts</p>
                </a>
              </li> 
              @endif

              @if((isset($session_module['Homeworks'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/posthomeworks')}}" class="nav-link @yield('master_posthomeworks')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Homeworks</p>
                </a>
              </li> 
              @endif

              @if((isset($session_module['SMS'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/postsms')}}" class="nav-link @yield('master_postsms')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>SMS</p>
                </a>
              </li> 
              @endif

              @if((isset($session_module['Staff Posts'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/posts_staff')}}" class="nav-link @yield('master_posts_staff')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Staff Posts</p>
                </a>
              </li> 
              @endif

              @if((isset($session_module['Group'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/group')}}" class="nav-link @yield('master_group')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Group</p>
                </a>
              </li> 
              @endif 

              @if((isset($session_module['Background Themes'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/bthemes')}}" class="nav-link @yield('master_bthemes')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Background Themes</p>
                </a>
              </li> 
              @endif

              @if((isset($session_module['Category'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/categories')}}" class="nav-link @yield('master_categories')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Category</p>
                </a>
              </li> 
              @endif

              @if((isset($session_module['Survey'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/survey')}}" class="nav-link @yield('master_survey')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Survey</p>
                </a>
              </li> 
              @endif

              @if((isset($session_module['Remarks'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/remarks')}}" class="nav-link @yield('master_remarks')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Remarks</p>
                </a>
              </li> 
              @endif

              @if((isset($session_module['Rewards'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/rewards')}}" class="nav-link @yield('master_rewards')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Rewards</p>
                </a>
              </li> 
              @endif
            </ul>
          </li> 

          @endif

          @if (!in_array($user_type, ['GUESTUSER', 'STUDENT', 'SUPER_ADMIN']) && (isset($session_module['Fees']) || ($user_type == 'SCHOOL')))
          <li class="nav-item @yield('menuopenfee')">
              <a href="#" class="nav-link @yield('feessettings')">
                  <img src="{{asset('/public/menuicons/fee.png')}}" class="menuicon">
                  <p>Fees 
                      <i class="fas fa-angle-left right"></i>
                      <span class="badge badge-info right"></span>
                  </p>
              </a>

              <ul class="nav nav-treeview">
                  @if((isset($session_module['Fees Collection'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fee_collection') }}" class="nav-link @yield('fees_collection')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Collection</p>
                      </a>
                  </li>
                  @endif
                  @if((isset($session_module['Fees Collection Report'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fee_report/collection') }}" class="nav-link @yield('fee_collection_report')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Collection Report</p>
                      </a>
                  </li> 
                  @endif
                  @if((isset($session_module['Fees Report'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fees_report') }}" class="nav-link @yield('fee_report')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Report</p>
                      </a>
                  </li> 
                  @endif
                  @if((isset($session_module['Fees Summary'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fees_summary_report') }}" class="nav-link @yield('fee_summary')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Summary</p>
                      </a>
                  </li>
                  @endif
                  @if((isset($session_module['Fees Receipts'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fees_receipts_report') }}" class="nav-link @yield('fee_receipts')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Receipts</p>
                      </a>
                  </li>
                  @endif
                  @if((isset($session_module['Fees Receipts Cancelled'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fees_receipts_cancelled_report') }}" class="nav-link @yield('fee_receipts_cancelled')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Receipts Cancelled</p>
                      </a>
                  </li>
                  @endif
                  @if((isset($session_module['Fees Overall'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fees_overall_report') }}" class="nav-link @yield('fee_overall')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Overall</p>
                      </a>
                  </li>
                  @endif

                  @if((isset($session_module['Fees Concession Report'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/conwai_fee_report/collection') }}" class="nav-link @yield('conwai_fee_report')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Concession Report</p>
                      </a>
                  </li> 
                  @endif
                  @if((isset($session_module['Fees Waiver Report'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/waiver_fee_report/collection') }}" class="nav-link @yield('waiver_fee_report')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Waiver Report</p>
                      </a>
                  </li> 
                  @endif
                  @if((isset($session_module['Fees Pending Report'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/pending_fee_report/collection') }}" class="nav-link @yield('pending_fee_report')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Pending Report</p>
                      </a>
                  </li> 
                  @endif
                  @if((isset($session_module['Fees Summary'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item d-none">
                      <a href="{{ URL('/admin/fee_summary') }}" class="nav-link @yield('fee_summary')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Summary</p>
                      </a>
                  </li>
                  @endif
                  @if((isset($session_module['Fees Structure'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fee_structure/list') }}" class="nav-link @yield('fees_structure')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Structure</p>
                      </a>
                  </li>
                  @endif
                  @if((isset($session_module['Receipt Head'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/receipt_head') }}" class="nav-link @yield('master_receipthead')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Receipt Head</p>
                      </a>
                  </li>
                  @endif
                  @if((isset($session_module['Fee Account'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fees_account') }}" class="nav-link @yield('master_fees_account')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fee Account</p>
                      </a>
                  </li>
                  @endif
                  @if((isset($session_module['Fees Category'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fee_category') }}" class="nav-link @yield('master_feecategory')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fees Category</p>
                      </a>
                  </li>
                  @endif
                  @if((isset($session_module['Fee Items'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fee_items') }}" class="nav-link @yield('master_fee_items')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fee Items</p>
                      </a>
                  </li>
                  @endif
                  @if((isset($session_module['Fee Terms'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fee_terms') }}" class="nav-link @yield('master_fee_terms')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fee Terms</p>
                      </a>
                  </li>
                  @endif
                  @if((isset($session_module['Payment Modes'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/payment_mode') }}" class="nav-link @yield('master_paymentmode')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Payment Modes</p>
                      </a>
                  </li>
                  @endif
                  @if((isset($session_module['Fee Cancel Reason'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/fee_cancel_reason') }}" class="nav-link @yield('master_fee_cancel')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Fee Cancel Reason</p>
                      </a>
                  </li>
                  @endif
                  @if((isset($session_module['Concession Category'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/concession_category') }}" class="nav-link @yield('master_concession_category')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>Concession Category</p>
                      </a>
                  </li>
                  @endif
                  @if((isset($session_module['Waiver Category'])) || ($user_type == 'SCHOOL')) 
                  <li class="nav-item">
                    <a href="{{ URL('/admin/wavier_category') }}" class="nav-link @yield('master_waiver_category')">
                        <i class="fas fa-database nav-icon"></i>
                        <p>Waiver Category</p>
                    </a>
                  </li>
                  @endif
                  @if((isset($session_module['School Bank List'])) || ($user_type == 'SCHOOL'))
                  <li class="nav-item">
                      <a href="{{ URL('/admin/bank_master') }}" class="nav-link @yield('master_bank_list')">
                          <i class="fas fa-database nav-icon"></i>
                          <p>School Bank List</p>
                      </a>
                  </li>
                  @endif
              </ul>
          </li> 
          @endif

          @if(!in_array($user_type, ['GUESTUSER', 'STUDENT', 'SUPER_ADMIN']) && (isset($session_module['Scholars']) || ($user_type == 'SCHOOL')))
          <li class="nav-item @yield('menuopensch')">
            <a href="#" class="nav-link @yield('schsettings')">
              <img src="{{asset('/public/menuicons/scholars.png')}}" class="menuicon">
              <p>Scholars
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview"> 
              @if((isset($session_module['Scholars'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/student')}}" class="nav-link @yield('master_students')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Scholars</p>
                </a>
              </li>
              @endif
              @if((isset($session_module['Pre Admission'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/pre_student')}}" class="nav-link @yield('master_pre_student')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Pre Admission</p>
                </a>
              </li>
              @endif
              @if((isset($session_module['Alumni'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/alumnis')}}" class="nav-link @yield('master_alumnis')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Alumni</p>
                </a>
              </li>
              @endif
              @if((isset($session_module['Import'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/import_students')}}" class="nav-link @yield('master_import_students')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Import</p>
                </a>
              </li>
              @endif
            </ul>
          </li>
          @endif

          @if(!in_array($user_type, ['GUESTUSER', 'STUDENT', 'SUPER_ADMIN']) && (isset($session_module['Staffs']) || ($user_type == 'SCHOOL')))
          <li class="nav-item @yield('menuopensta')">
            <a href="#" class="nav-link @yield('stasettings')">
              <img src="{{asset('/public/menuicons/staff.png')}}" class="menuicon">
              <p>Staffs
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview"> 
              @if((isset($session_module['Staffs'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/staffs')}}" class="nav-link @yield('master_teachers')">
                  <i class="fas fa-code-branch nav-icon"></i>
                  <p>Staffs</p>
                </a>
              </li>
              @endif
              @if((isset($session_module['Roles'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/userroles')}}" class="nav-link @yield('master_userroles')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Roles</p>
                </a>
              </li>
              @endif
              @if((isset($session_module['Departments'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/departments')}}" class="nav-link @yield('master_departments')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Departments</p>
                </a>
              </li>
              @endif
              @if((isset($session_module['Import'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/import_staffs')}}" class="nav-link @yield('master_import_teachers')">
                  <i class="fas fa-code-branch nav-icon"></i>
                  <p>Import</p>
                </a>
              </li>
              @endif
            </ul>
          </li>
          @endif

          @if(!in_array($user_type, ['GUESTUSER', 'STUDENT', 'SUPER_ADMIN']) && (isset($session_module['Mapping']) || ($user_type == 'SCHOOL')))
          <li class="nav-item @yield('menuopenmap')">
            <a href="#" class="nav-link @yield('mapsettings')">
              <img src="{{asset('/public/menuicons/mapping.png')}}" class="menuicon">
              <p>Mapping
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview">
              @if((isset($session_module['Class Teacher'])) || ($user_type == 'SCHOOL'))     
              <li class="nav-item">
                <a href="{{URL('/admin/ctutors')}}" class="nav-link @yield('master_class_teachers')">
                  <i class="fas fa-code-branch nav-icon"></i>
                  <p>Class Teacher</p>
                </a>
              </li>
              @endif
              @if((isset($session_module['Teachers Subjects'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/subject_mapping')}}" class="nav-link @yield('master_subject_mapping')">
                  <i class="fas fa-code-branch nav-icon"></i>
                  <p>Teachers Subjects</p>
                </a>
              </li>
              @endif
              @if((isset($session_module['Teacher Role Module'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/staff_module_mapping')}}" class="nav-link @yield('master_teacher_module_mapping')">
                  <i class="fas fa-code-branch nav-icon"></i>
                  <p>Teacher Role Module</p>
                </a>
              </li>
              @endif
              @if((isset($session_module['Role Module Mapping'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/role_module_mapping')}}" class="nav-link @yield('masterrole_module_mapping')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Role Module Mapping</p>
                </a>
              </li>
              @endif
              @if((isset($session_module['Role Class Module'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/role_class_mapping')}}" class="nav-link @yield('masterrole_class_mapping')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Role Class Mapping</p>
                </a>
              </li>
              @endif
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
            <a href="{{URL('/admin/smsschoolcredits')}}" class="nav-link @yield('master_smsschoolcredits')">
              <i class="fas fa-database nav-icon"></i>
              <p>SMS Credits</p>
            </a>
          </li> 

          <li class="nav-item">
            <a href="{{URL('/admin/smscredits')}}" class="nav-link @yield('master_smscredits')">
              <i class="fas fa-database nav-icon"></i>
              <p>SMS Credits History</p>
            </a>
          </li> 
          @endif
          
          @if(!in_array($user_type, ['GUESTUSER', 'STUDENT', 'SUPER_ADMIN']) && (isset($session_module['Academic Details']) || ($user_type == 'SCHOOL')))
          <li class="nav-item @yield('menuopena')">
            <a href="#" class="nav-link @yield('academic_settings')">
              <img src="{{asset('/public/menuicons/academics.png')}}" class="menuicon">
              <p>Academic Details
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview">
              @if((isset($session_module['Exams'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/examinations')}}" class="nav-link @yield('master_examinations')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Exams</p>
                </a>
              </li>
              @endif
              @if((isset($session_module['Exam Settings'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/examination_settings')}}" class="nav-link @yield('master_examination_settings')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Exam Settings</p>
                </a>
              </li>
              @endif
              @if((isset($session_module['Marks Entry'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/exam_marksentry')}}" class="nav-link @yield('master_marksentry')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Marks Entry</p>
                </a>
              </li>
              @endif
              {{-- <li class="nav-item">
                <a href="{{URL('/admin/exams')}}" class="nav-link @yield('master_exams')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Exams</p>
                </a>
              </li> --}}
              @if((isset($session_module['Terms'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/terms')}}" class="nav-link @yield('master_terms')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Terms</p>
                </a>
              </li>
              @endif
              {{-- <li class="nav-item">
                <a href="{{URL('/admin/student_academics')}}" class="nav-link @yield('master_classmappings')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Students Class Mappings</p>
                </a>
              </li> --}}
              @if((isset($session_module['Promotions'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/student_promotions')}}" class="nav-link @yield('master_promotions')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Promotions</p>
                </a>
              </li>
              @endif
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
              </li> 

              <li class="nav-item">
                <a href="{{URL('/admin/marks_entry')}}" class="nav-link @yield('master_marksentry')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Marks Entry</p>
                </a>
              </li>--}}
              @if((isset($session_module['Exam Results'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/exam_results')}}" class="nav-link @yield('master_exam_results')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Exam Results</p>
                </a>
              </li>
              @endif
            </ul>
          </li>
          @endif

          @if(!in_array($user_type, ['GUESTUSER', 'STUDENT', 'SUPER_ADMIN']) && (isset($session_module['Scholar Attendance']) || ($user_type == 'SCHOOL')))
          <li class="nav-item @yield('menuopenatt')">
            <a href="#" class="nav-link @yield('attendance_settings')">
              <img src="{{asset('/public/menuicons/scholarattendance.png')}}" class="menuicon">
              <p>Scholar Attendance
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview">

              <li class="nav-item">
                <a href="{{URL('/admin/mark_attendance')}}" class="nav-link @yield('master_mark_attendance')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Mark Attendance</p>
                </a>
              </li>




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
          @endif

          @if(!in_array($user_type, ['GUESTUSER', 'STUDENT', 'SUPER_ADMIN']) && (isset($session_module['Staff Attendance']) || ($user_type == 'SCHOOL')))
          <li class="nav-item @yield('menuopenstaatt')">
            <a href="#" class="nav-link @yield('staattendance_settings')">
              <img src="{{asset('/public/menuicons/staffattendance.png')}}" class="menuicon">
              <p>Staff Attendance
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{URL('/admin/staff_dailyattendance')}}" class="nav-link @yield('master_tattendance')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Staff Daily Attendance</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{URL('/admin/staff_leavelist')}}" class="nav-link @yield('master_teacherleave')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Staff Leave</p>
                </a>
              </li>


              <li class="nav-item">
                <a href="{{URL('/admin/staff_attendancerep')}}" class="nav-link @yield('master_teachersatten')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Teachers Attendance Report</p>
                </a>
              </li>

            </ul>
          </li>
          @endif

          @if(!in_array($user_type, ['GUESTUSER', 'STUDENT', 'SUPER_ADMIN']) && (isset($session_module['Question Bank']) || ($user_type == 'SCHOOL')))
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
          @endif

          @if(!in_array($user_type, ['GUESTUSER', 'STUDENT', 'SUPER_ADMIN']) && (isset($session_module['Test']) || ($user_type == 'SCHOOL')))
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
          @endif

          @if(!in_array($user_type, ['GUESTUSER', 'STUDENT', 'SUPER_ADMIN']) && (isset($session_module['Reports']) || ($user_type == 'SCHOOL')))
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
          @endif
          
          
          @if($user_type == 'SCHOOL' || $user_type == 'SUPER_ADMIN')

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
              <li class="nav-item">
                <a href="{{URL('/admin/bloodgroups')}}" class="nav-link @yield('master_bloodgroups')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Blood Groups</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('/admin/modules')}}" class="nav-link @yield('master_modules')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Modules</p>
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
                <a href="{{URL('/admin/gallery')}}" class="nav-link @yield('master_gallery')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Gallery</p>
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
          @endif

          @if($user_type == 'SCHOOLS')
          <li class="nav-item @yield('menuopenur')">
            <a href="#" class="nav-link @yield('rolesettings')">
              <img src="{{asset('/public/menuicons/activities.png')}}" class="menuicon">
              <p>User Role Settings
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>
            <ul class="nav nav-treeview">    
              <li class="nav-item">
                <a href="{{URL('/admin/userroles')}}" class="nav-link @yield('master_userroles')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Roles</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{URL('/admin/role_module_mapping')}}" class="nav-link @yield('masterrole_module_mapping')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Role Module Mapping</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{URL('/admin/role_class_mapping')}}" class="nav-link @yield('masterrole_class_mapping')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Role Class Mapping</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{URL('/admin/roleusers')}}" class="nav-link @yield('masterrole_roleusers')">
                  <i class="fas fa-database nav-icon"></i>
                  <p>Role Users</p>
                </a>
              </li> 
            </ul>
          </li>
          @endif

          @if($user_type == 'SCHOOL')
          <li class="nav-item @yield('menuopenac') d-none">
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
          
          @if(!in_array($user_type, ['GUESTUSER', 'STUDENT', 'SUPER_ADMIN']) && (isset($session_module['Reports']) || ($user_type == 'SCHOOL')))
          <li class="nav-item @yield('menuopen')">
            <a href="#" class="nav-link @yield('settings')">
              <img src="{{asset('/public/menuicons/settings.png')}}" class="menuicon">
              <p>General Settings
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview">
              @if((isset($session_module['Admin Settings'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/settings')}}" class="nav-link @yield('settings_admin')">
                  <i class="fas fa-cogs nav-icon"></i>
                  <p>Admin Settings</p>
                </a>
              </li>
              @endif
              @if((isset($session_module['About'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/about')}}" class="nav-link @yield('settings_about')">
                  <i class="fas fa-info nav-icon"></i>
                  <p>About</p>
                </a>
              </li>
              @endif
              @if((isset($session_module['Privacy Policy'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/privacypolicy')}}" class="nav-link @yield('settings_privacy')">
                  <i class="fas fa-clipboard-check nav-icon"></i>
                  <p>Privacy Policy</p>
                </a>
              </li>
              @endif
              @if((isset($session_module['Terms and Conditions'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/termscond')}}" class="nav-link @yield('settings_terms')">
                  <i class="fas fa-clipboard-check nav-icon"></i>
                  <p>Terms and Conditions</p>
                </a>
              </li>
              @endif
              @if((isset($session_module['Faq'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/faq')}}" class="nav-link @yield('settings_faq')">
                  <i class="fas fa-question-circle nav-icon"></i>
                  <p>Faq</p>
                </a>
              </li>
              @endif
              @if((isset($session_module['Contacts For'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/contacts_for')}}" class="nav-link @yield('settings_contacts_for')">
                  <i class="fas fa-question-circle nav-icon"></i>
                  <p>Contacts For </p>
                </a>
              </li>
              @endif
              @if((isset($session_module['Contacts List'])) || ($user_type == 'SCHOOL'))
              <li class="nav-item">
                <a href="{{URL('/admin/contacts_list')}}" class="nav-link @yield('settings_contacts_list')">
                  <i class="fas fa-question-circle nav-icon"></i>
                  <p>Contacts List </p>
                </a>
              </li>
              @endif
            </ul>
          </li>
          @elseif($user_type == 'SUPER_ADMIN')
          <li class="nav-item @yield('menuopen')">
            <a href="#" class="nav-link @yield('settings')">
              <img src="{{asset('/public/menuicons/settings.png')}}" class="menuicon">
              <p>General Settings
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right"></span>
              </p>
            </a>

            <ul class="nav nav-treeview"> 
              <li class="nav-item">
                <a href="{{URL('/admin/generalsettings')}}" class="nav-link @yield('settings_saadmin')">
                  <i class="fas fa-cogs nav-icon"></i>
                  <p>Admin Settings</p>
                </a>
              </li> 
              <li class="nav-item">
                <a href="{{URL('/admin/smstemplates')}}" class="nav-link @yield('settings_smstemplates')">
                  <i class="fas fa-cogs nav-icon"></i>
                  <p>SMS Templates</p>
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
