<?php require APPROOT . '/views/inc/admin_header.php'; ?>

<div class="dashboard-content">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card" data-stat-type="admin_properties">
            <div class="stat-icon">
                <i class="fas fa-home"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-admin_properties" onclick="toggleStatDropdown('admin_properties')">Total Properties</span>
                    <div class="stat-dropdown" id="stat-dropdown-admin_properties">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('admin_properties', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('admin_properties', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('admin_properties', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 class="stat-number" id="stat-value-admin_properties"><?php echo number_format($data['totalProperties'] ?? 0); ?></h3>
                <span class="stat-change" id="stat-subtitle-admin_properties">All properties in system</span>
            </div>
        </div>

        <div class="stat-card" data-stat-type="admin_tenants">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-admin_tenants" onclick="toggleStatDropdown('admin_tenants')">Active Tenants</span>
                    <div class="stat-dropdown" id="stat-dropdown-admin_tenants">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('admin_tenants', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('admin_tenants', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('admin_tenants', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 class="stat-number" id="stat-value-admin_tenants"><?php echo number_format($data['activeTenants'] ?? 0); ?></h3>
                <span class="stat-change" id="stat-subtitle-admin_tenants">Currently renting</span>
            </div>
        </div>

        <div class="stat-card" data-stat-type="admin_income">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-admin_income" onclick="toggleStatDropdown('admin_income')">Platform Income</span>
                    <div class="stat-dropdown" id="stat-dropdown-admin_income">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('admin_income', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('admin_income', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('admin_income', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 class="stat-number" id="stat-value-admin_income">LKR <?php echo number_format($data['monthlyRevenue'] ?? 0, 0); ?></h3>
                <span class="stat-change" id="stat-subtitle-admin_income">10% from rental + full maintenance payments</span>
            </div>
        </div>

        <div class="stat-card" data-stat-type="admin_approvals">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <div class="stat-header">
                    <span class="stat-label" id="stat-label-admin_approvals" onclick="toggleStatDropdown('admin_approvals')">Pending Approvals</span>
                    <div class="stat-dropdown" id="stat-dropdown-admin_approvals">
                        <div class="stat-dropdown-item selected" data-period="all" onclick="selectStatPeriod('admin_approvals', 'all', event)">All Time</div>
                        <div class="stat-dropdown-item" data-period="year" onclick="selectStatPeriod('admin_approvals', 'year', event)">Current Year</div>
                        <div class="stat-dropdown-item" data-period="month" onclick="selectStatPeriod('admin_approvals', 'month', event)">Current Month</div>
                    </div>
                </div>
                <h3 class="stat-number" id="stat-value-admin_approvals"><?php echo $data['pendingApprovals'] ?? 0; ?></h3>
                <span class="stat-change" id="stat-subtitle-admin_approvals">Requires attention</span>
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
            padding-bottom: 1.5rem;
            padding-left: 1rem;
            padding-right: 1rem;
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

    <!-- Recent Activity Feed -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Recent Activity</h2>
        </div>

        <div class="activity-feed">
            <?php if (!empty($data['recentActivities'])): ?>
                <?php foreach ($data['recentActivities'] as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon <?php echo $activity['color']; ?>">
                            <i class="fas <?php echo $activity['icon']; ?>"></i>
                        </div>
                        <div class="activity-content">
                            <p class="activity-title"><?php echo htmlspecialchars($activity['title']); ?></p>
                            <p class="activity-description"><?php echo htmlspecialchars($activity['description']); ?></p>
                        </div>
                        <div class="activity-time">
                            <?php
                            $timestamp = strtotime($activity['created_at']);
                            $diff = time() - $timestamp;
                            if ($diff < 60) {
                                echo 'Just now';
                            } elseif ($diff < 3600) {
                                echo floor($diff / 60) . 'm ago';
                            } elseif ($diff < 86400) {
                                echo floor($diff / 3600) . 'h ago';
                            } elseif ($diff < 604800) {
                                echo floor($diff / 86400) . 'd ago';
                            } else {
                                echo date('M j', $timestamp);
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="activity-empty">
                    <i class="fas fa-inbox"></i>
                    <p>No recent activity</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
        .activity-feed {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-item:hover {
            background: #f8fafc;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .activity-icon.primary {
            background: #dbeafe;
            color: #2563eb;
        }

        .activity-icon.success {
            background: #d1fae5;
            color: #059669;
        }

        .activity-icon.warning {
            background: #fef3c7;
            color: #d97706;
        }

        .activity-icon.danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .activity-icon.info {
            background: #e0e7ff;
            color: #4f46e5;
        }

        .activity-icon.secondary {
            background: #f1f5f9;
            color: #64748b;
        }

        .activity-content {
            flex: 1;
            min-width: 0;
        }

        .activity-title {
            font-weight: 600;
            color: #1e293b;
            margin: 0;
            font-size: 0.875rem;
        }

        .activity-description {
            color: #64748b;
            margin: 0.25rem 0 0 0;
            font-size: 0.8125rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .activity-time {
            font-size: 0.75rem;
            color: #94a3b8;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .activity-empty {
            padding: 3rem;
            text-align: center;
            color: #94a3b8;
        }

        .activity-empty i {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
        }

        .activity-empty p {
            margin: 0;
            font-size: 0.875rem;
        }
    </style>
</div>

<?php require APPROOT . '/views/inc/admin_footer.php'; ?>