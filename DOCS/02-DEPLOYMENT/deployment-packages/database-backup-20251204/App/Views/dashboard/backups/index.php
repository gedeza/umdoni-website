<?php
global $context;
$backups = $context->data['backups'] ?? [];
$stats = $context->data['stats'] ?? [
    'total_count' => 0,
    'total_size_formatted' => '0 B',
    'oldest_backup' => 'N/A',
    'newest_backup' => 'N/A'
];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Database Backups</h1>
                <a href="<?php echo buildurl('dashboard/backups/create'); ?>"
                   class="btn btn-success"
                   onclick="return confirm('Create a new database backup now?')">
                    <i class="bi bi-cloud-arrow-up"></i> Create Backup
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="bi bi-database text-primary fs-1"></i>
                    <h3 class="mt-2"><?php echo $stats['total_count']; ?></h3>
                    <p class="text-muted mb-0">Total Backups</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="bi bi-hdd text-info fs-1"></i>
                    <h3 class="mt-2"><?php echo $stats['total_size_formatted']; ?></h3>
                    <p class="text-muted mb-0">Total Size</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check text-success fs-1"></i>
                    <h5 class="mt-2"><?php echo $stats['newest_backup']; ?></h5>
                    <p class="text-muted mb-0">Latest Backup</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-x text-warning fs-1"></i>
                    <h5 class="mt-2"><?php echo $stats['oldest_backup']; ?></h5>
                    <p class="text-muted mb-0">Oldest Backup</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h5 class="alert-heading"><i class="bi bi-info-circle"></i> Backup Information</h5>
                <p class="mb-0">
                    <strong>Retention Policy:</strong> Daily (7 days) • Weekly (4 weeks) • Monthly (3 months)<br>
                    <strong>Automated Schedule:</strong> Daily at 2:00 AM server time<br>
                    <strong>Storage Location:</strong> <code>backups/database/</code><br>
                    <strong>Format:</strong> Compressed MySQL dumps (.sql.gz)
                </p>
            </div>
        </div>
    </div>

    <!-- Backup List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Backup History</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($backups)): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> No backups found. Click "Create Backup" to create your first backup.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="backupsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Filename</th>
                                        <th>Size</th>
                                        <th>Age</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($backups as $backup):
                                        $age = time() - $backup['timestamp'];
                                        $ageFormatted = $age < 86400
                                            ? round($age / 3600, 1) . ' hours'
                                            : round($age / 86400, 1) . ' days';
                                        $sizeFormatted = formatBytes($backup['size']);
                                    ?>
                                        <tr>
                                            <td><?php echo $backup['date']; ?></td>
                                            <td>
                                                <code><?php echo htmlspecialchars($backup['filename']); ?></code>
                                            </td>
                                            <td><?php echo $sizeFormatted; ?></td>
                                            <td><?php echo $ageFormatted; ?> ago</td>
                                            <td>
                                                <a href="<?php echo buildurl('dashboard/backups/download') . '?file=' . urlencode($backup['filename']); ?>"
                                                   class="btn btn-sm btn-primary"
                                                   title="Download">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <form method="POST"
                                                      action="<?php echo buildurl('dashboard/backups/delete'); ?>"
                                                      style="display: inline;"
                                                      onsubmit="return confirm('Delete this backup? This action cannot be undone.')">
                                                    <input type="hidden" name="file" value="<?php echo htmlspecialchars($backup['filename']); ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize DataTable if available
if (typeof simpleDatatables !== 'undefined' && document.getElementById('backupsTable')) {
    new simpleDatatables.DataTable('#backupsTable', {
        searchable: true,
        perPage: 10,
        labels: {
            placeholder: "Search backups...",
            perPage: "backups per page",
            noRows: "No backups found",
        }
    });
}
</script>

<?php
/**
 * Helper function to format bytes
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>
