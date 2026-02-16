<?php require APPROOT . '/views/inc/landlord_header.php'; ?>

<?php
// ADD PAGINATION
require_once APPROOT . '/../app/helpers/AutoPaginate.php';
AutoPaginate::init($data, 5);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="header-left">
        <h1 class="page-title">Maintenance Requests</h1>
        <p class="page-subtitle">Manage property maintenance and repairs</p>
    </div>
    <div class="header-actions">
        <button>
            <a href="<?php echo URLROOT; ?>/maintenance/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Request
            </a>
        </button>
    </div>
</div>

<?php flash('maintenance_message'); ?>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card warning" data-stat-type="maint_pending">
        <div class="stat-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-header">
                <span class="stat-label" id="stat-label-maint_pending" onclick="toggleStatDropdown('maint_pending')">Pending Requests</span>
                <div class="stat-dropdown" id="stat-dropdown-maint_pending">
                    <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('maint_pending', 'all', event)">All Time</div>
                    <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('maint_pending', 'year', event)">Current Year</div>
                    <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('maint_pending', 'month', event)">Current Month</div>
                </div>
            </div>
            <div class="stat-value" id="stat-value-maint_pending"><?php echo $data['maintenanceStats']->pending ?? 0; ?></div>
            <div class="stat-change" id="stat-subtitle-maint_pending">Awaiting action</div>
        </div>
    </div>
    <div class="stat-card info" data-stat-type="maint_in_progress">
        <div class="stat-icon">
            <i class="fas fa-tools"></i>
        </div>
        <div class="stat-content">
            <div class="stat-header">
                <span class="stat-label" id="stat-label-maint_in_progress" onclick="toggleStatDropdown('maint_in_progress')">In Progress</span>
                <div class="stat-dropdown" id="stat-dropdown-maint_in_progress">
                    <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('maint_in_progress', 'all', event)">All Time</div>
                    <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('maint_in_progress', 'year', event)">Current Year</div>
                    <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('maint_in_progress', 'month', event)">Current Month</div>
                </div>
            </div>
            <div class="stat-value" id="stat-value-maint_in_progress"><?php echo $data['maintenanceStats']->in_progress ?? 0; ?></div>
            <div class="stat-change" id="stat-subtitle-maint_in_progress">Being worked on</div>
        </div>
    </div>
    <div class="stat-card success" data-stat-type="maint_completed">
        <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-header">
                <span class="stat-label" id="stat-label-maint_completed" onclick="toggleStatDropdown('maint_completed')">Completed</span>
                <div class="stat-dropdown" id="stat-dropdown-maint_completed">
                    <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('maint_completed', 'all', event)">All Time</div>
                    <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('maint_completed', 'year', event)">Current Year</div>
                    <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('maint_completed', 'month', event)">Current Month</div>
                </div>
            </div>
            <div class="stat-value" id="stat-value-maint_completed"><?php echo $data['maintenanceStats']->completed ?? 0; ?></div>
            <div class="stat-change" id="stat-subtitle-maint_completed">Total completed</div>
        </div>
    </div>
    <div class="stat-card" data-stat-type="maint_total_cost">
        <div class="stat-icon">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
            <div class="stat-header">
                <span class="stat-label" id="stat-label-maint_total_cost" onclick="toggleStatDropdown('maint_total_cost')">Total Cost</span>
                <div class="stat-dropdown" id="stat-dropdown-maint_total_cost">
                    <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('maint_total_cost', 'all', event)">All Time</div>
                    <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('maint_total_cost', 'year', event)">Current Year</div>
                    <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('maint_total_cost', 'month', event)">Current Month</div>
                </div>
            </div>
            <div class="stat-value" id="stat-value-maint_total_cost">LKR <?php echo number_format($data['maintenanceStats']->total_cost ?? 0); ?></div>
            <div class="stat-change" id="stat-subtitle-maint_total_cost">Maintenance expenses</div>
        </div>
    </div>
</div>

<!-- Search and Filter Section -->
<form method="GET" action="<?php echo URLROOT; ?>/maintenance/index">
    <div class="search-filter-content">
        <div class="filter-dropdown-wrapper">
            <select name="filter_status" class="form-select">
                <option value="" <?php echo empty($data['filter_status']) ? 'selected' : ''; ?>>All Statuses</option>
                <option value="pending" <?php echo $data['filter_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="scheduled" <?php echo $data['filter_status'] === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                <option value="in_progress" <?php echo $data['filter_status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                <option value="completed" <?php echo $data['filter_status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="cancelled" <?php echo $data['filter_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
        </div>
        <div class="filter-dropdown-wrapper">
            <select name="filter_priority" class="form-select">
                <option value="" <?php echo empty($data['filter_priority']) ? 'selected' : ''; ?>>All Priorities</option>
                <option value="low" <?php echo $data['filter_priority'] === 'low' ? 'selected' : ''; ?>>Low</option>
                <option value="medium" <?php echo $data['filter_priority'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                <option value="high" <?php echo $data['filter_priority'] === 'high' ? 'selected' : ''; ?>>High</option>
                <option value="emergency" <?php echo $data['filter_priority'] === 'emergency' ? 'selected' : ''; ?>>Emergency</option>
            </select>
        </div>
        <div class="filter-dropdown-wrapper">
            <input type="date" name="filter_date_from" class="form-input" placeholder="From Date" value="<?php echo htmlspecialchars($data['filter_date_from'] ?? ''); ?>">
        </div>
        <div class="filter-dropdown-wrapper">
            <input type="date" name="filter_date_to" class="form-input" placeholder="To Date" value="<?php echo htmlspecialchars($data['filter_date_to'] ?? ''); ?>">
        </div>
        <button type="submit" class="btn btn-secondary">
            <i class="fas fa-filter"></i> Filter
        </button>
        <a href="<?php echo URLROOT; ?>/maintenance/index" class="btn btn-outline">
            <i class="fas fa-times"></i> Clear
        </a>
    </div>
</form>

<!-- Maintenance Requests -->
<?php if (!empty($data['maintenanceRequests'])): ?>
    <?php foreach ($data['maintenanceRequests'] as $request): ?>
        <?php
        // Determine priority class
        $priorityClass = '';
        switch ($request->priority) {
            case 'emergency':
                $priorityClass = 'urgent';
                break;
            case 'high':
                $priorityClass = 'high';
                break;
            default:
                $priorityClass = '';
        }

        // Calculate time elapsed since creation (for 5-minute edit window)
        $createdTime = strtotime($request->created_at);
        $currentTime = time();
        $minutesElapsed = ($currentTime - $createdTime) / 60;
        $canEdit = ($minutesElapsed <= 5);
        ?>
        <div class="request-card <?php echo $priorityClass; ?>" data-status="<?php echo $request->status; ?>">
            <div class="request-header">
                <div>
                    <h3 style="margin: 0; color: var(--text-primary);">
                        <?php echo htmlspecialchars($request->title); ?>
                    </h3>
                    <p style="margin: 0.5rem 0 0 0; color: var(--text-secondary);">
                        <?php echo htmlspecialchars($request->property_address); ?> •
                        Submitted <?php echo timeAgo($request->created_at); ?>
                    </p>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span class="priority-badge priority-<?php echo $request->priority; ?>">
                        <?php echo strtoupper($request->priority); ?>
                    </span>
                    <span class="badge badge-<?php echo getStatusBadgeClass($request->status); ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $request->status)); ?>
                    </span>
                </div>
            </div>
            <div class="request-body">
                <p><strong>Category:</strong> <?php echo ucfirst($request->category); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars(substr($request->description, 0, 150)); ?><?php echo strlen($request->description) > 150 ? '...' : ''; ?></p>

                <?php if ($request->provider_name): ?>
                    <p><strong>Service Provider:</strong> <?php echo htmlspecialchars($request->provider_name); ?></p>
                <?php endif; ?>

                <?php if ($request->scheduled_date): ?>
                    <p><strong>Scheduled:</strong> <?php echo date('M d, Y', strtotime($request->scheduled_date)); ?></p>
                <?php endif; ?>

                <!-- Quotation Status -->
                <?php if (isset($request->quotation_status)): ?>
                    <div class="quotation-status">
                        <?php if ($request->quotation_status === 'pending'): ?>
                            <span class="badge badge-warning">
                                <i class="fas fa-file-invoice"></i> Quotation Pending Review
                            </span>
                        <?php elseif ($request->quotation_status === 'approved'): ?>
                            <?php if (isset($request->payment_status) && $request->payment_status === 'completed'): ?>
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Payment Completed
                                </span>
                            <?php else: ?>
                                <span class="badge badge-info">
                                    <i class="fas fa-credit-card"></i> Awaiting Payment
                                </span>
                            <?php endif; ?>
                        <?php elseif ($request->quotation_status === 'rejected'): ?>
                            <span class="badge badge-danger">
                                <i class="fas fa-times-circle"></i> Quotation Rejected
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div style="margin-top: 1rem;">
                    <a href="<?php echo URLROOT; ?>/maintenance/details/<?php echo $request->id; ?>"
                        class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i> View Details
                    </a>

                    <?php if ($request->status === 'pending'): ?>
                        <?php if ($canEdit): ?>
                            <button class="btn btn-outline btn-sm"
                                onclick="editRequest(<?php echo $request->id; ?>, '<?php echo $request->created_at; ?>')"
                                data-created="<?php echo $request->created_at; ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        <?php else: ?>
                            <button class="btn btn-outline btn-sm btn-disabled"
                                title="Edit period expired (5 minutes)"
                                disabled>
                                <i class="fas fa-lock"></i> Edit Locked
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($request->status !== 'completed' && $request->status !== 'cancelled'): ?>
                        <?php if ($canEdit): ?>
                            <button class="btn btn-danger btn-sm"
                                onclick="cancelRequest(<?php echo $request->id; ?>, '<?php echo $request->created_at; ?>')"
                                data-created="<?php echo $request->created_at; ?>">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-sm btn-disabled"
                                title="Cancel period expired (5 minutes)"
                                disabled>
                                <i class="fas fa-ban"></i> Cannot Cancel
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="content-card">
        <div class="card-body text-center">
            <i class="fas fa-tools" style="font-size: 48px; color: #ccc; margin-bottom: 1rem;"></i>
            <p style="color: #666; margin-bottom: 1rem;">No maintenance requests found</p>
            <a href="<?php echo URLROOT; ?>/maintenance/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Request
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Cancel Modal -->
<div id="cancelModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Cancel Maintenance Request</h3>
            <span class="close" onclick="closeCancelModal()">&times;</span>
        </div>
        <form id="cancelForm" method="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label for="cancellation_reason">Reason for Cancellation <span class="required">*</span></label>
                    <textarea name="cancellation_reason" id="cancellation_reason" class="form-control" rows="4" required
                        placeholder="Please explain why you are cancelling this request..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCancelModal()">Back</button>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-times"></i> Cancel Request
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ADD PAGINATION HERE - Render at bottom -->
<?php echo AutoPaginate::render($data['_pagination']); ?>

<style>
    /* Filter Styles */
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-group label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text-primary, #333);
        font-size: 0.9rem;
    }

    .filter-actions {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .filter-actions .btn {
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            flex-direction: column;
            width: 100%;
        }

        .filter-actions .btn {
            width: 100%;
        }
    }

    /* Existing Styles */
    .quotation-status {
        margin: 10px 0;
    }

    .quotation-status .badge {
        padding: 6px 12px;
        font-size: 13px;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    /* Disabled Button Styles */
    .btn-disabled {
        cursor: not-allowed !important;
        opacity: 0.6;
        pointer-events: none;
    }

    .btn-disabled:hover {
        transform: none !important;
        box-shadow: none !important;
    }

    .required {
        color: #ef4444;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 10% auto;
        border-radius: 8px;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        color: #333;
    }

    .close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        line-height: 1;
    }

    .close:hover,
    .close:focus {
        color: #000;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        padding: 20px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
</style>

<script>
    // Helper function to check if request can be edited (within 5 minutes)
    function canEditRequest(createdAt) {
        const createdTime = new Date(createdAt).getTime();
        const currentTime = new Date().getTime();
        const minutesElapsed = (currentTime - createdTime) / 1000 / 60;
        return minutesElapsed <= 5;
    }

    // Helper function to get remaining edit time
    function getRemainingEditTime(createdAt) {
        const createdTime = new Date(createdAt).getTime();
        const currentTime = new Date().getTime();
        const secondsElapsed = (currentTime - createdTime) / 1000;
        const remainingSeconds = Math.max(0, 300 - secondsElapsed); // 300 seconds = 5 minutes

        const minutes = Math.floor(remainingSeconds / 60);
        const seconds = Math.floor(remainingSeconds % 60);

        return {
            total: remainingSeconds,
            minutes: minutes,
            seconds: seconds,
            display: `${minutes}:${seconds.toString().padStart(2, '0')}`
        };
    }

    function editRequest(requestId, createdAt) {
        if (!canEditRequest(createdAt)) {
            const timeRemaining = getRemainingEditTime(createdAt);
            alert('Edit period expired. You can only edit within 5 minutes of creation.');
            return;
        }

        const timeRemaining = getRemainingEditTime(createdAt);
        if (timeRemaining.total < 60) {
            if (!confirm(`Warning: Only ${Math.floor(timeRemaining.total)} seconds remaining to edit. Continue?`)) {
                return;
            }
        }

        window.location.href = '<?php echo URLROOT; ?>/maintenance/edit/' + requestId;
    }

    function cancelRequest(requestId, createdAt) {
        if (!canEditRequest(createdAt)) {
            alert('Cancel period expired. You can only cancel within 5 minutes of creation.');
            return;
        }

        const modal = document.getElementById('cancelModal');
        const form = document.getElementById('cancelForm');
        form.action = '<?php echo URLROOT; ?>/maintenance/cancel/' + requestId;
        modal.style.display = 'block';
    }

    function closeCancelModal() {
        const modal = document.getElementById('cancelModal');
        modal.style.display = 'none';
        document.getElementById('cancellation_reason').value = '';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('cancelModal');
        if (event.target == modal) {
            closeCancelModal();
        }
    }
</script>

<?php
// Helper functions
function getStatusBadgeClass($status)
{
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'scheduled':
        case 'in_progress':
            return 'info';
        case 'completed':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}

function timeAgo($datetime)
{
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M d, Y', $time);
    }
}
?>

<?php require APPROOT . '/views/inc/landlord_footer.php'; ?>