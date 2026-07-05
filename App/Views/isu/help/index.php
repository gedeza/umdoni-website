<?php
/**
 * ISU console — Help & Guides. Static content; uses Bootstrap accordion.
 */
?>

<div class="isu-card p-4 mb-4">
    <h6 class="status-live text-uppercase mb-2">Getting started</h6>
    <p class="muted mb-2" style="font-size:.9rem;">The ISU console is your private control panel for the uMdoni website — separate from
        the municipal dashboard. Use the tabs at the top to move between sections. Every important action is confirmed
        before it happens and recorded in an activity log.</p>
    <ul class="muted mb-0" style="font-size:.9rem;">
        <li><strong>Home</strong> — a status overview and quick actions.</li>
        <li><strong>Site Control</strong> — take the public site offline or bring it back.</li>
        <li><strong>ISU Admins</strong> — manage who can access this console.</li>
        <li><strong>Database</strong> — back up the database and run approved updates.</li>
        <li><strong>Deploy</strong> — push code changes and roll them back.</li>
    </ul>
</div>

<div class="accordion" id="isuHelp">
    <?php
    $guides = [
        [
            'id' => 'g1', 'icon' => '⚡', 'title' => 'Site Control — taking the site offline / online',
            'body' => '<p>Use this when uMdoni\'s annual renewal is unpaid, or for planned downtime.</p>
                <ol>
                  <li>Click <strong>Suspend service</strong>. The public site shows a neutral
                      "Site Temporarily Unavailable — contact uMdoni support" page. No payment wording is shown.</li>
                  <li>This console stays reachable the whole time, so you are never locked out.</li>
                  <li>When paid, open Site Control and click <strong>Restore service</strong> — the site is live again instantly.</li>
                </ol>
                <p class="muted mb-0">Every suspend/restore is logged with who, when and an optional note (e.g. "invoice #1234 paid").</p>',
        ],
        [
            'id' => 'g2', 'icon' => '👥', 'title' => 'ISU Admins — managing console access',
            'body' => '<ul>
                  <li><strong>Add admin:</strong> enter a name + email. A one-time password appears <em>once</em> — copy it and
                      share it securely. The new admin must change it on first login.</li>
                  <li><strong>Reset pw:</strong> issues a fresh one-time password for someone who is locked out.</li>
                  <li><strong>Deactivate:</strong> instantly revokes access. You cannot deactivate yourself or the last active admin.</li>
                </ul>',
        ],
        [
            'id' => 'g3', 'icon' => '🗄', 'title' => 'Database — backups & migrations',
            'body' => '<ul>
                  <li><strong>Back up now:</strong> creates a compressed database backup you can download.</li>
                  <li><strong>Migrations:</strong> pre-approved database updates shipped with the code. Click <strong>Run</strong>
                      on a pending one; each runs only once. There is deliberately no free-form SQL here.</li>
                </ul>
                <p class="muted mb-0">Keep a recent backup before running a migration or applying a patch.</p>',
        ],
        [
            'id' => 'g4', 'icon' => '⬆', 'title' => 'Deploy — pushing code changes safely',
            'body' => '<ol>
                  <li>Zip your changed files laid out from the project root (e.g. <code>App/…</code>, <code>public/…</code>).</li>
                  <li>Upload it — you\'ll see the exact file list and whether each file is <em>new</em> or an <em>overwrite</em>.</li>
                  <li>Only allowed files apply (protected files like config, .env, .htaccess are refused). Click <strong>Apply</strong>.</li>
                  <li>If anything looks wrong, click <strong>Roll back</strong> — overwritten files are restored and new files removed.</li>
                </ol>',
        ],
        [
            'id' => 'g5', 'icon' => '🔒', 'title' => 'Security & good habits',
            'body' => '<ul>
                  <li>Use a strong, unique password; you\'ll be signed out automatically after 30 minutes idle.</li>
                  <li>Only add ISU staff as admins; deactivate anyone who leaves.</li>
                  <li>Back up before big changes. Prefer the Deploy tool over editing files by hand.</li>
                  <li>Never share one-time passwords over insecure channels.</li>
                </ul>',
        ],
    ];
    foreach ($guides as $i => $g):
    ?>
        <div class="accordion-item" style="background:#111a2b; border-color:#1f2b40;">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" style="background:#111a2b; color:#e6edf3;"
                        type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $g['id']; ?>">
                    <span class="me-2"><?php echo $g['icon']; ?></span> <?php echo htmlspecialchars($g['title'], ENT_QUOTES, 'UTF-8'); ?>
                </button>
            </h2>
            <div id="<?php echo $g['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#isuHelp">
                <div class="accordion-body muted" style="font-size:.9rem;"><?php echo $g['body']; ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<p class="muted mt-4" style="font-size:.85rem;">Need more help? Contact ISU Technologies — <a href="mailto:nhlanhla@isutech.co.za">nhlanhla@isutech.co.za</a>.</p>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
