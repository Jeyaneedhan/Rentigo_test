<?php require APPROOT . '/views/inc/admin_header.php'; ?>

<?php
$policy = $data['policy'];
$categoryDisplay = str_replace('_', ' ', ucwords($policy->policy_category, '_'));
$statusDisplay = str_replace('_', ' ', strtoupper($policy->policy_status));
$typeDisplay = !empty($policy->policy_type) ? str_replace('_', ' ', ucwords($policy->policy_type, '_')) : 'Not provided';
?>

<div class="page-content">
    <div class="page-header">
        <div class="header-content">
            <h2>Policy Details</h2>
            <p>Review policy metadata and content</p>
        </div>
        <div class="header-actions">
            <a href="<?php echo URLROOT; ?>/policies/index" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Policies
            </a>
            <a href="<?php echo URLROOT; ?>/policies/edit/<?php echo $policy->policy_id; ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Policy
            </a>
        </div>
    </div>

    <div class="details-card">
        <div class="card-header">
            <h1>
                <?php echo htmlspecialchars($policy->policy_name); ?>
                <span class="admin-tag">Admin</span>
            </h1>
        </div>
        <div class="card-content">
            <div class="location-block">
                <div class="label-light">Description</div>
                <div class="address-text">
                    <?php echo !empty($policy->policy_description) ? htmlspecialchars($policy->policy_description) : 'Not provided'; ?>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Category</div>
                    <div class="info-value"><?php echo $categoryDisplay; ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Type</div>
                    <div class="info-value"><?php echo $typeDisplay; ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="status-chip <?php echo strtolower($policy->policy_status); ?>">
                            <?php echo $statusDisplay; ?>
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Version</div>
                    <div class="info-value">
                        <span class="approval-chip"><?php echo htmlspecialchars($policy->policy_version); ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Effective Date</div>
                    <div class="info-value">
                        <?php echo !empty($policy->effective_date) ? htmlspecialchars($policy->effective_date) : 'Not provided'; ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Expiry Date</div>
                    <div class="info-value">
                        <?php echo !empty($policy->expiry_date) ? htmlspecialchars($policy->expiry_date) : 'Not provided'; ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Created By</div>
                    <div class="info-value">
                        <?php echo !empty($policy->created_by_name) ? htmlspecialchars($policy->created_by_name) : 'Not provided'; ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Last Updated</div>
                    <div class="info-value">
                        <?php echo !empty($policy->last_updated) ? htmlspecialchars($policy->last_updated) : 'Not available'; ?>
                    </div>
                </div>
            </div>

            <div class="content-section">
                <div class="info-label">Policy Content</div>
                <div class="content-box">
                    <?php echo !empty($policy->policy_content) ? $policy->policy_content : '<p>Not provided.</p>'; ?>
                </div>
            </div>

            <hr />
            <div class="footer-note">
                <span>Policy record · admin overview</span>
            </div>
        </div>
    </div>
</div>

<style>
    .details-card {
        max-width: 760px;
        width: 100%;
        background: #ffffff;
        border-radius: 28px;
        box-shadow: 0 20px 32px -12px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.02);
        overflow: hidden;
        margin: 0 auto;
        transition: box-shadow 0.2s;
    }

    .details-card:hover {
        box-shadow: 0 24px 40px -14px rgba(0, 0, 0, 0.12);
    }

    .card-header {
        background: #45a9ea;
        padding: 1.2rem 2rem;
    }

    .card-header h1 {
        font-size: 1.6rem;
        font-weight: 600;
        letter-spacing: -0.2px;
        color: #ffffff;
        margin: 0;
    }

    .admin-tag {
        display: inline-block;
        background: rgba(255, 255, 255, 0.18);
        font-size: 0.7rem;
        font-weight: 500;
        padding: 3px 10px;
        border-radius: 40px;
        margin-left: 12px;
        vertical-align: middle;
    }

    .card-content {
        padding: 1.8rem 2rem 2rem 2rem;
    }

    .location-block {
        margin-bottom: 1.8rem;
        padding-bottom: 1.2rem;
        border-bottom: 1px solid #e9edf2;
    }

    .label-light {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        font-weight: 600;
        color: #5b6e8c;
        margin-bottom: 0.45rem;
    }

    .address-text {
        font-size: 1.2rem;
        font-weight: 600;
        color: #0f1825;
        line-height: 1.35;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.4rem 1.8rem;
        margin: 1.6rem 0 1.2rem 0;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }

    .info-label {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6c7e9e;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 500;
        color: #1f2a3e;
        word-break: break-word;
    }

    .status-chip {
        display: inline-block;
        background: #fef2e0;
        color: #b45f06;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 4px 14px;
        border-radius: 40px;
        letter-spacing: 0.2px;
    }

    .status-chip.active {
        background: #e3f5ec;
        color: #1f7840;
    }

    .status-chip.draft {
        background: #eef3ff;
        color: #2f4b8f;
    }

    .status-chip.inactive {
        background: #f0f2f5;
        color: #5c6f87;
    }

    .status-chip.archived {
        background: #f5f2ff;
        color: #5a3f9b;
    }

    .status-chip.under_review {
        background: #ffede8;
        color: #bc3900;
    }

    .approval-chip {
        display: inline-block;
        background: #eef3ff;
        color: #2f4b8f;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 4px 14px;
        border-radius: 40px;
    }

    .content-section {
        margin: 0.5rem 0 1rem 0;
    }

    .content-box {
        margin-top: 0.6rem;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.4rem;
        background: #f8fafc;
        color: #1f2a3e;
    }

    .content-box p {
        margin: 0 0 0.8rem 0;
    }

    hr {
        margin: 1.2rem 0 0.8rem 0;
        border: none;
        border-top: 1px solid #eef2f8;
    }

    .footer-note {
        display: flex;
        justify-content: flex-end;
        font-size: 0.7rem;
        color: #8da0b5;
    }

    @media (max-width: 600px) {
        .card-content {
            padding: 1.5rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .address-text {
            font-size: 1.1rem;
        }

        .card-header h1 {
            font-size: 1.4rem;
        }
    }
</style>

<?php require APPROOT . '/views/inc/admin_footer.php'; ?>