<?php require APPROOT . '/views/inc/tenant_header.php'; ?>

<div class="page-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h2>Edit Issue</h2>
            <p>Update your reported issue details</p>
        </div>
    </div>

    <!-- Edit Issue Form -->
    <div id="issueForm" class="dashboard-section">
        <div class="section-header">
            <h3>Issue Details</h3>
        </div>

        <form action="<?php echo URLROOT; ?>/issues/edit/<?php echo $data['issue']->id; ?>" method="POST" class="issue-form" novalidate>

            <!-- Property Selection -->
            <div class="form-group">
                <label>Property</label>
                <select name="property_id" id="property" class="form-select">
                    <option value="">Select Property</option>
                    <?php if (!empty($data['properties'])): ?>
                        <?php foreach ($data['properties'] as $property): ?>
                            <option value="<?php echo $property->id; ?>"
                                <?php echo ($property->id == ($data['property_id'] ?? $data['issue']->property_id)) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($property->address); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if (!empty($data['property_err'])): ?>
                    <span class="error-message"><?php echo $data['property_err']; ?></span>
                <?php endif; ?>
            </div>

            <!-- Issue Title -->
            <div class="form-group">
                <label>Issue Title</label>
                <input type="text"
                    name="title"
                    id="title"
                    value="<?php echo htmlspecialchars($data['issue_title'] ?? $data['issue']->title); ?>"
                    placeholder="Brief title for the issue..."
                    class="form-input">
                <?php if (!empty($data['title_err'])): ?>
                    <span class="error-message"><?php echo $data['title_err']; ?></span>
                <?php endif; ?>
            </div>

            <!-- Issue Category -->
            <div class="form-group">
                <label>Issue Category</label>
                <select name="category" id="category" class="form-select">
                    <option value="">Select Category</option>
                    <option value="plumbing" <?php echo (($data['category'] ?? $data['issue']->category) == 'plumbing') ? 'selected' : ''; ?>>Plumbing</option>
                    <option value="electrical" <?php echo (($data['category'] ?? $data['issue']->category) == 'electrical') ? 'selected' : ''; ?>>Electrical</option>
                    <option value="hvac" <?php echo (($data['category'] ?? $data['issue']->category) == 'hvac') ? 'selected' : ''; ?>>Heating/Cooling</option>
                    <option value="appliance" <?php echo (($data['category'] ?? $data['issue']->category) == 'appliance') ? 'selected' : ''; ?>>Appliances</option>
                    <option value="structural" <?php echo (($data['category'] ?? $data['issue']->category) == 'structural') ? 'selected' : ''; ?>>Structural</option>
                    <option value="pest" <?php echo (($data['category'] ?? $data['issue']->category) == 'pest') ? 'selected' : ''; ?>>Pest Control</option>
                    <option value="other" <?php echo (($data['category'] ?? $data['issue']->category) == 'other') ? 'selected' : ''; ?>>Other</option>
                </select>
                <?php if (!empty($data['category_err'])): ?>
                    <span class="error-message"><?php echo $data['category_err']; ?></span>
                <?php endif; ?>
            </div>

            <!-- Priority Level -->
            <div class="form-group">
                <label>Priority Level</label>
                <select name="priority" id="priority" class="form-select">
                    <option value="">Select Priority</option>
                    <option value="low" <?php echo (($data['priority'] ?? $data['issue']->priority) == 'low') ? 'selected' : ''; ?>>Low - Can wait a few days</option>
                    <option value="medium" <?php echo (($data['priority'] ?? $data['issue']->priority) == 'medium') ? 'selected' : ''; ?>>Medium - Within 24-48 hours</option>
                    <option value="high" <?php echo (($data['priority'] ?? $data['issue']->priority) == 'high') ? 'selected' : ''; ?>>High - Urgent, needs immediate attention</option>
                    <option value="emergency" <?php echo (($data['priority'] ?? $data['issue']->priority) == 'emergency') ? 'selected' : ''; ?>>Emergency - Safety concern, immediate action required</option>
                </select>
                <?php if (!empty($data['priority_err'])): ?>
                    <span class="error-message"><?php echo $data['priority_err']; ?></span>
                <?php endif; ?>
            </div>

            <!-- Current Status (Read-only for tenant) -->
            <div class="form-group">
                <label>Current Status</label>
                <div class="status-display">
                    <span class="status-badge <?php echo htmlspecialchars($data['issue']->status); ?>">
                        <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($data['issue']->status))); ?>
                    </span>
                </div>
                <!-- Hidden field to preserve status -->
                <input type="hidden" name="status" value="<?php echo $data['issue']->status; ?>">
            </div>

            <!-- Detailed Description -->
            <div class="form-group">
                <label>Detailed Description</label>
                <textarea name="description"
                    id="description"
                    placeholder="Please describe the issue in detail..."
                    class="form-textarea"><?php echo htmlspecialchars($data['description'] ?? $data['issue']->description); ?></textarea>
                <?php if (!empty($data['description_err'])): ?>
                    <span class="error-message"><?php echo $data['description_err']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Issue
                </button>
                <a href="<?php echo URLROOT; ?>/issues/track" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require APPROOT . '/views/inc/tenant_footer.php'; ?>