<?php require APPROOT . '/views/inc/landlord_header.php'; ?>

<?php
// ADD PAGINATION
require_once APPROOT . '/../app/helpers/AutoPaginate.php';
AutoPaginate::init($data, 5);
?>

<div class="page-header">
    <div class="header-left">
        <h1 class="page-title">Property Bookings</h1>
        <p class="page-subtitle">Manage booking requests for your properties</p>
    </div>
</div>

<?php flash('booking_message'); ?>

<!-- Booking Stats -->
<div class="stats-grid">
    <div class="stat-card" data-stat-type="booking_total">
        <div class="stat-icon">
            <i class="fas fa-calendar"></i>
        </div>
        <div class="stat-content">
            <div class="stat-header">
                <span class="stat-label" id="stat-label-booking_total" onclick="toggleStatDropdown('booking_total')">Total Bookings</span>
                <div class="stat-dropdown" id="stat-dropdown-booking_total">
                    <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('booking_total', 'all', event)">All Time</div>
                    <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('booking_total', 'year', event)">Current Year</div>
                    <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('booking_total', 'month', event)">Current Month</div>
                </div>
            </div>
            <div class="stat-value" id="stat-value-booking_total"><?php echo $data['bookingStats']->total ?? 0; ?></div>
            <div class="stat-change" id="stat-subtitle-booking_total">All time</div>
        </div>
    </div>
    <div class="stat-card warning" data-stat-type="booking_pending">
        <div class="stat-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-header">
                <span class="stat-label" id="stat-label-booking_pending" onclick="toggleStatDropdown('booking_pending')">Pending</span>
                <div class="stat-dropdown" id="stat-dropdown-booking_pending">
                    <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('booking_pending', 'all', event)">All Time</div>
                    <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('booking_pending', 'year', event)">Current Year</div>
                    <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('booking_pending', 'month', event)">Current Month</div>
                </div>
            </div>
            <div class="stat-value" id="stat-value-booking_pending"><?php echo $data['bookingStats']->pending ?? 0; ?></div>
            <div class="stat-change" id="stat-subtitle-booking_pending">Awaiting response</div>
        </div>
    </div>
    <div class="stat-card success" data-stat-type="booking_approved">
        <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-header">
                <span class="stat-label" id="stat-label-booking_approved" onclick="toggleStatDropdown('booking_approved')">Approved</span>
                <div class="stat-dropdown" id="stat-dropdown-booking_approved">
                    <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('booking_approved', 'all', event)">All Time</div>
                    <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('booking_approved', 'year', event)">Current Year</div>
                    <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('booking_approved', 'month', event)">Current Month</div>
                </div>
            </div>
            <div class="stat-value" id="stat-value-booking_approved"><?php echo $data['bookingStats']->approved ?? 0; ?></div>
            <div class="stat-change" id="stat-subtitle-booking_approved">Accepted bookings</div>
        </div>
    </div>
    <div class="stat-card" data-stat-type="booking_active">
        <div class="stat-icon">
            <i class="fas fa-home"></i>
        </div>
        <div class="stat-content">
            <div class="stat-header">
                <span class="stat-label" id="stat-label-booking_active" onclick="toggleStatDropdown('booking_active')">Active</span>
                <div class="stat-dropdown" id="stat-dropdown-booking_active">
                    <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('booking_active', 'all', event)">All Time</div>
                    <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('booking_active', 'year', event)">Current Year</div>
                    <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('booking_active', 'month', event)">Current Month</div>
                </div>
            </div>
            <div class="stat-value" id="stat-value-booking_active"><?php echo $data['bookingStats']->active ?? 0; ?></div>
            <div class="stat-change" id="stat-subtitle-booking_active">Currently occupied</div>
        </div>
    </div>
</div>

<!-- Search and Filter Section -->
<form method="GET" action="<?php echo URLROOT; ?>/landlord/bookings">
    <div class="search-filter-content">
        <div class="filter-dropdown-wrapper">
            <select name="filter_status" class="form-select">
                <option value="" <?php echo empty($data['filter_status']) ? 'selected' : ''; ?>>All Statuses</option>
                <option value="pending" <?php echo $data['filter_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="approved" <?php echo $data['filter_status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                <option value="active" <?php echo $data['filter_status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="rejected" <?php echo $data['filter_status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                <option value="cancelled" <?php echo $data['filter_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                <option value="completed" <?php echo $data['filter_status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
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
        <a href="<?php echo URLROOT; ?>/landlord/bookings" class="btn btn-outline">
            <i class="fas fa-times"></i> Clear
        </a>
    </div>
</form>

<!-- Bookings List -->
<div class="content-card">
    <div class="card-header">
        <h2 class="card-title">All Bookings</h2>
    </div>
    <div class="card-body">
        <?php if (!empty($data['bookings'])): ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Tenant</th>
                            <th>Move-in Date</th>
                            <th>Move-out Date</th>
                            <th>Monthly Rent</th>
                            <th>Deposit</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['bookings'] as $booking): ?>
                            <?php
                            $statusClass = '';
                            switch ($booking->status) {
                                case 'pending':
                                    $statusClass = 'warning';
                                    break;
                                case 'approved':
                                    $statusClass = 'success';
                                    break;
                                case 'active':
                                    $statusClass = 'info';
                                    break;
                                case 'rejected':
                                case 'cancelled':
                                    $statusClass = 'danger';
                                    break;
                                case 'completed':
                                    $statusClass = 'secondary';
                                    break;
                            }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking->address ?? 'N/A'); ?></td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($booking->tenant_name ?? 'N/A'); ?></strong>
                                        <br>
                                        <small><?php echo htmlspecialchars($booking->tenant_email ?? ''); ?></small>
                                    </div>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($booking->move_in_date)); ?></td>
                                <td>
                                    <?php
                                    echo $booking->move_out_date
                                        ? date('M d, Y', strtotime($booking->move_out_date))
                                        : 'Ongoing';
                                    ?>
                                </td>
                                <td>LKR <?php echo number_format($booking->monthly_rent, 2); ?></td>
                                <td>LKR <?php echo number_format($booking->deposit_amount, 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $statusClass; ?>">
                                        <?php echo ucfirst($booking->status); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?php echo URLROOT; ?>/bookings/details/<?php echo $booking->id; ?>"
                                            class="btn btn-sm btn-outline">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <?php if ($booking->status === 'pending'): ?>
                                            <a href="<?php echo URLROOT; ?>/bookings/approve/<?php echo $booking->id; ?>"
                                                class="btn btn-sm btn-success"
                                                onclick="return confirm('Approve this booking request?')">
                                                <i class="fas fa-check"></i> Approve
                                            </a>
                                            <a href="<?php echo URLROOT; ?>/bookings/reject/<?php echo $booking->id; ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Reject this booking request?')">
                                                <i class="fas fa-times"></i> Reject
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-alt"></i>
                <p>No bookings yet</p>
                <span>Booking requests will appear here when tenants book your properties.</span>
            </div>
        <?php endif; ?>
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

    .btn-group {
        display: flex;
        gap: 5px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 64px;
        color: #ddd;
        margin-bottom: 20px;
    }

    .empty-state p {
        font-size: 20px;
        font-weight: 600;
        color: #666;
        margin-bottom: 10px;
    }

    .empty-state span {
        font-size: 14px;
        color: #999;
    }
</style>

<?php require APPROOT . '/views/inc/landlord_footer.php'; ?>