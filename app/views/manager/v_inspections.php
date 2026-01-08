<?php require APPROOT . '/views/inc/manager_header.php'; ?>

<?php
// ADD PAGINATION
require_once APPROOT . '/../app/helpers/AutoPaginate.php';
AutoPaginate::init($data, 5);
?>

<?php

// Read filters from query string (no JS needed)
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$typeFilter = isset($_GET['type']) ? $_GET['type'] : 'all';     // values: all | Issue | Maintanace
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all'; // values: all | scheduled | in-progress | completed

// Normalize helper
$normalize = function ($v) {
    $v = strtolower((string) $v);
    $v = str_replace('_', '-', $v);
    return trim($v);
};

$filteredInspections = [];
if (!empty($data['inspections']) && is_iterable($data['inspections'])) {
    foreach ($data['inspections'] as $inspection) {
        $rowType = $normalize($inspection->type ?? '');
        $rowStatus = $normalize($inspection->status ?? '');
        $rowText = $normalize(
            'ins-' . ($inspection->id ?? '') . ' ' .
                ($inspection->property ?? '') . ' ' .
                ($inspection->type ?? '') . ' ' .
                ($inspection->scheduled_date ?? '') . ' ' .
                ($inspection->status ?? '') . ' ' .
                ($inspection->issues ?? '')
        );

        $matchesType = ($typeFilter === 'all') || ($normalize($typeFilter) === $rowType);
        $matchesStatus = ($statusFilter === 'all') || ($normalize($statusFilter) === $rowStatus);
        $matchesSearch = ($searchQuery === '') || (strpos($rowText, $normalize($searchQuery)) !== false);

        if ($matchesType && $matchesStatus && $matchesSearch) {
            $filteredInspections[] = $inspection;
        }
    }
}
?>

<div class="inspections-content">
    <div class="page-header">
        <div class="header-left">
            <h1 class="page-title">Pre-Inspection Reports</h1>
            <p class="page-subtitle">Manage inspection checklists and reports</p>
        </div>
        <div class="header-right">
            <a href="<?php echo URLROOT; ?>/inspections/add" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Schedule Inspection
            </a>
        </div>
    </div>

    <div class="tabs-container">
        <!-- All Inspections Tab -->
        <div id="all-inspections-tab" class="tab-content active">
            <!-- Search and Filter Section -->
            <form method="GET" action="<?php echo htmlspecialchars(strtok($_SERVER['REQUEST_URI'], '?')); ?>">
                <div class="search-filter-content">
                    <div class="search-input-wrapper">
                        <input type="text" class="form-input" placeholder="Search inspections..." name="q" value="<?php echo htmlspecialchars($searchQuery); ?>">
                    </div>
                    <div class="filter-dropdown-wrapper">
                        <select class="form-select" name="type">
                            <option value="all" <?php echo ($typeFilter === 'all') ? 'selected' : ''; ?>>All Types</option>
                            <option value="Issue" <?php echo ($typeFilter === 'Issue') ? 'selected' : ''; ?>>Issue</option>
                            <option value="Maintanace" <?php echo ($typeFilter === 'Maintanace') ? 'selected' : ''; ?>>Maintanace</option>
                        </select>
                    </div>
                    <div class="filter-dropdown-wrapper">
                        <select class="form-select" name="status">
                            <option value="all" <?php echo ($statusFilter === 'all') ? 'selected' : ''; ?>>All Status</option>
                            <option value="scheduled" <?php echo ($statusFilter === 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                            <option value="in-progress" <?php echo ($statusFilter === 'in-progress') ? 'selected' : ''; ?>>In Progress</option>
                            <option value="completed" <?php echo ($statusFilter === 'completed') ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="<?php echo htmlspecialchars(strtok($_SERVER['REQUEST_URI'], '?')); ?>" class="btn btn-outline">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>

            <!-- Inspections Table -->
            <div class="dashboard-section">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>PROPERTY</th>
                                <th>TYPE</th>
                                <th>SCHEDULED DATE</th>
                                <th>STATUS</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($filteredInspections)): ?>
                                <?php foreach ($filteredInspections as $inspection): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($inspection->property_address ?? 'N/A'); ?></td>
                                        <td><?php echo ucfirst($inspection->type); ?></td>
                                        <td><?php echo $inspection->scheduled_date; ?></td>
                                        <td><span
                                                class="status-badge pending"><?php echo ucfirst($inspection->status); ?></span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="<?php echo URLROOT; ?>/inspections/edit/<?php echo $inspection->id; ?>"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete(<?php echo $inspection->id; ?>)">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No inspections found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for deletion -->
<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="inspection_id" id="deleteInspectionId">
</form>

<!-- ADD PAGINATION HERE - Render at bottom -->
<?php echo AutoPaginate::render($data['_pagination']); ?>

<script>
    function confirmDelete(inspectionId) {
        if (confirm('Are you sure you want to delete this inspection? This action cannot be undone.')) {
            // Set the inspection ID in hidden form
            document.getElementById('deleteInspectionId').value = inspectionId;

            // Set form action to delete URL
            const form = document.getElementById('deleteForm');
            form.action = '<?php echo URLROOT; ?>/inspections/delete/' + inspectionId;

            // Submit form via POST
            form.submit();
        }
    }
</script>

<?php require APPROOT . '/views/inc/manager_footer.php'; ?>