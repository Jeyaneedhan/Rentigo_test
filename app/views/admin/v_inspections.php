<?php require APPROOT . '/views/inc/admin_header.php'; ?>

<?php
// ADD PAGINATION
require_once APPROOT . '/../app/helpers/AutoPaginate.php';
AutoPaginate::init($data, 5);
?>

<div class="page-content">
    <div class="page-header">
        <div class="header-content">
            <h2>All Inspections</h2>
            <p>View all inspections scheduled by property managers</p>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <form method="GET" action="<?php echo URLROOT; ?>/admin/inspections">
        <div class="search-filter-content">
            <div class="filter-dropdown-wrapper">
                <select class="form-select" name="filter_status">
                    <option value="">All Statuses</option>
                    <option value="scheduled" <?php echo ($data['filter_status'] ?? '') === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                    <option value="in_progress" <?php echo ($data['filter_status'] ?? '') === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo ($data['filter_status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo ($data['filter_status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="filter-dropdown-wrapper">
                <select class="form-select" name="filter_type">
                    <option value="">All Types</option>
                    <option value="routine_inspection" <?php echo ($data['filter_type'] ?? '') === 'routine_inspection' ? 'selected' : ''; ?>>Routine Inspection</option>
                    <option value="move_in_inspection" <?php echo ($data['filter_type'] ?? '') === 'move_in_inspection' ? 'selected' : ''; ?>>Move-in Inspection</option>
                    <option value="move_out_inspection" <?php echo ($data['filter_type'] ?? '') === 'move_out_inspection' ? 'selected' : ''; ?>>Move-out Inspection</option>
                    <option value="maintenance_inspection" <?php echo ($data['filter_type'] ?? '') === 'maintenance_inspection' ? 'selected' : ''; ?>>Maintenance Inspection</option>
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
            <a href="<?php echo URLROOT; ?>/admin/inspections" class="btn btn-outline">
                <i class="fas fa-times"></i> Clear
            </a>
        </div>
    </form>

    <div class="dashboard-section">
        <div class="section-header">
            <h3>Scheduled Inspections (<?php echo count($data['inspections'] ?? []); ?>)</h3>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Manager</th>
                        <th>Landlord</th>
                        <th>Tenant</th>
                        <th>Type</th>
                        <th>Scheduled Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['inspections'])): ?>
                        <?php foreach ($data['inspections'] as $inspection): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($inspection->property_address ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($inspection->manager_name ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($inspection->landlord_name ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($inspection->tenant_name ?? 'N/A'); ?></td>
                                <td>
                                    <?php
                                    $typeClass = '';
                                    switch ($inspection->type) {
                                        case 'routine':
                                            $typeClass = 'badge-routine';
                                            break;
                                        case 'move_in':
                                            $typeClass = 'badge-movein';
                                            break;
                                        case 'move_out':
                                            $typeClass = 'badge-moveout';
                                            break;
                                        case 'maintenance':
                                            $typeClass = 'badge-maintenance';
                                            break;
                                        case 'annual':
                                            $typeClass = 'badge-annual';
                                            break;
                                        case 'emergency':
                                            $typeClass = 'badge-emergency';
                                            break;
                                        case 'issue':
                                            $typeClass = 'badge-issue';
                                            break;
                                        default:
                                            $typeClass = 'badge-info';
                                    }
                                    ?>
                                    <span class="badge <?php echo $typeClass; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $inspection->type)); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($inspection->scheduled_date)); ?></td>
                                <td><?php echo $inspection->scheduled_time ? date('g:i A', strtotime($inspection->scheduled_time)) : '-'; ?></td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    switch ($inspection->status) {
                                        case 'scheduled':
                                            $statusClass = 'badge-primary';
                                            break;
                                        case 'in_progress':
                                            $statusClass = 'badge-warning';
                                            break;
                                        case 'completed':
                                            $statusClass = 'badge-success';
                                            break;
                                        case 'cancelled':
                                            $statusClass = 'badge-danger';
                                            break;
                                        default:
                                            $statusClass = 'badge-secondary';
                                    }
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <?php echo ucfirst($inspection->status); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($inspection->created_at)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2rem;">
                                <p>No inspections found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ADD PAGINATION HERE - Render at bottom -->
<?php echo AutoPaginate::render($data['_pagination']); ?>

<style>
    /* Badge Styles for Inspections */
    .badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 50px;
        text-transform: capitalize;
    }

    .badge-primary {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    .badge-success {
        background-color: #d1fae5;
        color: #059669;
    }

    .badge-warning {
        background-color: #fef3c7;
        color: #d97706;
    }

    .badge-danger {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .badge-info {
        background-color: #e0e7ff;
        color: #4f46e5;
    }

    .badge-secondary {
        background-color: #f1f5f9;
        color: #64748b;
    }

    /* Type-specific badges */
    .badge-routine {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    .badge-movein {
        background-color: #d1fae5;
        color: #059669;
    }

    .badge-moveout {
        background-color: #ffedd5;
        color: #c2410c;
    }

    .badge-maintenance {
        background-color: #fef3c7;
        color: #d97706;
    }

    .badge-annual {
        background-color: #e0e7ff;
        color: #4f46e5;
    }

    .badge-emergency {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .badge-issue {
        background-color: #fae8ff;
        color: #a21caf;
    }
</style>

<?php require APPROOT . '/views/inc/admin_footer.php'; ?>