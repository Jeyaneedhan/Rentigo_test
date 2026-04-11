<?php require APPROOT . '/views/inc/admin_header.php'; ?>

<?php
$transaction = $data['transaction'];
$isMaintenance = ($data['transaction_type'] ?? '') === 'maintenance' || (($transaction->payment_type ?? '') === 'maintenance');
$displayDate = $transaction->payment_date ?? $transaction->due_date ?? $transaction->created_at ?? '';
$description = $isMaintenance
    ? ($transaction->maintenance_title ?? 'Maintenance payment')
    : ($transaction->payment_method ?? 'Rental payment');
$propertyAddress = $transaction->property_address ?? 'N/A';
$totalAmount = $isMaintenance ? ($transaction->amount ?? 0) : (($transaction->amount ?? 0) * 1.10);
$platformFee = $isMaintenance ? null : (($transaction->amount ?? 0) * 0.10);
$status = $transaction->status ?? 'pending';
$statusDisplay = str_replace('_', ' ', strtoupper($status));
$transactionCode = $data['transaction_id'] ?? ($transaction->transaction_id ?? 'N/A');
?>

<div class="page-content">
    <div class="page-header">
        <div class="header-content">
            <h2>Transaction Details</h2>
            <p>Review payment and fee information</p>
        </div>
        <div class="header-actions">
            <a href="<?php echo URLROOT; ?>/admin/financials" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Financials
            </a>
        </div>
    </div>

    <div class="details-card">
        <div class="card-header">
            <h1>
                <?php echo $isMaintenance ? 'Maintenance Transaction' : 'Rental Transaction'; ?>
                <span class="admin-tag">Admin</span>
            </h1>
        </div>
        <div class="card-content">
            <div class="location-block">
                <div class="label-light">Property</div>
                <div class="address-text">
                    <?php echo htmlspecialchars($propertyAddress); ?>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Transaction ID</div>
                    <div class="info-value"><?php echo htmlspecialchars($transactionCode); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Description</div>
                    <div class="info-value"><?php echo htmlspecialchars($description); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="status-chip <?php echo strtolower($status); ?>"><?php echo $statusDisplay; ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Payment Date</div>
                    <div class="info-value"><?php echo !empty($displayDate) ? htmlspecialchars($displayDate) : 'Not available'; ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Total Payment</div>
                    <div class="info-value">LKR <?php echo number_format($totalAmount, 0); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Platform Fee (10%)</div>
                    <div class="info-value">
                        <?php if ($platformFee === null): ?>
                            <span class="approval-chip">N/A</span>
                        <?php else: ?>
                            <span class="approval-chip">LKR <?php echo number_format($platformFee, 0); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tenant</div>
                    <div class="info-value">
                        <?php echo !empty($transaction->tenant_name) ? htmlspecialchars($transaction->tenant_name) : 'N/A'; ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Landlord</div>
                    <div class="info-value">
                        <?php echo !empty($transaction->landlord_name) ? htmlspecialchars($transaction->landlord_name) : 'N/A'; ?>
                    </div>
                </div>
            </div>

            <div class="timestamp-section">
                <div class="info-label">Notes</div>
                <div class="timestamp-block">
                    <?php echo !empty($transaction->notes) ? htmlspecialchars($transaction->notes) : 'No additional notes'; ?>
                </div>
            </div>

            <hr />
            <div class="footer-note">
                <span>Financial record · admin overview</span>
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

    .status-chip.completed {
        background: #e3f5ec;
        color: #1f7840;
    }

    .status-chip.pending {
        background: #eef3ff;
        color: #2f4b8f;
    }

    .status-chip.overdue {
        background: #ffede8;
        color: #bc3900;
    }

    .status-chip.failed {
        background: #f5f2ff;
        color: #5a3f9b;
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
        padding: 0.6rem 1rem;
        border-radius: 16px;
        display: inline-block;
        font-size: 0.85rem;
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