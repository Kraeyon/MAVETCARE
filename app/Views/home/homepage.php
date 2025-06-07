<?php include_once '../app/views/includes/header.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MavetCare - Veterinary Medical Clinic</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"> <!-- Bootstrap Icons -->
</head>
<body>

<!-- Hero Section -->
<section class="py-5 bg-light text-center">
  <div class="container">
    <div class="row align-items-center">

      <!-- Text Content -->
      <div class="col-md-6 mb-4 mb-md-0">
      <h1 class="display-5 fw-bold mb-3">
  <span class="d-block text-primary fs-3 fw-semibold mb-2 fade-in-up" style="animation-delay: 0.2s;">Welcome to Mabolo Veterinary!</span>
  <span class="fade-in-up" style="animation-delay: 0.4s;">Providing Compassionate Care for Your Pets</span>
</h1>

        <p class="lead fade-in-up" style="animation-delay: 0.2s;">
          At Mabolo Veterinary Medical OPC, we treat your pets like family. From preventive care to advanced treatments, we're dedicated to keeping them healthy, happy, and safe.
        </p>
        <a href="/about" class="btn btn-primary btn-lg mt-3 fade-in-up shadow-sm" style="animation-delay: 0.4s;">Read More</a>
      </div>

      <!-- Image -->
      <div class="col-md-6">
        <img src="/assets/images/homepage_cat.png" class="img-fluid rounded shadow fade-in-up" alt="Dog and cat with stethoscope" style="animation-delay: 0.6s;">
      </div>

    </div>
  </div>
</section>


<!-- Divider -->
<div class="text-center py-4">
  <div class="d-flex justify-content-center align-items-center">
    <hr class="flex-grow-1 mx-3">
    <span class="fs-4 text-primary fw-semibold">🐾 Get to know us more 🐾</span>
    <hr class="flex-grow-1 mx-3">
  </div>
</div>

<!-- Info Section -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="row g-4">

      <!-- Map -->
      <div class="col-md-4">
        <div class="card h-100 border-0 shadow rounded-4 card-hover">
          <div class="card-body p-4">
            <h3 class="card-title fw-bold mb-3"><i class="bi bi-geo-alt-fill text-primary me-2"></i>Find Us</h3>
            <div class="ratio ratio-4x3 rounded overflow-hidden shadow-sm">
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3925.3032368660313!2d123.9166648!3d10.3207333!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a999bdc7b00af1%3A0xf9ee8a33875c3c82!2sMabolo%20Veterinary%20Clinic%20OPC!5e0!3m2!1sen!2sph!4v1714030381593!5m2!1sen!2sph"
                style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
        </div>
      </div>

      <!-- Address -->
      <div class="col-md-4">
        <div class="card h-100 border-0 shadow rounded-4 card-hover">
          <div class="card-body p-4">
            <h3 class="card-title fw-bold mb-3"><i class="bi bi-building text-primary me-2"></i>Address</h3>
            <p class="mb-0 fs-5"><strong class="text-primary">Mabolo Veterinary Medical OPC</strong></p>
            <p class="mb-4">
              2710 POPE JOHN PAUL II AVENUE,<br>
              MABOLO, CEBU CITY, Philippines
            </p>

            <h3 class="card-title fw-bold mb-3"><i class="bi bi-chat-dots-fill text-primary me-2"></i>Contact Us</h3>
            <ul class="list-unstyled">
              <li class="mb-2"><i class="bi bi-telephone-fill me-2 text-primary"></i> 233-20-39</li>
              <li class="mb-2"><i class="bi bi-telephone-fill me-2 text-primary"></i> 0916-295-8059</li>
              <li class="mb-2"><i class="bi bi-telephone-fill me-2 text-primary"></i> 0956-734-6746</li>
              <li><i class="bi bi-envelope-fill me-2 text-primary"></i> <a href="mailto:deliamontanez92@email.com" class="text-primary text-decoration-none">deliamontanez92@email.com</a></li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Operating Hours -->
      <div class="col-md-4">
        <div class="card h-100 border-0 shadow rounded-4 card-hover">
          <div class="card-body p-4">
            <h3 class="card-title fw-bold mb-3"><i class="bi bi-clock-fill text-primary me-2"></i>Operating Hours</h3>
            <ul class="list-unstyled fs-5">
              <li class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <strong>Monday to Friday</strong>
                </div>
                <div class="text-primary">9:00 AM – 6:00 PM</div>
              </li>
              <li class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <strong>Saturday</strong>
                </div>
                <div class="text-primary">9:00 AM – 5:00 PM</div>
              </li>
              <li>
                <div class="d-flex justify-content-between align-items-center">
                  <strong>Sunday</strong>
                </div>
                <div class="text-primary">Closed</div>
              </li>
            </ul>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include_once '../app/views/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<style>
  .card-hover:hover {
    transform: translateY(-8px);
    transition: transform 0.3s ease;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
  }
  .fade-in-up {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 1s ease-out forwards;
  }

  @keyframes fadeInUp {
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .fade-in-up[style*="animation-delay"] {
    animation-delay: inherit;
  }
  
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  
  .card {
    transition: all 0.3s ease;
  }
  
  .text-primary {
    color: #0d6efd !important;
  }
  
  .btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
    transition: all 0.3s ease;
  }
  
  .btn-primary:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
    transform: translateY(-2px);
  }
</style>
