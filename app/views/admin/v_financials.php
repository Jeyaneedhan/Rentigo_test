<?php require APPROOT . '/views/inc/admin_header.php'; ?>

<?php
// ADD PAGINATION
require_once APPROOT . '/../app/helpers/AutoPaginate.php';
AutoPaginate::init($data, 10);
?>

<div class="page-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h2>Financial Management</h2>
            <p>Monitor and manage all financial transactions</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card" data-stat-type="fin_revenue">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-fin_revenue" onclick="toggleStatDropdown('fin_revenue')">Platform Revenue</span>
                    <div class="stat-dropdown" id="stat-dropdown-fin_revenue">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('fin_revenue', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('fin_revenue', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('fin_revenue', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 class="stat-number" id="stat-value-fin_revenue">LKR <?php echo number_format($data['totalRevenue'] ?? 0, 0); ?></h3>
                <span class="stat-change" id="stat-subtitle-fin_revenue">All time</span>
            </div>
        </div>

        <div class="stat-card" data-stat-type="fin_collected">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-fin_collected" onclick="toggleStatDropdown('fin_collected')">Collected Fees</span>
                    <div class="stat-dropdown" id="stat-dropdown-fin_collected">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('fin_collected', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('fin_collected', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('fin_collected', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 class="stat-number" id="stat-value-fin_collected">LKR <?php echo number_format($data['collected'] ?? 0, 0); ?></h3>
                <span class="stat-change positive" id="stat-subtitle-fin_collected">All time</span>
            </div>
        </div>

        <div class="stat-card" data-stat-type="fin_pending">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-fin_pending" onclick="toggleStatDropdown('fin_pending')">Pending Fees</span>
                    <div class="stat-dropdown" id="stat-dropdown-fin_pending">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('fin_pending', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('fin_pending', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('fin_pending', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 class="stat-number" id="stat-value-fin_pending">LKR <?php echo number_format($data['pending'] ?? 0, 0); ?></h3>
                <span class="stat-change" id="stat-subtitle-fin_pending"><?php echo $data['pendingCount'] ?? 0; ?> pending (All time)</span>
            </div>
        </div>

        <div class="stat-card" data-stat-type="fin_overdue">
            <div class="stat-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-fin_overdue" onclick="toggleStatDropdown('fin_overdue')">Overdue Fees</span>
                    <div class="stat-dropdown" id="stat-dropdown-fin_overdue">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('fin_overdue', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('fin_overdue', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('fin_overdue', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 class="stat-number" id="stat-value-fin_overdue">LKR <?php echo number_format($data['overdue'] ?? 0, 0); ?></h3>
                <span class="stat-change negative" id="stat-subtitle-fin_overdue"><?php echo $data['overdueCount'] ?? 0; ?> overdue (All time)</span>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <form method="GET" action="<?php echo URLROOT; ?>/admin/financials">
        <div class="search-filter-content">
            <div class="filter-dropdown-wrapper">
                <select class="form-select" name="filter_type">
                    <option value="">All Types</option>
                    <option value="rental" <?php echo ($data['filter_type'] ?? '') === 'rental' ? 'selected' : ''; ?>>Rental</option>
                    <option value="maintenance" <?php echo ($data['filter_type'] ?? '') === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                </select>
            </div>
            <div class="filter-dropdown-wrapper">
                <select class="form-select" name="filter_status">
                    <option value="">All Statuses</option>
                    <option value="completed" <?php echo ($data['filter_status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="pending" <?php echo ($data['filter_status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="overdue" <?php echo ($data['filter_status'] ?? '') === 'overdue' ? 'selected' : ''; ?>>Overdue</option>
                    <option value="failed" <?php echo ($data['filter_status'] ?? '') === 'failed' ? 'selected' : ''; ?>>Failed</option>
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
            <a href="<?php echo URLROOT; ?>/admin/financials" class="btn btn-outline">
                <i class="fas fa-times"></i> Clear
            </a>
        </div>
    </form>

    <!-- Recent Transactions -->
    <div class="dashboard-section">
        <div class="section-header">
            <h3>Recent Transactions</h3>
        </div>

        <div class="table-container">
            <table class="data-table transactions-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Property</th>
                        <th>Total Payment</th>
                        <th>Platform Fee (10%)</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['recentTransactions'])): ?>
                        <?php foreach ($data['recentTransactions'] as $transaction): ?>
                            <?php
                            $isMaintenance = isset($transaction->payment_type) && $transaction->payment_type === 'maintenance';
                            $displayDate = $transaction->payment_date ?? $transaction->due_date ?? $transaction->created_at;
                            ?>
                            <tr data-type="income" data-status="<?php echo htmlspecialchars($transaction->status); ?>" data-date="<?php echo date('Y-m-d', strtotime($displayDate)); ?>">
                                <td>
                                    <div class="transaction-type">
                                        <div class="type-icon <?php echo $isMaintenance ? 'maintenance' : 'rental'; ?>">
                                            <i class="fas <?php echo $isMaintenance ? 'fa-tools' : 'fa-home'; ?>"></i>
                                        </div>
                                        <span class="type-label"><?php echo $isMaintenance ? 'Maintenance' : 'Rental'; ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="transaction-description">
                                        <div class="description-title">
                                            <?php
                                            if ($isMaintenance) {
                                                echo htmlspecialchars($transaction->maintenance_title ?? 'Maintenance payment');
                                            } else {
                                                echo htmlspecialchars($transaction->payment_method ?? 'Rental payment');
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="property-info">
                                        <div class="property-name">
                                            <?php echo htmlspecialchars($transaction->property_address ?? 'N/A'); ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="amount-display">
                                        <?php if ($isMaintenance): ?>
                                            LKR <?php echo number_format($transaction->amount, 0); ?>
                                        <?php else: ?>
                                            LKR <?php echo number_format($transaction->amount * 1.10, 0); ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="amount-display income">
                                        <?php if ($isMaintenance): ?>
                                            <span>-</span>
                                        <?php else: ?>
                                            <strong>LKR <?php echo number_format($transaction->amount * 0.10, 0); ?></strong>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?php echo date('m/d/Y', strtotime($displayDate)); ?></td>
                                <td>
                                    <span class="status-badge <?php
                                                                echo $transaction->status === 'completed' ? 'approved' : ($transaction->status === 'pending' ? 'pending' : 'rejected');
                                                                ?>">
                                        <?php echo ucfirst($transaction->status); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="transaction-actions">
                                        <a class="action-btn view-btn" href="<?php echo URLROOT; ?>/admin/view_financial/<?php echo $isMaintenance ? 'MAIN' : 'TXN'; ?><?php echo $transaction->id; ?>" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No transactions found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ADD PAGINATION HERE - Render at bottom -->
<?php echo AutoPaginate::render($data['_pagination']); ?>

<?php require APPROOT . '/views/inc/admin_footer.php'; ?>