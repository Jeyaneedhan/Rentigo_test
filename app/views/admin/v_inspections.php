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

    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" action="<?php echo URLROOT; ?>/admin/inspections" class="filter-form">
            <div class="filter-grid">
                <!-- Status Filter -->
                <div class="filter-group">
                    <label for="filter_status">Status</label>
                    <select name="filter_status" id="filter_status" class="filter-select">
                        <option value="">All Statuses</option>
                        <option value="scheduled" <?php echo ($data['filter_status'] ?? '') === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                        <option value="in_progress" <?php echo ($data['filter_status'] ?? '') === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="completed" <?php echo ($data['filter_status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo ($data['filter_status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>

                <!-- Type Filter -->
                <div class="filter-group">
                    <label for="filter_type">Type</label>
                    <select name="filter_type" id="filter_type" class="filter-select">
                        <option value="">All Types</option>
                        <option value="routine_inspection" <?php echo ($data['filter_type'] ?? '') === 'routine_inspection' ? 'selected' : ''; ?>>Routine Inspection</option>
                        <option value="move_in_inspection" <?php echo ($data['filter_type'] ?? '') === 'move_in_inspection' ? 'selected' : ''; ?>>Move-in Inspection</option>
                        <option value="move_out_inspection" <?php echo ($data['filter_type'] ?? '') === 'move_out_inspection' ? 'selected' : ''; ?>>Move-out Inspection</option>
                        <option value="maintenance_inspection" <?php echo ($data['filter_type'] ?? '') === 'maintenance_inspection' ? 'selected' : ''; ?>>Maintenance Inspection</option>
                    </select>
                </div>

                <!-- Date From Filter -->
                <div class="filter-group">
                    <label for="filter_date_from">From Date</label>
                    <input type="date" name="filter_date_from" id="filter_date_from" class="filter-input" value="<?php echo htmlspecialchars($data['filter_date_from'] ?? ''); ?>">
                </div>

                <!-- Date To Filter -->
                <div class="filter-group">
                    <label for="filter_date_to">To Date</label>
                    <input type="date" name="filter_date_to" id="filter_date_to" class="filter-input" value="<?php echo htmlspecialchars($data['filter_date_to'] ?? ''); ?>">
                </div>

                <!-- Filter Buttons -->
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary filter-btn">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="<?php echo URLROOT; ?>/admin/inspections" class="btn btn-secondary clear-btn">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>

            <!-- Active Filters Indicator -->
            <?php
            $activeFilters = [];
            if (!empty($data['filter_status'])) $activeFilters[] = 'Status: ' . ucfirst(str_replace('_', ' ', $data['filter_status']));
            if (!empty($data['filter_type'])) $activeFilters[] = 'Type: ' . ucfirst(str_replace('_', ' ', $data['filter_type']));
            if (!empty($data['filter_date_from'])) $activeFilters[] = 'From: ' . $data['filter_date_from'];
            if (!empty($data['filter_date_to'])) $activeFilters[] = 'To: ' . $data['filter_date_to'];
            
            if (!empty($activeFilters)):
            ?>
            <div class="active-filters">
                <span class="active-filters-label"><i class="fas fa-check-circle"></i> Active Filters:</span>
                <?php foreach ($activeFilters as $filter): ?>
                    <span class="filter-tag"><?php echo htmlspecialchars($filter); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </form>
    </div>

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
                                    <span class="badge badge-info">
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

<style>
/* Filter Section Styles */
.filter-section {
    background: #fff;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.filter-form {
    width: 100%;
}

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
    color: #333;
    font-size: 0.9rem;
}

.filter-select,
.filter-input {
    padding: 0.6rem;
    border: 2px solid #ddd;
    border-radius: 6px;
    font-size: 0.95rem;
    transition: border-color 0.3s;
    background: #fff;
}

.filter-select:focus,
.filter-input:focus {
    outline: none;
    border-color: #45a9ea;
}

.filter-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.filter-btn,
.clear-btn {
    padding: 0.6rem 1.2rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s;
    text-decoration: none;
    white-space: nowrap;
}

.filter-btn {
    background: #45a9ea;
    color: #fff;
}

.filter-btn:hover {
    background: #3a8fc7;
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(69, 169, 234, 0.3);
}

.clear-btn {
    background: #f5f5f5;
    color: #666;
    border: 2px solid #ddd;
}

.clear-btn:hover {
    background: #e0e0e0;
    color: #333;
}

.active-filters {
    margin-top: 1rem;
    padding: 0.8rem;
    background: #e3f2fd;
    border-left: 4px solid #45a9ea;
    border-radius: 4px;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
}

.active-filters-label {
    font-weight: 600;
    color: #1976d2;
    margin-right: 0.5rem;
}

.filter-tag {
    background: #45a9ea;
    color: #fff;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

/* Responsive Filters */
@media (max-width: 768px) {
    .filter-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .filter-btn,
    .clear-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<!-- ADD PAGINATION HERE - Render at bottom -->
<?php echo AutoPaginate::render($data['_pagination']); ?>

<?php require APPROOT . '/views/inc/admin_footer.php'; ?>