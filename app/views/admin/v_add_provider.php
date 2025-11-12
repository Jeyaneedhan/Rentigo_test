<?php require APPROOT . '/views/inc/admin_header.php'; ?>

<div class="page-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h2>Add Service Provider</h2>
            <p>Add a new service provider to your network</p>
        </div>
        <div class="header-actions">
            <a href="<?php echo URLROOT; ?>/providers/index" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Providers
            </a>
        </div>
    </div>

    <!-- Add Provider Form -->
    <div class="form-container">
        <div class="form-card">
            <div class="form-header">
                <h3>Provider Information</h3>
                <p>Enter the details of the new service provider</p>
            </div>

            <form action="<?php echo URLROOT; ?>/providers/add" method="POST" class="provider-form" novalidate>
                <div class="form-grid">
                    <!-- Provider Name -->
                    <div class="form-group">
                        <label for="name" class="form-label">Provider Name <span class="required">*</span></label>
                        <input type="text"
                            id="name"
                            name="name"
                            class="form-input <?php echo !empty($data['name_err']) ? 'input-error' : ''; ?>"
                            placeholder="Enter provider name"
                            value="<?php echo $data['name'] ?? ''; ?>">
                        <?php if (!empty($data['name_err'])): ?>
                            <span class="error-message"><?php echo $data['name_err']; ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Company -->
                    <div class="form-group">
                        <label for="company" class="form-label">Company Name</label>
                        <input type="text"
                            id="company"
                            name="company"
                            class="form-input <?php echo !empty($data['company_err']) ? 'input-error' : ''; ?>"
                            placeholder="Enter company name"
                            value="<?php echo $data['company'] ?? ''; ?>">
                        <?php if (!empty($data['company_err'])): ?>
                            <span class="error-message"><?php echo $data['company_err']; ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Specialty -->
                    <div class="form-group">
                        <label for="specialty" class="form-label">Specialty <span class="required">*</span></label>
                        <select id="specialty" name="specialty" class="form-select <?php echo !empty($data['specialty_err']) ? 'input-error' : ''; ?>">
                            <option value="">Select specialty</option>
                            <option value="plumbing" <?php echo ($data['specialty'] ?? '') == 'plumbing' ? 'selected' : ''; ?>>Plumbing</option>
                            <option value="electrical" <?php echo ($data['specialty'] ?? '') == 'electrical' ? 'selected' : ''; ?>>Electrical</option>
                            <option value="hvac" <?php echo ($data['specialty'] ?? '') == 'hvac' ? 'selected' : ''; ?>>HVAC</option>
                            <option value="carpentry" <?php echo ($data['specialty'] ?? '') == 'carpentry' ? 'selected' : ''; ?>>Carpentry</option>
                            <option value="painting" <?php echo ($data['specialty'] ?? '') == 'painting' ? 'selected' : ''; ?>>Painting</option>
                            <option value="landscaping" <?php echo ($data['specialty'] ?? '') == 'landscaping' ? 'selected' : ''; ?>>Landscaping</option>
                            <option value="cleaning" <?php echo ($data['specialty'] ?? '') == 'cleaning' ? 'selected' : ''; ?>>Cleaning</option>
                            <option value="pest_control" <?php echo ($data['specialty'] ?? '') == 'pest_control' ? 'selected' : ''; ?>>Pest Control</option>
                            <option value="general" <?php echo ($data['specialty'] ?? '') == 'general' ? 'selected' : ''; ?>>General Maintenance</option>
                        </select>
                        <?php if (!empty($data['specialty_err'])): ?>
                            <span class="error-message"><?php echo $data['specialty_err']; ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number <span class="required">*</span></label>
                        <input type="tel"
                            id="phone"
                            name="phone"
                            class="form-input <?php echo !empty($data['phone_err']) ? 'input-error' : ''; ?>"
                            placeholder="e.g., +1234567890"
                            value="<?php echo $data['phone'] ?? ''; ?>">
                        <?php if (!empty($data['phone_err'])): ?>
                            <span class="error-message"><?php echo $data['phone_err']; ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address <span class="required">*</span></label>
                        <input type="email"
                            id="email"
                            name="email"
                            class="form-input <?php echo !empty($data['email_err']) ? 'input-error' : ''; ?>"
                            placeholder="provider@example.com"
                            value="<?php echo $data['email'] ?? ''; ?>">
                        <?php if (!empty($data['email_err'])): ?>
                            <span class="error-message"><?php echo $data['email_err']; ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Rating -->
                    <div class="form-group">
                        <label for="rating" class="form-label">Initial Rating</label>
                        <select id="rating" name="rating" class="form-select <?php echo !empty($data['rating_err']) ? 'input-error' : ''; ?>">
                            <option value="0.0">No Rating</option>
                            <option value="1.0" <?php echo ($data['rating'] ?? '') == '1.0' ? 'selected' : ''; ?>>⭐ 1.0</option>
                            <option value="1.5" <?php echo ($data['rating'] ?? '') == '1.5' ? 'selected' : ''; ?>>⭐ 1.5</option>
                            <option value="2.0" <?php echo ($data['rating'] ?? '') == '2.0' ? 'selected' : ''; ?>>⭐⭐ 2.0</option>
                            <option value="2.5" <?php echo ($data['rating'] ?? '') == '2.5' ? 'selected' : ''; ?>>⭐⭐ 2.5</option>
                            <option value="3.0" <?php echo ($data['rating'] ?? '') == '3.0' ? 'selected' : ''; ?>>⭐⭐⭐ 3.0</option>
                            <option value="3.5" <?php echo ($data['rating'] ?? '') == '3.5' ? 'selected' : ''; ?>>⭐⭐⭐ 3.5</option>
                            <option value="4.0" <?php echo ($data['rating'] ?? '') == '4.0' ? 'selected' : ''; ?>>⭐⭐⭐⭐ 4.0</option>
                            <option value="4.5" <?php echo ($data['rating'] ?? '') == '4.5' ? 'selected' : ''; ?>>⭐⭐⭐⭐ 4.5</option>
                            <option value="5.0" <?php echo ($data['rating'] ?? '') == '5.0' ? 'selected' : ''; ?>>⭐⭐⭐⭐⭐ 5.0</option>
                        </select>
                        <?php if (!empty($data['rating_err'])): ?>
                            <span class="error-message"><?php echo $data['rating_err']; ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select <?php echo !empty($data['status_err']) ? 'input-error' : ''; ?>">
                            <option value="active" <?php echo ($data['status'] ?? 'active') == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($data['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            <option value="suspended" <?php echo ($data['status'] ?? '') == 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                        </select>
                        <?php if (!empty($data['status_err'])): ?>
                            <span class="error-message"><?php echo $data['status_err']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Address - Full Width -->
                <div class="form-group">
                    <label for="address" class="form-label">Address</label>
                    <textarea id="address"
                        name="address"
                        class="form-textarea <?php echo !empty($data['address_err']) ? 'input-error' : ''; ?>"
                        placeholder="Enter complete address"
                        rows="3"><?php echo $data['address'] ?? ''; ?></textarea>
                    <?php if (!empty($data['address_err'])): ?>
                        <span class="error-message"><?php echo $data['address_err']; ?></span>
                    <?php endif; ?>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="<?php echo URLROOT; ?>/providers/index" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Provider
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Form Container */
    .form-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .form-card {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        padding: 2rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }

    .form-header {
        margin-bottom: 2rem;
        text-align: center;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 1rem;
    }

    .form-header h3 {
        color: #1f2937;
        margin-bottom: 0.5rem;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .form-header p {
        color: #6b7280;
        margin: 0;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #374151;
        font-size: 0.875rem;
    }

    .required {
        color: #dc2626;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .input-error {
        border-color: #dc2626 !important;
        background-color: #fef2f2;
    }

    .error-message {
        color: #dc2626;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .form-actions .btn {
            width: 100%;
        }
    }
</style>

<?php require APPROOT . '/views/inc/admin_footer.php'; ?>