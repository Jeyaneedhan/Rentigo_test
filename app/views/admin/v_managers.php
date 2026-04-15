<?php require APPROOT . '/views/inc/admin_header.php'; ?>

<?php
// ADD PAGINATION
require_once APPROOT . '/../app/helpers/AutoPaginate.php';
AutoPaginate::init($data, 5);
?>

<div class="page-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h2>Property Managers</h2>
            <p>Manage property manager registrations and assignments</p>
        </div>
    </div>

    <?php flash('manager_message'); ?>

    <!-- Stats Cards -->
    <?php
    $totalManagers = 0;
    $pendingCount = 0;
    $approvedCount = 0;
    $rejectedCount = 0;

    $statsManagers = $data['allManagers'] ?? ($data['managers'] ?? []);
    if (!empty($statsManagers)) {
        $totalManagers = count($statsManagers);
        foreach ($statsManagers as $manager) {
            if ($manager->approval_status === 'pending') $pendingCount++;
            if ($manager->approval_status === 'approved') $approvedCount++;
            if ($manager->approval_status === 'rejected') $rejectedCount++;
        }
    }
    ?>

    <div class="stats-grid">
        <div class="stat-card" data-stat-type="mgr_total">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-mgr_total" onclick="toggleStatDropdown('mgr_total')">Total Managers</span>
                    <div class="stat-dropdown" id="stat-dropdown-mgr_total">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('mgr_total', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('mgr_total', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('mgr_total', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 class="stat-number" id="stat-value-mgr_total"><?php echo $totalManagers; ?></h3>
                <span class="stat-change" id="stat-subtitle-mgr_total">All Registered</span>
            </div>
        </div>

        <div class="stat-card" data-stat-type="mgr_pending">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-mgr_pending" onclick="toggleStatDropdown('mgr_pending')">Awaiting Review</span>
                    <div class="stat-dropdown" id="stat-dropdown-mgr_pending">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('mgr_pending', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('mgr_pending', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('mgr_pending', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 class="stat-number" id="stat-value-mgr_pending"><?php echo $pendingCount; ?></h3>
                <span class="stat-change" id="stat-subtitle-mgr_pending">Pending Approvals</span>
            </div>
        </div>

        <div class="stat-card" data-stat-type="mgr_approved">
            <div class="stat-icon">
                <i class="fas fa-check"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-mgr_approved" onclick="toggleStatDropdown('mgr_approved')">Currently Approved</span>
                    <div class="stat-dropdown" id="stat-dropdown-mgr_approved">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('mgr_approved', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('mgr_approved', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('mgr_approved', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 class="stat-number" id="stat-value-mgr_approved"><?php echo $approvedCount; ?></h3>
                <span class="stat-change" id="stat-subtitle-mgr_approved">Active Managers</span>
            </div>
        </div>

        <div class="stat-card" data-stat-type="mgr_rejected">
            <div class="stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-mgr_rejected" onclick="toggleStatDropdown('mgr_rejected')">Rejected</span>
                    <div class="stat-dropdown" id="stat-dropdown-mgr_rejected">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('mgr_rejected', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('mgr_rejected', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('mgr_rejected', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 class="stat-number" id="stat-value-mgr_rejected"><?php echo $rejectedCount; ?></h3>
                <span class="stat-change" id="stat-subtitle-mgr_rejected">Declined Applications</span>
            </div>
        </div>
    </div>


    <!-- Search and Filter -->
    <form method="GET" action="<?php echo URLROOT; ?>/admin/managers">
        <div class="search-filter-row">
            <div class="search-container">
                <input type="text"
                    class="search-input"
                    name="search"
                    placeholder="Search managers..."
                    value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>">
            </div>
            <div class="filter-container">
                <select class="filter-select" name="status">
                    <option value="" <?php echo (empty($data['status_filter'])) ? 'selected' : ''; ?>>All Managers</option>
                    <option value="pending" <?php echo (($data['status_filter'] ?? '') === 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo (($data['status_filter'] ?? '') === 'approved') ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo (($data['status_filter'] ?? '') === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-search"></i> Search
            </button>
            <a href="<?php echo URLROOT; ?>/admin/managers" class="btn btn-outline">Reset</a>
        </div>
    </form>

    <!-- Manager Applications Table -->
    <h3 class="table-title">All Property Manager Applications (<?php echo count($data['managers'] ?? []); ?>)</h3>
    <div class="table-container">
        <table class="managers-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Employee ID</th>
                    <th>Join Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="managers-tbody">
                <?php if (!empty($data['managers'])): ?>
                    <?php foreach ($data['managers'] as $manager): ?>
                        <tr id="manager-row-<?php echo $manager->id; ?>" data-status="<?php echo $manager->approval_status; ?>">
                            <td><?php echo htmlspecialchars($manager->name); ?></td>
                            <td><?php echo htmlspecialchars($manager->email); ?></td>
                            <td>
                                <a href="<?php echo URLROOT; ?>/users/viewEmployeeId/<?php echo $manager->id; ?>" target="_blank">
                                    <?php echo htmlspecialchars($manager->employee_id_filename); ?>
                                </a>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($manager->created_at)); ?></td>
                            <td>
                                <?php
                                $statusClass = '';
                                $statusText = ucfirst($manager->approval_status);

                                switch ($manager->approval_status) {
                                    case 'pending':
                                        $statusClass = 'status-pending';
                                        break;
                                    case 'approved':
                                        $statusClass = 'status-approved';
                                        break;
                                    case 'rejected':
                                        $statusClass = 'status-rejected';
                                        break;
                                    default:
                                        $statusClass = 'status-unknown';
                                }
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo $statusText; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($manager->approval_status === 'pending'): ?>
                                    <!-- Pending: Show Approve and Reject buttons -->
                                    <div class="action-buttons-group">
                                        <form method="POST" action="<?php echo URLROOT; ?>/admin/approvePM/<?php echo $manager->id; ?>" class="action-form">
                                            <button type="submit" class="action-btn approve-btn" title="Approve Manager" onclick="return confirm('Are you sure you want to approve this manager?');">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="<?php echo URLROOT; ?>/admin/rejectPM/<?php echo $manager->id; ?>" class="action-form">
                                            <button type="submit" class="action-btn reject-btn" title="Reject Manager" onclick="return confirm('Are you sure you want to reject this property manager application?');">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                <?php elseif ($manager->approval_status === 'approved'): ?>
                                    <!-- Approved: Show Remove button only -->
                                    <form method="POST" action="<?php echo URLROOT; ?>/admin/removePropertyManager/<?php echo $manager->id; ?>" class="action-form">
                                        <button type="submit" class="action-btn reject-btn" title="Remove Manager" onclick="return confirm('Are you sure you want to remove this property manager? This action cannot be undone.');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                <?php elseif ($manager->approval_status === 'rejected'): ?>
                                    <!-- Rejected: Show Remove button only -->
                                    <form method="POST" action="<?php echo URLROOT; ?>/admin/removePropertyManager/<?php echo $manager->id; ?>" class="action-form">
                                        <button type="submit" class="action-btn reject-btn" title="Remove Manager" onclick="return confirm('Are you sure you want to remove this property manager? This action cannot be undone.');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No property managers found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ADD PAGINATION HERE - Render at bottom -->
<?php echo AutoPaginate::renderWithParam($data['_pagination']); ?>

<?php require APPROOT . '/views/inc/admin_footer.php'; ?>

<style>
    /* Table Container */
    .table-container {
        overflow-x: auto;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    /* Table Styles */
    .managers-table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
    }

    .managers-table thead {
        background-color: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }

    .managers-table thead th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #374151;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }

    .managers-table tbody td {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        color: #1f2937;
        vertical-align: middle;
    }

    .managers-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .managers-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Status Badge Styles */
    .status-badge {
        display: inline-block;
        padding: 0.375rem 0.875rem;
        border-radius: 9999px;
        font-size: 0.813rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        white-space: nowrap;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fcd34d;
    }

    .status-approved {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #6ee7b7;
    }

    .status-rejected {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .status-unknown {
        background-color: #e5e7eb;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    /* Action Buttons Group */
    .action-buttons-group {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .action-form {
        display: inline;
        margin: 0;
    }

    /* Action Button Styles */
    .action-btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
    }

    /* Employee ID Link */
    .managers-table tbody td a {
        color: #2563eb;
        text-decoration: none;
        font-weight: 500;
    }

    .managers-table tbody td a:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .table-container {
            overflow-x: scroll;
        }

        .managers-table {
            min-width: 1000px;
        }

        .action-btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.813rem;
        }

        .status-badge {
            padding: 0.25rem 0.625rem;
            font-size: 0.75rem;
        }

        .action-buttons-group {
            flex-direction: column;
            gap: 0.25rem;
        }
    }

    /* Loading Spinner Animation */
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .fa-spinner {
        animation: spin 1s linear infinite;
    }
</style>