<?php
// sidebar.php
?>
<!-- Begin Sidebar Styles -->
<style>
  /* ============================================
     تعديل للمحتوى الرئيسي بحيث لا يغطيه الشريط الجانبي
     ============================================ */
  body {
    margin-left: 250px; /* عرض الشريط الجانبي المفتوح */
    transition: margin-left 0.3s ease;
  }
  body.sidebar-toggled {
    margin-left: 80px; /* عرض الشريط الجانبي عند الإغلاق */
  }

  /* ============================================
     Sidebar General Styles
     ============================================ */
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100vh;
    overflow-y: auto;
    background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
    color: #fff;
    transition: all 0.3s ease;
    z-index: 1000;
  }
  .sidebar.toggled {
    width: 80px;
  }

  /* ============================================
     Sidebar Brand
     ============================================ */
  .sidebar .sidebar-brand {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    font-size: 1.25rem;
    text-decoration: none;
    color: #fff;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
  }
  .sidebar .sidebar-brand-icon {
    font-size: 2rem;
    margin-right: 0.5rem;
  }

  /* ============================================
     Sidebar Navigation
     ============================================ */
  .navbar-nav {
    padding: 0;
    margin: 0;
    list-style: none;
  }
  .sidebar .nav-item {
    position: relative;
  }
  .sidebar .nav-link {
    display: block;
    padding: 0.75rem 1.25rem;
    color: #fff;
    text-decoration: none;
    transition: background 0.3s ease;
    white-space: nowrap;
  }
  .sidebar .nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
  }
  .sidebar .nav-item.active > .nav-link {
    background: rgba(255, 255, 255, 0.2);
  }
  .sidebar .nav-link i {
    margin-right: 0.5rem;
  }

  /* ============================================
     Divider & Heading
     ============================================ */
  .sidebar-divider {
    border-top: 1px solid rgba(255, 255, 255, 0.15);
    margin: 1rem 0;
  }
  .sidebar-heading {
    padding: 0.75rem 1.25rem;
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.75);
    text-transform: uppercase;
  }

  /* ============================================
     Collapse (Dropdown) Menu Items
     ============================================ */
  .collapse {
    transition: height 0.3s ease;
  }
  .collapse .collapse-inner {
    background: #fff;
    color: #6c757d;
    padding: 0.5rem 0;
    border-radius: 0 0.25rem 0.25rem 0;
  }
  .collapse .collapse-inner .collapse-header {
    padding: 0.5rem 1.5rem;
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #858796;
  }
  .collapse-item {
    display: block;
    padding: 0.5rem 1.5rem;
    font-size: 0.9rem;
    color: #6c757d;
    text-decoration: none;
    transition: background 0.3s ease, color 0.3s ease;
  }
  .collapse-item:hover,
  .collapse-item.active {
    background-color: #f8f9fc;
    color: #3a3b45;
  }

  /* ============================================
     Sidebar Toggle Button (Always Visible)
     ============================================ */
  #sidebarToggle {
    position: fixed;
    top: 15px;
    left: 260px; /* بجانب الشريط المفتوح */
    background-color: #4e73df;
    border: none;
    color: #fff;
    font-size: 1.5rem;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    z-index: 1100;
    transition: left 0.3s ease;
  }
  body.sidebar-toggled #sidebarToggle {
    left: 90px;
  }

  /* ============================================
     Responsive Adjustments
     ============================================ */
  @media (max-width: 768px) {
    .sidebar {
      position: relative;
      width: 100%;
      height: auto;
    }
    #sidebarToggle {
      left: auto;
      right: 15px;
      top: 15px;
    }
  }

  /* ============================================
     Additional Utility Classes
     ============================================ */
  .rotate-90 {
    transform: rotate(90deg);
    transition: transform 0.3s ease;
  }
  .sidebar.toggled .nav-link span,
  .sidebar.toggled .sidebar-brand-text {
    display: none;
  }
  .sidebar {
    scroll-behavior: smooth;
  }
</style>
<!-- End Sidebar Styles -->

<!-- Begin Sidebar Markup -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
    <div class="sidebar-brand-icon">
      <i class="fas fa-heartbeat"></i>
    </div>
    <div class="sidebar-brand-text mx-3">Optimal Lifestyle</div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-0">

  <!-- Nav Item - Dashboard -->
  <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
    <a class="nav-link" href="index.php">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span>
    </a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider">

  <!-- Heading -->
  <div class="sidebar-heading">
    Management
  </div>

  <!-- Nav Item - User Management -->
  <li class="nav-item <?php echo in_array(basename($_SERVER['PHP_SELF']), ['users.php', 'user-add.php', 'user-edit.php', 'user-view.php']) ? 'active' : ''; ?>">
    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseUsers" aria-expanded="true" aria-controls="collapseUsers">
      <i class="fas fa-fw fa-users"></i>
      <span>User Management</span>
    </a>
    <div id="collapseUsers" class="collapse <?php echo in_array(basename($_SERVER['PHP_SELF']), ['users.php', 'user-add.php', 'user-edit.php', 'user-view.php']) ? 'show' : ''; ?>" aria-labelledby="headingUsers" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">User Components:</h6>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="users.php">All Users</a>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'user-add.php' ? 'active' : ''; ?>" href="user-add.php">Add New User</a>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'user-roles.php' ? 'active' : ''; ?>" href="user-roles.php">Roles & Permissions</a>
      </div>
    </div>
  </li>

  <!-- Nav Item - Content Management -->
  <li class="nav-item <?php echo in_array(basename($_SERVER['PHP_SELF']), ['content.php', 'content-add.php', 'content-edit.php', 'categories.php']) ? 'active' : ''; ?>">
    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseContent" aria-expanded="true" aria-controls="collapseContent">
      <i class="fas fa-fw fa-newspaper"></i>
      <span>Content Management</span>
    </a>
    <div id="collapseContent" class="collapse <?php echo in_array(basename($_SERVER['PHP_SELF']), ['content.php', 'content-add.php', 'content-edit.php', 'categories.php']) ? 'show' : ''; ?>" aria-labelledby="headingContent" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Content Components:</h6>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'content.php' ? 'active' : ''; ?>" href="content.php">All Content</a>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'content-add.php' ? 'active' : ''; ?>" href="content-add.php">Add New Content</a>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>" href="categories.php">Categories</a>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'media.php' ? 'active' : ''; ?>" href="media.php">Media Library</a>
      </div>
    </div>
  </li>

  <!-- Nav Item - Fitness Management -->
  <li class="nav-item <?php echo in_array(basename($_SERVER['PHP_SELF']), ['fitness.php', 'workouts.php', 'exercises.php', 'plans.php']) ? 'active' : ''; ?>">
    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseFitness" aria-expanded="true" aria-controls="collapseFitness">
      <i class="fas fa-fw fa-dumbbell"></i>
      <span>Fitness Management</span>
    </a>
    <div id="collapseFitness" class="collapse <?php echo in_array(basename($_SERVER['PHP_SELF']), ['fitness.php', 'workouts.php', 'exercises.php', 'plans.php']) ? 'show' : ''; ?>" aria-labelledby="headingFitness" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Fitness Components:</h6>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'fitness.php' ? 'active' : ''; ?>" href="fitness.php">Dashboard</a>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'workouts.php' ? 'active' : ''; ?>" href="workouts.php">Workouts</a>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'exercises.php' ? 'active' : ''; ?>" href="exercises.php">Exercises</a>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'plans.php' ? 'active' : ''; ?>" href="plans.php">Training Plans</a>
      </div>
    </div>
  </li>

  <!-- Nav Item - Nutrition Management -->
  <li class="nav-item <?php echo in_array(basename($_SERVER['PHP_SELF']), ['nutrition.php', 'foods.php', 'recipes.php', 'meal-plans.php']) ? 'active' : ''; ?>">
    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseNutrition" aria-expanded="true" aria-controls="collapseNutrition">
      <i class="fas fa-fw fa-utensils"></i>
      <span>Nutrition Management</span>
    </a>
    <div id="collapseNutrition" class="collapse <?php echo in_array(basename($_SERVER['PHP_SELF']), ['nutrition.php', 'foods.php', 'recipes.php', 'meal-plans.php']) ? 'show' : ''; ?>" aria-labelledby="headingNutrition" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Nutrition Components:</h6>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'nutrition.php' ? 'active' : ''; ?>" href="nutrition.php">Dashboard</a>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'foods.php' ? 'active' : ''; ?>" href="foods.php">Food Database</a>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'recipes.php' ? 'active' : ''; ?>" href="recipes.php">Recipes</a>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'meal-plans.php' ? 'active' : ''; ?>" href="meal-plans.php">Meal Plans</a>
      </div>
    </div>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider">

  <!-- Heading -->
  <div class="sidebar-heading">
    Analytics
  </div>

  <!-- Nav Item - Analytics -->
  <li class="nav-item <?php echo in_array(basename($_SERVER['PHP_SELF']), ['analytics.php', 'reports.php']) ? 'active' : ''; ?>">
    <a class="nav-link" href="analytics.php">
      <i class="fas fa-fw fa-chart-line"></i>
      <span>Analytics</span>
    </a>
  </li>

  <!-- Nav Item - Reports -->
  <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
    <a class="nav-link" href="reports.php">
      <i class="fas fa-fw fa-file-alt"></i>
      <span>Reports</span>
    </a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider">

  <!-- Heading -->
  <div class="sidebar-heading">
    System
  </div>

  <!-- Nav Item - System -->
  <li class="nav-item <?php echo in_array(basename($_SERVER['PHP_SELF']), ['settings.php', 'backup.php', 'logs.php']) ? 'active' : ''; ?>">
    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSystem" aria-expanded="true" aria-controls="collapseSystem">
      <i class="fas fa-fw fa-cog"></i>
      <span>System</span>
    </a>
    <div id="collapseSystem" class="collapse <?php echo in_array(basename($_SERVER['PHP_SELF']), ['settings.php', 'backup.php', 'logs.php']) ? 'show' : ''; ?>" aria-labelledby="headingSystem" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">System Components:</h6>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="settings.php">Settings</a>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'backup.php' ? 'active' : ''; ?>" href="backup.php">Backup & Restore</a>
        <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'logs.php' ? 'active' : ''; ?>" href="logs.php">System Logs</a>
        <?php if ($admin_role == 'super_admin'): ?>
          <a class="collapse-item <?php echo basename($_SERVER['PHP_SELF']) == 'api.php' ? 'active' : ''; ?>" href="api.php">API Management</a>
        <?php endif; ?>
      </div>
    </div>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider d-none d-md-block">
</ul>
<!-- End of Sidebar Markup -->

<!-- Always Visible Sidebar Toggle Button -->
<button id="sidebarToggle">
  <!-- أيقونة الفتح الافتراضية -->
  <i class="fas fa-angle-double-left"></i>
</button>

<!-- Begin Sidebar Scripts -->
<script>
  // عند النقر على زر التبديل، يتم تبديل حالة الشريط وتغيير محتوى الأيقونة
  document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.body.classList.toggle('sidebar-toggled');
    document.querySelector('.sidebar').classList.toggle('toggled');
    
    if(document.body.classList.contains('sidebar-toggled')){
       // عند الإغلاق، تظهر أيقونة fa-chevron-down فقط
       this.innerHTML = '<i class="fas fa-chevron-down"></i>';
    } else {
       // عند الفتح، تعود الأيقونة الافتراضية
       this.innerHTML = '<i class="fas fa-angle-double-left"></i>';
    }
  });

  // تهيئة عناصر القائمة عند تحميل الصفحة
  document.addEventListener('DOMContentLoaded', function() {
    const collapseLinks = document.querySelectorAll('.nav-link[data-bs-toggle="collapse"]');
    collapseLinks.forEach(function(link) {
      link.addEventListener('click', function(e) {
        if (window.innerWidth < 768) {
          e.preventDefault();
          const target = document.querySelector(this.getAttribute('data-bs-target'));
          target.classList.toggle('show');
        }
        const icon = this.querySelector('.fa-chevron-down');
        if (icon) {
          icon.classList.toggle('rotate-90');
        }
      });
    });

    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(function(item) {
      const link = item.querySelector('.nav-link:not(.collapsed)');
      if (link) {
        const pageMatch = link.getAttribute('href') === currentPage;
        const collapseMatch = item.querySelector(`.collapse-item[href="${currentPage}"]`);
        if (pageMatch || collapseMatch) {
          item.classList.add('active');
          const collapseParent = link.closest('.collapse');
          if (collapseParent) {
            collapseParent.classList.add('show');
            const parentLink = document.querySelector(`[data-bs-target="#${collapseParent.id}"]`);
            if (parentLink) {
              parentLink.classList.remove('collapsed');
            }
          }
        }
      }
    });

    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
      sidebar.addEventListener('scroll', function() {
        localStorage.setItem('sidebar-scroll', this.scrollTop);
      });
      const savedScroll = localStorage.getItem('sidebar-scroll');
      if (savedScroll) {
        sidebar.scrollTop = savedScroll;
      }
    }
  });
</script>
<!-- End Sidebar Scripts -->
