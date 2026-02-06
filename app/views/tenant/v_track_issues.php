<?php
require APPROOT . '/views/inc/tenant_header.php';

// Function to calculate average days to resolve
function calculateAverageDaysToResolve($issues)
{
    $totalDays = 0;
    $resolvedCount = 0;

    foreach ($issues as $issue) {
        if ($issue->status === 'resolved' && !empty($issue->resolved_at)) {
            $resolvedDate = new DateTime($issue->resolved_at);
            $createdDate = new DateTime($issue->created_at);
            $daysToResolve = $resolvedDate->diff($createdDate)->days;

            $totalDays += $daysToResolve;
            $resolvedCount++;
        }
    }

    return $resolvedCount > 0 ? $totalDays / $resolvedCount : 0; // Return average or 0 if no resolved issues
}

// Handle filters
$statusFilter = $_POST['statusFilter'] ?? '';
$priorityFilter = $_POST['priorityFilter'] ?? '';
$categoryFilter = $_POST['categoryFilter'] ?? '';

// Calculate average days
$averageDaysToResolve = calculateAverageDaysToResolve($data['issues']);
?>

<?php
// ADD PAGINATION
require_once APPROOT . '/../app/helpers/AutoPaginate.php';
AutoPaginate::init($data, 5);
?>

<div class="page-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h2>Track Issue Status</h2>
            <p>Monitor the progress of your reported issues</p>
        </div>
        <div class="header-actions">
            <a href="<?php echo URLROOT; ?>/tenant/report_issue" class="btn btn-primary">
                <i class="fas fa-plus"></i> Report New Issue
            </a>
        </div>
    </div>

    <!-- Issue Status Overview -->
    <div class="stats-grid">
        <!-- Pending Issues -->
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-info">
                <h3 class="stat-number"><?php echo $data['pending_issues'] ?? 0; ?></h3>
                <p class="stat-label">Pending Issues</p>
            </div>
        </div>

        <!-- In Progress -->
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-tools"></i></div>
            <div class="stat-info">
                <h3 class="stat-number"><?php echo $data['in_progress_issues'] ?? 0; ?></h3>
                <p class="stat-label">In Progress</p>
            </div>
        </div>

        <!-- Resolved -->
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <h3 class="stat-number"><?php echo $data['resolved_issues'] ?? 0; ?></h3>
                <p class="stat-label">Resolved</p>
            </div>
        </div>

        <!-- Average Days -->
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-info">
                <h3 class="stat-number"><?php echo number_format($averageDaysToResolve, 1); ?></h3>
                <p class="stat-label">Avg. Days to Resolve</p>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <form method="POST" action="">
        <div class="search-filter-content">
            <div class="filter-dropdown-wrapper">
                <select class="form-select" name="statusFilter">
                    <option value="">All Status</option>
                    <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="in_progress" <?= $statusFilter == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="resolved" <?= $statusFilter == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                </select>
            </div>
            <div class="filter-dropdown-wrapper">
                <select class="form-select" name="priorityFilter">
                    <option value="">All Priorities</option>
                    <option value="low" <?= $priorityFilter == 'low' ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?= $priorityFilter == 'medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?= $priorityFilter == 'high' ? 'selected' : ''; ?>>High</option>
                    <option value="emergency" <?= $priorityFilter == 'emergency' ? 'selected' : ''; ?>>Emergency</option>
                </select>
            </div>
            <div class="filter-dropdown-wrapper">
                <select class="form-select" name="categoryFilter">
                    <option value="">All Categories</option>
                    <option value="plumbing" <?= $categoryFilter == 'plumbing' ? 'selected' : ''; ?>>Plumbing</option>
                    <option value="electrical" <?= $categoryFilter == 'electrical' ? 'selected' : ''; ?>>Electrical</option>
                    <option value="hvac" <?= $categoryFilter == 'hvac' ? 'selected' : ''; ?>>Heating/Cooling</option>
                    <option value="appliance" <?= $categoryFilter == 'appliance' ? 'selected' : ''; ?>>Appliances</option>
                    <option value="structural" <?= $categoryFilter == 'structural' ? 'selected' : ''; ?>>Structural</option>
                    <option value="pest" <?= $categoryFilter == 'pest' ? 'selected' : ''; ?>>Pest Control</option>
                    <option value="other" <?= $categoryFilter == 'other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <button class="btn btn-secondary" type="submit">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>
    </form>

    <!-- Issues Table -->
    <div class="dashboard-section">
        <div class="section-header">
            <h3>Your Issues</h3>
        </div>

        <?php
        // Pre-filter issues
        $filteredIssues = array_filter($data['issues'], function ($issue) use ($statusFilter, $priorityFilter, $categoryFilter) {
            if ($statusFilter && $issue->status !== $statusFilter) return false;
            if ($priorityFilter && $issue->priority !== $priorityFilter) return false;
            if ($categoryFilter && $issue->category !== $categoryFilter) return false;
            return true;
        });
        ?>

        <?php if (empty($filteredIssues)): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3>No Issues Found</h3>
                <p>No issues match your current filter criteria.</p>
                <a href="<?php echo URLROOT; ?>/tenant/report_issue" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Report New Issue
                </a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Issue ID</th>
                            <th>Property</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Report Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filteredIssues as $issue): ?>
                            <?php
                            // Check if issue can be edited/deleted (within 1 minute of creation)
                            $createdTime = new DateTime($issue->created_at);
                            $currentTime = new DateTime();
                            $timeDiff = $currentTime->getTimestamp() - $createdTime->getTimestamp();
                            $canEdit = ($timeDiff <= 60); // 60 seconds = 1 minute
                            $canDelete = ($timeDiff <= 60); // 60 seconds = 1 minute
                            ?>
                            <tr>
                                <td><strong><?= $issue->id; ?></strong></td>
                                <td><?= $issue->property_address; ?></td>
                                <td><?= $issue->category; ?></td>
                                <td><span
                                        class="priority-badge <?= $issue->priority; ?>"><?= ucfirst($issue->priority); ?></span>
                                </td>
                                <td><span
                                        class="status-badge <?= $issue->status; ?>"><?= ucfirst(str_replace('_', ' ', $issue->status)); ?></span>
                                </td>
                                <td><?= date("F d, Y", strtotime($issue->created_at)); ?></td>
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <a href="<?= URLROOT; ?>/issues/details/<?= $issue->id; ?>"
                                            class="btn btn-icon btn-primary"
                                            title="View Issue Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($canEdit): ?>
                                            <a href="<?= URLROOT; ?>/issues/edit/<?= $issue->id; ?>"
                                                class="btn btn-icon btn-secondary"
                                                title="Edit Issue">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-icon btn-secondary"
                                                disabled
                                                title="Can only edit within 1 minute of creation">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($canDelete): ?>
                                            <a href="<?= URLROOT; ?>/issues/delete/<?= $issue->id; ?>"
                                                class="btn btn-icon btn-danger"
                                                title="Delete Issue"
                                                onclick="return confirm('Are you sure you want to delete this issue? This action cannot be undone.');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-icon btn-danger"
                                                disabled
                                                title="Can only delete within 1 minute of creation">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Issue Details Modal -->
<div id="issueModal" class="modal-overlay hidden">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3>Issue Details</h3>
            <button class="modal-close" onclick="closeIssueModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="modalContent">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</div>

<!-- ADD PAGINATION HERE - Render at bottom -->
<?php echo AutoPaginate::render($data['_pagination']); ?>

<style>
    /* Empty State Styles */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        text-align: center;
        background: #fff;
        border-radius: 10px;
        margin-top: -3rem;
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    .empty-icon i {
        font-size: 2.5rem;
        color: #94a3b8;
    }

    .empty-state h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 0.5rem 0;
    }

    .empty-state p {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0 0 1.5rem 0;
        max-width: 300px;
    }

    .empty-state .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .actions-cell {
        white-space: nowrap;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        justify-content: center;
    }

    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 6px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.875rem;
    }

    .btn-icon:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .btn-icon:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-icon.btn-primary {
        background: #45a9ea;
        color: white;
    }

    .btn-icon.btn-primary:hover:not(:disabled) {
        background: #3a8bc7;
    }

    .btn-icon.btn-secondary {
        background: #6b7280;
        color: white;
    }

    .btn-icon.btn-secondary:hover:not(:disabled) {
        background: #4b5563;
    }

    .btn-icon.btn-danger {
        background: #ef4444;
        color: white;
    }

    .btn-icon.btn-danger:hover:not(:disabled) {
        background: #dc2626;
    }

    .btn-icon i {
        margin: 0;
    }
</style>

<?php require APPROOT . '/views/inc/tenant_footer.php'; ?>