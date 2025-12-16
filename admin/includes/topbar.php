<?php
/**
 * Admin Top Navigation Bar
 */
?>

<style>
  /* ============================================
   Admin Top Navigation Bar Styles
   ============================================ */

/* Topbar container */
.topbar {
  background-color: #fff;         /* White background */
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  margin-bottom: 1.5rem;
  position: relative;
  z-index: 1030;
  padding: 0.5rem 1rem;
}

/* Ensure only topbar elements are affected */
.topbar * {
  box-sizing: border-box;
}

/* Sidebar Toggle (Topbar) - for mobile view */
#sidebarToggleTop {
  font-size: 1.25rem;
  margin-right: 1rem;
  color: #4e73df;
  background: none;
  border: none;
}

/* Topbar Search Form */
.navbar-search {
  position: relative;
  display: inline-block;
}
.navbar-search .form-control {
  border: 1px solid #e3e6f0;
  border-radius: 2rem;
  padding: 0.375rem 1rem;
  font-size: 0.875rem;
}
.navbar-search .input-group-append .btn {
  border-radius: 0 2rem 2rem 0;
  background-color: #4e73df;
  border: 1px solid #4e73df;
  color: #fff;
}

/* Topbar Navbar Items */
.topbar .navbar-nav {
  align-items: center;
}
.topbar .nav-item {
  position: relative;
  margin-left: 1rem;
}
.topbar .nav-link {
  color: #858796;
  padding: 0.5rem;
}
.topbar .nav-link:hover {
  color: #4e73df;
}

/* Badge for alerts and messages */
.badge-counter {
  position: absolute;
  top: -0.25rem;
  right: -0.5rem;
  font-size: 0.65rem;
  background-color: #e74a3b;
  color: #fff;
  padding: 0.15rem 0.3rem;
  border-radius: 10rem;
}

/* Dropdown Menu Styling */
.topbar .dropdown-menu {
  width: 300px;
  border: none;
  border-radius: 0.35rem;
  box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
  padding: 0.5rem 0;
}
.topbar .dropdown-header {
  font-size: 0.85rem;
  color: #6e707e;
  padding: 0.75rem 1.5rem;
}
.topbar .dropdown-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 1.5rem;
  color: #3a3b45;
  text-decoration: none;
}
.topbar .dropdown-item:hover {
  background-color: #f8f9fc;
}

/* Icon circle used inside dropdown items */
.topbar .icon-circle {
  width: 2.5rem;
  height: 2.5rem;
  background-color: #4e73df;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.75rem;
}
.topbar .icon-circle i {
  color: #fff;
}

/* Dropdown List Image for Messages */
.topbar .dropdown-list-image {
  position: relative;
  margin-right: 0.75rem;
}
.topbar .dropdown-list-image img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}
.topbar .status-indicator {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background-color: #1cc88a;
  border: 2px solid #fff;
}

/* User Information (Profile) */
.topbar .img-profile {
  width: 2.5rem;
  height: 2.5rem;
  object-fit: cover;
}
.topbar .dropdown-divider {
  margin: 0.5rem 0;
}

/* Modal Content (Logout Modal) */
/* Using Bootstrap defaults is recommended; add customizations here if needed */
.modal-content {
  border-radius: 0.35rem;
}

/* =====================================================
   Scoped styling: these rules target only the topbar area.
   They ensure that the styles do not affect other parts of the pages.
   ===================================================== */
body .topbar,
body .topbar * {
  /* Increase specificity if necessary */
}

  
  
</style>
<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search -->
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" id="globalSearch">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                <span class="badge badge-danger badge-counter">3+</span>
            </a>
            <!-- Dropdown - Alerts -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                    Alerts Center
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="mr-3">
                        <div class="icon-circle bg-primary">
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Today</div>
                        <span class="font-weight-bold">5 new users registered</span>
                    </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="mr-3">
                        <div class="icon-circle bg-success">
                            <i class="fas fa-donate text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Yesterday</div>
                        3 new content items published
                    </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="mr-3">
                        <div class="icon-circle bg-warning">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">December 2, 2023</div>
                        System maintenance scheduled
                    </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="alerts.php">Show All Alerts</a>
            </div>
        </li>

        <!-- Nav Item - Messages -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <!-- Counter - Messages -->
                <span class="badge badge-danger badge-counter">7</span>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">
                    Message Center
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="dropdown-list-image mr-3">
                        <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60" alt="...">
                        <div class="status-indicator bg-success"></div>
                    </div>
                    <div>
                        <div class="text-truncate">Hi there! I have a question about my account.</div>
                        <div class="small text-gray-500">Emily Fowler · 58m</div>
                    </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="messages.php">Read More Messages</a>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($admin_name); ?></span>
                <img class="img-profile rounded-circle" src="<?php echo htmlspecialchars($admin_avatar); ?>" alt="User Avatar">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="profile.php">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                <a class="dropdown-item" href="settings.php">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                    Settings
                </a>
                <a class="dropdown-item" href="activities.php">
                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                    Activity Log
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>

    </ul>

</nav>
<!-- End of Topbar -->

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<script>
// Global search functionality
document.getElementById('globalSearch').addEventListener('keyup', function(e) {
    if (e.key === 'Enter') {
        const query = this.value.trim();
        if (query.length > 2) {
            // AJAX search implementation
            fetch('includes/search.php?q=' + encodeURIComponent(query), {
                headers: {
                    'X-CSRF-TOKEN': '<?php echo $_SESSION['csrf_token']; ?>'
                }
            })
                .then(response => response.json())
                .then(data => {
                    // Handle search results
                    console.log(data);
                    // You would typically display results in a dropdown or modal
                })
                .catch(error => console.error('Error:', error));
        }
    }
});

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>