<?php require APPROOT . '/views/inc/landlord_header.php'; ?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="header-content">
            <a href="<?php echo URLROOT; ?>/properties" class="btn btn-secondary" style="margin-bottom:1rem;">
                <i class="fas fa-arrow-left"></i> Back to Properties
            </a>
            <h2 class="page-title">
                <i class="fas fa-home"></i> Property Details
            </h2>
            <p class="page-subtitle">View complete property information</p>
        </div>
    </div>

    <?php flash('property_message'); ?>

    <?php if (isset($data['property'])): ?>
        <?php
        $property = $data['property'];
        $approval = strtolower($property->approval_status ?? 'pending');
        $isApproved = $approval === 'approved';
        $listingType = $property->listing_type ?? 'rent';
        $isMaintenanceProperty = ($listingType === 'maintenance');
        ?>

        <div class="property-details-card">
            <!-- Header with Status Badges -->
            <div class="card-header">
                <div>
                    <h3><?php echo htmlspecialchars($property->address); ?></h3>
                    <p class="property-type"><?php echo ucfirst($property->property_type); ?></p>
                </div>
                <div class="header-badges">
                    <!-- Listing Type Badge -->
                    <span class="listing-badge <?php echo $isMaintenanceProperty ? 'maintenance' : 'rent'; ?>">
                        <i class="fas <?php echo $isMaintenanceProperty ? 'fa-tools' : 'fa-home'; ?>"></i>
                        <?php echo $isMaintenanceProperty ? 'Maintenance Only' : 'For Rent'; ?>
                    </span>
                    <!-- Approval Status -->
                    <span class="status-badge <?php echo $approval; ?>">
                        <?php echo ucfirst($approval); ?>
                    </span>
                    <!-- Property Status -->
                    <span class="status-badge <?php echo strtolower($property->status ?? 'unknown'); ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $property->status ?? 'Unknown')); ?>
                    </span>
                </div>
            </div>

            <div class="card-body">
                <!-- Property Images Gallery -->
                <?php if (!empty($property->images) && count($property->images) > 0): ?>
                    <div class="info-section">
                        <h4><i class="fas fa-images"></i> Property Images</h4>
                        <div class="property-images-gallery">
                            <?php foreach ($property->images as $img): ?>
                                <div class="gallery-item">
                                    <a href="<?php echo $img['url']; ?>" target="_blank">
                                        <img src="<?php echo $img['url']; ?>" alt="Property Image">
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Basic Information -->
                <div class="info-section">
                    <h4><i class="fas fa-info-circle"></i> Basic Information</h4>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Property Type:</label>
                            <span><?php echo ucfirst(htmlspecialchars($property->property_type ?? 'N/A')); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Listing Type:</label>
                            <span><?php echo $isMaintenanceProperty ? 'Maintenance Only' : 'For Rent'; ?></span>
                        </div>
                        <?php if (!$isMaintenanceProperty): ?>
                            <div class="info-item">
                                <label>Monthly Rent:</label>
                                <span class="highlight">Rs <?php echo number_format($property->rent ?? 0); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Security Deposit:</label>
                                <span><?php echo isset($property->deposit) && $property->deposit > 0 ? 'Rs ' . number_format($property->deposit) : 'N/A'; ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($isMaintenanceProperty && !empty($property->current_occupant)): ?>
                            <div class="info-item">
                                <label>Current Occupant:</label>
                                <span><?php echo htmlspecialchars($property->current_occupant); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Property Features -->
                <?php if (!$isMaintenanceProperty): ?>
                    <div class="info-section">
                        <h4><i class="fas fa-bed"></i> Property Features</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Bedrooms:</label>
                                <span><?php echo $property->bedrooms ?? 'N/A'; ?></span>
                            </div>
                            <div class="info-item">
                                <label>Bathrooms:</label>
                                <span><?php echo isset($property->bathrooms) ? (int)$property->bathrooms : 'N/A'; ?></span>
                            </div>
                            <div class="info-item">
                                <label>Square Footage:</label>
                                <span><?php echo isset($property->sqft) && $property->sqft > 0 ? number_format($property->sqft) . ' sq ft' : 'N/A'; ?></span>
                            </div>
                            <div class="info-item">
                                <label>Available Date:</label>
                                <span><?php echo !empty($property->available_date) ? date('F d, Y', strtotime($property->available_date)) : 'N/A'; ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Amenities -->
                    <div class="info-section">
                        <h4><i class="fas fa-star"></i> Amenities</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Parking:</label>
                                <span>
                                    <?php if (isset($property->parking) && $property->parking > 0): ?>
                                        <i class="fas fa-check text-success"></i> <?php echo $property->parking; ?> space(s)
                                    <?php else: ?>
                                        <i class="fas fa-times text-danger"></i> No parking
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <label>Pet Policy:</label>
                                <span>
                                    <?php
                                    $petPolicy = $property->pet_policy ?? 'no';
                                    if ($petPolicy === 'yes' || $petPolicy === 'allowed') {
                                        echo '<i class="fas fa-paw text-success"></i> Pets Allowed';
                                    } elseif ($petPolicy === 'negotiable') {
                                        echo '<i class="fas fa-paw text-warning"></i> Negotiable';
                                    } else {
                                        echo '<i class="fas fa-ban text-danger"></i> No Pets';
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <label>Laundry:</label>
                                <span>
                                    <?php
                                    $laundry = $property->laundry ?? 'none';
                                    echo ucfirst(str_replace('_', ' ', $laundry));
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Description -->
                <?php if (!empty($property->description)): ?>
                    <div class="info-section">
                        <h4><i class="fas fa-align-left"></i> Description</h4>
                        <div class="description-content">
                            <?php echo nl2br(htmlspecialchars($property->description)); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Property Documents -->
                <?php if (!empty($property->documents) && count($property->documents) > 0): ?>
                    <div class="info-section">
                        <h4><i class="fas fa-file-alt"></i> Property Documents</h4>
                        <ul class="documents-list">
                            <?php foreach ($property->documents as $doc): ?>
                                <li>
                                    <a href="<?php echo $doc['url']; ?>" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                        <?php echo htmlspecialchars($doc['name']); ?>
                                    </a>
                                    <span class="doc-info">(<?php echo strtoupper($doc['type']); ?>, <?php echo round($doc['size'] / 1024); ?> KB)</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Status Information -->
                <div class="info-section">
                    <h4><i class="fas fa-clipboard-check"></i> Status Information</h4>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Property Status:</label>
                            <span class="status-badge <?php echo strtolower($property->status ?? 'unknown'); ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $property->status ?? 'Unknown')); ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <label>Approval Status:</label>
                            <span class="status-badge <?php echo $approval; ?>">
                                <?php echo ucfirst($approval); ?>
                            </span>
                        </div>
                        <?php if ($isApproved && !empty($property->approved_at)): ?>
                            <div class="info-item">
                                <label>Approved On:</label>
                                <span><?php echo date('F d, Y', strtotime($property->approved_at)); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="info-item">
                            <label>Created On:</label>
                            <span><?php echo !empty($property->created_at) ? date('F d, Y', strtotime($property->created_at)) : 'N/A'; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <?php if (!$isApproved): ?>
                        <a href="<?php echo URLROOT; ?>/properties/edit/<?php echo $property->id; ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Property
                        </a>
                        <a href="<?php echo URLROOT; ?>/properties/delete/<?php echo $property->id; ?>"
                            class="btn btn-danger"
                            onclick="return confirm('Are you sure you want to delete this property and all its images?');">
                            <i class="fas fa-trash"></i> Delete Property
                        </a>
                    <?php else: ?>
                        <div class="locked-notice">
                            <i class="fas fa-lock"></i>
                            <span>This property has been approved and cannot be edited or deleted. Contact support if changes are needed.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="no-data-card">
            <i class="fas fa-exclamation-circle"></i>
            <h3>Property Not Found</h3>
            <p>The property you're looking for doesn't exist or you don't have access to it.</p>
            <a href="<?php echo URLROOT; ?>/properties" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Properties
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
    .property-details-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .card-header h3 {
        margin: 0 0 0.25rem 0;
        color: #1e293b;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .card-header .property-type {
        margin: 0;
        color: #64748b;
        font-size: 0.95rem;
    }

    .header-badges {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .listing-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.813rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .listing-badge.rent {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .listing-badge.maintenance {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .card-body {
        padding: 2rem;
    }

    .info-section {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .info-section:last-of-type {
        border-bottom: none;
        margin-bottom: 1rem;
    }

    .info-section h4 {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #1e293b;
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0 0 1rem 0;
    }

    .info-section h4 i {
        color: #45a9ea;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .info-item label {
        font-size: 0.813rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .info-item span {
        font-size: 1rem;
        color: #1e293b;
    }

    .info-item span.highlight {
        font-weight: 700;
        color: #059669;
        font-size: 1.125rem;
    }

    /* Status Badges */
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.813rem;
        font-weight: 600;
        letter-spacing: 0.025em;
    }

    .status-badge.approved {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #10b981;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #f59e0b;
    }

    .status-badge.rejected {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #ef4444;
    }

    .status-badge.available {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #3b82f6;
    }

    .status-badge.occupied {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #10b981;
    }

    .status-badge.maintenance,
    .status-badge.maintenance_only {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #f59e0b;
    }

    /* Images Gallery */
    .property-images-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .gallery-item {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 4px;
        background: #f8fafc;
        transition: all 0.2s ease;
    }

    .gallery-item:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .gallery-item img {
        display: block;
        width: 150px;
        height: 110px;
        object-fit: cover;
        border-radius: 6px;
    }

    /* Description */
    .description-content {
        background: #f8fafc;
        padding: 1rem 1.25rem;
        border-radius: 8px;
        color: #334155;
        line-height: 1.7;
        border: 1px solid #e2e8f0;
    }

    /* Documents */
    .documents-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .documents-list li {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        margin-bottom: 0.5rem;
    }

    .documents-list li a {
        color: #2563eb;
        text-decoration: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .documents-list li a:hover {
        text-decoration: underline;
    }

    .documents-list .doc-info {
        color: #64748b;
        font-size: 0.813rem;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e2e8f0;
    }

    .locked-notice {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: #fef3c7;
        color: #92400e;
        padding: 1rem 1.25rem;
        border-radius: 8px;
        border: 1px solid #f59e0b;
        font-size: 0.95rem;
        width: 100%;
    }

    .locked-notice i {
        font-size: 1.25rem;
    }

    /* No Data Card */
    .no-data-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        padding: 3rem;
        text-align: center;
    }

    .no-data-card i {
        font-size: 3rem;
        color: #e2e8f0;
        margin-bottom: 1rem;
    }

    .no-data-card h3 {
        color: #1e293b;
        margin: 0 0 0.5rem 0;
    }

    .no-data-card p {
        color: #64748b;
        margin: 0 0 1.5rem 0;
    }

    /* Text helpers */
    .text-success {
        color: #10b981;
    }

    .text-warning {
        color: #f59e0b;
    }

    .text-danger {
        color: #ef4444;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
        }

        .card-body {
            padding: 1.25rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .gallery-item img {
            width: 120px;
            height: 90px;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<?php require APPROOT . '/views/inc/landlord_footer.php'; ?>