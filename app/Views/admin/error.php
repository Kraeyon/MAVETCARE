<?php include_once '../app/Views/includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/assets/images/paw.png">
    <title><?php echo htmlspecialchars($title ?? 'Error'); ?> - MavetCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <?php include_once '../app/Views/includes/sidebar.php'; ?>
        
        <div class="flex-grow-1 p-4" style="margin-top: 0;">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($title ?? 'Error'); ?></h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="bi bi-database-x text-danger" style="font-size: 3rem;"></i>
                        <p class="mt-3 fs-5"><?php echo htmlspecialchars($message ?? 'An error has occurred.'); ?></p>
                        <a href="<?php echo htmlspecialchars($back_url ?? '/admin'); ?>" class="btn btn-primary mt-3">
                            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 