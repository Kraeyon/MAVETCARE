<?php include_once '../app/views/includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MaVetCare - Reviews</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .fade-in {
      opacity: 0;
      transform: translateY(20px);
      animation: fadeInUp 0.6s ease-out forwards;
    }

    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .img-fluid.homepage-img {
      width: 100%;
      height: auto;
    }

    @media (max-width: 767px) {
      h1.display-5 {
        font-size: 2rem;
      }

      .img-fluid.homepage-img {
        max-height: 180px;
        object-fit: cover;
      }
    }
  </style>
</head>
<body>

<div class="container py-5">

  <!-- Header Section -->
  <div class="row mb-5 align-items-center fade-in">
    <div class="col-md-7 mb-4 mb-md-0">
      <h1 class="display-5 fw-bold">PAW-SITIVE REVIEWS</h1>
      <p class="lead">From loving pet parents</p>
      <button class="btn btn-outline-primary btn-sm">Read More</button>
    </div>
    <div class="col-md-5 text-center">
      <img src="/assets/images/homepage_cat.png" class="img-fluid homepage-img rounded" alt="Dog and cat with stethoscope">
    </div>
  </div>

  <!-- Divider -->
  <div class="text-center mb-4 fade-in">
    <div class="d-flex justify-content-center align-items-center">
      <hr class="flex-grow-1 mx-2">
      <span class="fs-5">Ratings and Reviews</span>
      <hr class="flex-grow-1 mx-2">
    </div>
  </div>

  <!-- Average Rating -->
  <div class="text-center mb-5 fade-in">
    <div class="mb-2">
      <img src="/api/placeholder/70/70" alt="User Avatar" class="rounded-circle">
    </div>
    <h3>What do you think?</h3>
    <div id="average-stars" class="mb-3">
      <!-- Star icons go here via AJAX -->
    </div>
    <button class="btn btn-primary" id="write-review-btn">Write a Review</button>
  </div>

  <!-- Rating Distribution -->
  <div class="mb-5 fade-in" id="rating-distribution">
    <h4>Community Ratings</h4>
    <!-- AJAX will populate star bars here -->
  </div>

  <!-- Reviews List -->
  <div id="reviews-container" class="mb-5 fade-in">
    <!-- AJAX will inject review cards here -->
  </div>

  <!-- Review Form -->
  <div class="fade-in">
    <h4>Write a Review</h4>
    <form id="review-form">
      <div class="mb-3">
        <label for="rating" class="form-label">Rating</label>
        <select class="form-select" name="rating" id="rating">
          <option value="5" selected>5 - Excellent</option>
          <option value="4">4 - Good</option>
          <option value="3">3 - Average</option>
          <option value="2">2 - Poor</option>
          <option value="1">1 - Terrible</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="review" class="form-label">Review</label>
        <textarea class="form-control" id="review" name="review" rows="4" placeholder="Share your experience..."></textarea>
      </div>
      <button type="submit" class="btn btn-success">Post Review</button>
    </form>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Animate on scroll
  const faders = document.querySelectorAll('.fade-in');
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.animationDelay = '0.1s';
        entry.target.classList.add('visible');
      }
    });
  }, { threshold: 0.1 });

  faders.forEach(fadeEl => observer.observe(fadeEl));
</script>

</body>
</html>

<?php include_once '../app/views/includes/footer.php'; ?>
