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

    <!-- Right text -->
    <div class="col-md-6" data-aos="fade-right" data-aos-delay="100">
        <h1 class="display-4 fw-bold">Look for the services that you need.</h1>
        <p class="lead">Whether it’s a check-up or a special procedure, we’ve got your furry friend covered.</p>
      </div>

      <!-- Left image -->
      <div class="col-md-6 text-center text-md-start" data-aos="fade-left">
        <img src="/assets/images/services_dog&cat.png" alt="Vet clinic" class="img-fluid">
      </div>
      </div>
  </div>
</section>

<!-- What Do We Offer Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php 
            // Updated services array with titles for overlay text
            $services = [
                ["title" => "Vaccinations", "image" => "/assets/images/vaccination.png", "text" => "Vaccinations"],
                ["title" => "Deworming", "image" => "/assets/images/deworming.png", "text" => "Deworming"],
                ["title" => "Anti Parasitic Program", "image" => "/assets/images/anti parasitic program.png", "text" => "Anti Parasitic Program"],
                ["title" => "Surgeries", "image" => "/assets/images/surgery.png", "text" => "Surgeries"],
                ["title" => "Grooming", "image" => "/assets/images/grooming.png", "text" => "Grooming"],
                ["title" => "Treatment", "image" => "/assets/images/treatment.png", "text" => "Treatment"],
                ["title" => "Confinement", "image" => "/assets/images/confinement.png", "text" => "Confinement"]

            ];
            ?>
            <?php foreach($services as $service): ?>
            <div class="col-md-3">
                <div class="card h-100 position-relative service-card">
                    <img src="<?php echo htmlspecialchars($service['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($service['title']); ?>">
                    <div class="overlay position-absolute top-50 start-50 translate-middle text-center">
                        <h5 class="text-white"><?php echo htmlspecialchars($service['text']); ?></h5>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AOS Animation JS -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init();
</script>


</body>
</html>


<?php include_once '../app/views/includes/footer.php'; ?>