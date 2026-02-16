<?php require APPROOT . '/views/inc/landlord_header.php'; ?>

<div class="page-content">
    <!-- Time Restriction Alert -->
    <?php if (isset($data['remaining_seconds']) && $data['remaining_seconds'] > 0): ?>
        <div class="alert alert-warning time-alert" id="timeAlert">
            <i class="fas fa-clock"></i>
            <span>
                <strong>Time Remaining:</strong>
                <span id="countdown"><?php echo gmdate("i:s", $data['remaining_seconds']); ?></span>
                (You can only edit within 5 minutes of creation)
            </span>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-left">
            <a href="<?php echo URLROOT; ?>/maintenance/index" class="back-button">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="page-title">Edit Maintenance Request</h1>
                <p class="page-subtitle">Update your maintenance request details</p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php flash('maintenance_message'); ?>

    <!-- New Maintenance Request Form -->
    <div class="form-container">
        <form action="<?php echo URLROOT; ?>/maintenance/edit/<?php echo $data['maintenance']->id; ?>" method="POST" class="maintenance-form">

            <!-- Property Information (READ-ONLY) -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-building"></i> Property Information
                </h3>

                <div class="form-group">
                    <label class="form-label">Property</label>
                    <div class="property-display">
                        <i class="fas fa-home"></i>
                        <span><?php echo htmlspecialchars($data['maintenance']->property_address ?? 'Unknown Property'); ?></span>
                    </div>
                    <small class="form-text text-muted">
                        <i class="fas fa-lock"></i> Property cannot be changed after request creation
                    </small>
                </div>
            </div>

            <!-- Request Details -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-file-alt"></i> Request Details
                </h3>

                <!-- Title -->
                <div class="form-group">
                    <label for="title" class="form-label required">Issue Title</label>
                    <input type="text"
                        name="title"
                        id="title"
                        class="form-control"
                        placeholder="e.g., Water Leak in Bathroom, Broken AC Unit"
                        value="<?php echo htmlspecialchars($data['maintenance']->title); ?>"
                        required>
                </div>

                <!-- Category -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="category" class="form-label required">Category</label>
                        <select name="category" id="category" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            <option value="plumbing" <?php echo ($data['maintenance']->category == 'plumbing') ? 'selected' : ''; ?>>Plumbing</option>
                            <option value="electrical" <?php echo ($data['maintenance']->category == 'electrical') ? 'selected' : ''; ?>>Electrical</option>
                            <option value="hvac" <?php echo ($data['maintenance']->category == 'hvac') ? 'selected' : ''; ?>>HVAC (Heating/Cooling)</option>
                            <option value="appliance" <?php echo ($data['maintenance']->category == 'appliance') ? 'selected' : ''; ?>>Appliance</option>
                            <option value="structural" <?php echo ($data['maintenance']->category == 'structural') ? 'selected' : ''; ?>>Structural</option>
                            <option value="pest" <?php echo ($data['maintenance']->category == 'pest') ? 'selected' : ''; ?>>Pest Control</option>
                            <option value="other" <?php echo ($data['maintenance']->category == 'other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>

                    <!-- Priority -->
                    <div class="form-group">
                        <label for="priority" class="form-label required">Priority Level</label>
                        <select name="priority" id="priority" class="form-control" required>
                            <option value="">-- Select Priority --</option>
                            <option value="low" <?php echo ($data['maintenance']->priority == 'low') ? 'selected' : ''; ?>>Low - Can wait</option>
                            <option value="medium" <?php echo ($data['maintenance']->priority == 'medium') ? 'selected' : ''; ?>>Medium - Within a week</option>
                            <option value="high" <?php echo ($data['maintenance']->priority == 'high') ? 'selected' : ''; ?>>High - Within 2-3 days</option>
                            <option value="emergency" <?php echo ($data['maintenance']->priority == 'emergency') ? 'selected' : ''; ?>>Emergency - Immediate attention</option>
                        </select>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description" class="form-label required">Detailed Description</label>
                    <textarea name="description"
                        id="description"
                        rows="6"
                        class="form-control"
                        placeholder="Please provide a detailed description of the issue..."
                        required><?php echo htmlspecialchars($data['maintenance']->description); ?></textarea>
                    <small class="form-text">Be as specific as possible to help resolve the issue quickly</small>
                </div>


            </div>

            <!-- Additional Information -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-info-circle"></i> Additional Information (Optional)
                </h3>

                <div class="form-group">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea name="notes"
                        id="notes"
                        rows="3"
                        class="form-control"
                        placeholder="Any additional notes or special instructions..."><?php echo htmlspecialchars($data['maintenance']->notes ?? ''); ?></textarea>
                    <small class="form-text">Include access instructions, tenant contact, or other relevant information</small>
                </div>

                <div class="form-group">
                    <label for="estimated_cost" class="form-label">Estimated Cost (LKR)</label>
                    <input type="number"
                        name="estimated_cost"
                        id="estimated_cost"
                        class="form-control"
                        step="0.01"
                        min="0"
                        placeholder="Optional: Enter estimated cost"
                        value="<?php echo htmlspecialchars($data['maintenance']->estimated_cost ?? ''); ?>">
                    <small class="form-text">If you have an idea of the repair cost</small>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="<?php echo URLROOT; ?>/maintenance/index" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .page-content {
        padding: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f3f4f6;
        color: #374151;
        text-decoration: none;
        transition: all 0.3s;
    }

    .back-button:hover {
        background-color: #e5e7eb;
        color: #1f2937;
    }

    .page-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .page-subtitle {
        color: #6b7280;
        margin: 0.25rem 0 0 0;
    }

    /* Time Alert Styles */
    .time-alert {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #f59e0b;
        border-radius: 0.75rem;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        animation: pulse 2s ease-in-out infinite;
    }

    .time-alert i {
        font-size: 1.5rem;
        color: #d97706;
    }

    .time-alert span {
        color: #92400e;
        font-size: 0.938rem;
    }

    .time-alert strong {
        font-weight: 600;
    }

    #countdown {
        font-family: 'Courier New', monospace;
        font-weight: 700;
        font-size: 1.125rem;
        color: #d97706;
    }

    @keyframes pulse {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4);
        }

        50% {
            box-shadow: 0 0 0 8px rgba(245, 158, 11, 0);
        }
    }

    /* Property Display (Read-only) */
    .property-display {
        padding: 0.75rem 1rem;
        background: #f3f4f6;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #374151;
        font-weight: 500;
    }

    .property-display i {
        color: #45a9ea;
        font-size: 1.125rem;
    }

    .text-muted {
        color: #6b7280 !important;
        font-style: italic;
    }

    .form-container {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }

    .form-section {
        margin-bottom: 2.5rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .form-section:last-of-type {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i {
        color: #45a9ea;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .form-label.required::after {
        content: '*';
        color: #ef4444;
        margin-left: 0.25rem;
    }

    .form-control {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-control.is-invalid {
        border-color: #ef4444;
    }

    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .invalid-feedback {
        display: block;
        color: #ef4444;
        font-size: 0.813rem;
        margin-top: 0.25rem;
    }

    .form-text {
        display: block;
        color: #6b7280;
        font-size: 0.813rem;
        margin-top: 0.25rem;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .form-control-file {
        display: block;
        width: 100%;
        padding: 0.5rem;
        border: 2px dashed #d1d5db;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .form-control-file:hover {
        border-color: #3b82f6;
        background-color: #f9fafb;
    }

    .photo-preview-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .photo-preview {
        position: relative;
        aspect-ratio: 1;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    .photo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-primary {
        background-color: #45a9ea;
        color: white;
    }

    .btn-primary:hover {
        background-color: #2563eb;
    }

    .btn-secondary {
        background-color: #6b7280;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #4b5563;
    }

    @media (max-width: 768px) {
        .page-content {
            padding: 1rem;
        }

        .form-container {
            padding: 1.5rem;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<script>
    // Countdown timer
    <?php if (isset($data['remaining_seconds']) && $data['remaining_seconds'] > 0): ?>
        let remainingSeconds = <?php echo $data['remaining_seconds']; ?>;

        function updateCountdown() {
            if (remainingSeconds <= 0) {
                // Time expired - redirect with message
                window.location.href = '<?php echo URLROOT; ?>/maintenance/index';
                return;
            }

            const minutes = Math.floor(remainingSeconds / 60);
            const seconds = remainingSeconds % 60;
            const display = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            document.getElementById('countdown').textContent = display;

            // Change color when time is running out
            if (remainingSeconds <= 60) {
                document.getElementById('countdown').style.color = '#dc2626';
                document.querySelector('.time-alert').style.background = 'linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)';
                document.querySelector('.time-alert').style.borderColor = '#ef4444';
            }

            remainingSeconds--;
        }

        // Update immediately
        updateCountdown();

        // Update every second
        const countdownInterval = setInterval(updateCountdown, 1000);
    <?php endif; ?>

    // Form validation
    document.querySelector('.maintenance-form').addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();
        const category = document.getElementById('category').value;
        const priority = document.getElementById('priority').value;

        if (!title || !description || !category || !priority) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
        }
    });

    // Priority color indicator
    document.getElementById('priority').addEventListener('change', function() {
        const colors = {
            'low': '#10b981',
            'medium': '#f59e0b',
            'high': '#ef4444',
            'emergency': '#dc2626'
        };
        this.style.borderLeftWidth = '4px';
        this.style.borderLeftColor = colors[this.value] || '#d1d5db';
    });

    // Set initial priority color
    window.addEventListener('DOMContentLoaded', function() {
        const prioritySelect = document.getElementById('priority');
        const colors = {
            'low': '#10b981',
            'medium': '#f59e0b',
            'high': '#ef4444',
            'emergency': '#dc2626'
        };
        if (prioritySelect.value) {
            prioritySelect.style.borderLeftWidth = '4px';
            prioritySelect.style.borderLeftColor = colors[prioritySelect.value] || '#d1d5db';
        }
    });
</script>

<?php require APPROOT . '/views/inc/landlord_footer.php'; ?>