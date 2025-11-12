<?php require APPROOT . '/views/inc/admin_header.php'; ?>

<div class="page-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h2>Add New Policy</h2>
            <p>Create a new policy document</p>
        </div>
        <div class="header-actions">
            <a href="<?php echo URLROOT; ?>/policies" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Policies
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php flash('policy_message'); ?>

    <!-- Add Policy Form -->
    <div class="dashboard-section">
        <div class="section-header">
            <h3>Policy Information</h3>
        </div>

        <div class="card-body">
            <form action="<?php echo URLROOT; ?>/policies/add" method="post" id="addPolicyForm">

                <!-- Basic Information -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="policy_name">Policy Name <span class="required">*</span></label>
                        <input type="text"
                            name="policy_name"
                            id="policy_name"
                            class="form-control <?php echo (!empty($data['policy_name_err'])) ? 'is-invalid' : ''; ?>"
                            value="<?php echo $data['policy_name'] ?? ''; ?>"
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
                                    <?php echo (isset($data['policy_category']) && $data['policy_category'] == $key) ? 'selected' : ''; ?>>
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
                            value="<?php echo $data['policy_version'] ?? 'v1.0'; ?>"
                            placeholder="e.g., v1.0">
                        <?php if (!empty($data['policy_version_err'])): ?>
                            <span class="error-message"><?php echo $data['policy_version_err']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="policy_status">Status</label>
                        <select name="policy_status" id="policy_status" class="form-control">
                            <?php foreach ($data['statuses'] as $key => $value): ?>
                                <option value="<?php echo $key; ?>"
                                    <?php echo (isset($data['policy_status']) && $data['policy_status'] == $key) ? 'selected' : (($key == 'draft') ? 'selected' : ''); ?>>
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
                            <?php foreach ($data['types'] as $key => $value): ?>
                                <option value="<?php echo $key; ?>"
                                    <?php echo (isset($data['policy_type']) && $data['policy_type'] == $key) ? 'selected' : (($key == 'standard') ? 'selected' : ''); ?>>
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
                            value="<?php echo $data['effective_date'] ?? date('Y-m-d'); ?>">
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
                            value="<?php echo $data['expiry_date'] ?? ''; ?>">
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
                        placeholder="Enter a brief description of the policy"><?php echo $data['policy_description'] ?? ''; ?></textarea>
                    <?php if (!empty($data['policy_description_err'])): ?>
                        <span class="error-message"><?php echo $data['policy_description_err']; ?></span>
                    <?php endif; ?>
                </div>

                <!-- Policy Content -->
                <div class="form-group">
                    <label for="policy_content">Policy Content <span class="required">*</span></label>

                    <!-- Editor Toolbar -->
                    <div class="editor-toolbar">
                        <button type="button" onclick="formatText('bold')" class="editor-btn" title="Bold">
                            <i class="fas fa-bold"></i>
                        </button>
                        <button type="button" onclick="formatText('italic')" class="editor-btn" title="Italic">
                            <i class="fas fa-italic"></i>
                        </button>
                        <button type="button" onclick="formatText('underline')" class="editor-btn" title="Underline">
                            <i class="fas fa-underline"></i>
                        </button>
                        <span class="toolbar-divider"></span>
                        <button type="button" onclick="insertHeading('h3')" class="editor-btn" title="Heading 3">
                            H3
                        </button>
                        <button type="button" onclick="insertHeading('h4')" class="editor-btn" title="Heading 4">
                            H4
                        </button>
                        <span class="toolbar-divider"></span>
                        <button type="button" onclick="insertList('ul')" class="editor-btn" title="Bullet List">
                            <i class="fas fa-list-ul"></i>
                        </button>
                        <button type="button" onclick="insertList('ol')" class="editor-btn" title="Numbered List">
                            <i class="fas fa-list-ol"></i>
                        </button>
                    </div>

                    <!-- Content Editor -->
                    <div id="policy_content_editor"
                        contenteditable="true"
                        class="content-editor"
                        data-placeholder="Enter the detailed policy content..."></div>

                    <!-- Hidden textarea for form submission -->
                    <textarea name="policy_content"
                        id="policy_content"
                        style="display: none;"
                        class="<?php echo (!empty($data['policy_content_err'])) ? 'is-invalid' : ''; ?>"><?php echo $data['policy_content'] ?? ''; ?></textarea>

                    <?php if (!empty($data['policy_content_err'])): ?>
                        <span class="error-message"><?php echo $data['policy_content_err']; ?></span>
                    <?php endif; ?>
                    <small class="form-text">Use the toolbar above to format your policy content.</small>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <div class="action-buttons">
                        <a href="<?php echo URLROOT; ?>/policies" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Create Policy
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

    .btn-outline {
        background: white;
        color: var(--primary-color, #2563eb);
        border: 1px solid var(--primary-color, #2563eb);
    }

    .btn-outline:hover {
        background: var(--primary-color, #2563eb);
        color: white;
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
    // Rich Text Editor Functions
    function formatText(command) {
        document.execCommand(command, false, null);
        document.getElementById('policy_content_editor').focus();
    }

    function insertHeading(tag) {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            const selectedText = range.toString();

            if (selectedText) {
                const heading = document.createElement(tag);
                heading.textContent = selectedText;
                range.deleteContents();
                range.insertNode(heading);

                // Clear selection and position cursor after heading
                selection.removeAllRanges();
                const newRange = document.createRange();
                newRange.setStartAfter(heading);
                newRange.collapse(true);
                selection.addRange(newRange);
            } else {
                // If no text selected, insert empty heading
                const heading = document.createElement(tag);
                heading.innerHTML = 'Heading&nbsp;';
                range.insertNode(heading);

                // Place cursor inside heading
                const newRange = document.createRange();
                newRange.selectNodeContents(heading);
                selection.removeAllRanges();
                selection.addRange(newRange);
            }
        }
        document.getElementById('policy_content_editor').focus();
    }

    function insertList(listType) {
        document.execCommand('insert' + (listType === 'ul' ? 'UnorderedList' : 'OrderedList'), false, null);
        document.getElementById('policy_content_editor').focus();
    }

    // Sync editor content with hidden textarea
    document.getElementById('policy_content_editor').addEventListener('input', function() {
        document.getElementById('policy_content').value = this.innerHTML;
    });

    // Form submission handler
    document.getElementById('addPolicyForm').addEventListener('submit', function(e) {
        // Sync editor content before submission
        const editorContent = document.getElementById('policy_content_editor').innerHTML.trim();
        document.getElementById('policy_content').value = editorContent;

        // Don't perform client-side validation - let server handle it
        // Just make sure content is synced
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Load existing content if editing
        const hiddenContent = document.getElementById('policy_content').value;
        if (hiddenContent) {
            document.getElementById('policy_content_editor').innerHTML = hiddenContent;
        }

        // Set minimum date for effective date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('effective_date').setAttribute('min', today);

        // Handle expiry date validation
        document.getElementById('effective_date').addEventListener('change', function() {
            const expiryDateInput = document.getElementById('expiry_date');
            expiryDateInput.setAttribute('min', this.value);

            // Clear expiry date if it's before effective date
            if (expiryDateInput.value && expiryDateInput.value <= this.value) {
                expiryDateInput.value = '';
            }
        });

        // Prevent form submission on toolbar buttons
        document.querySelectorAll('.editor-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
            });
        });

        // Version format validation
        const versionInput = document.getElementById('policy_version');

        versionInput.addEventListener('blur', function() {
            let value = this.value.toLowerCase().replace(/[^v\d.]/g, '');

            if (value && !value.startsWith('v')) {
                value = 'v' + value;
            }

            // Ensure format v#.#
            const match = value.match(/v(\d+)\.?(\d*)/);
            if (match) {
                const major = match[1];
                const minor = match[2] || '0';
                this.value = `v${major}.${minor}`;
            } else if (!value) {
                this.value = 'v1.0';
            }
        });

        // Remove validation errors on input
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    });

    // Prevent paste with formatting
    document.getElementById('policy_content_editor').addEventListener('paste', function(e) {
        e.preventDefault();
        const text = e.clipboardData.getData('text/plain');
        document.execCommand('insertText', false, text);
    });
</script>

<?php require APPROOT . '/views/inc/admin_footer.php'; ?>