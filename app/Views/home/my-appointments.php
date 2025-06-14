<?php include_once '../app/views/includes/header.php'; ?>

<style>
    /* Styles for the notes button */
    .view-notes-btn {
        transition: all 0.3s ease;
    }
    .view-notes-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    /* Modal content styles */
    #modalNotesContent {
        white-space: pre-line; /* Preserve line breaks */
        font-size: 1rem;
        line-height: 1.5;
        color: #333;
    }
    
    /* Fade-in animation for modal content */
    .modal.show .modal-content {
        animation: fadeInUp 0.3s;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Highlight animation for note cells */
    td .btn-outline-primary {
        position: relative;
    }
    td .btn-outline-primary::after {
        content: '';
        position: absolute;
        top: -4px;
        left: -4px;
        right: -4px;
        bottom: -4px;
        border-radius: 4px;
        border: 2px solid transparent;
        animation: pulse 2s infinite;
        pointer-events: none;
        display: none;
    }
    td .btn-outline-primary:hover::after {
        display: block;
    }
    
    @keyframes pulse {
        0% {
            border-color: rgba(13, 110, 253, 0);
            transform: scale(1);
        }
        50% {
            border-color: rgba(13, 110, 253, 0.5);
            transform: scale(1.03);
        }
        100% {
            border-color: rgba(13, 110, 253, 0);
            transform: scale(1);
        }
    }
</style>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<section class="py-5 bg-light text-center">
    <div class="container">
        <div class="row align-items-center">
            <!-- Text Content -->
            <div class="col-md-6 mb-4 mb-md-0 text-md-start text-center">
                <h1 class="display-5 fw-bold mb-3">
                    <span class="d-block text-primary fs-3 fw-semibold mb-2 fade-in-up" style="animation-delay: 0.2s;">
                        Hello, <?php echo htmlspecialchars($user['name']); ?>!
                    </span>
                    <span class="fade-in-up" style="animation-delay: 0.4s;">
                        My Appointments
                    </span>
                </h1>
                <p class="lead fade-in-up" style="animation-delay: 0.6s;">
                    View and track all your pet's appointments in one place.
                </p>
                <a href="/appointment" class="btn btn-primary mt-3 fade-in-up" style="animation-delay: 0.8s;">
                    Book New Appointment
                </a>
            </div>
            <!-- Image -->
            <div class="col-md-6">
                <img src="../assets/images/services_dog&cat.png" class="img-fluid rounded fade-in-up" alt="Pet Appointment" style="animation-delay: 1s;">
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-check text-primary me-2"></i> 
                        Your Appointment History
                    </h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Status
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item active" href="#" data-filter="all">All Appointments</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" data-filter="pending">Pending</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="confirmed">Confirmed</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="completed">Completed</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="cancelled">Cancelled</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($appointments)): ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-calendar-times fa-4x text-muted"></i>
                        </div>
                        <h4 class="text-muted">No appointments found</h4>
                        <p class="text-muted mb-4">You haven't booked any appointments yet.</p>
                        <a href="/appointment" class="btn btn-primary">Book Your First Appointment</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Pet</th>
                                    <th scope="col">Service</th>
                                    <th scope="col">Date & Time</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($appointments as $appointment): ?>
                                    <?php 
                                        // Determine status class based on appointment status
                                        $statusClass = '';
                                        $statusText = strtolower($appointment['status'] ?? 'pending');
                                        
                                        switch($statusText) {
                                            case 'confirmed':
                                                $statusClass = 'bg-success';
                                                $statusIcon = 'fa-check-circle';
                                                break;
                                            case 'completed':
                                                $statusClass = 'bg-info';
                                                $statusIcon = 'fa-check-double';
                                                break;
                                            case 'cancelled':
                                                $statusClass = 'bg-danger';
                                                $statusIcon = 'fa-times-circle';
                                                break;
                                            case 'pending':
                                            default:
                                                $statusClass = 'bg-warning';
                                                $statusIcon = 'fa-clock';
                                                break;
                                        }
                                    ?>
                                    <tr class="appointment-row" data-status="<?php echo htmlspecialchars($statusText); ?>">
                                        <td>
                                            <strong>#<?php echo htmlspecialchars($appointment['appt_code']); ?></strong>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($appointment['pet_name'] ?? 'N/A'); ?>
                                            <span class="text-muted d-block small">
                                                <?php echo htmlspecialchars($appointment['pet_type'] ?? ''); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($appointment['service'] ?? 'N/A'); ?></td>
                                        <td>
                                            <?php 
                                                $date = isset($appointment['appt_datetime']) 
                                                    ? date('M d, Y', strtotime($appointment['appt_datetime'])) 
                                                    : 'N/A';
                                                $time = isset($appointment['appt_datetime']) 
                                                    ? date('h:i A', strtotime($appointment['appt_datetime'])) 
                                                    : '';
                                                echo htmlspecialchars($date);
                                            ?>
                                            <span class="text-muted d-block small">
                                                <?php echo htmlspecialchars($time); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo htmlspecialchars(ucfirst(strtolower($appointment['appointment_type'] ?? 'N/A'))); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $statusClass; ?>">
                                                <i class="fas <?php echo $statusIcon; ?> me-1"></i>
                                                <?php echo htmlspecialchars(ucfirst($statusText)); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($appointment['additional_notes'])): ?>
                                                <button type="button" class="btn btn-sm btn-outline-primary view-notes-btn" 
                                                    onclick="showNotes('<?php echo htmlspecialchars(addslashes($appointment['additional_notes'])); ?>', '<?php echo htmlspecialchars($appointment['appt_code']); ?>')">
                                                    <i class="fas fa-sticky-note me-1"></i> View Notes
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Appointment Status Guide Section -->
<section class="py-4 bg-light">
    <div class="container">
        <h5 class="mb-4 text-primary">Appointment Status Guide</h5>
        
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <h5 class="card-title">Pending</h5>
                        <p class="card-text small text-muted">Your appointment has been submitted and is awaiting review by our staff.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <h5 class="card-title">Confirmed</h5>
                        <p class="card-text small text-muted">Your appointment has been reviewed and confirmed. Please arrive on time.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-check-double fa-2x"></i>
                        </div>
                        <h5 class="card-title">Completed</h5>
                        <p class="card-text small text-muted">The appointment has been successfully completed. Thank you for visiting!</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                        <h5 class="card-title">Cancelled</h5>
                        <p class="card-text small text-muted">This appointment has been cancelled. Please book a new one if needed.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Notes Modal -->
<div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="notesModalLabel">Appointment Notes</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="p-2 bg-light rounded">
          <p id="modalNotesContent" class="mb-0 p-2"></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Add this script at the end of the file -->
<script>
// Function to show notes modal with the right content
function showNotes(notes, apptId) {
    // Get the modal elements
    const modalTitle = document.getElementById('notesModalLabel');
    const modalContent = document.getElementById('modalNotesContent');
    
    // Set the content
    modalTitle.textContent = `Appointment #${apptId} Notes`;
    modalContent.textContent = notes;
    
    // Show the modal using Bootstrap's JavaScript API
    const notesModal = new bootstrap.Modal(document.getElementById('notesModal'));
    notesModal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Appointment filtering
    const filterLinks = document.querySelectorAll('[data-filter]');
    const appointmentRows = document.querySelectorAll('.appointment-row');
    
    filterLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update active state in dropdown
            filterLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            
            appointmentRows.forEach(row => {
                if (filter === 'all') {
                    row.style.display = '';
                } else {
                    const status = row.getAttribute('data-status');
                    row.style.display = (status === filter) ? '' : 'none';
                }
            });
        });
    });
});
</script>

<?php include_once '../app/views/includes/footer.php'; ?> 