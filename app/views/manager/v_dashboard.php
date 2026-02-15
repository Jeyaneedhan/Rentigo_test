<?php require APPROOT . '/views/inc/manager_header.php'; ?>

<div class="dashboard-content">
    <div class="page-header">
        <div class="header-left">
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Welcome back! Here's what's happening with your properties.</p>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="stats-grid">
        <div class="stat-card" data-stat-type="mgr_properties">
            <div class="stat-icon">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-mgr_properties" onclick="toggleStatDropdown('mgr_properties')">Total Properties</span>
                    <div class="stat-dropdown" id="stat-dropdown-mgr_properties">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('mgr_properties', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('mgr_properties', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('mgr_properties', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 id="stat-value-mgr_properties"><?php echo $data['totalProperties'] ?? 0; ?></h3>
                <div class="stat-change" id="stat-subtitle-mgr_properties">Assigned to you</div>
            </div>
        </div>

        <div class="stat-card" data-stat-type="mgr_units">
            <div class="stat-icon">
                <i class="fas fa-home"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-mgr_units" onclick="toggleStatDropdown('mgr_units')">Occupied Properties</span>
                    <div class="stat-dropdown" id="stat-dropdown-mgr_units">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('mgr_units', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('mgr_units', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('mgr_units', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 id="stat-value-mgr_units"><?php echo $data['totalUnits'] ?? 0; ?></h3>
                <div class="stat-change" id="stat-subtitle-mgr_units"><?php echo $data['occupiedUnits'] ?? 0; ?> occupied</div>
            </div>
        </div>

        <div class="stat-card" data-stat-type="mgr_income">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-mgr_income" onclick="toggleStatDropdown('mgr_income')">Platform Income</span>
                    <div class="stat-dropdown" id="stat-dropdown-mgr_income">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('mgr_income', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('mgr_income', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('mgr_income', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 id="stat-value-mgr_income">LKR <?php echo number_format($data['totalIncome'] ?? 0, 0); ?></h3>
                <div class="stat-change" id="stat-subtitle-mgr_income">10% rent fees + maintenance</div>
            </div>
        </div>

        <div class="stat-card" data-stat-type="mgr_expenses">
            <div class="stat-icon">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-mgr_expenses" onclick="toggleStatDropdown('mgr_expenses')">Total Expenses</span>
                    <div class="stat-dropdown" id="stat-dropdown-mgr_expenses">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('mgr_expenses', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('mgr_expenses', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('mgr_expenses', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 id="stat-value-mgr_expenses">LKR <?php echo number_format($data['totalExpenses'] ?? 0, 0); ?></h3>
                <div class="stat-change" id="stat-subtitle-mgr_expenses">Maintenance costs</div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-grid">
        <!-- Payment History -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2>Recent Payments</h2>
                <a href="<?php echo URLROOT; ?>/manager/allPayments" class="btn btn-sm btn-secondary">View All</a>
            </div>
        </div>

        <!-- Maintenance Status -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2>Maintenance Status</h2>
                <a href="<?php echo URLROOT; ?>/manager/maintenance" class="btn btn-sm btn-secondary">View All</a>
            </div>
        </div>
    </div>

    <!-- Unpaid Tenants Section -->
    <div class="dashboard-section" style="margin-top: 0.5rem;">
        <div class="section-header">
            <h2>Unpaid Tenants (Overdue)</h2>
        </div>

        <?php if (empty($data['unpaidTenants'])): ?>
            <div class="empty-state">
                <p class="text-muted">No tenants with overdue payments.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tenant</th>
                            <th>Property</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['unpaidTenants'] as $payment): ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-details">
                                            <span class="user-name"><?php echo $payment->tenant_name; ?></span>
                                            <span class="user-email"><?php echo $payment->tenant_email; ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $payment->property_address; ?></td>
                                <td>
                                    <span class="badge badge-danger">
                                        <?php echo date('M d, Y', strtotime($payment->due_date)); ?>
                                    </span>
                                </td>
                                <td><strong>LKR <?php echo number_format($payment->amount, 2); ?></strong></td>
                                <td>
                                    <a href="<?php echo URLROOT; ?>/manager/tenants?property_id=<?php echo $payment->property_id; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-section" style="margin-top: 1.5rem;">
        <div class="section-header">
            <h2>Quick Actions</h2>
        </div>
        <div style="padding: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="<?php echo URLROOT; ?>/manager/maintenance" class="btn btn-primary">
                <i class="fas fa-tools"></i>
                View Maintenance
            </a>
            <a href="<?php echo URLROOT; ?>/manager/inspections" class="btn btn-primary">
                <i class="fas fa-clipboard-check"></i>
                Schedule Inspection
            </a>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/manager_footer.php'; ?>