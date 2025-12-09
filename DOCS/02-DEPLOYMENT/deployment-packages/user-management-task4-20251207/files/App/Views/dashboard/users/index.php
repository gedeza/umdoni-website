<?php
global $context;

$data = $context->data;
$crumbs = getCrumbs();

use App\Models\RolesModel;

// Calculate statistics
$totalUsers = count($data);
$activeUsers = count(array_filter($data, fn($u) => $u['locked'] == 0));
$inactiveUsers = $totalUsers - $activeUsers;
$confirmedUsers = count(array_filter($data, fn($u) => $u['verified'] == 1));
$roles = RolesModel::getAll();
?>

<style>
/* User Management Custom Styles */
.stats-card {
    border-left: 4px solid;
    transition: transform 0.2s, box-shadow 0.2s;
}
.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.stats-card.primary { border-left-color: #6777ef; }
.stats-card.success { border-left-color: #4fbe87; }
.stats-card.danger { border-left-color: #f3616d; }
.stats-card.warning { border-left-color: #ffa426; }

.stats-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    font-size: 24px;
}
.stats-icon.primary { background: rgba(103, 119, 239, 0.1); color: #6777ef; }
.stats-icon.success { background: rgba(79, 190, 135, 0.1); color: #4fbe87; }
.stats-icon.danger { background: rgba(243, 97, 109, 0.1); color: #f3616d; }
.stats-icon.warning { background: rgba(255, 164, 38, 0.1); color: #ffa426; }

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e9ecef;
}

.user-avatar-placeholder {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6777ef, #4fbe87);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 16px;
}

.action-buttons .btn {
    margin: 0 2px;
    padding: 6px 10px;
}

.search-filter-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.filter-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.role-select {
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 13px;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.role-select:hover {
    border-color: #6777ef;
    background: #f8f9fa;
}

.status-switch {
    transform: scale(1.2);
    cursor: pointer;
}

@media (max-width: 768px) {
    .stats-card {
        margin-bottom: 15px;
    }
    .search-filter-section {
        padding: 15px;
    }
}
</style>

<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">

  <!-- Header -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="mb-0 text-gray-800">
      <i class="fas fa-users text-primary"></i> User Management
    </h1>
    <ol class="breadcrumb">
      <?php
      foreach ($crumbs as $key => $crumb) {
        $active = ($key == (count($crumbs) - 1)) ? 'active' : '';
        echo '<li class="breadcrumb-item ' . $active . '" aria-current="page">' . $crumb . '</li>';
      }
      ?>
    </ol>
  </div>

  <!-- Alert Messages -->
  <div class="row mb-3">
    <div class="col-12">
      <?php include('Includes/parts/alerts.php') ?>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stats-card primary">
        <div class="card-body d-flex align-items-center">
          <div class="stats-icon primary me-3">
            <i class="fas fa-users"></i>
          </div>
          <div>
            <div class="text-muted small">Total Users</div>
            <div class="h4 mb-0 font-weight-bold"><?php echo $totalUsers; ?></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stats-card success">
        <div class="card-body d-flex align-items-center">
          <div class="stats-icon success me-3">
            <i class="fas fa-user-check"></i>
          </div>
          <div>
            <div class="text-muted small">Active Users</div>
            <div class="h4 mb-0 font-weight-bold text-success"><?php echo $activeUsers; ?></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stats-card danger">
        <div class="card-body d-flex align-items-center">
          <div class="stats-icon danger me-3">
            <i class="fas fa-user-slash"></i>
          </div>
          <div>
            <div class="text-muted small">Inactive Users</div>
            <div class="h4 mb-0 font-weight-bold text-danger"><?php echo $inactiveUsers; ?></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stats-card warning">
        <div class="card-body d-flex align-items-center">
          <div class="stats-icon warning me-3">
            <i class="fas fa-user-shield"></i>
          </div>
          <div>
            <div class="text-muted small">Confirmed</div>
            <div class="h4 mb-0 font-weight-bold text-warning"><?php echo $confirmedUsers; ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content Card -->
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
          <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h6 class="m-0 font-weight-bold text-primary">
              <i class="fas fa-list"></i> User Directory
            </h6>
            <a href="<?php echo buildurl('dashboard/users/add'); ?>" class="btn btn-primary btn-sm">
              <i class="fas fa-user-plus"></i> Create New User
            </a>
          </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="card-body">
          <div class="search-filter-section">
            <div class="row g-3">
              <div class="col-md-5">
                <div class="input-group">
                  <span class="input-group-text bg-white">
                    <i class="fas fa-search"></i>
                  </span>
                  <input type="text" class="form-control" id="searchInput"
                         placeholder="Search by name, email, or username...">
                </div>
              </div>

              <div class="col-md-3">
                <select class="form-select" id="roleFilter">
                  <option value="">All Roles</option>
                  <?php foreach ($roles as $role): ?>
                    <option value="<?php echo htmlspecialchars($role['id']); ?>">
                      <?php echo htmlspecialchars($role['name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-2">
                <select class="form-select" id="statusFilter">
                  <option value="">All Status</option>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div>

              <div class="col-md-2">
                <button class="btn btn-secondary w-100" id="resetFilters">
                  <i class="fas fa-redo"></i> Reset
                </button>
              </div>
            </div>

            <div class="mt-3">
              <small class="text-muted">
                <i class="fas fa-info-circle"></i>
                Showing <span id="resultCount"><?php echo $totalUsers; ?></span> of <?php echo $totalUsers; ?> users
              </small>
            </div>
          </div>

          <!-- Users Table -->
          <div class="table-responsive">
            <table class="table table-hover align-middle" id="usersTable">
              <thead class="table-light">
                <tr>
                  <th style="width: 80px;">Avatar</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th style="width: 250px;">Status</th>
                  <th style="width: 150px;" class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($data)): ?>
                  <tr>
                    <td colspan="6" class="text-center py-5">
                      <i class="fas fa-users fa-3x text-muted mb-3"></i>
                      <p class="text-muted">No users found</p>
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($data as $user):
                    // Get user initials for avatar placeholder
                    $name = !empty($user['username']) ? $user['username'] : $user['first_name'] ?? '';
                    $surname = $user['surname'] ?? $user['last_name'] ?? '';
                    $initials = strtoupper(substr($name, 0, 1) . substr($surname, 0, 1));
                    $fullName = trim($name . ' ' . $surname);
                    if (empty($fullName)) $fullName = 'N/A';
                  ?>
                  <tr data-role="<?php echo htmlspecialchars($user['role_id']); ?>"
                      data-status="<?php echo $user['locked'] == 0 ? 'active' : 'inactive'; ?>"
                      data-search="<?php echo strtolower($fullName . ' ' . $user['email']); ?>">

                    <!-- Avatar -->
                    <td>
                      <?php if (isset($user['location']) && !empty($user['location'])): ?>
                        <img src="<?php echo htmlspecialchars($user['location']); ?>"
                             alt="Avatar" class="user-avatar">
                      <?php else: ?>
                        <div class="user-avatar-placeholder">
                          <?php echo $initials ?: 'NA'; ?>
                        </div>
                      <?php endif; ?>
                    </td>

                    <!-- Name -->
                    <td>
                      <div class="fw-bold"><?php echo htmlspecialchars($fullName); ?></div>
                      <small class="text-muted">@<?php echo htmlspecialchars($user['username'] ?? 'user'); ?></small>
                    </td>

                    <!-- Email -->
                    <td>
                      <i class="fas fa-envelope text-muted me-1"></i>
                      <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>" class="text-decoration-none">
                        <?php echo htmlspecialchars($user['email']); ?>
                      </a>
                    </td>

                    <!-- Role -->
                    <td>
                      <select class="role-select form-select-sm"
                              id="role_<?php echo $user['user_id']; ?>"
                              name="role"
                              onchange="handleSelect(event)">
                        <?php foreach ($roles as $role):
                          $selected = ($user['role_id'] == $role['id']) ? 'selected' : '';
                        ?>
                          <option value="<?php echo $role['id']; ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($role['name']); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </td>

                    <!-- Status Badges -->
                    <td>
                      <div class="d-flex flex-wrap gap-1">
                        <?php if ($user['verified'] == 1): ?>
                          <span class="badge bg-primary">
                            <i class="fas fa-check-circle"></i> Verified
                          </span>
                        <?php else: ?>
                          <span class="badge bg-warning">
                            <i class="fas fa-clock"></i> Unverified
                          </span>
                        <?php endif; ?>

                        <?php if ($user['locked'] == 0): ?>
                          <span class="badge bg-success">
                            <i class="fas fa-unlock"></i> Active
                          </span>
                        <?php else: ?>
                          <span class="badge bg-danger">
                            <i class="fas fa-lock"></i> Inactive
                          </span>
                        <?php endif; ?>
                      </div>
                    </td>

                    <!-- Actions -->
                    <td class="text-center">
                      <div class="action-buttons">
                        <a href="details?id=<?php echo $user['user_id']; ?>"
                           class="btn btn-sm btn-info"
                           title="View Details">
                          <i class="fas fa-eye"></i>
                        </a>

                        <a href="add?id=<?php echo $user['user_id']; ?>"
                           class="btn btn-sm btn-primary"
                           title="Edit User">
                          <i class="fas fa-edit"></i>
                        </a>

                        <label class="form-check form-switch d-inline-block mb-0" title="Toggle Active Status">
                          <input class="form-check-input status-switch"
                                 type="checkbox"
                                 onclick="handleToggle(event)"
                                 data_id="<?php echo $user['user_id']; ?>"
                                 id="switch_<?php echo $user['user_id']; ?>"
                                 <?php echo ($user['locked'] == 0) ? 'checked' : ''; ?>>
                        </label>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- No Results Message -->
          <div id="noResults" class="text-center py-5" style="display: none;">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <p class="text-muted">No users match your search criteria</p>
            <button class="btn btn-sm btn-outline-primary" id="clearSearch">
              Clear Filters
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// User Management JavaScript
(function() {
  'use strict';

  // Search and Filter Functionality
  const searchInput = document.getElementById('searchInput');
  const roleFilter = document.getElementById('roleFilter');
  const statusFilter = document.getElementById('statusFilter');
  const resetFilters = document.getElementById('resetFilters');
  const clearSearch = document.getElementById('clearSearch');
  const resultCount = document.getElementById('resultCount');
  const tableRows = document.querySelectorAll('#usersTable tbody tr[data-search]');
  const noResults = document.getElementById('noResults');

  function filterTable() {
    const searchTerm = searchInput.value.toLowerCase();
    const roleValue = roleFilter.value;
    const statusValue = statusFilter.value;
    let visibleCount = 0;

    tableRows.forEach(row => {
      const searchData = row.getAttribute('data-search');
      const roleData = row.getAttribute('data-role');
      const statusData = row.getAttribute('data-status');

      const matchesSearch = !searchTerm || searchData.includes(searchTerm);
      const matchesRole = !roleValue || roleData === roleValue;
      const matchesStatus = !statusValue || statusData === statusValue;

      if (matchesSearch && matchesRole && matchesStatus) {
        row.style.display = '';
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    });

    resultCount.textContent = visibleCount;

    if (visibleCount === 0) {
      document.querySelector('#usersTable').style.display = 'none';
      noResults.style.display = 'block';
    } else {
      document.querySelector('#usersTable').style.display = 'table';
      noResults.style.display = 'none';
    }
  }

  if (searchInput) {
    searchInput.addEventListener('keyup', filterTable);
    roleFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);

    resetFilters.addEventListener('click', function() {
      searchInput.value = '';
      roleFilter.value = '';
      statusFilter.value = '';
      filterTable();
    });

    if (clearSearch) {
      clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        roleFilter.value = '';
        statusFilter.value = '';
        filterTable();
      });
    }
  }

  // Role Change Handler
  window.handleSelect = function(e) {
    if (!confirm("Are you sure you want to change this user's role?")) {
      e.target.value = e.target.getAttribute('data-original-value');
      return;
    }

    // Store original value for rollback
    e.target.setAttribute('data-original-value', e.target.value);

    const selection = e.target.value;
    const user_id = e.target.id.replace('role_', '');
    const formData = new FormData();
    formData.append("role_id", selection);
    formData.append("user_id", user_id);

    const currentURL = window.location.href;
    const stripped = currentURL.substring(0, currentURL.lastIndexOf("/"));

    // Show loading state
    e.target.disabled = true;

    fetch(stripped + '/managerole', {
      method: "post",
      body: formData,
    })
    .then((response) => {
      e.target.disabled = false;
      Toastify({
        text: "User role has been updated successfully",
        duration: 3000,
        gravity: "bottom",
        position: "right",
        backgroundColor: "#4fbe87",
      }).showToast();
    })
    .catch((err) => {
      e.target.disabled = false;
      e.target.value = e.target.getAttribute('data-original-value');
      Toastify({
        text: "Failed to update user role",
        duration: 3000,
        gravity: "bottom",
        position: "right",
        backgroundColor: "#f3616d",
      }).showToast();
      console.error(err);
    });
  }

  // Status Toggle Handler
  window.handleToggle = function(event) {
    const userSwitch = event.target;
    const locked = userSwitch.checked;
    const user_id = userSwitch.getAttribute('data_id');
    const formData = new FormData();
    formData.append("locked", locked);
    formData.append("user_id", user_id);

    const currentURL = window.location.href;
    const stripped = currentURL.substring(0, currentURL.lastIndexOf("/"));

    // Disable switch during request
    userSwitch.disabled = true;

    fetch(stripped + '/manageuser', {
      method: "post",
      body: formData,
    })
    .then((response) => {
      userSwitch.disabled = false;
      Toastify({
        text: locked ? "User activated successfully" : "User deactivated successfully",
        duration: 3000,
        gravity: "bottom",
        position: "right",
        backgroundColor: "#4fbe87",
      }).showToast();

      // Update row status attribute
      const row = userSwitch.closest('tr');
      row.setAttribute('data-status', locked ? 'active' : 'inactive');

      // Reload after short delay to update badges
      setTimeout(() => location.reload(), 1500);
    })
    .catch((err) => {
      userSwitch.disabled = false;
      userSwitch.checked = !locked; // Revert checkbox
      Toastify({
        text: "Status change failed",
        duration: 3000,
        gravity: "bottom",
        position: "right",
        backgroundColor: "#f3616d",
      }).showToast();
      console.error(err);
    });
  }

  // Store original role values on page load
  document.querySelectorAll('.role-select').forEach(select => {
    select.setAttribute('data-original-value', select.value);
  });

})();
</script>
