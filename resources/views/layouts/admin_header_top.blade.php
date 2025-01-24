<?php  use App\Countries; 
if(isset(Auth::User()->id))  { 
  } else {  header('Location: '.URL('/admin'));exit;?>
<?php }

$countries = Countries::where('status', 'ACTIVE')->orderby('position', 'asc')->get(); 
$session_country = Session::get('session_country');
?>

<?php
use App\User;
use App\Module;
use App\RoleModuleMapping;
  
$active_page = basename($_SERVER['REQUEST_URI']);
$current_page = '';
$session_module = session()->get('module');
$role_fk = session()->get('role_fk');
$user_role = session()->get('user_type');
$current_page_result = Module::where('url', $active_page)->first();

if (! empty($current_page)) {
    $current_page = $current_page_result->parent_module_fk;
} 

//echo "<pre>"; print_r($session_module); exit; 
?>
 
<!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">
      <a href="{{URL('/admin/home')}}" class="navbar-brand">
        <img src="{{asset('/public/image/logo.png')}}" alt='{{ config("constants.site_name") }} Logo' class="brand-image img-circle elevation-3" style="opacity: .8; height: 50px; width: 50px;">
        <span class="brand-text font-weight-light">{{ config("constants.site_name") }}</span>
      </a>

      <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <!-- Left navbar links -->
        <ul class="navbar-nav"> 
          @if($user_role == 'SUPER_ADMIN' || (isset($session_module['home'])))
          <li class="nav-item">
            <a href="{{URL('/admin/home')}}" class="nav-link">Home</a>
          </li>
          @endif
          @if($user_role == 'SUPER_ADMIN' || (isset($session_module['settings']))  || (isset($session_module['about'])) || (isset($session_module['terms'])) || (isset($session_module['faq'])))
          <li class="nav-item dropdown @yield('settings')">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Fuji Settings</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
              @if($user_role == 'SUPER_ADMIN' || (isset($session_module['settings'])) || (isset($session_module['about'])) || (isset($session_module['terms'])) || (isset($session_module['faq'])))
              <li class="dropdown-submenu dropdown-hover">
                <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">General Settings</a>
                <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                  @if($user_role == 'SUPER_ADMIN' || (isset($session_module['settings'])))
                  <li>
                    <a tabindex="-1" href="{{URL('/admin/settings')}}" class="dropdown-item">Admin Settings</a>
                  </li>
                  @endif 
                  @if($user_role == 'SUPER_ADMIN' || (isset($session_module['about'])))
                  <li>
                    <a tabindex="-1" href="{{URL('/admin/about')}}" class="dropdown-item">About</a>
                  </li>
                  @endif 
                  @if($user_role == 'SUPER_ADMIN' || (isset($session_module['privacypolicy'])))
                  <li>
                    <a tabindex="-1" href="{{URL('/admin/privacypolicy')}}" class="dropdown-item">Privacy Policy</a>
                  </li>
                  @endif 
                  @if($user_role == 'SUPER_ADMIN' || (isset($session_module['terms'])))
                  <li>
                    <a tabindex="-1" href="{{URL('/admin/terms')}}" class="dropdown-item">Terms and Conditions</a>
                  </li>
                  @endif 
                  @if($user_role == 'SUPER_ADMIN' || (isset($session_module['faq'])))
                  <li>
                    <a tabindex="-1" href="{{URL('/admin/faq')}}" class="dropdown-item">Faq</a>
                  </li>
                  @endif 
                </ul>
              </li>
              @endif 
              
              <li class="dropdown-divider"></li>
              @if($user_role == 'SUPER_ADMIN' || (isset($session_module['branches'])) || (isset($session_module['stores'])) || (isset($session_module['teams'])) || (isset($session_module['employees'])))
              <!-- Level two dropdown-->
              <li class="dropdown-submenu dropdown-hover">
                <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Master Settings</a>
                <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                  @if($user_role == 'SUPER_ADMIN' || (isset($session_module['departments'])))
                  <li>
                    <a tabindex="-1" href="{{URL('/admin/departments')}}" class="dropdown-item">Departments</a>
                  </li>
                  @endif 
                  @if($user_role == 'SUPER_ADMIN' || (isset($session_module['branches'])))
                  <li>
                    <a tabindex="-1" href="{{URL('/admin/branches')}}" class="dropdown-item">Plants</a>
                  </li>
                  @endif 
                  @if($user_role == 'SUPER_ADMIN' || (isset($session_module['stores'])))
                  <li>
                    <a tabindex="-1" href="{{URL('/admin/stores')}}" class="dropdown-item">Stores</a>
                  </li>
                  @endif 
                  @if($user_role == 'SUPER_ADMIN' || (isset($session_module['models'])))
                  <li>
                    <a tabindex="-1" href="{{URL('/admin/models')}}" class="dropdown-item">Models</a>
                  </li>
                  @endif 
                  @if($user_role == 'SUPER_ADMIN' || (isset($session_module['ratings'])))
                  <li>
                    <a tabindex="-1" href="{{URL('/admin/ratings')}}" class="dropdown-item">Ratings</a>
                  </li>
                  @endif 
                  <!-- @if($user_role == 'SUPER_ADMIN' || (isset($session_module['teams'])))
                  <li>
                    <a tabindex="-1" href="{{URL('/admin/teams')}}" class="dropdown-item">Teams</a>
                  </li>
                  @endif 
                  @if($user_role == 'SUPER_ADMIN' || (isset($session_module['employees'])))
                  <li>
                    <a tabindex="-1" href="{{URL('/admin/employees')}}" class="dropdown-item">Employees</a>
                  </li>
                  @endif  -->
                </ul>
              </li>
              @endif 

              <li class="dropdown-divider"></li>
              @if($user_role == 'SUPER_ADMIN' || (isset($session_module['modules'])) || (isset($session_module['role_module_mapping'])) || (isset($session_module['userroles'])))
              <!-- Level two dropdown-->
              <li class="dropdown-submenu dropdown-hover">
                <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Access Settings</a>
                <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                  @if($user_role == 'SUPER_ADMIN' || (isset($session_module['userroles'])))
                  <li>
                    <a tabindex="-1" href="{{URL('/admin/userroles')}}" class="dropdown-item">Roles</a>
                  </li>
                  @endif 
                  @if($user_role == 'SUPER_ADMIN' || (isset($session_module['modules'])))
                  <li>
                    <a tabindex="-1" href="{{URL('/admin/modules')}}" class="dropdown-item">Modules</a>
                  </li>
                  @endif 
                  @if($user_role == 'SUPER_ADMIN' || (isset($session_module['role_module_mapping'])))
                  <li>
                    <a tabindex="-1" href="{{URL('/admin/role_module_mapping')}}" class="dropdown-item">Role Module Mapping</a>
                  </li>
                  @endif
                </ul>
              </li>
              @endif 
              <!-- End Level two -->
            </ul>
          </li>
          @endif
          <li class="nav-item">
            <a href="#" class="nav-link">Reports</a>
          </li> 
        </ul>

      <!-- Right navbar links -->
      <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown d-none">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="fas fa-comments"></i>
            <span class="badge badge-danger navbar-badge">3</span>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <a href="#" class="dropdown-item">
              <!-- Message Start -->
              <div class="media">
                <img src="{{asset('/public/dist/img/user1-128x128.jpg')}}" alt="User Avatar" class="img-size-50 mr-3 img-circle">
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
                <img src="{{asset('/public/dist/img/user8-128x128.jpg')}}" alt="User Avatar" class="img-size-50 img-circle mr-3">
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
                <img src="{{asset('/public/dist/img/user3-128x128.jpg')}}" alt="User Avatar" class="img-size-50 img-circle mr-3">
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
        <li class="nav-item dropdown d-none">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-bell"></i>
            <span class="badge badge-warning navbar-badge">15</span>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <span class="dropdown-header">15 Notifications</span>
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
        <li class="nav-item">
          <a class="nav-link"  href="{{URL('/admin/logout')}}" >
            <i class="fas fa-power-off"></i>
          </a>
        </li>
      </ul>
    </div>
  </nav>
  <!-- /.navbar -->
 