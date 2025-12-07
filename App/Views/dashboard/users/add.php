<?php
    global $context;
    if(!is_null($context->data))
        $data = $context->data;

    use App\Models\Countries;
    use App\Models\RolesModel;

    $crumbs = getCrumbs();

    // Determine if this is create or edit mode
    $isEditMode = isset($data['user_id']) && !empty($data['user_id']);
    $pageTitle = $isEditMode ? 'Edit User' : 'Create New User';
    $submitButtonText = $isEditMode ? 'Update User' : 'Create User';
    $action = $isEditMode ? 'update' : 'save';

    // Get form data with defaults
    $user_id = $data['user_id'] ?? '';
    $first_name = $data['first_name'] ?? '';
    $last_name = $data['last_name'] ?? '';
    $email = $data['email'] ?? '';
    $mobile_number = $data['mobile_number'] ?? '';
    $address_1 = $data['address_1'] ?? '';
    $address_2 = $data['address_2'] ?? '';
    $town = $data['town'] ?? '';
    $postal_code = $data['postal_code'] ?? '';
    $province = $data['province_id'] ?? '';
    $city = $data['region_id'] ?? '';
    $role_id = $data['role_id'] ?? '';

    // Get roles for dropdown
    $roles = RolesModel::getAll();
?>

<style>
/* Enhanced Form Styles */
.form-card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-radius: 10px;
}

.form-card .card-header {
    background: linear-gradient(135deg, #6777ef 0%, #4fbe87 100%);
    color: white;
    border-radius: 10px 10px 0 0 !important;
    padding: 20px;
}

.form-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.form-section-title {
    font-size: 16px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #6777ef;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 8px;
}

.form-label .required {
    color: #f3616d;
    font-weight: bold;
}

.form-control, .form-select {
    border: 1px solid #e3e6f0;
    border-radius: 6px;
    padding: 10px 15px;
    transition: all 0.3s;
}

.form-control:focus, .form-select:focus {
    border-color: #6777ef;
    box-shadow: 0 0 0 0.2rem rgba(103, 119, 239, 0.15);
}

.password-strength {
    height: 5px;
    border-radius: 3px;
    margin-top: 8px;
    background: #e3e6f0;
    overflow: hidden;
}

.password-strength-bar {
    height: 100%;
    transition: all 0.3s;
    width: 0%;
}

.strength-weak { background: #f3616d; width: 33%; }
.strength-medium { background: #ffa426; width: 66%; }
.strength-strong { background: #4fbe87; width: 100%; }

.password-requirements {
    font-size: 12px;
    margin-top: 8px;
    color: #6c757d;
}

.password-requirements li {
    list-style: none;
    padding: 3px 0;
}

.password-requirements li.met {
    color: #4fbe87;
}

.password-requirements li.met::before {
    content: "✓ ";
    font-weight: bold;
}

.password-requirements li.unmet::before {
    content: "○ ";
}

.btn-primary {
    background: linear-gradient(135deg, #6777ef 0%, #4fbe87 100%);
    border: none;
    padding: 12px 30px;
    border-radius: 6px;
    font-weight: 500;
    transition: transform 0.2s;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(103, 119, 239, 0.3);
}

.btn-secondary {
    padding: 12px 30px;
    border-radius: 6px;
    font-weight: 500;
}

.input-icon {
    position: relative;
}

.input-icon i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

.input-icon .form-control {
    padding-left: 40px;
}

@media (max-width: 768px) {
    .form-section {
        padding: 15px;
    }
}
</style>

<div class="container-fluid" id="container-wrapper">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
      <i class="fas fa-user-<?php echo $isEditMode ? 'edit' : 'plus'; ?> text-primary"></i>
      <?php echo $pageTitle; ?>
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

  <div class="row">
    <div class="col-xl-10 col-lg-12 mx-auto">
      <div class="card form-card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">
              <i class="fas fa-id-card"></i>
              <?php echo $isEditMode ? 'Update User Information' : 'Enter User Details'; ?>
            </h6>
            <?php if ($isEditMode): ?>
              <a href="delete?id=<?php echo $user_id; ?>"
                 class="btn btn-danger btn-sm"
                 onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                <i class="fas fa-trash-alt"></i> Delete User
              </a>
            <?php endif; ?>
          </div>
        </div>

        <div class="card-body">
          <form class="form" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="userForm">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

            <!-- Account Information Section -->
            <div class="form-section">
              <div class="form-section-title">
                <i class="fas fa-user-circle"></i>
                Account Information
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="first_name" class="form-label">
                    First Name <span class="required">*</span>
                  </label>
                  <input type="text" class="form-control" name="first_name" id="first_name"
                         value="<?php echo htmlspecialchars($first_name); ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="last_name" class="form-label">
                    Last Name <span class="required">*</span>
                  </label>
                  <input type="text" class="form-control" name="last_name" id="last_name"
                         value="<?php echo htmlspecialchars($last_name); ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="email" class="form-label">
                    Email Address <span class="required">*</span>
                  </label>
                  <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" class="form-control" name="email" id="email"
                           value="<?php echo htmlspecialchars($email); ?>" required>
                  </div>
                  <small class="text-muted">User will use this email to log in</small>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="role_id" class="form-label">
                    User Role <span class="required">*</span>
                  </label>
                  <select class="form-select" name="role_id" id="role_id" required>
                    <option value="">-- Select Role --</option>
                    <?php foreach ($roles as $role):
                      $selected = ($role_id == $role['id']) ? 'selected' : '';
                    ?>
                      <option value="<?php echo $role['id']; ?>" <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($role['name']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <small class="text-muted">Determines user permissions and access level</small>
                </div>
              </div>
            </div>

            <!-- Password Section (Only for Create Mode) -->
            <?php if (!$isEditMode): ?>
            <div class="form-section">
              <div class="form-section-title">
                <i class="fas fa-key"></i>
                Password Setup
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="password" class="form-label">
                    Password <span class="required">*</span>
                  </label>
                  <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="form-control" name="password" id="password"
                           required minlength="8">
                  </div>
                  <div class="password-strength">
                    <div class="password-strength-bar" id="strengthBar"></div>
                  </div>
                  <div id="strengthText" class="small mt-1"></div>
                  <ul class="password-requirements mt-2">
                    <li id="req-length" class="unmet">Minimum 8 characters</li>
                    <li id="req-uppercase" class="unmet">At least one uppercase letter</li>
                    <li id="req-lowercase" class="unmet">At least one lowercase letter</li>
                    <li id="req-number" class="unmet">At least one number</li>
                  </ul>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="confirm_password" class="form-label">
                    Confirm Password <span class="required">*</span>
                  </label>
                  <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password"
                           required minlength="8">
                  </div>
                  <div id="passwordMatch" class="small mt-1"></div>
                </div>
              </div>
            </div>
            <?php endif; ?>

            <!-- Contact Information Section -->
            <div class="form-section">
              <div class="form-section-title">
                <i class="fas fa-address-card"></i>
                Contact Information
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="mobile_number" class="form-label">Phone Number</label>
                  <div class="input-icon">
                    <i class="fas fa-phone"></i>
                    <input type="tel" class="form-control" name="mobile_number" id="mobile_number"
                           value="<?php echo htmlspecialchars($mobile_number); ?>">
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="postal_code" class="form-label">Postal Code</label>
                  <div class="input-icon">
                    <i class="fas fa-mailbox"></i>
                    <input type="text" class="form-control" name="postal_code" id="postal_code"
                           value="<?php echo htmlspecialchars($postal_code); ?>">
                  </div>
                </div>

                <div class="col-md-12 mb-3">
                  <label for="address_1" class="form-label">Address Line 1</label>
                  <input type="text" class="form-control" name="address_1" id="address_1"
                         value="<?php echo htmlspecialchars($address_1); ?>">
                </div>

                <div class="col-md-12 mb-3">
                  <label for="address_2" class="form-label">Address Line 2</label>
                  <input type="text" class="form-control" name="address_2" id="address_2"
                         value="<?php echo htmlspecialchars($address_2); ?>">
                </div>

                <div class="col-md-6 mb-3">
                  <label for="province" class="form-label">Province</label>
                  <?php
                    $provinces = Countries::getProvinces();
                    $provinces = array_column($provinces, 'ProvinceName', 'ProvinceID');
                    $selectedProvince = !empty($province) ? $provinces[$province] : '';
                  ?>
                  <select class="form-select" name="province" id="province">
                    <option value="">-- Select Province --</option>
                    <?php foreach ($provinces as $id => $name):
                      $selected = ($province == $id) ? 'selected' : '';
                    ?>
                      <option value="<?php echo $id; ?>" <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($name); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="city" class="form-label">City</label>
                  <?php
                    if (isset($province) && $province > 0) {
                        $regions = Countries::getRegion($province);
                        $regions = array_column($regions, 'RegionName', 'RegionID');
                    } else {
                        $regions = Countries::getRegions();
                        $regions = array_column($regions, 'RegionName', 'RegionID');
                    }
                    $selectedRegion = !empty($city) ? $regions[$city] : '';
                  ?>
                  <select class="form-select" name="city" id="city">
                    <option value="">-- Select City --</option>
                    <?php foreach ($regions as $id => $name):
                      $selected = ($city == $id) ? 'selected' : '';
                    ?>
                      <option value="<?php echo $id; ?>" <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($name); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-12 mb-3">
                  <label for="town" class="form-label">Town</label>
                  <input type="text" class="form-control" name="town" id="town"
                         value="<?php echo htmlspecialchars($town); ?>">
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-between align-items-center mt-4">
              <a href="<?php echo buildurl('dashboard/users/index'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Cancel
              </a>
              <button type="submit" name="submit-btn" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-<?php echo $isEditMode ? 'save' : 'user-plus'; ?>"></i>
                <?php echo $submitButtonText; ?>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
  'use strict';

  // Province-City Dynamic Loading
  const provinceSelect = document.getElementById('province');
  const citySelect = document.getElementById('city');

  if (provinceSelect) {
    provinceSelect.addEventListener('change', function() {
      const provinceId = this.value;

      if (!provinceId) {
        citySelect.innerHTML = '<option value="">-- Select City --</option>';
        return;
      }

      // Fetch cities for selected province
      fetch('<?php echo buildurl("dashboard/users/regions?ProvinceID="); ?>' + provinceId)
        .then(response => response.json())
        .then(data => {
          citySelect.innerHTML = '<option value="">-- Select City --</option>';

          if (data && data.length > 0) {
            data.forEach(region => {
              const option = document.createElement('option');
              option.value = region.RegionID;
              option.textContent = region.RegionName;
              citySelect.appendChild(option);
            });
          }
        })
        .catch(err => console.error('Error loading cities:', err));
    });
  }

  // Password Strength Checker
  const passwordInput = document.getElementById('password');
  const confirmPasswordInput = document.getElementById('confirm_password');

  if (passwordInput) {
    passwordInput.addEventListener('input', function() {
      const password = this.value;
      const strengthBar = document.getElementById('strengthBar');
      const strengthText = document.getElementById('strengthText');

      // Check requirements
      const hasLength = password.length >= 8;
      const hasUppercase = /[A-Z]/.test(password);
      const hasLowercase = /[a-z]/.test(password);
      const hasNumber = /[0-9]/.test(password);

      // Update requirement indicators
      document.getElementById('req-length').className = hasLength ? 'met' : 'unmet';
      document.getElementById('req-uppercase').className = hasUppercase ? 'met' : 'unmet';
      document.getElementById('req-lowercase').className = hasLowercase ? 'met' : 'unmet';
      document.getElementById('req-number').className = hasNumber ? 'met' : 'unmet';

      // Calculate strength
      const score = [hasLength, hasUppercase, hasLowercase, hasNumber].filter(Boolean).length;

      strengthBar.className = 'password-strength-bar';
      if (score <= 2) {
        strengthBar.classList.add('strength-weak');
        strengthText.textContent = 'Weak password';
        strengthText.className = 'small mt-1 text-danger';
      } else if (score === 3) {
        strengthBar.classList.add('strength-medium');
        strengthText.textContent = 'Medium strength password';
        strengthText.className = 'small mt-1 text-warning';
      } else {
        strengthBar.classList.add('strength-strong');
        strengthText.textContent = 'Strong password';
        strengthText.className = 'small mt-1 text-success';
      }
    });

    // Password Match Checker
    const checkPasswordMatch = function() {
      const password = passwordInput.value;
      const confirmPassword = confirmPasswordInput.value;
      const matchDiv = document.getElementById('passwordMatch');

      if (!confirmPassword) {
        matchDiv.textContent = '';
        return;
      }

      if (password === confirmPassword) {
        matchDiv.textContent = '✓ Passwords match';
        matchDiv.className = 'small mt-1 text-success';
      } else {
        matchDiv.textContent = '✗ Passwords do not match';
        matchDiv.className = 'small mt-1 text-danger';
      }
    };

    confirmPasswordInput.addEventListener('input', checkPasswordMatch);
    passwordInput.addEventListener('input', checkPasswordMatch);
  }

  // Form Validation
  const form = document.getElementById('userForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      const password = document.getElementById('password');
      const confirmPassword = document.getElementById('confirm_password');

      if (password && confirmPassword) {
        if (password.value !== confirmPassword.value) {
          e.preventDefault();
          alert('Passwords do not match. Please check and try again.');
          confirmPassword.focus();
          return false;
        }
      }
    });
  }
})();
</script>
