<?php require APPROOT . '/views/inc/tenant_header.php'; ?>

<div class="page-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h2>Report an Issue</h2>
            <p>Report maintenance issues with your property</p>
        </div>
    </div>

    <!-- Issue Form -->
    <div id="issueForm" class="dashboard-section">
        <div class="section-header">
            <h3>Issue Details</h3>
        </div>

        <form action="<?php echo URLROOT; ?>/issues/report" method="POST" class="issue-form" novalidate>

            <!-- Property Selection -->
            <div class="form-group">
                <label>Property</label>
                <select name="property_id" id="property" class="form-select">
                    <option value="">Select Property</option>
                    <?php if (!empty($data['properties'])): ?>
                        <?php foreach ($data['properties'] as $property): ?>
                            <option value="<?php echo $property->id; ?>"
                                <?php echo ($data['property_id'] ?? '') == $property->id ? 'selected' : ''; ?>>
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
                    placeholder="Brief title for the issue..."
                    class="form-input"
                    value="<?php echo $data['issue_title'] ?? ''; ?>">
                <?php if (!empty($data['title_err'])): ?>
                    <span class="error-message"><?php echo $data['title_err']; ?></span>
                <?php endif; ?>
            </div>

            <!-- Issue Category -->
            <div class="form-group">
                <label>Issue Category</label>
                <select name="category" id="category" class="form-select">
                    <option value="">Select Category</option>
                    <option value="plumbing" <?php echo ($data['category'] ?? '') == 'plumbing' ? 'selected' : ''; ?>>Plumbing</option>
                    <option value="electrical" <?php echo ($data['category'] ?? '') == 'electrical' ? 'selected' : ''; ?>>Electrical</option>
                    <option value="hvac" <?php echo ($data['category'] ?? '') == 'hvac' ? 'selected' : ''; ?>>Heating/Cooling</option>
                    <option value="appliance" <?php echo ($data['category'] ?? '') == 'appliance' ? 'selected' : ''; ?>>Appliances</option>
                    <option value="structural" <?php echo ($data['category'] ?? '') == 'structural' ? 'selected' : ''; ?>>Structural</option>
                    <option value="pest" <?php echo ($data['category'] ?? '') == 'pest' ? 'selected' : ''; ?>>Pest Control</option>
                    <option value="other" <?php echo ($data['category'] ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
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
                    <option value="low" <?php echo ($data['priority'] ?? '') == 'low' ? 'selected' : ''; ?>>Low - Can wait a few days</option>
                    <option value="medium" <?php echo ($data['priority'] ?? 'medium') == 'medium' ? 'selected' : ''; ?>>Medium - Within 24-48 hours</option>
                    <option value="high" <?php echo ($data['priority'] ?? '') == 'high' ? 'selected' : ''; ?>>High - Urgent, needs immediate attention</option>
                    <option value="emergency" <?php echo ($data['priority'] ?? '') == 'emergency' ? 'selected' : ''; ?>>Emergency - Safety concern, immediate action required</option>
                </select>
                <?php if (!empty($data['priority_err'])): ?>
                    <span class="error-message"><?php echo $data['priority_err']; ?></span>
                <?php endif; ?>
            </div>

            <!-- Detailed Description -->
            <div class="form-group">
                <label>Detailed Description</label>
                <textarea name="description"
                    id="description"
                    placeholder="Please describe the issue in detail..."
                    class="form-textarea"><?php echo $data['description'] ?? ''; ?></textarea>
                <?php if (!empty($data['description_err'])): ?>
                    <span class="error-message"><?php echo $data['description_err']; ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" id="submitBtn" class="btn btn-primary w-full">
                <i class="fas fa-paper-plane"></i> Report Issue
            </button>
        </form>
    </div>

    <!-- Recent Issues -->
    <div class="dashboard-section">
        <div class="section-header">
            <h3>Recent Issues</h3>
            <a href="<?php echo URLROOT; ?>/issues/track" class="btn btn-secondary btn-sm">View All</a>
        </div>

        <div class="recent-issues">
            <?php if (!empty($data['recentIssues']) && count($data['recentIssues']) > 0): ?>
                <?php foreach ($data['recentIssues'] as $issue): ?>
                    <div class="issue-item">
                        <div class="issue-status">
                            <span class="status-badge <?php echo htmlspecialchars($issue->status); ?>">
                                <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($issue->status))); ?>
                            </span>
                        </div>

                        <div class="issue-details">
                            <h4><?php echo htmlspecialchars($issue->title ?? 'Untitled Issue'); ?></h4>
                            <p><?php echo htmlspecialchars(substr($issue->description ?? 'No description provided', 0, 100)) . (strlen($issue->description ?? '') > 100 ? '...' : ''); ?></p>
                            <span class="issue-date">Reported: <?php echo date("F d, Y", strtotime($issue->created_at)); ?></span>
                        </div>

                        <div class="issue-priority">
                            <span class="priority-badge <?php echo htmlspecialchars($issue->priority); ?>">
                                <?php echo ucfirst(htmlspecialchars($issue->priority)); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-issues">
                    <p style="text-align: center; padding: 2rem; color: #6b7280;">No issues reported yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php require APPROOT . '/views/inc/tenant_footer.php'; ?>