<?php require APPROOT . '/views/inc/landlord_header.php'; ?>

<?php
// Determine listing type
$listingType = $data['property']->listing_type ?? 'rent';
$isMaintenanceProperty = ($listingType === 'maintenance');
?>

<!-- Page Header -->
<div class="page-header">
  <div class="header-left">
    <h1 class="page-title">Edit Property</h1>
    <p class="page-subtitle">Update property details</p>

    <!-- Listing Type Badge -->
    <?php if ($isMaintenanceProperty): ?>
      <span class="badge badge-warning" style="margin-top: 0.5rem;">
        <i class="fas fa-tools"></i> Maintenance Only Property
      </span>
    <?php else: ?>
      <span class="badge badge-success" style="margin-top: 0.5rem;">
        <i class="fas fa-home"></i> Rental Property
      </span>
    <?php endif; ?>
  </div>
  <div class="header-actions">
    <a href="<?php echo URLROOT; ?>/properties/index" class="btn btn-outline">
      <i class="fas fa-arrow-left"></i> Back to Properties
    </a>
  </div>
</div>

<!-- Edit Property Form -->
<div class="content-card">
  <div class="card-header">
    <h2 class="card-title">Property Information</h2>
    <?php if ($isMaintenanceProperty): ?>
      <span class="badge badge-info">Simplified Form (Maintenance)</span>
    <?php endif; ?>
  </div>
  <div class="card-body">
    <form id="editPropertyForm" method="POST" action="<?php echo URLROOT; ?>/properties/edit/<?php echo $data['property']->id; ?>" enctype="multipart/form-data" novalidate>

      <?php if ($isMaintenanceProperty): ?>

        <!-- MAINTENANCE PROPERTY FORM -->
        <div class="info-box">
          <i class="fas fa-info-circle"></i>
          <div>
            <h4>Maintenance Only Property</h4>
            <p>This property is tracked for maintenance purposes only and is not listed for rent.</p>
          </div>
        </div>

        <!-- Property Address -->
        <div class="form-group">
          <label class="form-label">Property Address *</label>
          <input type="text"
            class="form-control <?php echo !empty($data['address_err']) ? 'error' : ''; ?>"
            name="address"
            value="<?php echo htmlspecialchars($data['property']->address ?? ''); ?>">
          <?php if (!empty($data['address_err'])): ?>
            <span class="error-message"><?php echo $data['address_err']; ?></span>
          <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <!-- Property Type -->
          <div class="form-group">
            <label class="form-label">Property Type *</label>
            <select class="form-control <?php echo !empty($data['type_err']) ? 'error' : ''; ?>" name="property_type">
              <option value="">Select Type</option>
              <option value="apartment" <?php echo ($data['property']->property_type ?? '') == 'apartment' ? 'selected' : ''; ?>>Apartment</option>
              <option value="house" <?php echo ($data['property']->property_type ?? '') == 'house' ? 'selected' : ''; ?>>House</option>
              <option value="condo" <?php echo ($data['property']->property_type ?? '') == 'condo' ? 'selected' : ''; ?>>Condo</option>
              <option value="townhouse" <?php echo ($data['property']->property_type ?? '') == 'townhouse' ? 'selected' : ''; ?>>Townhouse</option>
              <option value="commercial" <?php echo ($data['property']->property_type ?? '') == 'commercial' ? 'selected' : ''; ?>>Commercial</option>
              <option value="land" <?php echo ($data['property']->property_type ?? '') == 'land' ? 'selected' : ''; ?>>Land</option>
              <option value="other" <?php echo ($data['property']->property_type ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
            </select>
            <?php if (!empty($data['type_err'])): ?>
              <span class="error-message"><?php echo $data['type_err']; ?></span>
            <?php endif; ?>
          </div>

          <!-- Square Footage -->
          <div class="form-group">
            <label class="form-label">Square Footage (Optional)</label>
            <input type="number"
              class="form-control <?php echo !empty($data['sqft_err']) ? 'error' : ''; ?>"
              name="sqft"
              value="<?php echo htmlspecialchars($data['property']->sqft ?? ''); ?>"
              min="1" max="50000" step="1">
            <?php if (!empty($data['sqft_err'])): ?>
              <span class="error-message"><?php echo $data['sqft_err']; ?></span>
            <?php endif; ?>
          </div>
        </div>

        <!-- Current Occupant -->
        <div class="form-group">
          <label class="form-label">Current Occupant (Optional)</label>
          <input type="text"
            class="form-control"
            name="current_occupant"
            value="<?php echo htmlspecialchars($data['property']->current_occupant ?? ''); ?>"
            placeholder="Name of current tenant or occupant">
        </div>

        <!-- Property Notes -->
        <div class="form-group">
          <label class="form-label">Property Notes</label>
          <textarea class="form-control" name="description" rows="4"><?php echo htmlspecialchars($data['property']->description ?? ''); ?></textarea>
        </div>

        <!-- Hidden fields for maintenance properties -->
        <input type="hidden" name="bedrooms" value="<?php echo $data['property']->bedrooms ?? 0; ?>">
        <input type="hidden" name="bathrooms" value="<?php echo $data['property']->bathrooms ?? 1; ?>">
        <input type="hidden" name="rent" value="0">
        <input type="hidden" name="deposit" value="">
        <input type="hidden" name="available_date" value="">
        <input type="hidden" name="parking" value="0">
        <input type="hidden" name="pets" value="no">
        <input type="hidden" name="laundry" value="none">

      <?php else: ?>

        <!-- RENTAL PROPERTY FORM -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
          <!-- Left Column -->
          <div>
            <!-- Property Address -->
            <div class="form-group">
              <label class="form-label">Property Address *</label>
              <input type="text"
                class="form-control <?php echo !empty($data['address_err']) ? 'error' : ''; ?>"
                name="address"
                value="<?php echo htmlspecialchars($data['property']->address ?? ''); ?>">
              <?php if (!empty($data['address_err'])): ?>
                <span class="error-message"><?php echo $data['address_err']; ?></span>
              <?php endif; ?>
            </div>

            <!-- Property Type -->
            <div class="form-group">
              <label class="form-label">Property Type *</label>
              <select class="form-control <?php echo !empty($data['type_err']) ? 'error' : ''; ?>" name="property_type">
                <option value="">Select Type</option>
                <option value="apartment" <?php echo ($data['property']->property_type ?? '') == 'apartment' ? 'selected' : ''; ?>>Apartment</option>
                <option value="house" <?php echo ($data['property']->property_type ?? '') == 'house' ? 'selected' : ''; ?>>House</option>
                <option value="condo" <?php echo ($data['property']->property_type ?? '') == 'condo' ? 'selected' : ''; ?>>Condo</option>
                <option value="townhouse" <?php echo ($data['property']->property_type ?? '') == 'townhouse' ? 'selected' : ''; ?>>Townhouse</option>
              </select>
              <?php if (!empty($data['type_err'])): ?>
                <span class="error-message"><?php echo $data['type_err']; ?></span>
              <?php endif; ?>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
              <!-- Bedrooms -->
              <div class="form-group">
                <label class="form-label">Bedrooms *</label>
                <select class="form-control <?php echo !empty($data['bedrooms_err']) ? 'error' : ''; ?>" name="bedrooms">
                  <option value="">Select</option>
                  <option value="0" <?php echo ($data['property']->bedrooms ?? '') == '0' ? 'selected' : ''; ?>>Studio</option>
                  <option value="1" <?php echo ($data['property']->bedrooms ?? '') == '1' ? 'selected' : ''; ?>>1 Bedroom</option>
                  <option value="2" <?php echo ($data['property']->bedrooms ?? '') == '2' ? 'selected' : ''; ?>>2 Bedrooms</option>
                  <option value="3" <?php echo ($data['property']->bedrooms ?? '') == '3' ? 'selected' : ''; ?>>3 Bedrooms</option>
                  <option value="4" <?php echo ($data['property']->bedrooms ?? '') >= '4' ? 'selected' : ''; ?>>4+ Bedrooms</option>
                </select>
                <?php if (!empty($data['bedrooms_err'])): ?>
                  <span class="error-message"><?php echo $data['bedrooms_err']; ?></span>
                <?php endif; ?>
              </div>

              <!-- Bathrooms -->
              <div class="form-group">
                <label class="form-label">Bathrooms *</label>
                <select class="form-control <?php echo !empty($data['bathrooms_err']) ? 'error' : ''; ?>" name="bathrooms">
                  <option value="">Select</option>
                  <option value="1" <?php echo ($data['property']->bathrooms ?? '') == '1' ? 'selected' : ''; ?>>1 Bathroom</option>
                  <option value="2" <?php echo ($data['property']->bathrooms ?? '') == '2' ? 'selected' : ''; ?>>2 Bathrooms</option>
                  <option value="3" <?php echo ($data['property']->bathrooms ?? '') >= '3' ? 'selected' : ''; ?>>3+ Bathrooms</option>
                </select>
                <?php if (!empty($data['bathrooms_err'])): ?>
                  <span class="error-message"><?php echo $data['bathrooms_err']; ?></span>
                <?php endif; ?>
              </div>
            </div>

            <!-- Square Footage -->
            <div class="form-group">
              <label class="form-label">Square Footage</label>
              <input type="number"
                class="form-control <?php echo !empty($data['sqft_err']) ? 'error' : ''; ?>"
                name="sqft"
                value="<?php echo htmlspecialchars($data['property']->sqft ?? ''); ?>"
                min="1" max="50000" step="1">
              <?php if (!empty($data['sqft_err'])): ?>
                <span class="error-message"><?php echo $data['sqft_err']; ?></span>
              <?php endif; ?>
            </div>

            <!-- Monthly Rent -->
            <div class="form-group">
              <label class="form-label">Monthly Rent (Rs) *</label>
              <input type="number"
                step="0.01"
                class="form-control <?php echo !empty($data['rent_err']) ? 'error' : ''; ?>"
                name="rent"
                value="<?php echo htmlspecialchars($data['property']->rent ?? ''); ?>"
                min="1000" max="10000000">
              <?php if (!empty($data['rent_err'])): ?>
                <span class="error-message"><?php echo $data['rent_err']; ?></span>
              <?php endif; ?>
            </div>
          </div>

          <!-- Right Column -->
          <div>
            <!-- Security Deposit -->
            <div class="form-group">
              <label class="form-label">Security Deposit (Rs)</label>
              <input type="number"
                class="form-control <?php echo !empty($data['deposit_err']) ? 'error' : ''; ?>"
                name="deposit"
                value="<?php echo htmlspecialchars($data['property']->deposit ?? ''); ?>"
                min="0" max="10000000" step="100">
              <?php if (!empty($data['deposit_err'])): ?>
                <span class="error-message"><?php echo $data['deposit_err']; ?></span>
              <?php endif; ?>
            </div>

            <!-- Available Date -->
            <div class="form-group">
              <label class="form-label">Available Date</label>
              <input type="date"
                class="form-control"
                name="available_date"
                value="<?php echo htmlspecialchars($data['property']->available_date ?? ''); ?>">
            </div>

            <!-- Parking Spaces -->
            <div class="form-group">
              <label class="form-label">Parking Spaces</label>
              <select class="form-control" name="parking">
                <option value="0" <?php echo ($data['property']->parking ?? '0') == '0' ? 'selected' : ''; ?>>No Parking</option>
                <option value="1" <?php echo ($data['property']->parking ?? '') == '1' ? 'selected' : ''; ?>>1 Space</option>
                <option value="2" <?php echo ($data['property']->parking ?? '') == '2' ? 'selected' : ''; ?>>2 Spaces</option>
                <option value="3" <?php echo ($data['property']->parking ?? '') == '3' ? 'selected' : ''; ?>>3+ Spaces</option>
              </select>
            </div>

            <!-- Pet Policy -->
            <div class="form-group">
              <label class="form-label">Pet Policy</label>
              <select class="form-control" name="pets">
                <option value="no" <?php echo ($data['property']->pet_policy ?? '') == 'no' ? 'selected' : ''; ?>>No Pets</option>
                <option value="cats" <?php echo ($data['property']->pet_policy ?? '') == 'cats' ? 'selected' : ''; ?>>Cats Only</option>
                <option value="dogs" <?php echo ($data['property']->pet_policy ?? '') == 'dogs' ? 'selected' : ''; ?>>Dogs Only</option>
                <option value="both" <?php echo ($data['property']->pet_policy ?? '') == 'both' ? 'selected' : ''; ?>>Cats & Dogs</option>
              </select>
            </div>

            <!-- Laundry Facilities -->
            <div class="form-group">
              <label class="form-label">Laundry Facilities</label>
              <select class="form-control" name="laundry">
                <option value="none" <?php echo ($data['property']->laundry ?? '') == 'none' ? 'selected' : ''; ?>>No Laundry</option>
                <option value="shared" <?php echo ($data['property']->laundry ?? '') == 'shared' ? 'selected' : ''; ?>>Shared Laundry</option>
                <option value="hookups" <?php echo ($data['property']->laundry ?? '') == 'hookups' ? 'selected' : ''; ?>>Washer/Dryer Hookups</option>
                <option value="in_unit" <?php echo ($data['property']->laundry ?? '') == 'in_unit' ? 'selected' : ''; ?>>In-Unit Washer/Dryer</option>
                <option value="included" <?php echo ($data['property']->laundry ?? '') == 'included' ? 'selected' : ''; ?>>Washer/Dryer Included</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Property Description -->
        <div class="form-group">
          <label class="form-label">Property Description</label>
          <textarea class="form-control" name="description" rows="4"><?php echo htmlspecialchars($data['property']->description ?? ''); ?></textarea>
        </div>

      <?php endif; ?>

      <!-- ========================================== -->
      <!-- EXISTING PROPERTY IMAGES SECTION -->
      <!-- ========================================== -->
      <?php if (!empty($data['property']->images)): ?>
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-images"></i> Current Property Images
          </label>
          <div class="existing-images-container">
            <div class="existing-images-grid">
              <?php foreach ($data['property']->images as $index => $image): ?>
                <div class="existing-image-item" id="existing-image-<?php echo $index; ?>">
                  <img src="<?php echo $image['url']; ?>" alt="Property Image">

                  <!-- ✅ DELETE BUTTON -->
                  <button type="button"
                    class="delete-existing-btn"
                    onclick="deleteExistingImage('<?php echo $image['name']; ?>', <?php echo $data['property']->id; ?>, <?php echo $index; ?>)"
                    title="Delete this image">
                    <i class="fas fa-trash-alt"></i>
                  </button>

                  <div class="image-info">
                    <span class="image-name"><?php echo htmlspecialchars($image['name']); ?></span>
                    <span class="image-size"><?php echo number_format($image['size'] / 1024, 1); ?> KB</span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <p class="help-text">
              <i class="fas fa-info-circle"></i>
              Click the trash icon to delete an image. Upload new images below to add more photos.
            </p>
          </div>
        </div>
      <?php endif; ?>

      <!-- ========================================== -->
      <!-- ADD NEW PROPERTY IMAGES SECTION -->
      <!-- ========================================== -->
      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-cloud-upload-alt"></i> Add New Property Images (Optional)
        </label>
        <div class="upload-zone" id="uploadZone">
          <div class="upload-icon">
            <i class="fas fa-cloud-upload-alt"></i>
          </div>
          <div class="upload-text">
            <h4>Drag & Drop Images Here</h4>
            <p>or click to browse files</p>
            <small>Max 5 images • 2MB per image • JPG, PNG, GIF, WebP</small>
          </div>
          <input type="file" name="photos[]" multiple accept="image/*" id="photoInput" style="display: none;">
        </div>

        <!-- Image Preview Container -->
        <div id="imagePreviewContainer" class="image-preview-container" style="display: none;">
          <div class="preview-header">
            <h4>New Images to Upload (<span id="imageCount">0</span>/5)</h4>
            <div class="preview-actions">
              <span id="totalSizeDisplay" class="size-display">Total: 0 MB</span>
              <button type="button" onclick="clearImages()" class="btn btn-outline btn-sm">Clear All</button>
            </div>
          </div>
          <div id="imagePreviewGrid" class="image-preview-grid"></div>
          <div id="uploadErrors" class="upload-errors" style="display: none;"></div>
        </div>
      </div>

      <!-- ========================================== -->
      <!-- EXISTING PROPERTY DOCUMENTS SECTION -->
      <!-- ========================================== -->
      <?php if (!empty($data['property']->documents)): ?>
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-file-alt"></i> Current Property Documents
          </label>
          <div class="existing-documents-container">
            <div class="existing-documents-grid">
              <?php foreach ($data['property']->documents as $index => $document): ?>
                <div class="existing-document-item" id="existing-document-<?php echo $index; ?>">
                  <div class="doc-icon <?php echo $document['type']; ?>">
                    <?php
                    $iconClass = 'fas fa-file';
                    if ($document['type'] === 'pdf') $iconClass = 'fas fa-file-pdf';
                    elseif (in_array($document['type'], ['doc', 'docx'])) $iconClass = 'fas fa-file-word';
                    elseif (in_array($document['type'], ['jpg', 'jpeg', 'png', 'gif'])) $iconClass = 'fas fa-file-image';
                    ?>
                    <i class="<?php echo $iconClass; ?>"></i>
                  </div>

                  <div class="document-info">
                    <!-- ✅ ADDED title ATTRIBUTE FOR TOOLTIP -->
                    <span class="document-name" title="<?php echo htmlspecialchars($document['name']); ?>">
                      <?php echo htmlspecialchars($document['name']); ?>
                    </span>
                    <span class="document-size"><?php echo number_format($document['size'] / 1024, 1); ?> KB</span>
                    <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                      <a href="<?php echo $document['url']; ?>" target="_blank" class="btn-view-doc">
                        <i class="fas fa-external-link-alt"></i> View
                      </a>

                      <!-- ✅ DELETE BUTTON -->
                      <button type="button"
                        class="btn-delete-doc"
                        onclick="deleteExistingDocument('<?php echo $document['name']; ?>', <?php echo $data['property']->id; ?>, <?php echo $index; ?>)"
                        title="Delete this document">
                        <i class="fas fa-trash-alt"></i> Delete
                      </button>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <p class="help-text">
              <i class="fas fa-info-circle"></i>
              Click "Delete" to remove a document. Upload new documents below to add more files.
            </p>
          </div>
        </div>
      <?php endif; ?>

      <!-- ========================================== -->
      <!-- ADD NEW PROPERTY DOCUMENTS SECTION -->
      <!-- ========================================== -->
      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-file-upload"></i> Add New Property Documents (Optional)
        </label>
        <div class="document-upload-zone" id="documentUploadZone">
          <div class="upload-icon">
            <i class="fas fa-file-upload"></i>
          </div>
          <div class="upload-text">
            <h4>Upload Property Documents</h4>
            <p>Agreements, floor plans, etc.</p>
            <small>Max 3 documents • 5MB per file • PDF, DOC, DOCX, JPG, PNG</small>
          </div>
          <input type="file" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif" id="documentInput" style="display: none;">
        </div>

        <!-- Document Preview Container -->
        <div id="documentPreviewContainer" class="document-preview-container" style="display: none;">
          <div class="preview-header">
            <h4>New Documents to Upload (<span id="documentCount">0</span>/3)</h4>
            <div class="preview-actions">
              <span id="documentSizeDisplay" class="size-display">Total: 0 MB</span>
              <button type="button" onclick="clearDocuments()" class="btn btn-outline btn-sm">Clear All</button>
            </div>
          </div>
          <div id="documentPreviewGrid" class="document-preview-grid"></div>
          <div id="documentErrors" class="upload-errors" style="display: none;"></div>
        </div>
      </div>

      <!-- Submit Buttons -->
      <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
        <button type="submit" class="btn btn-primary" id="submitBtn">
          <i class="fas fa-save"></i> Update Property
        </button>
        <a href="<?php echo URLROOT; ?>/properties/index" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<style>
  /* Info Box */
  .info-box {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    border: 2px solid #60a5fa;
    border-radius: 0.75rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
  }

  .info-box>i {
    font-size: 1.5rem;
    color: #2563eb;
    flex-shrink: 0;
    margin-top: 0.25rem;
  }

  .info-box h4 {
    margin: 0 0 0.5rem 0;
    color: #1d4ed8;
    font-size: 1rem;
  }

  .info-box p {
    margin: 0;
    color: #1e3a8a;
    font-size: 0.875rem;
  }

  /* Badge Styles */
  .badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.875rem;
    border-radius: 0.375rem;
    font-size: 0.813rem;
    font-weight: 600;
  }

  .badge-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
  }

  .badge-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
  }

  .badge-info {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
  }

  /* Error Messages */
  .error-message {
    color: #dc2626;
    font-size: 0.813rem;
    margin-top: 0.25rem;
    display: block;
  }

  .form-control.error {
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
  }

  /* Existing Images/Documents Display */
  .existing-images-container,
  .existing-documents-container {
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.5rem;
    margin-bottom: 0.5rem;
  }

  .existing-images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
  }

  .existing-image-item {
    position: relative;
    border-radius: 0.5rem;
    overflow: hidden;
    border: 2px solid #d1d5db;
    background: white;
    transition: all 0.3s ease;
  }

  .existing-image-item:hover {
    border-color: #3b82f6;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }

  .existing-image-item img {
    width: 100%;
    aspect-ratio: 1;
    object-fit: cover;
  }

  .existing-image-item .image-info {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 0.5rem;
    font-size: 0.75rem;
  }

  /* Delete Button for Existing Images */
  .delete-existing-btn {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    background: rgba(239, 68, 68, 0.95);
    color: white;
    border: none;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.875rem;
    transition: all 0.3s;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  }

  .delete-existing-btn:hover {
    background: #dc2626;
    transform: scale(1.15);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.5);
  }

  /* Delete Button for Existing Documents */
  .btn-delete-doc {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
  }

  .btn-delete-doc:hover {
    background: #dc2626;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
  }

  /* Fade out animation */
  @keyframes fadeOut {
    from {
      opacity: 1;
      transform: scale(1);
    }

    to {
      opacity: 0;
      transform: scale(0.8);
    }
  }

  .deleting {
    animation: fadeOut 0.3s ease;
    pointer-events: none;
  }

  .image-name {
    display: block;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
    font-weight: 600;
  }

  .image-size,
  .document-size {
    display: block;
    opacity: 0.8;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #e5e7eb;
  }

  /* ✅ UPDATED DOCUMENTS GRID */
  .existing-documents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
  }

  /* ✅ UPDATED DOCUMENT ITEM */
  .existing-document-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border: 2px solid #d1d5db;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    overflow: hidden;
    min-width: 0;
  }

  .existing-document-item:hover {
    border-color: #f59e0b;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }

  .existing-document-item .doc-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #6b7280;
    flex-shrink: 0;
  }

  .existing-document-item .doc-icon.pdf {
    color: #dc2626;
  }

  .existing-document-item .doc-icon.doc,
  .existing-document-item .doc-icon.docx {
    color: #2563eb;
  }

  .existing-document-item .doc-icon.jpg,
  .existing-document-item .doc-icon.jpeg,
  .existing-document-item .doc-icon.png,
  .existing-document-item .doc-icon.gif {
    color: #10b981;
  }

  /* ✅ UPDATED DOCUMENT INFO */
  .document-info {
    flex: 1;
    min-width: 0;
    overflow: hidden;
  }

  /* ✅ UPDATED DOCUMENT NAME WITH TOOLTIP */
  .document-name {
    display: block;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
    font-weight: 600;
    cursor: help;
    position: relative;
    max-width: 100%;
    color: #374151;
  }

  /* ✅ TOOLTIP ON HOVER */
  .document-name:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 0;
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    white-space: normal;
    word-break: break-all;
    max-width: 300px;
    z-index: 1000;
    margin-bottom: 0.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    pointer-events: none;
  }

  .document-size {
    color: #6b7280;
  }

  .btn-view-doc {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    background: #3b82f6;
    color: white;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    text-decoration: none;
    transition: all 0.2s;
  }

  .btn-view-doc:hover {
    background: #2563eb;
    color: white;
  }

  .help-text {
    margin: 0;
    padding: 0.75rem;
    background: #eff6ff;
    border-left: 3px solid #3b82f6;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    color: #1e40af;
  }

  .help-text i {
    margin-right: 0.5rem;
  }

  /* Upload Zone Styles */
  .upload-zone,
  .document-upload-zone {
    border: 3px dashed #d1d5db;
    border-radius: 0.75rem;
    padding: 3rem 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fafafa;
  }

  .upload-zone:hover,
  .document-upload-zone:hover {
    border-color: #3b82f6;
    background: #f0f9ff;
  }

  .upload-zone.dragover {
    border-color: #10b981;
    background: #f0fdf4;
    transform: scale(1.02);
  }

  .document-upload-zone {
    border-color: #f59e0b;
    background: #fffbeb;
  }

  .document-upload-zone:hover {
    border-color: #d97706;
    background: #fef3c7;
  }

  .document-upload-zone.dragover {
    border-color: #d97706;
    background: #fef3c7;
    transform: scale(1.02);
  }

  .upload-icon {
    font-size: 3rem;
    color: #9ca3af;
    margin-bottom: 1rem;
  }

  .upload-text h4 {
    margin: 0 0 0.5rem 0;
    color: #374151;
    font-size: 1.25rem;
  }

  .upload-text p {
    margin: 0 0 0.5rem 0;
    color: #6b7280;
  }

  .upload-text small {
    color: #9ca3af;
    font-size: 0.813rem;
  }

  /* Preview Containers */
  .image-preview-container,
  .document-preview-container {
    border: 2px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.5rem;
    background: white;
    margin-top: 1rem;
  }

  .document-preview-container {
    border-color: #f59e0b;
  }

  .preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f3f4f6;
  }

  .preview-header h4 {
    margin: 0;
    color: #374151;
    font-size: 1.125rem;
  }

  .preview-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .size-display {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 600;
  }

  .image-preview-grid,
  .document-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
  }

  .document-preview-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  }

  .image-preview-item,
  .document-preview-item {
    position: relative;
    border-radius: 0.5rem;
    overflow: hidden;
    border: 2px solid #e5e7eb;
    background: white;
    transition: all 0.3s ease;
  }

  .image-preview-item {
    aspect-ratio: 1;
  }

  .document-preview-item {
    padding: 1rem;
    min-height: 120px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    border-color: #f59e0b;
    background: #fffbeb;
  }

  .image-preview-item:hover,
  .document-preview-item:hover {
    border-color: #3b82f6;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }

  .document-preview-item:hover {
    border-color: #f59e0b;
  }

  .image-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .image-preview-item .remove-btn,
  .document-preview-item .remove-btn {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: rgba(239, 68, 68, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.875rem;
    transition: all 0.2s;
  }

  .image-preview-item .remove-btn:hover,
  .document-preview-item .remove-btn:hover {
    background: #dc2626;
    transform: scale(1.1);
  }

  .document-preview-item .doc-icon {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    color: #f59e0b;
  }

  .document-preview-item .doc-icon.pdf {
    color: #dc2626;
  }

  .document-preview-item .doc-icon.doc,
  .document-preview-item .doc-icon.docx {
    color: #2563eb;
  }

  .document-preview-item .doc-icon.image {
    color: #10b981;
  }

  .image-preview-item .file-info {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 0.5rem;
    font-size: 0.75rem;
  }

  .file-name {
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
    font-weight: 600;
  }

  .file-size {
    opacity: 0.8;
    margin-top: 0.25rem;
  }

  .upload-errors {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-top: 1rem;
  }

  .upload-errors h5 {
    color: #dc2626;
    margin: 0 0 0.5rem 0;
    font-size: 0.875rem;
  }

  .upload-errors ul {
    margin: 0;
    padding-left: 1.25rem;
    color: #dc2626;
    font-size: 0.813rem;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .existing-images-grid {
      grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }

    .existing-documents-grid {
      grid-template-columns: 1fr;
    }

    .image-preview-grid {
      grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }

    .document-preview-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<script>
  let selectedFiles = [];
  let selectedDocuments = [];
  const MAX_FILES = 5;
  const MAX_DOCUMENTS = 3;
  const MAX_FILE_SIZE = 2 * 1024 * 1024;
  const MAX_DOCUMENT_SIZE = 5 * 1024 * 1024;
  const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
  const ALLOWED_DOCUMENT_TYPES = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'image/jpeg', 'image/jpg', 'image/png', 'image/gif'
  ];

  document.addEventListener('DOMContentLoaded', function() {
    initializeUpload();
    initializeDocumentUpload();
  });

  // Delete existing image
  function deleteExistingImage(imageName, propertyId, index) {
    if (!confirm('Are you sure you want to delete this image? This action cannot be undone.')) {
      return;
    }

    const imageElement = document.getElementById('existing-image-' + index);
    imageElement.classList.add('deleting');

    const formData = new FormData();
    formData.append('image_name', imageName);
    formData.append('property_id', propertyId);

    fetch('<?php echo URLROOT; ?>/properties/deleteImage', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          imageElement.remove();
          showNotification('Image deleted successfully', 'success');

          const container = document.querySelector('.existing-images-grid');
          if (container && container.children.length === 0) {
            document.querySelector('.existing-images-container').parentElement.remove();
          }
        } else {
          imageElement.classList.remove('deleting');
          showNotification(data.message || 'Failed to delete image', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        imageElement.classList.remove('deleting');
        showNotification('An error occurred while deleting the image', 'error');
      });
  }

  // Delete existing document
  function deleteExistingDocument(documentName, propertyId, index) {
    if (!confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
      return;
    }

    const documentElement = document.getElementById('existing-document-' + index);
    documentElement.classList.add('deleting');

    const formData = new FormData();
    formData.append('document_name', documentName);
    formData.append('property_id', propertyId);

    fetch('<?php echo URLROOT; ?>/properties/deleteDocument', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          documentElement.remove();
          showNotification('Document deleted successfully', 'success');

          const container = document.querySelector('.existing-documents-grid');
          if (container && container.children.length === 0) {
            document.querySelector('.existing-documents-container').parentElement.remove();
          }
        } else {
          documentElement.classList.remove('deleting');
          showNotification(data.message || 'Failed to delete document', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        documentElement.classList.remove('deleting');
        showNotification('An error occurred while deleting the document', 'error');
      });
  }

  // Show notification
  function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.textContent = message;

    const colors = {
      success: '#10b981',
      error: '#ef4444',
      warning: '#f59e0b',
      info: '#3b82f6'
    };

    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 1rem 1.5rem;
      background: ${colors[type] || colors.info};
      color: white;
      border-radius: 0.75rem;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      z-index: 10000;
      font-weight: 700;
      font-size: 1rem;
      animation: slideInRight 0.4s ease;
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
      notification.style.animation = 'slideOutRight 0.4s ease';
      setTimeout(() => notification.remove(), 400);
    }, 3000);
  }

  // Initialize image upload
  function initializeUpload() {
    const uploadZone = document.getElementById('uploadZone');
    const photoInput = document.getElementById('photoInput');

    uploadZone.addEventListener('click', () => photoInput.click());
    uploadZone.addEventListener('dragover', (e) => {
      e.preventDefault();
      uploadZone.classList.add('dragover');
    });
    uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('dragover'));
    uploadZone.addEventListener('drop', (e) => {
      e.preventDefault();
      uploadZone.classList.remove('dragover');
      handleFiles(e.dataTransfer.files);
    });
    photoInput.addEventListener('change', (e) => handleFiles(e.target.files));
  }

  // Initialize document upload
  function initializeDocumentUpload() {
    const documentUploadZone = document.getElementById('documentUploadZone');
    const documentInput = document.getElementById('documentInput');

    documentUploadZone.addEventListener('click', () => documentInput.click());
    documentUploadZone.addEventListener('dragover', (e) => {
      e.preventDefault();
      documentUploadZone.classList.add('dragover');
    });
    documentUploadZone.addEventListener('dragleave', () => documentUploadZone.classList.remove('dragover'));
    documentUploadZone.addEventListener('drop', (e) => {
      e.preventDefault();
      documentUploadZone.classList.remove('dragover');
      handleDocuments(e.dataTransfer.files);
    });
    documentInput.addEventListener('change', (e) => handleDocuments(e.target.files));
  }

  // Handle file selection
  function handleFiles(files) {
    const errors = [];
    const validFiles = [];

    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      if (selectedFiles.length + validFiles.length >= MAX_FILES) {
        errors.push(`Maximum ${MAX_FILES} images allowed`);
        break;
      }
      if (!ALLOWED_TYPES.includes(file.type.toLowerCase())) {
        errors.push(`${file.name}: Invalid file type`);
        continue;
      }
      if (file.size > MAX_FILE_SIZE) {
        errors.push(`${file.name}: File too large (max 2MB)`);
        continue;
      }
      validFiles.push(file);
    }

    selectedFiles = selectedFiles.concat(validFiles);
    updateFileInput();
    displayImagePreviews();
    displayUploadErrors(errors);
  }

  // Handle document selection
  function handleDocuments(files) {
    const errors = [];
    const validDocuments = [];

    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      if (selectedDocuments.length + validDocuments.length >= MAX_DOCUMENTS) {
        errors.push(`Maximum ${MAX_DOCUMENTS} documents allowed`);
        break;
      }
      if (!ALLOWED_DOCUMENT_TYPES.includes(file.type.toLowerCase())) {
        errors.push(`${file.name}: Invalid file type`);
        continue;
      }
      if (file.size > MAX_DOCUMENT_SIZE) {
        errors.push(`${file.name}: File too large (max 5MB)`);
        continue;
      }
      validDocuments.push(file);
    }

    selectedDocuments = selectedDocuments.concat(validDocuments);
    updateDocumentInput();
    displayDocumentPreviews();
    displayDocumentErrors(errors);
  }

  // Display image previews
  function displayImagePreviews() {
    const container = document.getElementById('imagePreviewContainer');
    const grid = document.getElementById('imagePreviewGrid');
    const countSpan = document.getElementById('imageCount');
    const sizeSpan = document.getElementById('totalSizeDisplay');

    if (selectedFiles.length === 0) {
      container.style.display = 'none';
      return;
    }

    container.style.display = 'block';
    grid.innerHTML = '';
    countSpan.textContent = selectedFiles.length;

    let totalSize = 0;
    selectedFiles.forEach((file, index) => {
      totalSize += file.size;
      const reader = new FileReader();
      reader.onload = function(event) {
        const div = document.createElement('div');
        div.className = 'image-preview-item';
        div.innerHTML = `
          <img src="${event.target.result}" alt="Preview">
          <button type="button" class="remove-btn" onclick="removeImage(${index})" title="Remove">×</button>
          <div class="file-info">
            <div class="file-name">${file.name}</div>
            <div class="file-size">${formatBytes(file.size)}</div>
          </div>
        `;
        grid.appendChild(div);
      };
      reader.readAsDataURL(file);
    });

    sizeSpan.textContent = `Total: ${formatBytes(totalSize)}`;
  }

  // Display document previews
  function displayDocumentPreviews() {
    const container = document.getElementById('documentPreviewContainer');
    const grid = document.getElementById('documentPreviewGrid');
    const countSpan = document.getElementById('documentCount');
    const sizeSpan = document.getElementById('documentSizeDisplay');

    if (selectedDocuments.length === 0) {
      container.style.display = 'none';
      return;
    }

    container.style.display = 'block';
    grid.innerHTML = '';
    countSpan.textContent = selectedDocuments.length;

    let totalSize = 0;
    selectedDocuments.forEach((file, index) => {
      totalSize += file.size;
      const div = document.createElement('div');
      div.className = 'document-preview-item';
      const extension = file.name.split('.').pop().toLowerCase();
      let iconClass = 'fas fa-file';
      let iconType = 'file';

      if (extension === 'pdf') {
        iconClass = 'fas fa-file-pdf';
        iconType = 'pdf';
      } else if (['doc', 'docx'].includes(extension)) {
        iconClass = 'fas fa-file-word';
        iconType = 'doc';
      } else if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
        iconClass = 'fas fa-file-image';
        iconType = 'image';
      }

      div.innerHTML = `
        <button type="button" class="remove-btn" onclick="removeDocument(${index})" title="Remove">×</button>
        <div class="doc-icon ${iconType}">
          <i class="${iconClass}"></i>
        </div>
        <div class="file-info">
          <div class="file-name">${file.name}</div>
          <div class="file-size">${formatBytes(file.size)}</div>
        </div>
      `;
      grid.appendChild(div);
    });

    sizeSpan.textContent = `Total: ${formatBytes(totalSize)}`;
  }

  function removeImage(index) {
    selectedFiles.splice(index, 1);
    updateFileInput();
    displayImagePreviews();
    hideUploadErrors();
  }

  function removeDocument(index) {
    selectedDocuments.splice(index, 1);
    updateDocumentInput();
    displayDocumentPreviews();
    hideDocumentErrors();
  }

  function clearImages() {
    selectedFiles = [];
    updateFileInput();
    displayImagePreviews();
    hideUploadErrors();
  }

  function clearDocuments() {
    selectedDocuments = [];
    updateDocumentInput();
    displayDocumentPreviews();
    hideDocumentErrors();
  }

  function updateFileInput() {
    const input = document.getElementById('photoInput');
    const dt = new DataTransfer();
    selectedFiles.forEach(file => dt.items.add(file));
    input.files = dt.files;
  }

  function updateDocumentInput() {
    const input = document.getElementById('documentInput');
    const dt = new DataTransfer();
    selectedDocuments.forEach(file => dt.items.add(file));
    input.files = dt.files;
  }

  function displayUploadErrors(errors) {
    const errorDiv = document.getElementById('uploadErrors');
    if (errors.length === 0) {
      errorDiv.style.display = 'none';
      return;
    }
    errorDiv.style.display = 'block';
    errorDiv.innerHTML = `
      <h5><i class="fas fa-exclamation-triangle"></i> Image Upload Issues:</h5>
      <ul>${errors.map(error => `<li>${error}</li>`).join('')}</ul>
    `;
  }

  function displayDocumentErrors(errors) {
    const errorDiv = document.getElementById('documentErrors');
    if (errors.length === 0) {
      errorDiv.style.display = 'none';
      return;
    }
    errorDiv.style.display = 'block';
    errorDiv.innerHTML = `
      <h5><i class="fas fa-exclamation-triangle"></i> Document Upload Issues:</h5>
      <ul>${errors.map(error => `<li>${error}</li>`).join('')}</ul>
    `;
  }

  function hideUploadErrors() {
    document.getElementById('uploadErrors').style.display = 'none';
  }

  function hideDocumentErrors() {
    document.getElementById('documentErrors').style.display = 'none';
  }

  function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }

  // Animation styles
  const animationStyle = document.createElement('style');
  animationStyle.textContent = `
    @keyframes slideInRight {
      from { opacity: 0; transform: translateX(100px); }
      to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideOutRight {
      from { opacity: 1; transform: translateX(0); }
      to { opacity: 0; transform: translateX(100px); }
    }
  `;
  document.head.appendChild(animationStyle);

  // Show loading state on submit
  document.getElementById('editPropertyForm').addEventListener('submit', function() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    submitBtn.disabled = true;
  });
</script>

<?php require APPROOT . '/views/inc/landlord_footer.php'; ?>