<?php include_once '../app/views/includes/header.php'; ?>

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
  <span class="d-block text-primary fs-3 fw-semibold mb-2 fade-in-up" style="animation-delay: 0.2s;">Welcome to MaVetCare!</span>
  <span class="fade-in-up" style="animation-delay: 0.4s;">Providing Compassionate Care for Your Pets</span>
</h1>

        <p class="lead fade-in-up" style="animation-delay: 0.2s;">
          At MavetCare, we treat your pets like family. From preventive care to advanced treatments, we’re dedicated to keeping them healthy, happy, and safe.
        </p>
        <a href="#" class="btn btn-primary mt-3 fade-in-up" style="animation-delay: 0.4s;">Read More</a>
      </div>

      <!-- Image -->
      <div class="col-md-6">
        <img src="/assets/images/homepage_cat.png" class="img-fluid rounded fade-in-up" alt="Dog and cat with stethoscope" style="animation-delay: 0.6s;">
      </div>

    </div>
  </div>
</section>


<!-- Divider -->
<div class="text-center py-4">
  <div class="d-flex justify-content-center align-items-center">
    <hr class="flex-grow-1 mx-2">
    <span class="fs-4">🐾 Get to know us more 🐾</span>
    <hr class="flex-grow-1 mx-2">
  </div>
</div>

<!-- Info Section -->
<section class="py-5">
  <div class="container">
    <div class="row g-4">

      <!-- Map -->
      <div class="col-md-4">
        <div class="card h-100 shadow-lg rounded-4 card-hover">
          <div class="card-body">
            <h3 class="card-title">Explore</h3>
            <div class="ratio ratio-4x3 rounded">
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d497.3607081363063!2d123.91190659568983!3d10.319147106849568!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a9996cc4806e69%3A0x98c24f76a1744f65!2sFil-Chi%20Animal%20Clinic!5e0!3m2!1sen!2sph!4v1745573059437!5m2!1sen!2sph" 
                style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
        </div>
      </div>

      <!-- Address -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm rounded-4 card-hover">
          <div class="card-body">
            <h3 class="card-title">Address</h3>
            <p><strong>Mabolo Veterinary Medical Clinic</strong><br>
              Juan Luna Ave, Mabolo,<br>
              Cebu City, Philippines.</p>

            <h3 class="card-title mt-4">Contact Details</h3>
            <p class="text-primary">
              <i class="bi bi-telephone-fill me-2"></i> 233-20-39<br>
              <i class="bi bi-telephone-fill me-2"></i> 0916-295-8059<br>
              <i class="bi bi-telephone-fill me-2"></i> 0956-734-6746<br>
              <i class="bi bi-envelope-fill me-2"></i> <a href="mailto:mavetcare@email.com" class="text-primary text-decoration-none">deliamontanez92@email.com</a>
            </p>
          </div>
        </div>
      </div>

      <!-- Operating Hours -->
      <div class="col-md-4">
        <div class="card h-100 shadow-lg rounded-4 card-hover">
          <div class="card-body">
            <h3 class="card-title">Operating Hours</h3>
            <ul class="list-unstyled">
              <li><strong>Monday to Friday:</strong> 9:00 AM – 6:00 PM</li>
              <li><strong>Saturday:</strong> 9:00 AM – 5:00 PM</li>
              <li><strong>Sunday:</strong> Closed</li>
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
</style>
