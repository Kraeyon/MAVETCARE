<?php include_once '../app/views/includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MavetCare - Veterinary Medical Clinic</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Hero Section -->
<section class="py-5 bg-light text-center">
  <div class="container">
    <div class="row align-items-center">
      
      <div class="col-md-6 mb-4 mb-md-0">
        <h1 class="display-5 fw-bold">Providing Compassionate Care for Your Pets</h1>
        <p class="lead">At MavetCare, we treat your pets like family. From preventive care to advanced treatments, we’re dedicated to keeping them healthy, happy, and safe.</p>
        <a href="#" class="btn btn-primary">Read More</a>
      </div>

      <div class="col-md-6">
        <img src="/assets/images/homepage_cat.png" class="img-fluid rounded" alt="Dog and cat with stethoscope">
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
        <div class="card h-100">
          <div class="card-body">
            <h3 class="card-title">Explore</h3>
            <div class="ratio ratio-4x3">
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d497.3607081363063!2d123.91190659568983!3d10.319147106849568!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a9996cc4806e69%3A0x98c24f76a1744f65!2sFil-Chi%20Animal%20Clinic!5e0!3m2!1sen!2sph!4v1745573059437!5m2!1sen!2sph" 
                style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
        </div>
      </div>

      <!-- Address -->
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body">
            <h3 class="card-title">Address</h3>
            <p><strong>MavetCare Veterinary Medical Clinic</strong><br>
              Juan Luna Ave, Mabolo,<br>
              Cebu City, Philippines.</p>

            <h3 class="card-title mt-4">Contact Details</h3>
            <p>
              📞 233-20-39<br>
              📞 0916-295-8059<br>
              📞 0956-734-6746<br>
              ✉️ <a href="mailto:mavetcare@email.com">deliamontanez92@email.com</a>
            </p>
          </div>
        </div>
      </div>

      <!-- Operating Hours -->
      <div class="col-md-4">
        <div class="card h-100">
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
