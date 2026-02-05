<?php require APPROOT . '/views/inc/admin_header.php'; ?>

<div class="dashboard-content">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-home"></i>
            </div>
            <div class="stat-info">
                <h3 class="stat-number"><?php echo number_format($data['totalProperties'] ?? 0); ?></h3>
                <p class="stat-label">Total Properties</p>
                <span class="stat-change">All properties in system</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3 class="stat-number"><?php echo number_format($data['activeTenants'] ?? 0); ?></h3>
                <p class="stat-label">Active Tenants</p>
                <span class="stat-change">Currently renting</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3 class="stat-number">LKR <?php echo number_format($data['monthlyRevenue'] ?? 0, 0); ?></h3>
                <p class="stat-label">Platform Income</p>
                <span class="stat-change">10% from rental + full maintenance payments</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3 class="stat-number"><?php echo $data['pendingApprovals'] ?? 0; ?></h3>
                <p class="stat-label">Pending Approvals</p>
                <span class="stat-change">Requires attention</span>
            </div>
        </div>
    </div>

    <!-- Admin Attention Summary -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Admin Attention Summary</h2>
        </div>

        <div class="attention-summary-grid">
            <a href="<?php echo URLROOT; ?>/AdminProperties/index" class="attention-card">
                <div class="attention-icon pending">
                    <i class="fas fa-home"></i>
                </div>
                <div class="attention-info">
                    <h3 class="attention-count"><?php echo $data['pendingPropertyApprovals'] ?? 0; ?></h3>
                    <p class="attention-label">Pending Property Approvals</p>
                </div>
                <div class="attention-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <a href="<?php echo URLROOT; ?>/admin/managers" class="attention-card">
                <div class="attention-icon warning">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="attention-info">
                    <h3 class="attention-count"><?php echo $data['pendingPMApprovals'] ?? 0; ?></h3>
                    <p class="attention-label">Pending PM Approvals</p>
                </div>
                <div class="attention-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>

            <a href="<?php echo URLROOT; ?>/admin/policies" class="attention-card">
                <div class="attention-icon success">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="attention-info">
                    <h3 class="attention-count"><?php echo $data['activePoliciesCount'] ?? 0; ?></h3>
                    <p class="attention-label">Active Policies</p>
                </div>
                <div class="attention-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
        </div>
    </div>

    <style>
        .attention-summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .attention-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .attention-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .attention-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .attention-icon.pending {
            background: #fef3c7;
            color: #d97706;
        }

        .attention-icon.warning {
            background: #fee2e2;
            color: #dc2626;
        }

        .attention-icon.success {
            background: #d1fae5;
            color: #059669;
        }

        .attention-info {
            flex: 1;
        }

        .attention-count {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            line-height: 1.2;
        }

        .attention-label {
            font-size: 0.875rem;
            color: #64748b;
            margin: 0.25rem 0 0 0;
        }

        .attention-arrow {
            color: #94a3b8;
            font-size: 1rem;
        }

        .attention-card:hover .attention-arrow {
            color: #45a9ea;
        }
    </style>
</div>

<?php require APPROOT . '/views/inc/admin_footer.php'; ?>