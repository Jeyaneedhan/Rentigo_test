<?php require APPROOT . '/views/inc/manager_header.php'; ?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="header-content">
            <h2 class="page-title">
                <i class="fas fa-money-bill-wave"></i> Payment Tracking
            </h2>
            <p class="page-subtitle">Monitor rent payments for your assigned properties</p>
        </div>
    </div>

    <?php flash('payment_message'); ?>

    <!-- Payment Statistics -->
    <div class="stats-container">
        <div class="stat-card stat-success" data-stat-type="payment_total">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-details">
                <span class="stat-number" id="stat-value-payment_total">Rs <?php echo number_format($data['totalIncome'] * 1.10 ?? 0, 0); ?></span>
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-payment_total" onclick="toggleStatDropdown('payment_total')">Total Collected (with fees)</span>
                    <div class="stat-dropdown" id="stat-dropdown-payment_total">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('payment_total', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('payment_total', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('payment_total', 'month', event)">Current Month</div>
                    </div>
                </div>
                <div class="stat-change" id="stat-subtitle-payment_total"></div>
            </div>
        </div>
        <div class="stat-card stat-info" data-stat-type="payment_completed">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-details">
                <span class="stat-number" id="stat-value-payment_completed"><?php echo $data['completedCount'] ?? 0; ?></span>
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-payment_completed" onclick="toggleStatDropdown('payment_completed')">Completed Payments</span>
                    <div class="stat-dropdown" id="stat-dropdown-payment_completed">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('payment_completed', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('payment_completed', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('payment_completed', 'month', event)">Current Month</div>
                    </div>
                </div>
                <div class="stat-change" id="stat-subtitle-payment_completed"></div>
            </div>
        </div>
        <div class="stat-card stat-warning" data-stat-type="payment_pending">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-details">
                <span class="stat-number" id="stat-value-payment_pending"><?php echo $data['pendingCount'] ?? 0; ?></span>
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-payment_pending" onclick="toggleStatDropdown('payment_pending')">Pending Payments</span>
                    <div class="stat-dropdown" id="stat-dropdown-payment_pending">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('payment_pending', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('payment_pending', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('payment_pending', 'month', event)">Current Month</div>
                    </div>
                </div>
                <small id="stat-subtitle-payment_pending">Rs <?php echo number_format($data['pendingAmount'] * 1.10 ?? 0, 0); ?> due</small>
            </div>
        </div>
        <div class="stat-card stat-primary" data-stat-type="payment_properties">
            <div class="stat-icon">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-details">
                <span class="stat-number" id="stat-value-payment_properties"><?php echo $data['propertyCount'] ?? 0; ?></span>
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-payment_properties" onclick="toggleStatDropdown('payment_properties')">Properties Managed</span>
                    <div class="stat-dropdown" id="stat-dropdown-payment_properties">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('payment_properties', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('payment_properties', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('payment_properties', 'month', event)">Current Month</div>
                    </div>
                </div>
                <div class="stat-change" id="stat-subtitle-payment_properties"></div>
            </div>
        </div>
    </div>

    <!-- Payment History Table -->
    <div class="table-card">
        <div class="card-header">
            <h3 class="card-title">Payment History</h3>
            <div class="card-actions">
                <span class="badge badge-primary"><?php echo count($data['payments'] ?? []); ?> Total Payments</span>
            </div>
        </div>

        <div class="card-body">
            <?php if (!empty($data['payments'])): ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Property</th>
                                <th>Tenant</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Transaction ID</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['payments'] as $payment): ?>
                                <?php
                                $statusClass = '';
                                switch ($payment->status) {
                                    case 'completed':
                                        $statusClass = 'success';
                                        break;
                                    case 'pending':
                                        $statusClass = 'warning';
                                        break;
                                    case 'failed':
                                        $statusClass = 'danger';
                                        break;
                                    case 'refunded':
                                        $statusClass = 'info';
                                        break;
                                    default:
                                        $statusClass = 'secondary';
                                }

                                // Check if overdue
                                $isOverdue = ($payment->status === 'pending' && strtotime($payment->due_date) < time());
                                ?>
                                <tr class="<?php echo $isOverdue ? 'overdue-row' : ''; ?>">
                                    <td>
                                        <?php
                                        if ($payment->payment_date) {
                                            echo date('M d, Y', strtotime($payment->payment_date));
                                        } else {
                                            echo '<span class="text-muted">Due: ' . date('M d, Y', strtotime($payment->due_date)) . '</span>';
                                            if ($isOverdue) {
                                                $daysOverdue = floor((time() - strtotime($payment->due_date)) / 86400);
                                                echo '<br><small class="text-danger">' . $daysOverdue . ' days overdue</small>';
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars(substr($payment->property_address ?? 'N/A', 0, 40)); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($payment->tenant_name ?? 'N/A'); ?></td>
                                    <td>
                                        <strong>Rs <?php echo number_format($payment->amount * 1.10, 2); ?></strong>
                                        <br><small class="text-muted">Base: Rs <?php echo number_format($payment->amount, 2); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'Pending'))); ?></td>
                                    <td>
                                        <?php if ($payment->transaction_id): ?>
                                            <code><?php echo htmlspecialchars($payment->transaction_id); ?></code>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $statusClass; ?>">
                                            <?php echo ucfirst($payment->status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($payment->status === 'completed'): ?>
                                            <a href="<?php echo URLROOT; ?>/payments/receipt/<?php echo $payment->id; ?>"
                                                class="btn btn-sm btn-secondary" target="_blank">
                                                <i class="fas fa-receipt"></i> Receipt
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-money-bill-wave"></i>
                    <p>No payment records</p>
                    <span>Payment records will appear here once tenants start making payments for your assigned properties.</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        gap: 1rem;
        border-left: 4px solid #e0e0e0;
    }

    .stat-card.stat-success {
        border-left-color: #10b981;
    }

    .stat-card.stat-info {
        border-left-color: #3b82f6;
    }

    .stat-card.stat-warning {
        border-left-color: #f59e0b;
    }

    .stat-card.stat-primary {
        border-left-color: #45a9ea;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        background: #f8f9fa;
    }

    .stat-card.stat-success .stat-icon {
        background: #d1fae5;
        color: #059669;
    }

    .stat-card.stat-info .stat-icon {
        background: #dbeafe;
        color: #2563eb;
    }

    .stat-card.stat-warning .stat-icon {
        background: #fef3c7;
        color: #d97706;
    }

    .stat-card.stat-primary .stat-icon {
        background: #e0f2fe;
        color: #45a9ea;
    }

    .stat-details {
        flex: 1;
    }

    .stat-number {
        display: block;
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        display: block;
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }

    .stat-details small {
        display: block;
        font-size: 0.75rem;
        color: #9ca3af;
        margin-top: 0.25rem;
    }

    .table-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .card-body {
        padding: 1.5rem;
    }

    .text-muted {
        color: #9ca3af;
    }

    .text-danger {
        color: #ef4444;
        font-weight: 600;
    }

    code {
        background: #f3f4f6;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        color: #374151;
    }

    .overdue-row {
        background: #fef2f2;
        border-left: 3px solid #ef4444;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: #d1d5db;
    }

    .empty-state p {
        font-size: 1.125rem;
        margin-bottom: 0.5rem;
        color: #6b7280;
        font-weight: 600;
    }

    .empty-state span {
        font-size: 0.875rem;
    }

    @media (max-width: 768px) {
        .stats-container {
            grid-template-columns: 1fr;
        }

        .card-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
    }
</style>

<?php require APPROOT . '/views/inc/manager_footer.php'; ?>