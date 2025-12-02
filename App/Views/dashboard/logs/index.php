<?php
global $context;
$data = $context->data; // Get all data from context
$logs = $data['logs'] ?? $context->data; // Handle both old and new format
$filterType = $data['filterType'] ?? 'all';
$filterUser = $data['filterUser'] ?? '';
$limit = $data['limit'] ?? 100;
$crumbs = getCrumbs();
?>

<!-- Container Fluid-->
<div class="container-fluid" id="container-wrapper">

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="mb-0 text-gray-800">Activity Logs</h1>

    <ol class="breadcrumb">
      <?php
      foreach ($crumbs as $key => $crumb) {
        if ($key == (count($crumbs) - 1)) {
          $active = 'active';
          echo ' <li class="breadcrumb-item ' . $active . '" aria-current="page">' . $crumb . '</li>  ';
        } else {
          $active = '';
          echo '<li class="breadcrumb-item ' . $active . '" aria-current="page">' . $crumb . '</li>';
        }
      }
      ?>
    </ol>
  </div>

  <div class="row">
    <div class="col-md">
      <div class="card">
        <div class="card-header">
          <p class="card-title fw-light">Activity Logs</p>
          <div class="float-start float-lg-end">
            <div class="card-content">
              <?php include('Includes/parts/alerts.php') ?>
            </div>
          </div>
        </div>

        <!-- Filter Form -->
        <div class="card-body border-bottom">
          <form method="GET" action="<?php echo url('dashboard/logs/index'); ?>" class="row g-3">
            <div class="col-md-3">
              <label for="filterType" class="form-label">Log Type</label>
              <select class="form-select" id="filterType" name="type">
                <option value="all" <?php echo $filterType === 'all' ? 'selected' : ''; ?>>All Logs</option>
                <option value="login" <?php echo $filterType === 'login' ? 'selected' : ''; ?>>Logins</option>
                <option value="logout" <?php echo $filterType === 'logout' ? 'selected' : ''; ?>>Logouts</option>
                <option value="error" <?php echo $filterType === 'error' ? 'selected' : ''; ?>>Errors</option>
                <option value="warning" <?php echo $filterType === 'warning' ? 'selected' : ''; ?>>Warnings</option>
                <option value="info" <?php echo $filterType === 'info' ? 'selected' : ''; ?>>Info</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="filterUser" class="form-label">Search User</label>
              <input type="text" class="form-control" id="filterUser" name="user" placeholder="Username or email" value="<?php echo htmlspecialchars($filterUser); ?>">
            </div>
            <div class="col-md-2">
              <label for="limit" class="form-label">Show</label>
              <select class="form-select" id="limit" name="limit">
                <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50 entries</option>
                <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100 entries</option>
                <option value="250" <?php echo $limit == 250 ? 'selected' : ''; ?>>250 entries</option>
                <option value="500" <?php echo $limit == 500 ? 'selected' : ''; ?>>500 entries</option>
              </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <a href="<?php echo url('dashboard/logs/index'); ?>" class="btn btn-secondary w-100">Clear</a>
            </div>
          </form>
        </div>

        <div class="card-body">
          <div class="table-responsive" >
            <table class="table align-items-center table-flush table-hover" id="table1">
              <thead class="thead-light">
                <tr>
                  <th>Type</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Message/Action</th>
                  <th>Timestamp</th>
                  <th>IP Address</th>
                  <th>Logout</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if (empty($logs)) {
                  echo '<tr><td colspan="7" class="text-center text-muted">No logs found matching your filters</td></tr>';
                } else {
                  foreach ($logs as $key => $log)
                  {
                    $logType = $log['status'] ?? 'info';
                    $actions = $log['actions'] ?? '';
                    $location = $log['location'] ?? '';

                    // Parse IP from location (format: "IP | User Agent")
                    $ipParts = explode(' | ', $location);
                    $ipAddress = $ipParts[0] ?? $location;
                    $userAgent = $ipParts[1] ?? '';

                    // Color coding based on log type
                    $rowClass = '';
                    $badgeClass = 'bg-secondary';
                    $badgeText = strtoupper($logType);

                    switch ($logType) {
                      case 'error':
                        $rowClass = 'table-danger';
                        $badgeClass = 'bg-danger';
                        break;
                      case 'warning':
                        $rowClass = 'table-warning';
                        $badgeClass = 'bg-warning';
                        break;
                      case 'login':
                        $rowClass = 'table-success';
                        $badgeClass = 'bg-success';
                        break;
                      case 'logout':
                        $rowClass = 'table-info';
                        $badgeClass = 'bg-info';
                        break;
                      case 'info':
                        $badgeClass = 'bg-primary';
                        break;
                    }

                    echo '<tr class="' . $rowClass . '">
                            <td><span class="badge ' . $badgeClass . '">' . $badgeText . '</span></td>
                            <td>' . htmlspecialchars($log['username']) . '</td>
                            <td>' . htmlspecialchars($log['email']) . '</td>
                            <td>';

                    // Show action message if it's an error/warning, otherwise show "Login/Logout"
                    if (!empty($actions)) {
                      echo '<small>' . htmlspecialchars($actions) . '</small>';
                      if (!empty($userAgent)) {
                        echo '<br><small class="text-muted" title="' . htmlspecialchars($userAgent) . '">UA: ' . htmlspecialchars(substr($userAgent, 0, 50)) . '...</small>';
                      }
                    } else {
                      echo $badgeText . ' Activity';
                    }

                    echo '</td>
                            <td><small>' . $log['time_log'] . '</small></td>
                            <td><small>' . htmlspecialchars($ipAddress) . '</small></td>
                            <td><small>' . ($log['logout'] ?? '-') . '</small></td>
                          </tr>';
                  }
                }
                ?>
              </tbody>
            </table>
          </div>
          <div class="card-footer"></div>
        </div>
      </div>
    </div>
  </div>
</div>
