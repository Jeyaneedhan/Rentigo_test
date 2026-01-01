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
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Properties</div>
                <h3><?php echo $data['totalProperties'] ?? 0; ?></h3>
                <div class="stat-change">Assigned to you</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-home"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Occupied Properties</div>
                <h3><?php echo $data['totalUnits'] ?? 0; ?></h3>
                <div class="stat-change"><?php echo $data['occupiedUnits'] ?? 0; ?> occupied</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Platform Income</div>
                <h3>LKR <?php echo number_format($data['totalIncome'] ?? 0, 0); ?></h3>
                <div class="stat-change">10% service fees collected</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Expenses</div>
                <h3>LKR <?php echo number_format($data['totalExpenses'] ?? 0, 0); ?></h3>
                <div class="stat-change">Maintenance costs</div>
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

    <!-- Quick Actions -->
    <div class="dashboard-section" style="margin-top: 0.5rem;">
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