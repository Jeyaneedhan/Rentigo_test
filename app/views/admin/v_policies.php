<?php require APPROOT . '/views/inc/admin_header.php'; ?>

<?php
// ADD PAGINATION
require_once APPROOT . '/../app/helpers/AutoPaginate.php';
AutoPaginate::init($data, 5);
?>
<!-- Flash Messages -->
<?php flash('policy_message'); ?>

<div class="page-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h2>Policy Management</h2>
            <p>Create and manage rental policies and documents</p>
        </div>
        <div class="header-actions">
            <a href="<?php echo URLROOT; ?>/policies/add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Policy
            </a>
        </div>
    </div>



    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card" data-stat-type="policy_total">
            <div class="stat-icon">
                <i class="fas fa-file-text"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-policy_total" onclick="toggleStatDropdown('policy_total')">Total Policies</span>
                    <div class="stat-dropdown" id="stat-dropdown-policy_total">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('policy_total', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('policy_total', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('policy_total', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 class="stat-number" id="stat-value-policy_total"><?php echo $data['stats']['total']; ?></h3>
                <span class="stat-change" id="stat-subtitle-policy_total">All documents</span>
            </div>
        </div>

        <div class="stat-card" data-stat-type="policy_active">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-policy_active" onclick="toggleStatDropdown('policy_active')">Active Policies</span>
                    <div class="stat-dropdown" id="stat-dropdown-policy_active">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('policy_active', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('policy_active', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('policy_active', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 class="stat-number" id="stat-value-policy_active"><?php echo $data['stats']['active']; ?></h3>
                <span class="stat-change positive" id="stat-subtitle-policy_active">Currently enforced</span>
            </div>
        </div>

        <div class="stat-card" data-stat-type="policy_draft">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-policy_draft" onclick="toggleStatDropdown('policy_draft')">Draft Policies</span>
                    <div class="stat-dropdown" id="stat-dropdown-policy_draft">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('policy_draft', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('policy_draft', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('policy_draft', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 class="stat-number" id="stat-value-policy_draft"><?php echo $data['stats']['draft']; ?></h3>
                <span class="stat-change" id="stat-subtitle-policy_draft">Under development</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-info">
                <h3 class="stat-number">
                    <?php
                    if ($data['stats']['last_updated']) {
                        echo date('d/m/Y', strtotime($data['stats']['last_updated']));
                    } else {
                        echo 'Never';
                    }
                    ?>
                </h3>
                <p class="stat-label">Last Updated</p>
                <span class="stat-change">Recent policy update</span>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <form method="GET" action="<?php echo URLROOT; ?>/policies" id="filterForm">
        <div class="search-filter-content">
            <div class="search-input-wrapper">
                <input type="text"
                    class="form-input"
                    name="search"
                    placeholder="Search policies..."
                    value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>">
            </div>
            <div class="filter-dropdown-wrapper">
                <select class="form-select" name="status">
                    <option value="" <?php echo empty($data['status_filter']) ? 'selected' : ''; ?>>All Statuses</option>
                    <option value="active" <?php echo (($data['status_filter'] ?? '') == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="draft" <?php echo (($data['status_filter'] ?? '') == 'draft') ? 'selected' : ''; ?>>Draft</option>
                    <option value="inactive" <?php echo (($data['status_filter'] ?? '') == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    <option value="archived" <?php echo (($data['status_filter'] ?? '') == 'archived') ? 'selected' : ''; ?>>Archived</option>
                    <option value="under_review" <?php echo (($data['status_filter'] ?? '') == 'under_review') ? 'selected' : ''; ?>>Under Review</option>
                </select>
            </div>
            <div class="filter-dropdown-wrapper">
                <select class="form-select" name="category">
                    <option value="" <?php echo empty($data['category_filter']) ? 'selected' : ''; ?>>All Categories</option>
                    <?php foreach ($data['categories'] as $key => $value): ?>
                        <option value="<?php echo $key; ?>" <?php echo (($data['category_filter'] ?? '') == $key) ? 'selected' : ''; ?>>
                            <?php echo $value; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">Filter</button>
            <a href="<?php echo URLROOT; ?>/policies" class="btn btn-outline">Clear</a>
        </div>
    </form>

    <!-- Policy Documents -->
    <div class="dashboard-section">
        <div class="section-header">
            <h3>Policy Documents (<?php echo count($data['policies']); ?>)</h3>
        </div>

        <?php if (!empty($data['policies'])): ?>
            <div class="table-container">
                <table class="data-table policies-table">
                    <thead>
                        <tr>
                            <th>Policy</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Version</th>
                            <th>Last Updated</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['policies'] as $policy): ?>
                            <tr data-status="<?php echo $policy->policy_status; ?>" data-policy-id="<?php echo $policy->policy_id; ?>">
                                <td>
                                    <div class="policy-info">
                                        <div class="policy-icon">
                                            <?php
                                            // Choose icon based on category
                                            $icons = [
                                                'rental' => 'fas fa-file-contract',
                                                'security' => 'fas fa-shield-alt',
                                                'maintenance' => 'fas fa-tools',
                                                'financial' => 'fas fa-dollar-sign',
                                                'general' => 'fas fa-file-text'
                                            ];
                                            $icon = $icons[$policy->policy_category] ?? 'fas fa-file-text';
                                            ?>
                                            <i class="<?php echo $icon; ?>"></i>
                                        </div>
                                        <div class="policy-details">
                                            <div class="policy-name"><?php echo htmlspecialchars($policy->policy_name); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    // Category badges with different colors
                                    $categoryClasses = [
                                        'rental' => 'category-badge rental',
                                        'security' => 'category-badge security',
                                        'maintenance' => 'category-badge maintenance',
                                        'financial' => 'category-badge financial',
                                        'general' => 'category-badge general',
                                        'privacy' => 'category-badge privacy',
                                        'terms_of_service' => 'category-badge terms',
                                        'refund' => 'category-badge refund',
                                        'data_protection' => 'category-badge data-protection'
                                    ];
                                    $categoryClass = $categoryClasses[$policy->policy_category] ?? 'category-badge';
                                    // Format display name
                                    $displayCategory = str_replace('_', ' ', ucwords($policy->policy_category, '_'));
                                    ?>
                                    <span class="<?php echo $categoryClass; ?>"><?php echo $displayCategory; ?></span>
                                </td>
                                <td>
                                    <div class="policy-description"><?php echo htmlspecialchars($policy->policy_description); ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($policy->policy_version); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($policy->last_updated)); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $policy->policy_status; ?>">
                                        <?php echo ucfirst($policy->policy_status); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="policy-actions">
                                        <a href="<?php echo URLROOT; ?>/policies/view_policy/<?php echo $policy->policy_id; ?>"
                                            class="action-btn view-btn"
                                            title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/policies/edit/<?php echo $policy->policy_id; ?>"
                                            class="action-btn edit-btn"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($policy->policy_status === 'draft'): ?>
                                            <form method="POST" action="<?php echo URLROOT; ?>/policies/updateStatus/<?php echo $policy->policy_id; ?>" class="action-form" onsubmit="return confirm('Are you sure you want to activate this policy?');">
                                                <input type="hidden" name="status" value="active">
                                                <button type="submit" class="action-btn approve-btn" title="Activate">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        <?php elseif ($policy->policy_status === 'active'): ?>
                                            <form method="POST" action="<?php echo URLROOT; ?>/policies/updateStatus/<?php echo $policy->policy_id; ?>" class="action-form" onsubmit="return confirm('Are you sure you want to deactivate this policy?');">
                                                <input type="hidden" name="status" value="inactive">
                                                <button type="submit" class="action-btn status-btn" title="Deactivate">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" action="<?php echo URLROOT; ?>/policies/updateStatus/<?php echo $policy->policy_id; ?>" class="action-form" onsubmit="return confirm('Are you sure you want to activate this policy?');">
                                                <input type="hidden" name="status" value="active">
                                                <button type="submit" class="action-btn approve-btn" title="Activate">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" action="<?php echo URLROOT; ?>/policies/delete/<?php echo $policy->policy_id; ?>" class="action-form" onsubmit="return confirm('Are you sure you want to delete this policy? This action cannot be undone.');">
                                            <button type="submit" class="action-btn danger-btn" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem;">
                <i class="fas fa-file-text" style="font-size: 3rem; color: #e2e8f0; margin-bottom: 1rem;"></i>
                <h3 style="color: #64748b; margin-bottom: 0.5rem;">No Policies Found</h3>
                <p style="color: #94a3b8; margin-bottom: 2rem;">
                    <?php if (!empty($data['search']) || !empty($data['category_filter']) || !empty($data['status_filter'])): ?>
                        No policies match your current filters. Try adjusting your search criteria.
                    <?php else: ?>
                        Get started by creating your first policy document.
                    <?php endif; ?>
                </p>
                <a href="<?php echo URLROOT; ?>/policies/add" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create First Policy
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ADD PAGINATION HERE - Render at bottom -->
<?php echo AutoPaginate::render($data['_pagination']); ?>

<style>
    .action-form {
        display: inline-block;
        margin: 0;
    }
</style>

<script>
    // Filters submit only via the button.
</script>

<?php require APPROOT . '/views/inc/admin_footer.php'; ?>