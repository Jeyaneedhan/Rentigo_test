<?php require APPROOT . '/views/inc/header.php'; ?>

<style>
    .privacy-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 3rem 2rem;
        background-color: #f9fafb;
        min-height: 100vh;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: white;
        color: #45a9ea;
        border: 2px solid #45a9ea;
        border-radius: 0.5rem;
        text-decoration: none;
        font-weight: 500;
        margin-bottom: 2rem;
        transition: all 0.3s ease;
    }

    .back-button:hover {
        background: #45a9ea;
        color: white;
    }

    .privacy-header {
        text-align: center;
        margin-bottom: 3rem;
        padding-bottom: 2rem;
        border-bottom: 3px solid #45a9ea;
    }

    .privacy-header h1 {
        font-size: 2.5rem;
        color: #1f2937;
        margin-bottom: 0.5rem;
        font-weight: 700;
    }

    .privacy-header .subtitle {
        color: #6b7280;
        font-size: 1rem;
    }

    .privacy-header .last-updated {
        display: inline-block;
        margin-top: 1rem;
        padding: 0.5rem 1.5rem;
        background: rgba(69, 169, 234, 0.1);
        color: #45a9ea;
        border-radius: 2rem;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .privacy-header .version-badge {
        display: inline-block;
        margin-left: 0.5rem;
        padding: 0.25rem 0.75rem;
        background: #10b981;
        color: white;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .privacy-content {
        background: white;
        border-radius: 1rem;
        padding: 3rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        line-height: 1.8;
    }

    .privacy-content h2 {
        font-size: 1.75rem;
        color: #1f2937;
        margin: 2rem 0 1rem 0;
        font-weight: 600;
    }

    .privacy-content h3 {
        font-size: 1.25rem;
        color: #374151;
        margin: 1.5rem 0 0.75rem 0;
        font-weight: 600;
    }

    .privacy-content p {
        color: #4b5563;
        line-height: 1.8;
        margin-bottom: 1rem;
        font-size: 1rem;
    }

    .privacy-content ul,
    .privacy-content ol {
        color: #4b5563;
        line-height: 1.8;
        margin-bottom: 1rem;
        padding-left: 2rem;
    }

    .privacy-content li {
        margin-bottom: 0.5rem;
    }

    .privacy-content strong {
        color: #1f2937;
        font-weight: 600;
    }

    .no-policy-message {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .no-policy-message i {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .no-policy-message h2 {
        color: #6b7280;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .no-policy-message p {
        color: #9ca3af;
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .privacy-container {
            padding: 2rem 1rem;
        }

        .privacy-header h1 {
            font-size: 2rem;
        }

        .privacy-content {
            padding: 2rem 1.5rem;
        }

        .privacy-content h2 {
            font-size: 1.5rem;
        }
    }
</style>

<div class="privacy-container">
    <a href="javascript:history.back()" class="back-button">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <?php if (!empty($data['policy'])): ?>
        <div class="privacy-header">
            <h1><?php echo htmlspecialchars($data['policy']->policy_name); ?></h1>
            <p class="subtitle">Rentigo - Rental Property Management System</p>
            <div style="margin-top: 1rem;">
                <span class="last-updated">
                    <i class="fas fa-calendar-alt"></i>
                    Effective Date: <?php echo date('F d, Y', strtotime($data['policy']->effective_date)); ?>
                </span>
                <?php if (!empty($data['policy']->policy_version)): ?>
                    <span class="version-badge">
                        <?php echo htmlspecialchars($data['policy']->policy_version); ?>
                    </span>
                <?php endif; ?>
            </div>
            <?php if (!empty($data['policy']->last_updated)): ?>
                <div style="margin-top: 0.5rem; color: #6b7280; font-size: 0.875rem;">
                    Last Updated: <?php echo date('F d, Y', strtotime($data['policy']->last_updated)); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="privacy-content">
            <?php if (!empty($data['policy']->policy_description)): ?>
                <div style="background: #f3f4f6; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem; border-left: 4px solid #45a9ea;">
                    <p style="margin: 0; color: #374151;">
                        <i class="fas fa-shield-alt" style="color: #45a9ea;"></i>
                        <?php echo htmlspecialchars($data['policy']->policy_description); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php echo $data['policy']->policy_content; ?>

            <?php if (!empty($data['policy']->expiry_date)): ?>
                <div style="margin-top: 3rem; padding: 1rem; background: #fef3c7; border-radius: 0.5rem; border-left: 4px solid #f59e0b;">
                    <p style="margin: 0; color: #92400e;">
                        <i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i>
                        <strong>Note:</strong> This policy expires on <?php echo date('F d, Y', strtotime($data['policy']->expiry_date)); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="no-policy-message">
            <i class="fas fa-shield-alt"></i>
            <h2>Privacy Policy Not Available</h2>
            <p>Our Privacy Policy is currently being updated. Please check back later.</p>
        </div>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
