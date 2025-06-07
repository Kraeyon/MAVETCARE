<?php include_once '../app/views/includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaVetCare - Professional Veterinary Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AOS Animation CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- stylesheet -->
    <link rel="stylesheet" href="/assets/css/services.css">

</head>
<body>

<!-- Hero Section -->
<section class="pt-0 pb-5 bg-light">
  <div class="container">
    <div class="row align-items-center">

    <!-- Left text -->
    <div class="col-md-6" data-aos="fade-right" data-aos-delay="100">
        <h1 class="display-4 fw-bold">Look for the services that you need.</h1>
        <p class="lead">Whether it's a check-up or a special procedure, we've got your furry friend covered.</p>
      </div>

      <!-- Right image -->
      <div class="col-md-6 text-center text-md-start" data-aos="fade-left">
        <img src="/assets/images/services_dog&cat.png" alt="Vet clinic" class="img-fluid">
      </div>
      </div>
  </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php if (empty($services)): ?>
                <div class="col-12 text-center">
                    <p>No services available at the moment. Please check back later.</p>
                </div>
            <?php else: ?>
                <?php foreach($services as $service): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 position-relative service-card" onclick="showServiceDetails('<?php echo htmlspecialchars($service['service_code']); ?>', '<?php echo htmlspecialchars(ucwords(strtolower($service['service_name']))); ?>', '<?php echo htmlspecialchars(addslashes($service['service_desc'])); ?>', '<?php echo number_format($service['service_fee'], 2); ?>', '<?php echo !empty($service['service_img']) ? htmlspecialchars($service['service_img']) : '/assets/images/services/default.png'; ?>')">
                        <img src="<?php echo !empty($service['service_img']) ? htmlspecialchars($service['service_img']) : '/assets/images/services/default.png'; ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars(ucwords(strtolower($service['service_name']))); ?>"
                             onerror="this.src='/assets/images/services/default.png'">
                        <div class="price-tag position-absolute">₱<?php echo number_format($service['service_fee'], 2); ?></div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars(ucwords(strtolower($service['service_name']))); ?></h5>
                            <?php if (!empty($service['service_desc'])): ?>
                                <p class="card-text small text-muted"><?php echo htmlspecialchars(substr($service['service_desc'], 0, 100) . (strlen($service['service_desc']) > 100 ? '...' : '')); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Service Details Modal -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceModalLabel">Service Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4 modal-image-container">
                    <img id="modalServiceImage" src="" alt="Service Image" class="img-fluid rounded">
                </div>
                <h3 id="modalServiceName" class="mb-3 text-center"></h3>
                <div class="d-flex justify-content-center mb-4">
                    <span id="modalServicePrice" class="badge bg-primary px-3 py-2 fs-5"></span>
                </div>
                <p id="modalServiceDescription" class="lead"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="bookServiceBtn" href="/appointment" class="btn btn-primary">Book Now</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AOS Animation JS -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init();

  function showServiceDetails(id, name, description, price, image) {
    // Set modal content
    document.getElementById('modalServiceName').textContent = name;
    document.getElementById('modalServicePrice').textContent = '₱' + price;
    document.getElementById('modalServiceDescription').textContent = description;
    document.getElementById('modalServiceImage').src = image;
    
    // Show the modal
    var serviceModal = new bootstrap.Modal(document.getElementById('serviceModal'));
    serviceModal.show();
  }
</script>

</body>
</html>


<?php include_once '../app/views/includes/footer.php'; ?>