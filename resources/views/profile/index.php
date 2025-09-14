<?php
/**
 * Profile Page View
 * Displays user profile with storage unit management
 */

// Check authentication first
if (!\StorageUnit\Models\User::isLoggedIn()) {
    header('Location: /signIn.php');
    exit;
}

$user = \StorageUnit\Models\User::getCurrentUser();
if (!$user) {
    header('Location: /signIn.php');
    exit;
}

// Include header
include __DIR__ . '/../header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">
                        <i class="fas fa-user-circle mr-2"></i>User Profile
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Profile Picture Section -->
                    <div class="row mb-4">
                        <div class="col-md-12 text-center">
                            <div class="profile-picture-container mb-3">
                                <div class="profile-picture-wrapper">
                                    <?php if ($user->getProfilePicture()): ?>
                                        <img src="/uploads/profiles/<?= htmlspecialchars($user->getProfilePicture()) ?>" 
                                             alt="Profile Picture" 
                                             class="profile-picture" 
                                             id="profilePicture">
                                    <?php else: ?>
                                        <div class="profile-picture-placeholder" id="profilePicturePlaceholder">
                                            <i class="fas fa-user fa-3x"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="profile-picture-actions mt-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('profilePictureInput').click()">
                                        <i class="fas fa-camera mr-2"></i>Change Picture
                                    </button>
                                    <?php if ($user->getProfilePicture()): ?>
                                    <button type="button" class="btn btn-outline-danger btn-sm ml-2" onclick="deleteProfilePicture()">
                                        <i class="fas fa-trash mr-2"></i>Remove Picture
                                    </button>
                                    <?php endif; ?>
                                </div>
                                <input type="file" id="profilePictureInput" accept="image/*" style="display: none;" onchange="uploadProfilePicture(this)">
                            </div>
                        </div>
                    </div>

                    <!-- User Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user mr-2"></i>Name
                                </label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($user->getName()) ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-envelope mr-2"></i>Email
                                </label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($user->getEmail()) ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Storage Unit Section -->
                    <div class="border-top pt-4">
                        <h4 class="mb-4">
                            <i class="fas fa-warehouse mr-2"></i>Storage Unit Information
                        </h4>
                        
                        <form id="storageUnitForm">
                            <div class="form-group">
                                <label for="storage_unit_name" class="form-label">
                                    <i class="fas fa-warehouse mr-2"></i>Storage Unit Name *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="storage_unit_name" 
                                       name="storage_unit_name" 
                                       value="<?= htmlspecialchars($user->getStorageUnitName() ?? '') ?>"
                                       placeholder="Enter storage unit name"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="storage_unit_address" class="form-label">
                                    <i class="fas fa-map-marker-alt mr-2"></i>Storage Unit Address *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="storage_unit_address" 
                                       name="storage_unit_address" 
                                       value="<?= htmlspecialchars($user->getStorageUnitAddress() ?? '') ?>"
                                       placeholder="Enter storage unit address"
                                       required>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Start typing to search for your storage unit location
                                </small>
                            </div>

                            <!-- Hidden fields for coordinates -->
                            <input type="hidden" id="storage_unit_latitude" name="storage_unit_latitude" value="<?= $user->getStorageUnitLatitude() ?? '' ?>">
                            <input type="hidden" id="storage_unit_longitude" name="storage_unit_longitude" value="<?= $user->getStorageUnitLongitude() ?? '' ?>">

                            <!-- Current Storage Unit Display -->
                            <?php if ($user->getStorageUnitName()): ?>
                            <div class="alert alert-info">
                                <h6 class="mb-2">
                                    <i class="fas fa-warehouse mr-2"></i>Current Storage Unit
                                </h6>
                                <p class="mb-1"><strong><?= htmlspecialchars($user->getStorageUnitName()) ?></strong></p>
                                <p class="mb-0 text-muted"><?= htmlspecialchars($user->getStorageUnitAddress()) ?></p>
                                <?php if ($user->getStorageUnitUpdatedAt()): ?>
                                <small class="text-muted">
                                    <i class="fas fa-clock mr-1"></i>
                                    Last updated: <?= date('M j, Y g:i A', strtotime($user->getStorageUnitUpdatedAt())) ?>
                                </small>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save mr-2"></i>Update Storage Unit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Google Maps API -->
<?php
$googleMapsConfig = include __DIR__ . '/../../config/app/google_maps.php';
$apiKey = $googleMapsConfig['api_key'];
$libraries = implode(',', $googleMapsConfig['libraries']);
$callback = $googleMapsConfig['callback'];
?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= $apiKey ?>&libraries=<?= $libraries ?>&callback=<?= $callback ?>" async defer></script>

<script>
// Initialize Google Maps Autocomplete
function initAutocomplete() {
    const addressInput = document.getElementById('storage_unit_address');
    const latitudeInput = document.getElementById('storage_unit_latitude');
    const longitudeInput = document.getElementById('storage_unit_longitude');
    
    // Create autocomplete instance
    const autocomplete = new google.maps.places.Autocomplete(addressInput, {
        types: ['establishment'],
        fields: ['name', 'formatted_address', 'geometry.location']
    });

    // Listen for place selection
    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();
        
        if (!place.geometry || !place.geometry.location) {
            console.log('No details available for input: ' + place.name);
            return;
        }

        // Update the address input with the formatted address
        addressInput.value = place.formatted_address;
        
        // Update hidden coordinate fields
        latitudeInput.value = place.geometry.location.lat();
        longitudeInput.value = place.geometry.location.lng();
        
        // Update the storage unit name if it's empty
        const nameInput = document.getElementById('storage_unit_name');
        if (!nameInput.value && place.name) {
            nameInput.value = place.name;
        }
    });
}

// Handle form submission
document.getElementById('storageUnitForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    // Show loading state
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
    submitButton.disabled = true;
    
    // Send AJAX request
    fetch('/index.php?script=profile&action=updateStorageUnit', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showAlert('success', data.message);
            
            // Update the current storage unit display
            updateStorageUnitDisplay(data.data);
            
            // Reload page after a short delay to show updated information
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while updating storage unit');
    })
    .finally(() => {
        // Reset button state
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
});

// Show alert message
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} mr-2"></i>
        ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    
    // Insert at the top of the card body
    const cardBody = document.querySelector('.card-body');
    cardBody.insertBefore(alertDiv, cardBody.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Update storage unit display
function updateStorageUnitDisplay(data) {
    const currentUnitDiv = document.querySelector('.alert-info');
    if (currentUnitDiv) {
        currentUnitDiv.querySelector('strong').textContent = data.name;
        currentUnitDiv.querySelector('p.text-muted').textContent = data.address;
    }
}

// Upload profile picture
function uploadProfilePicture(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            showAlert('danger', 'Please select a valid image file');
            return;
        }
        
        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            showAlert('danger', 'File size too large. Maximum size is 5MB');
            return;
        }
        
        const formData = new FormData();
        formData.append('profile_picture', file);
        
        // Show loading state
        const changeBtn = document.querySelector('button[onclick*="profilePictureInput"]');
        const originalText = changeBtn.innerHTML;
        changeBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
        changeBtn.disabled = true;
        
        fetch('/index.php?script=profile&action=uploadProfilePicture', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                updateProfilePictureDisplay(data.data.url);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred while uploading profile picture');
        })
        .finally(() => {
            // Reset button state
            changeBtn.innerHTML = originalText;
            changeBtn.disabled = false;
            // Clear file input
            input.value = '';
        });
    }
}

// Delete profile picture
function deleteProfilePicture() {
    if (!confirm('Are you sure you want to remove your profile picture?')) {
        return;
    }
    
    fetch('/index.php?script=profile&action=deleteProfilePicture', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            updateProfilePictureDisplay(null);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while deleting profile picture');
    });
}

// Update profile picture display
function updateProfilePictureDisplay(imageUrl) {
    const profilePicture = document.getElementById('profilePicture');
    const placeholder = document.getElementById('profilePicturePlaceholder');
    const removeBtn = document.querySelector('button[onclick="deleteProfilePicture()"]');
    
    if (imageUrl) {
        // Show image
        if (profilePicture) {
            profilePicture.src = imageUrl;
        } else {
            // Create image element
            const img = document.createElement('img');
            img.src = imageUrl;
            img.alt = 'Profile Picture';
            img.className = 'profile-picture';
            img.id = 'profilePicture';
            
            const wrapper = document.querySelector('.profile-picture-wrapper');
            wrapper.innerHTML = '';
            wrapper.appendChild(img);
        }
        
        // Hide placeholder
        if (placeholder) {
            placeholder.style.display = 'none';
        }
        
        // Show remove button
        if (removeBtn) {
            removeBtn.style.display = 'inline-block';
        } else {
            // Create remove button
            const actionsDiv = document.querySelector('.profile-picture-actions');
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn btn-outline-danger btn-sm ml-2';
            removeButton.onclick = deleteProfilePicture;
            removeButton.innerHTML = '<i class="fas fa-trash mr-2"></i>Remove Picture';
            actionsDiv.appendChild(removeButton);
        }
    } else {
        // Show placeholder
        if (placeholder) {
            placeholder.style.display = 'flex';
        }
        
        // Hide image
        if (profilePicture) {
            profilePicture.remove();
        }
        
        // Hide remove button
        if (removeBtn) {
            removeBtn.style.display = 'none';
        }
    }
}
</script>

<?php
// Include footer
include __DIR__ . '/../footer.php';
?>
