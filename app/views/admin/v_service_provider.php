<?php require APPROOT . '/views/inc/admin_header.php'; ?>

<?php $provider = $data['provider']; ?>

<div class="page-content">
    <div class="page-header">
        <div class="header-content">
            <h2>Service Provider Details</h2>
            <p>View full profile and contact information</p>
        </div>
        <div class="header-actions">
            <a href="<?php echo URLROOT; ?>/providers/index" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Providers
            </a>
            <a href="<?php echo URLROOT; ?>/providers/edit/<?php echo $provider->id; ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Provider
            </a>
        </div>
    </div>

    <div class="details-card">
        <div class="card-header">
            <h1>
                Service Provider Details
                <span class="admin-tag">Admin</span>
            </h1>
        </div>
        <div class="card-content">
            <div class="location-block">
                <div class="label-light">Address</div>
                <div class="address-text">
                    <?php echo !empty($provider->address) ? htmlspecialchars($provider->address) : 'Not provided'; ?>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Name</div>
                    <div class="info-value">
                        <?php echo htmlspecialchars($provider->name); ?>
                        <?php if (!empty($provider->company)): ?>
                            <span class="info-subvalue"><?php echo htmlspecialchars($provider->company); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Specialty</div>
                    <div class="info-value"><?php echo ucfirst(str_replace('_', ' ', $provider->specialty)); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="status-chip <?php echo strtolower($provider->status); ?>">
                            <?php echo strtoupper($provider->status); ?>
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Rating</div>
                    <div class="info-value">
                        <span class="approval-chip">
                            <?php echo $provider->rating > 0 ? number_format($provider->rating, 1) : 'NO RATING'; ?>
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Phone</div>
                    <div class="info-value"><?php echo !empty($provider->phone) ? htmlspecialchars($provider->phone) : 'Not provided'; ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?php echo !empty($provider->email) ? htmlspecialchars($provider->email) : 'Not provided'; ?></div>
                </div>
            </div>

            <div class="timestamp-section">
                <div class="info-label">Created At</div>
                <div class="timestamp-block">
                    <?php echo !empty($provider->created_at) ? htmlspecialchars($provider->created_at) : 'N/A'; ?>
                </div>
            </div>

            <hr />
            <div class="footer-note">
                <span>Service provider record · admin overview</span>
            </div>
        </div>
    </div>
</div>

<style>
    .details-card {
        max-width: 720px;
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
        font-size: 1.3rem;
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

    .info-subvalue {
        font-size: 0.8rem;
        color: #4f6f8f;
        font-weight: normal;
        margin-left: 6px;
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

    .status-chip.inactive {
        background: #f0f2f5;
        color: #5c6f87;
    }

    .status-chip.suspended {
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

    .timestamp-section {
        margin: 0.25rem 0 1rem 0;
    }

    .timestamp-block {
        background: #f8fafd;
        padding: 0.45rem 1rem;
        border-radius: 16px;
        display: inline-block;
        font-size: 0.85rem;
        font-family: "SFMono-Regular", ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        color: #2c405c;
        border: 1px solid #eef2f8;
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

    @media (max-width: 550px) {
        .card-content {
            padding: 1.5rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .address-text {
            font-size: 1.15rem;
        }

        .card-header h1 {
            font-size: 1.4rem;
        }
    }
</style>

<?php require APPROOT . '/views/inc/admin_footer.php'; ?>