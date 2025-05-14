<?php include '../includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Pet - MaVetCare</title>
    <link rel="stylesheet" href="../assets/css/appointmentpage.css"> <!-- Reuse appointment CSS -->
    <style>
        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        
        .success-message {
            color: #28a745;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <?php include_once '../app/views/includes/header.php'; ?>

    <section class="py-5 bg-light text-center">
        <div class="container">
            <div class="row align-items-center">
                <!-- Text Content -->
                <div class="col-md-6 mb-4 mb-md-0 text-md-start text-center">
                    <h1 class="display-5 fw-bold mb-3">
                        <span class="d-block text-primary fs-3 fw-semibold mb-2 fade-in-up" style="animation-delay: 0.2s;">
                            Welcome, <?php echo htmlspecialchars($user['name']); ?>!
                        </span>
                        <span class="fade-in-up" style="animation-delay: 0.4s;">
                            Add Your Pet
                        </span>
                    </h1>

                    <p class="lead fade-in-up" style="animation-delay: 0.6s;">
                        Before booking an appointment, please add your pet's information.
                    </p>
                </div>

                <!-- Image -->
                <div class="col-md-6">
                    <img src="../assets/images/services_dog&cat.png" class="img-fluid rounded fade-in-up" alt="Pet" style="animation-delay: 1s;">
                </div>
            </div>
        </div>
    </section>

    <!-- Add Pet Form Section -->
    <section id="add-pet-section">
        <div class="appointment-header">
            <span class="paw-icon">🐾</span>
            <h1>Add Pet Information</h1>
            <span class="paw-icon">🐾</span>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form id="add-pet-form" method="POST" action="/add-pet">
            <!-- Pet Name -->
            <label for="pet_name">Pet Name:</label>
            <input type="text" id="pet_name" name="pet_name" value="<?php echo isset($pet_name) ? htmlspecialchars($pet_name) : ''; ?>" required>

            <!-- Pet Type -->
            <label for="pet_type">Pet Type:</label>
            <select id="pet_type" name="pet_type" required>
                <option value="">-- Select Pet Type --</option>
                <option value="Dog" <?php echo (isset($pet_type) && $pet_type === 'Dog') ? 'selected' : ''; ?>>Dog</option>
                <option value="Cat" <?php echo (isset($pet_type) && $pet_type === 'Cat') ? 'selected' : ''; ?>>Cat</option>
                <option value="Bird" <?php echo (isset($pet_type) && $pet_type === 'Bird') ? 'selected' : ''; ?>>Bird</option>
                <option value="Rabbit" <?php echo (isset($pet_type) && $pet_type === 'Rabbit') ? 'selected' : ''; ?>>Rabbit</option>
                <option value="Hamster/Guinea Pig" <?php echo (isset($pet_type) && $pet_type === 'Hamster/Guinea Pig') ? 'selected' : ''; ?>>Hamster/Guinea Pig</option>
                <option value="Reptile" <?php echo (isset($pet_type) && $pet_type === 'Reptile') ? 'selected' : ''; ?>>Reptile</option>
                <option value="Fish" <?php echo (isset($pet_type) && $pet_type === 'Fish') ? 'selected' : ''; ?>>Fish</option>
                <option value="Other" <?php echo (isset($pet_type) && $pet_type === 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>

            <!-- Pet Breed -->
            <label for="pet_breed">Pet Breed:</label>
            <input type="text" id="pet_breed" name="pet_breed" value="<?php echo isset($pet_breed) ? htmlspecialchars($pet_breed) : ''; ?>">

            <!-- Pet Age -->
            <label for="pet_age">Pet Age (years):</label>
            <input type="number" id="pet_age" name="pet_age" min="0" value="<?php echo isset($pet_age) ? htmlspecialchars($pet_age) : ''; ?>" required>

            <!-- Medical History -->
            <label for="pet_med_history">Medical History (optional):</label>
            <textarea id="pet_med_history" name="pet_med_history"><?php echo isset($pet_med_history) ? htmlspecialchars($pet_med_history) : ''; ?></textarea>

            <!-- Submit Button -->
            <button type="submit">Add Pet</button>
        </form>
    </section>

    <!-- Footer Section -->
    <?php include_once '../app/views/includes/footer.php'; ?>
</body>
</html> 