<?php require APPROOT . '/views/inc/manager_header.php'; ?>

<?php
// ADD PAGINATION
require_once APPROOT . '/../app/helpers/AutoPaginate.php';
AutoPaginate::init($data, 5);
?>

<div class="page-header">
    <div class="header-left">
        <h1 class="page-title">Lease Agreements</h1>
        <p class="page-subtitle">Manage lease agreements for your assigned properties</p>
    </div>
</div>

<?php flash('lease_message'); ?>

<!-- Lease Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-content">
            <h3 class="stat-label">Draft</h3>
            <div class="stat-value"><?php echo $data['draftCount'] ?? 0; ?></div>
            <div class="stat-change">Pending creation</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h3 class="stat-label">Active</h3>
            <div class="stat-value"><?php echo $data['activeCount'] ?? 0; ?></div>
            <div class="stat-change">Currently active</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-archive"></i>
        </div>
        <div class="stat-content">
            <h3 class="stat-label">Completed</h3>
            <div class="stat-value"><?php echo $data['completedCount'] ?? 0; ?></div>
            <div class="stat-change">Ended leases</div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-container">
    <form method="GET" action="<?php echo URLROOT; ?>/manager/leases" class="filter-form">
        <div class="filter-row">
            <div class="filter-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="all" <?php echo ($data['currentStatusFilter'] ?? 'all') === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                    <option value="draft" <?php echo ($data['currentStatusFilter'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="pending_signatures" <?php echo ($data['currentStatusFilter'] ?? '') === 'pending_signatures' ? 'selected' : ''; ?>>Pending</option>
                    <option value="active" <?php echo ($data['currentStatusFilter'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="completed" <?php echo ($data['currentStatusFilter'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="terminated" <?php echo ($data['currentStatusFilter'] ?? '') === 'terminated' ? 'selected' : ''; ?>>Terminated</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="property_id">Property</label>
                <select name="property_id" id="property_id" class="form-control">
                    <option value="all" <?php echo ($data['currentPropertyFilter'] ?? 'all') === 'all' ? 'selected' : ''; ?>>All Properties</option>
                    <?php if (!empty($data['assignedProperties'])): ?>
                        <?php foreach ($data['assignedProperties'] as $property): ?>
                            <option value="<?php echo $property->id; ?>" <?php echo ($data['currentPropertyFilter'] ?? '') == $property->id ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($property->address); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="date_from">From Date</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo htmlspecialchars($data['currentDateFromFilter'] ?? ''); ?>">
            </div>

            <div class="filter-group">
                <label for="date_to">To Date</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo htmlspecialchars($data['currentDateToFilter'] ?? ''); ?>">
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
                <a href="<?php echo URLROOT; ?>/manager/leases" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Leases Table -->
<div class="table-container-wrapper">
    <?php if (!empty($data['allLeases'])): ?>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Tenant</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Monthly Rent</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['allLeases'] as $lease): ?>
                        <tr class="<?php echo $lease->status === 'active' ? 'active-row' : ''; ?>">
                            <td>
                                <strong><?php echo htmlspecialchars($lease->address ?? 'N/A'); ?></strong><br>
                                <small><?php echo ucfirst($lease->property_type ?? ''); ?></small>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($lease->tenant_name ?? 'N/A'); ?></strong><br>
                                <small><?php echo htmlspecialchars($lease->tenant_email ?? ''); ?></small>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($lease->start_date)); ?></td>
                            <td><?php echo date('M d, Y', strtotime($lease->end_date)); ?></td>
                            <td>Rs <?php echo number_format($lease->monthly_rent * 1.10); ?></td>
                            <td><?php echo ($lease->lease_duration_months ?? 0); ?> months</td>
                            <td>
                                <?php
                                $statusClass = '';
                                $statusText = ucfirst(str_replace('_', ' ', $lease->status));
                                switch ($lease->status) {
                                    case 'draft':
                                        $statusClass = 'badge-draft';
                                        break;
                                    case 'active':
                                        $statusClass = 'badge-active';
                                        break;
                                    case 'completed':
                                        $statusClass = 'badge-completed';
                                        break;
                                    case 'pending_signatures':
                                        $statusClass = 'badge-warning';
                                        break;
                                    case 'terminated':
                                        $statusClass = 'badge-danger';
                                        break;
                                    default:
                                        $statusClass = 'badge-secondary';
                                }
                                ?>
                                <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($lease->created_at)); ?></td>
                            <td class="actions">
                                <a href="<?php echo URLROOT; ?>/leaseagreements/details/<?php echo $lease->id; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No lease agreements found<?php echo (isset($data['currentStatusFilter']) && $data['currentStatusFilter'] !== 'all') || (isset($data['currentPropertyFilter']) && $data['currentPropertyFilter'] !== 'all') ? ' matching your filters' : ''; ?></p>
        </div>
    <?php endif; ?>
</div>

<!-- ADD PAGINATION HERE - Render at bottom -->
<?php echo AutoPaginate::render($data['_pagination']); ?>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        background: #45a9ea;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        flex-shrink: 0;
    }

    .stat-content {
        flex: 1;
    }

    .stat-label {
        font-size: 14px;
        color: #666;
        margin-bottom: 5px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin-bottom: 2px;
    }

    .stat-change {
        font-size: 12px;
        color: #999;
    }

    .filter-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
        margin-bottom: 30px;
    }

    .filter-form {
        width: 100%;
    }

    .filter-row {
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
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .filter-actions {
        display: flex;
        gap: 0.75rem;
        align-items: flex-end;
    }

    .table-container-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .table-container {
        overflow-x: auto;
        padding: 1.5rem;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead {
        background: #f9fafb;
    }

    .data-table th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        color: #374151;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .data-table td {
        padding: 1rem;
        border-top: 1px solid #e5e7eb;
    }

    .data-table tbody tr:hover {
        background: #f9fafb;
    }

    .data-table tbody tr.active-row {
        background: #f0fdf4;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .badge-draft {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-active {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-completed {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-secondary {
        background: #e5e7eb;
        color: #374151;
    }

    .actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6b7280;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    .btn {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-primary {
        background: #45a9ea;
        color: white;
    }

    .btn-primary:hover {
        background: #3a8bc7;
    }

    .btn-secondary {
        background: #6b7280;
        color: white;
    }

    .btn-secondary:hover {
        background: #4b5563;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8125rem;
    }

    .form-control {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 1rem;
    }

    .form-control:focus {
        outline: none;
        border-color: #45a9ea;
        box-shadow: 0 0 0 3px rgba(69, 169, 234, 0.1);
    }

    @media (max-width: 768px) {
        .filter-row {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            width: 100%;
        }

        .filter-actions .btn {
            flex: 1;
        }
    }
</style>

<?php require APPROOT . '/views/inc/manager_footer.php'; ?>