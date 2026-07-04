<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>VYAPTO | @yield('title', 'Dashboard')</title>
  @php
    $companyTabIcon = \App\Models\Setting::where('type', 'company_web_logo')->first();
    $tabIconUrl = $companyTabIcon
      ? asset('storage/company/' . $companyTabIcon->value)
      : asset('assets/admin/images/company_logo.png');
  @endphp
  <link rel="icon" type="image/png" href="{{ $tabIconUrl }}">
  <link rel="shortcut icon" href="{{ $tabIconUrl }}">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!--  style css -->
  <link href="{{ asset('assets/admin/css/style.css') }}" rel="stylesheet">
  {{-- Sidebar: extra vertical padding + line-height so descenders (g, j, p, y) are not clipped --}}
  <style>
    /* Override assets/admin/css/style.css if it tightens nav links */
    .sidebar .nav-pills .menu-item.nav-link,
    .sidebar .nav-pills .nav-link.menu-item {
      padding: 0.45rem 0.75rem !important;
      line-height: 1.4 !important;
      min-height: auto;
      overflow: visible !important;
      display: flex;
      align-items: center;
    }
    .sidebar .nav-pills .menu-item.nav-link span,
    .sidebar .nav-pills .nav-link.menu-item span {
      line-height: 1.45;
    }
    .sidebar .nav-item {
      overflow: visible !important;
    }
    .sidebar-brand {
      flex-shrink: 0;
    }
    .sidebar-menu {
      flex: 1 1 auto;
      min-height: 0;
      overflow-y: auto;
      overflow-x: hidden;
      scrollbar-width: thin;
      overflow-anchor: none;
      margin: 0 -0.5rem;
      padding: 0 0.5rem 0.5rem;
    }
    .sidebar-submenu {
      list-style: none;
      padding-left: 0.75rem;
      margin: 0 0 0.25rem;
    }
    .sidebar-submenu .nav-link {
      font-size: 0.85rem;
      padding: 0.45rem 0.75rem !important;
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

  <!-- Sidebar -->
  <nav class="sidebar d-flex flex-column p-3" id="sidebar">
    <div class="sidebar-brand text-center">
      <!-- Logo -->
      @php
      $company_web_logo = \App\Models\Setting::where('type', 'company_web_logo')->first();
      @endphp
      <img
        src="{{ $company_web_logo ? asset('storage/company/'.$company_web_logo->value) : asset('images/developer.png') }}"
        alt="LEMS Logo"
        class="system-logo mx-auto d-block mb-3"
        style="max-height: 80px;"
        onerror="this.src='{{ asset('/assets/admin/images/company_logo.png') }}';">

      <!-- System Name -->
      <h4 class="system-name text-white mb-3">VYAPTO</h4>
    </div>

    <div class="sidebar-menu">
    <ul class="nav nav-pills flex-column mb-0">
      <li class="nav-item mb-1">
        <a href="{{ route('admin.dashboard') }}" class="menu-item nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
          <i class="bi bi-speedometer2 me-2"></i><span> Dashboard</span>
        </a>
      </li>
      <!-- <li class="nav-item mb-1">
        <a href="{{ route('roles.index') }}" class="menu-item nav-link {{ request()->routeIs('roles.index') ? 'active' : '' }}" data-tooltip="Manage Role">
          <i class="bi bi-person-badge-fill me-2"></i><span> Manage Role</span>
        </a>
      </li> -->

      <!-- departments -->
      <li class="nav-item mb-1">
        <a href="{{ route('departments.index') }}" class="menu-item nav-link {{ request()->routeIs('departments.index') ? 'active' : '' }}" data-tooltip="Departments">
          <i class="bi bi-building me-2"></i><span>Departments</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('employees.index') }}" class="menu-item nav-link {{ request()->routeIs('employees.index') ? 'active' : '' }}" data-tooltip="employees">
          <i class="bi bi-people-fill me-2"></i><span>Employees</span>
        </a>
      </li>

      <li class="nav-item mb-1">
        <a href="{{ route('vendors.index') }}" class="menu-item nav-link {{ request()->routeIs('vendors.index') ? 'active' : '' }}" data-tooltip="Vendors">
          <i class="bi bi-shop me-2"></i><span>Vendors</span>
        </a>
      </li>

      <li class="nav-item mb-1">
        <a href="{{ route('vehicles.index') }}" class="menu-item nav-link {{ request()->routeIs('vehicles.index') ? 'active' : '' }}" data-tooltip="vehicles">
          <i class="bi bi-truck me-2"></i><span>Vehicles</span>
        </a>
      </li>

      <li class="nav-item mb-1">
        <a href="{{ route('attendance.index') }}" class="menu-item nav-link {{ request()->routeIs('attendance.index') ? 'active' : '' }}" data-tooltip="Attendance">
          <i class="bi bi-calendar-check me-2"></i><span>Attendance</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('attendance.report') }}" class="menu-item nav-link {{ request()->routeIs('attendance.report') ? 'active' : '' }}" data-tooltip="Attendance Report">
          <i class="bi bi-calendar3-week me-2"></i><span>Attendance Report</span>
        </a>
      </li>

      <li class="nav-item mb-1">
        <a href="{{ route('salary-slips.index') }}" class="menu-item nav-link {{ request()->routeIs('salary-slips.index') ? 'active' : '' }}" data-tooltip="Salary Slips">
          <i class="bi bi-file-earmark-text me-2"></i><span>Salary Slips</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('user-salaries.index') }}" class="menu-item nav-link {{ request()->routeIs('user-salaries.*') ? 'active' : '' }}" data-tooltip="User Salaries">
          <i class="bi bi-wallet2 me-2"></i><span>Salaries</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('settings.company-info') }}" class="menu-item nav-link {{ request()->routeIs('settings.company-info') ? 'active' : '' }}" data-tooltip="Settings">
          <i class="bi bi-gear me-2"></i><span>Settings</span>
        </a>
      </li>

      <li class="nav-item mb-1">
        <a href="javascript:void(0)"
          class="menu-item nav-link d-flex align-items-center {{ request()->routeIs('admin.website.*') ? 'active' : '' }}"
          data-bs-toggle="collapse"
          data-bs-target="#websiteSubmenu"
          role="button"
          aria-expanded="false"
          aria-controls="websiteSubmenu"
          data-tooltip="Website CMS">
          <span><i class="bi bi-globe me-2"></i>Website</span>
          <i class="bi bi-chevron-down ms-auto small"></i>
        </a>
        <ul class="sidebar-submenu collapse" id="websiteSubmenu">
          <li class="nav-item mb-1">
            <a href="{{ route('admin.website.page-sections.index') }}" class="menu-item nav-link {{ request()->routeIs('admin.website.page-sections.*') ? 'active' : '' }}">
              <span>Page Sections</span>
            </a>
          </li>
          <li class="nav-item mb-1">
            <a href="{{ route('admin.website.services.index') }}" class="menu-item nav-link {{ request()->routeIs('admin.website.services.*') ? 'active' : '' }}">
              <span>Services</span>
            </a>
          </li>
          <li class="nav-item mb-1">
            <a href="{{ route('admin.website.products.index') }}" class="menu-item nav-link {{ request()->routeIs('admin.website.products.*') ? 'active' : '' }}">
              <span>Products</span>
            </a>
          </li>
          <li class="nav-item mb-1">
            <a href="{{ route('admin.website.careers.index') }}" class="menu-item nav-link {{ request()->routeIs('admin.website.careers.*') ? 'active' : '' }}">
              <span>Careers</span>
            </a>
          </li>
          <li class="nav-item mb-1">
            <a href="{{ route('admin.website.blogs.index') }}" class="menu-item nav-link {{ request()->routeIs('admin.website.blogs.*') ? 'active' : '' }}">
              <span>Blogs</span>
            </a>
          </li>
          <li class="nav-item mb-1">
            <a href="{{ route('admin.website.contact-messages.index') }}" class="menu-item nav-link {{ request()->routeIs('admin.website.contact-messages.*') ? 'active' : '' }}">
              <span>Contact Messages</span>
            </a>
          </li>
        </ul>
      </li>

      <!-- <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.hubs.index') }}">
          <i class="bi bi-shop me-2"></i> Hubs
        </a>
      </li> -->
      <li class="nav-item mb-1">
    <a href="{{ route('admin.hubs.index') }}"
       class="menu-item nav-link {{ request()->routeIs('admin.hubs.*') ? 'active' : '' }}"
       data-tooltip="Hubs">
       
        <i class="bi bi-shop me-2"></i>
        <span>Hubs</span>
    </a>
</li>
      <li class="nav-item mb-1">
        <a href="{{ route('admin.assignment-parcel.index') }}"
          class="menu-item nav-link {{ request()->routeIs('admin.assignment-parcel.*') ? 'active' : '' }}"
          data-tooltip="Assignment Parcel">
          <i class="bi bi-box-seam me-2"></i>
          <span>Assignment</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('admin.vehicle-usage.index') }}"
          class="menu-item nav-link {{ request()->routeIs('admin.vehicle-usage.*') ? 'active' : '' }}"
          data-tooltip="Vehicle Usage">
          <i class="bi bi-truck me-2"></i>
          <span>Vehicle Usage</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('admin.vehicle-usage.today-km-summary') }}"
          class="menu-item nav-link {{ request()->routeIs('admin.vehicle-usage.today-km-summary') ? 'active' : '' }}"
          data-tooltip="Today’s KM summary (same data as the API, as a page)">
          <i class="bi bi-clipboard2-pulse me-2"></i>
          <span>Today’s KM summary</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('admin.faq-categories.index') }}"
          class="menu-item nav-link {{ request()->routeIs('admin.faq-categories.*') ? 'active' : '' }}"
          data-tooltip="FAQ Categories">
          <i class="bi bi-tags me-2"></i>
          <span>FAQ Categories</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('admin.faqs.index') }}"
          class="menu-item nav-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}"
          data-tooltip="FAQs">
          <i class="bi bi-question-circle me-2"></i>
          <span>FAQs</span>
        </a>
      </li>
      <!-- <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.hubs.map') }}">
          <i class="fas fa-map"></i> Hub Map
        </a>
      </li> -->

      <li class="nav-item mb-1">
        <a href="{{ route('static-pages.index') }}"
          class="menu-item nav-link {{ request()->routeIs('static-pages.*') ? 'active' : '' }}"
          data-tooltip="Static Pages">
          <i class="bi bi-file-earmark-text me-2"></i>
          <span>Static Pages</span>
        </a>
      </li>

      <!-- Add the rest of your menu items here... -->
    </ul>
    </div>

    <!-- <hr>
    <div>
        <a href="#" 
        class="menu-item d-flex align-items-center text-white text-decoration-none rounded px-4 py-1"
        data-bs-toggle="modal"
        data-bs-target="#logoutModal"
        data-tooltip="Logout">
        <i class="bi bi-box-arrow-right me-2"></i><span> Logout</span>
      </a>
    </div> -->
  </nav>

  <!-- Logout Confirmation Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border border-1 border-primary rounded-4 shadow">

        <div class="modal-header py-2 px-3">
          <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          Are you sure you want to logout?
        </div>

        <div class="modal-footer">
          <form method="POST" action="{{ route('logout') }}">
            @csrf

            <!-- Modern-style Cancel button -->
            <button type="button"
              class="btn btn-sm border border-primary text-primary bg-white"
              data-bs-dismiss="modal">
              Cancel
            </button>

            <!-- Primary-style Logout button -->
            <button type="submit" class="btn btn-primary btn-sm">
              Logout
            </button>
          </form>
        </div>

      </div>
    </div>
  </div>



  <!-- Content + Topbar Wrapper -->
  <div class="content-wrapper">

    <!-- Topbar -->
    <nav class="topbar d-flex align-items-center ms-5 sticky-header" style="padding: 10px 16px;">
      <!-- Sidebar Toggle -->
      <button id="toggleSidebar" class="toggle-btn btn btn-outline-secondary me-2 m-0" onclick="toggleSidebar()"
        style="padding: 2px 6px; font-size: 0.75rem; line-height: 1;">
        <i class="bi bi-list"></i>
      </button>


      <!-- Page Title -->
      <h3 class="mb-0 text-truncate text-ellipsis">

      </h3>

      <!-- Right-side controls -->
      <div class="ms-auto d-flex align-items-center gap-2">
        <!-- Notification Bell -->
        <!-- <button class="btn position-relative" onclick="toggleNotifications()">
              <i class="bi bi-bell"></i>
          </button> -->

        <!-- User Dropdown -->
        <div class="dropdown">
          <button class="btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle fs-5"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
            <li class="dropdown-item-text fw-semibold">
              {{ Auth::user()->name }}
              <br>
              <small class="text-muted">{{ Auth::user()->email }}</small>
              <small class="text-primary text-uppercase">
                {{ Auth::user()->getRoleNames()->first() ?? 'No role assigned' }}
              </small>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <!-- <a class="dropdown-item" href="{{ route('profile.edit') }}">
                          <i class="bi bi-person-lines-fill me-2"></i>Profile
                      </a> -->
              <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                <i class="bi bi-person-lines-fill me-2"></i>Profile
              </a>
            </li>
            <li>
              <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Notification Drawer -->
    <div id="notificationDrawer" class="position-fixed top-0 end-0 bg-white border-start shadow h-100 p-3" style="width: 300px; z-index: 1050; transform: translateX(100%); transition: transform 0.3s;">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Notifications</h5>
        <button class="btn-close" onclick="toggleNotifications()"></button>
      </div>
      <div>
        <p class="small text-danger">No new notifications.</p>
        <!-- Dynamic notifications can be listed here -->
      </div>
    </div>

    <!-- About Modal -->
    <div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="aboutModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border border-1 border-primary rounded-4 shadow">

          <div class="modal-header">
            <h5 class="modal-title" id="aboutModalLabel">About</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <!-- System Info -->
            <h6>System Features & Purpose</h6>
            <ul class="small mb-4">
              <li>Sends batch or individual emails to selected users</li>
              <li>Maintains an audit log of every sent email (recipient, subject, timestamp)</li>
              <li>Filter, search and paginate through email logs</li>
              <li>Register new users directly from the admin dashboard</li>
              <li>Real-time notifications for successful or failed sends</li>
            </ul>
            <p class="small">
              This application streamlines your communication workflow by letting you compose, send, and track emails—all from one intuitive dashboard.
            </p>

            <!-- Developer Info -->
            <div class="text-center">
              <img
                src="{{ asset('images/developer.png') }}"
                alt="App Mailer Logo"
                class="mx-auto d-block mb-3"
                style="max-height: 80px;">
              <h6>Developers</h6><br>
              <p class="small mb-0">
                <strong>Leonard T. Domingo</strong> <br>
                <strong>Allyssa Mae T. Ligsay</strong> <br>
                <strong>Airiz Krizzle Placido </strong> <br>
                <strong>Mary Ann S. Cabagui</strong> <br>
                <strong>Karylle Mia Abella</strong> <br>
                <strong>Alexis Jane Labinay Tabunan</strong> <br>
                <strong>Mariz Jocel L. Tomas</strong> <br>
                <strong>David John Caliboso</strong> <br>
                Bachelor of Science in Information Technology<br>
                <a href="mailto:leonardtdomingovida@gmail.com">lems@gmail.com</a>
              </p>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          </div>

        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="content py-0">
      @yield('content')
    </div>

    <!-- Footer -->
    <footer class="text-center py-3 mt-auto bg-light" style="font-size: 0.85rem;">
      <div class="container">
        <span class="text-muted">© {{ date('Y') }} Vyapto. All rights reserved.</span>
      </div>
    </footer>

  </div>

  <!-- Scripts -->
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap 5 JS -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('collapsed');
    }

    function toggleNotifications() {
      const drawer = document.getElementById('notificationDrawer');
      drawer.style.transform = drawer.style.transform === 'translateX(0%)' ? 'translateX(100%)' : 'translateX(0%)';
    }

    document.addEventListener('DOMContentLoaded', function () {
      const menu = document.querySelector('.sidebar-menu');
      const submenu = document.getElementById('websiteSubmenu');
      const submenuToggle = document.querySelector('[data-bs-target="#websiteSubmenu"]');

      if (menu) {
        menu.addEventListener('scroll', function () {
          sessionStorage.setItem('sidebarScroll', menu.scrollTop);
        }, { passive: true });

        menu.querySelectorAll('a.menu-item[href]').forEach(function (link) {
          link.addEventListener('mousedown', function () {
            sessionStorage.setItem('sidebarScroll', menu.scrollTop);
          });
        });
      }

      if (submenu && submenuToggle) {
        const open = sessionStorage.getItem('websiteSubmenuOpen') === '1';

        if (open) {
          submenu.classList.add('show');
          submenuToggle.setAttribute('aria-expanded', 'true');
        }

        submenu.addEventListener('shown.bs.collapse', function () {
          sessionStorage.setItem('websiteSubmenuOpen', '1');
        });
        submenu.addEventListener('hidden.bs.collapse', function () {
          sessionStorage.setItem('websiteSubmenuOpen', '0');
        });
      }

      if (menu) {
        requestAnimationFrame(function () {
          const savedScroll = sessionStorage.getItem('sidebarScroll');
          if (savedScroll !== null) {
            const maxScroll = Math.max(0, menu.scrollHeight - menu.clientHeight);
            menu.scrollTop = Math.min(parseInt(savedScroll, 10) || 0, maxScroll);
          }
        });
      }
    });

    $(document).ready(function() {
      $('#usersTable').DataTable({
        "lengthChange": true,
        "pageLength": 10,
        "order": [],
        "language": {
          search: "_INPUT_",
          searchPlaceholder: "Search users..."
        },
        "columnDefs": [{
            "orderable": false,
            "targets": 6
          } // Disable ordering on Actions column
        ]
      });
    });
  </script>

  @stack('scripts')

</body>

</html>