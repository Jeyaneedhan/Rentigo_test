<?php require APPROOT . '/views/inc/admin_header.php'; ?>

<div class="page-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h2>Edit Policy</h2>
            <p>Update policy document</p>
        </div>
        <div class="header-actions">
            <a href="<?php echo URLROOT; ?>/policies" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Policies
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php flash('policy_message'); ?>

    <!-- Edit Policy Form -->
    <div class="dashboard-section">
        <div class="section-header">
            <h3>Policy Information</h3>
        </div>

        <div class="card-body">
            <form action="<?php echo URLROOT; ?>/policies/edit/<?php echo $data['policy']->policy_id; ?>" method="post" id="editPolicyForm">

                <!-- Hidden Policy ID -->
                <input type="hidden" name="policy_id" value="<?php echo $data['policy']->policy_id; ?>">

                <!-- Basic Information -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="policy_name">Policy Name <span class="required">*</span></label>
                        <input type="text"
                            name="policy_name"
                            id="policy_name"
                            class="form-control <?php echo (!empty($data['policy_name_err'])) ? 'is-invalid' : ''; ?>"
                            value="<?php echo $data['policy_name'] ?? $data['policy']->policy_name; ?>"
                            placeholder="Enter policy name">
                        <?php if (!empty($data['policy_name_err'])): ?>
                            <span class="error-message"><?php echo $data['policy_name_err']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="policy_category">Category <span class="required">*</span></label>
                        <select name="policy_category"
                            id="policy_category"
                            class="form-control <?php echo (!empty($data['policy_category_err'])) ? 'is-invalid' : ''; ?>">
                            <option value="">Select Category</option>
                            <?php foreach ($data['categories'] as $key => $value): ?>
                                <option value="<?php echo $key; ?>"
                                    <?php
                                    $selected = isset($data['policy_category']) ? $data['policy_category'] : $data['policy']->policy_category;
                                    echo ($selected == $key) ? 'selected' : '';
                                    ?>>
                                    <?php echo $value; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($data['policy_category_err'])): ?>
                            <span class="error-message"><?php echo $data['policy_category_err']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="policy_version">Version <span class="required">*</span></label>
                        <input type="text"
                            name="policy_version"
                            id="policy_version"
                            class="form-control <?php echo (!empty($data['policy_version_err'])) ? 'is-invalid' : ''; ?>"
                            value="<?php echo $data['policy_version'] ?? $data['policy']->policy_version; ?>"
                            placeholder="e.g., v1.0">
                        <?php if (!empty($data['policy_version_err'])): ?>
                            <span class="error-message"><?php echo $data['policy_version_err']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="policy_status">Status</label>
                        <select name="policy_status" id="policy_status" class="form-control">
                            <?php foreach ($data['statuses'] as $key => $value):
                                $selected = isset($data['policy_status']) ? $data['policy_status'] : $data['policy']->policy_status;
                            ?>
                                <option value="<?php echo $key; ?>"
                                    <?php echo ($selected == $key) ? 'selected' : ''; ?>>
                                    <?php echo $value; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="policy_type">Type</label>
                        <select name="policy_type" id="policy_type" class="form-control">
                            <?php foreach ($data['types'] as $key => $value):
                                $selected = isset($data['policy_type']) ? $data['policy_type'] : $data['policy']->policy_type;
                            ?>
                                <option value="<?php echo $key; ?>"
                                    <?php echo ($selected == $key) ? 'selected' : ''; ?>>
                                    <?php echo $value; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="effective_date">Effective Date <span class="required">*</span></label>
                        <input type="date"
                            name="effective_date"
                            id="effective_date"
                            class="form-control <?php echo (!empty($data['effective_date_err'])) ? 'is-invalid' : ''; ?>"
                            value="<?php echo $data['effective_date'] ?? $data['policy']->effective_date; ?>">
                        <?php if (!empty($data['effective_date_err'])): ?>
                            <span class="error-message"><?php echo $data['effective_date_err']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="expiry_date">Expiry Date</label>
                        <input type="date"
                            name="expiry_date"
                            id="expiry_date"
                            class="form-control"
                            value="<?php echo $data['expiry_date'] ?? $data['policy']->expiry_date ?? ''; ?>">
                        <small class="form-text">Leave empty if policy doesn't expire</small>
                        <?php if (!empty($data['expiry_date_err'])): ?>
                            <span class="error-message"><?php echo $data['expiry_date_err']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="policy_description">Policy Description <span class="required">*</span></label>
                    <textarea name="policy_description"
                        id="policy_description"
                        rows="3"
                        class="form-control <?php echo (!empty($data['policy_description_err'])) ? 'is-invalid' : ''; ?>"
                        placeholder="Enter a brief description of the policy"><?php echo $data['policy_description'] ?? $data['policy']->policy_description; ?></textarea>
                    <?php if (!empty($data['policy_description_err'])): ?>
                        <span class="error-message"><?php echo $data['policy_description_err']; ?></span>
                    <?php endif; ?>
                </div>

                <!-- Policy Content -->
                <div class="form-group">
                    <label for="policy_content">Policy Content <span class="required">*</span></label>

                    <!-- Content Editor -->
                    <div id="policy_content_editor"
                        contenteditable="true"
                        class="content-editor"
                        data-placeholder="Enter the detailed policy content..."><?php echo $data['policy_content'] ?? $data['policy']->policy_content; ?></div>

                    <!-- Hidden textarea for form submission -->
                    <textarea name="policy_content"
                        id="policy_content"
                        style="display: none;"
                        class="<?php echo (!empty($data['policy_content_err'])) ? 'is-invalid' : ''; ?>"><?php echo $data['policy_content'] ?? $data['policy']->policy_content; ?></textarea>

                    <?php if (!empty($data['policy_content_err'])): ?>
                        <span class="error-message"><?php echo $data['policy_content_err']; ?></span>
                    <?php endif; ?>
                    <small class="form-text">Use the toolbar above to format your policy content.</small>
                </div>

                <!-- Meta Information -->
                <div class="meta-info">
                    <div class="meta-row">
                        <div class="meta-item">
                            <i class="fas fa-user"></i>
                            <span>Created by: <strong><?php echo $data['policy']->created_by_name ?? 'Unknown'; ?></strong></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <span>Created: <strong><?php echo date('M d, Y', strtotime($data['policy']->created_at)); ?></strong></span>
                        </div>
                        <?php if (!empty($data['policy']->updated_at)): ?>
                            <div class="meta-item">
                                <i class="fas fa-clock"></i>
                                <span>Last Updated: <strong><?php echo date('M d, Y', strtotime($data['policy']->updated_at)); ?></strong></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <div class="action-buttons">
                        <a href="<?php echo URLROOT; ?>/policies" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Policy
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Include Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    /* Form Styles */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .required {
        color: #dc2626;
    }

    .form-control {
        padding: 0.625rem 0.875rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color, #2563eb);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-control.is-invalid {
        border-color: #dc2626;
    }

    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    .error-message {
        color: #dc2626;
        font-size: 0.8rem;
        margin-top: 0.25rem;
        display: block;
    }

    .form-text {
        color: #64748b;
        font-size: 0.8rem;
        margin-top: 0.25rem;
        display: block;
    }

    /* Editor Toolbar Styles */
    .editor-toolbar {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.5rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem 0.375rem 0 0;
        border-bottom: none;
    }

    .editor-btn {
        padding: 0.375rem 0.625rem;
        background: white;
        border: 1px solid transparent;
        border-radius: 0.25rem;
        color: #64748b;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
    }

    .editor-btn:hover {
        background: #f1f5f9;
        color: #334155;
        border-color: #e2e8f0;
    }

    .editor-btn:active {
        background: #e2e8f0;
    }

    .toolbar-divider {
        width: 1px;
        height: 24px;
        background: #e2e8f0;
        margin: 0 0.25rem;
    }

    /* Content Editor Styles */
    .content-editor {
        min-height: 350px;
        max-height: 600px;
        overflow-y: auto;
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0 0 0.375rem 0.375rem;
        background: white;
        line-height: 1.6;
        font-size: 0.9rem;
        color: #1e293b;
    }

    .content-editor:focus {
        outline: 2px solid var(--primary-color, #2563eb);
        outline-offset: 2px;
    }

    .content-editor:empty:before {
        content: attr(data-placeholder);
        color: #94a3b8;
    }

    /* Editor Content Formatting */
    .content-editor h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 1rem 0 0.5rem 0;
        color: #1e293b;
    }

    .content-editor h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0.75rem 0 0.25rem 0;
        color: #334155;
    }

    .content-editor ul,
    .content-editor ol {
        padding-left: 1.5rem;
        margin: 0.5rem 0;
    }

    .content-editor li {
        margin: 0.25rem 0;
    }

    .content-editor p {
        margin: 0.5rem 0;
    }

    .content-editor strong {
        font-weight: 600;
    }

    .content-editor em {
        font-style: italic;
    }

    .content-editor u {
        text-decoration: underline;
    }

    /* Meta Information */
    .meta-info {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .meta-row {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #64748b;
        font-size: 0.875rem;
    }

    .meta-item i {
        color: #94a3b8;
    }

    .meta-item strong {
        color: #1e293b;
    }

    /* Form Actions */
    .form-actions {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 2px solid #e2e8f0;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    /* Button Styles */
    .btn {
        padding: 0.625rem 1.25rem;
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-primary {
        background: var(--primary-color, #2563eb);
        color: white;
    }

    .btn-primary:hover {
        background: #1d4ed8;
    }

    .btn-secondary {
        background: #64748b;
        color: white;
    }

    .btn-secondary:hover {
        background: #475569;
    }

    /* Dashboard Section */
    .dashboard-section {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .section-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .section-header h3 {
        margin: 0;
        font-size: 1.25rem;
        color: #1e293b;
    }

    .card-body {
        padding: 1.5rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editPolicyForm');
        const editor = document.getElementById('policy_content_editor');
        const hiddenField = document.getElementById('policy_content');

        // Restore editor content after server-side validation errors.
        if (hiddenField.value) {
            editor.innerHTML = hiddenField.value;
        }

        // Ensure contenteditable content is submitted to backend.
        form.addEventListener('submit', function() {
            hiddenField.value = editor.innerHTML.trim();
        });
    });
</script>

<?php require APPROOT . '/views/inc/admin_footer.php'; ?>