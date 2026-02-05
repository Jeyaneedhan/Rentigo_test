<?php

// AutoPaginate class - makes it super easy to add pagination to any list of items
// We use this throughout the app for tables and lists
class AutoPaginate
{
    /**
     * Initialize pagination - this automatically finds arrays in your data and paginates them
     * Just pass in your data array and it handles the rest!
     */
    public static function init(&$data, $perPage = 10)
    {
        // Get the current page from the URL, default to page 1
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

        // Auto-detect which array in the data needs pagination
        // We're looking for the first array that contains objects or arrays
        $itemsKey = null;
        $items = null;

        foreach ($data as $key => $value) {
            if (is_array($value) && !empty($value)) {
                // Re-index array to ensure key 0 exists (fixes issues with array_filter results)
                $value = array_values($value);
                if (isset($value[0]) && (is_object($value[0]) || is_array($value[0]))) {
                    $itemsKey = $key;
                    $items = $value;
                    $data[$key] = $value; // Update the original data with re-indexed array
                    break;  // Found it, stop looking
                }
            }
        }

        // If we didn't find any items to paginate, return empty pagination data
        if (! $items) {
            $data['_paginated'] = [];
            $data['_pagination'] = ['total' => 0, 'current' => 1, 'pages' => 0];
            return;
        }

        // Calculate pagination numbers
        $totalItems = count($items);
        $totalPages = ceil($totalItems / $perPage);
        $currentPage = min($currentPage, max(1, $totalPages));  // Make sure we're within valid range

        // Slice the array to get just the items for this page
        $offset = ($currentPage - 1) * $perPage;
        $paginatedItems = array_slice($items, $offset, $perPage);

        // Replace the original full array with just the items for this page
        $data[$itemsKey] = $paginatedItems;

        // Store pagination metadata so the view can display page numbers
        $data['_pagination'] = [
            'current' => $currentPage,
            'total' => $totalPages,
            'items' => $totalItems,
            'perPage' => $perPage
        ];
    }

    /**
     * THE VIEW BUTTONS: This generates the actual HTML buttons.
     * It includes logic for "Previous", "Next", and those "..." dots for long lists.
     */
    public static function render($pagination)
    {
        // If there's only one page or less, don't show pagination
        if (! is_array($pagination) || $pagination['total'] <= 1) {
            return '';
        }

        // Get the base URL without query parameters
        $baseUrl = strtok($_SERVER['REQUEST_URI'], '?  ');
        $current = $pagination['current'];
        $total = $pagination['total'];

        // Start output buffering so we can return the HTML as a string
        ob_start();
?>

        <div class="pagination-section">
            <div class="pagination-info">
                <span class="page-counter">
                    Page <strong><?php echo $current; ?></strong> of <strong><?php echo $total; ?></strong>
                </span>
            </div>

            <nav class="pagination-nav" aria-label="Pagination Navigation">
                <ul class="pagination-buttons">
                    <!-- First & Previous Buttons -->
                    <?php if ($current > 1): ?>
                        <li>
                            <a href="<?php echo $baseUrl; ?>?page=1" class="pagination-btn first-btn" title="Go to first page">
                                <i class="fas fa-step-backward"></i> First
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $baseUrl; ?>?page=<?php echo $current - 1; ?>" class="pagination-btn prev-btn" title="Go to previous page">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <span class="pagination-btn first-btn disabled">
                                <i class="fas fa-step-backward"></i> First
                            </span>
                        </li>
                        <li>
                            <span class="pagination-btn prev-btn disabled">
                                <i class="fas fa-chevron-left"></i> Previous
                            </span>
                        </li>
                    <?php endif; ?>

                    <!-- Page Number Buttons -->
                    <?php
                    // Show a range of 5 page numbers around the current page
                    $range = 5;
                    $start = max(1, $current - floor($range / 2));
                    $end = min($total, $start + $range - 1);
                    $start = max(1, $end - $range + 1);  // Adjust start if we're near the end

                    if ($start > 1): ?>
                        <li>
                            <a href="<?php echo $baseUrl; ?>?page=1" class="pagination-btn number-btn">1</a>
                        </li>
                        <?php if ($start > 2): ?>
                            <li>
                                <span class="pagination-btn ellipsis">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <li>
                            <?php if ($i === $current): ?>
                                <span class="pagination-btn number-btn active" aria-current="page">
                                    <?php echo $i; ?>
                                </span>
                            <?php else: ?>
                                <a href="<?php echo $baseUrl; ?>?page=<?php echo $i; ?>" class="pagination-btn number-btn">
                                    <?php echo $i; ?>
                                </a>
                            <?php endif; ?>
                        </li>
                    <?php endfor; ?>

                    <?php if ($end < $total): ?>
                        <?php if ($end < $total - 1): ?>
                            <li>
                                <span class="pagination-btn ellipsis">...</span>
                            </li>
                        <?php endif; ?>
                        <li>
                            <a href="<?php echo $baseUrl; ?>?page=<?php echo $total; ?>" class="pagination-btn number-btn">
                                <?php echo $total; ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Next & Last Buttons -->
                    <?php if ($current < $total): ?>
                        <li>
                            <a href="<?php echo $baseUrl; ?>?page=<?php echo $current + 1; ?>" class="pagination-btn next-btn" title="Go to next page">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $baseUrl; ?>? page=<?php echo $total; ?>" class="pagination-btn last-btn" title="Go to last page">
                                Last <i class="fas fa-step-forward"></i>
                            </a>
                        </li>
                    <?php else:  ?>
                        <li>
                            <span class="pagination-btn next-btn disabled">
                                Next <i class="fas fa-chevron-right"></i>
                            </span>
                        </li>
                        <li>
                            <span class="pagination-btn last-btn disabled">
                                Last <i class="fas fa-step-forward"></i>
                            </span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <style>
            /* Pagination Container - NO BACKGROUND */
            .pagination-section {
                margin: 2rem 0;
                padding: 1. 5rem 0;
            }

            /* Pagination Info */
            .pagination-info {
                text-align: center;
                margin-bottom: 1rem;
                display: flex;
                justify-content: center;
                gap: 1rem;
                flex-wrap: wrap;
            }

            .page-counter {
                font-size: 1rem;
                color: #333;
            }

            .page-counter strong {
                color: #45a9ea;
                font-weight: 600;
            }

            /* Pagination Navigation */
            .pagination-nav {
                display: flex;
                justify-content: center;
            }

            .pagination-buttons {
                display: flex;
                list-style: none;
                gap: 0.5rem;
                padding: 0;
                margin: 0;
                flex-wrap: wrap;
                justify-content: center;
            }

            /* Pagination Buttons */
            .pagination-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 2. 5rem;
                height: 2.5rem;
                padding: 0 0.75rem;
                border: 2px solid #45a9ea;
                border-radius: 6px;
                background-color: #fff;
                color: #45a9ea;
                text-decoration: none;
                font-weight: 500;
                transition: all 0.3s ease;
                cursor: pointer;
                gap: 0.5rem;
            }

            /* Hover Effect */
            .pagination-btn:not(.disabled):not(.active):not(.ellipsis):hover {
                background-color: #45a9ea;
                color: #fff;
                transform: translateY(-2px);
                box-shadow: 0 2px 8px rgba(69, 169, 234, 0.3);
            }

            /* Active Button */
            .pagination-btn.active {
                background-color: #45a9ea;
                color: #fff;
                font-weight: 700;
                cursor: default;
            }

            /* Disabled Buttons */
            .pagination-btn.disabled {
                background-color: #f5f5f5;
                border-color: #ddd;
                color: #999;
                cursor: not-allowed;
                opacity: 0.6;
            }

            /* Ellipsis */
            .pagination-btn.ellipsis {
                border: none;
                background: transparent;
                color: #666;
                cursor: default;
            }

            /* Number Buttons */
            .pagination-btn.number-btn {
                min-width: 2.5rem;
                height: 2.5rem;
            }

            /* Special Buttons */
            .pagination-btn.first-btn,
            .pagination-btn.prev-btn,
            .pagination-btn.next-btn,
            .pagination-btn.last-btn {
                min-width: auto;
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            /* Icons in buttons */
            .pagination-btn i {
                font-size: 0.85rem;
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .pagination-section {
                    padding: 1rem 0;
                    margin: 1.5rem 0;
                }

                .pagination-info {
                    gap: 0.5rem;
                    font-size: 0.9rem;
                }

                .pagination-buttons {
                    gap: 0.25rem;
                }

                .pagination-btn {
                    min-width: 2rem;
                    height: 2rem;
                    padding: 0 0.5rem;
                    font-size: 0.85rem;
                }

                .pagination-btn.first-btn,
                .pagination-btn.prev-btn,
                .pagination-btn.next-btn,
                .pagination-btn.last-btn {
                    padding: 0.4rem 0.6rem;
                    font-size: 0.75rem;
                }
            }

            @media (max-width: 480px) {
                .pagination-buttons {
                    gap: 0.25rem;
                }

                .pagination-btn {
                    min-width: 1.8rem;
                    height: 1.8rem;
                    padding: 0 0.3rem;
                    font-size: 0.75rem;
                }

                .pagination-btn.first-btn,
                .pagination-btn.prev-btn,
                .pagination-btn.next-btn,
                .pagination-btn.last-btn {
                    padding: 0.3rem 0.4rem;
                    font-size: 0.7rem;
                }
            }
        </style>

<?php
        return ob_get_clean();
    }
}
?>