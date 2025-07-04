<!-- Bootstrap CSS (already included) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Back to Home Link (Top Left of Page) -->
<div class="position-absolute" style="top: 1rem; left: 1rem;">
  <a href="/" class="text-muted text-decoration-none small">← Back to Home</a>
</div>

<section class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
  <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
    <h2 class="text-center mb-4">Login to System</h2>

    <!-- Error Alert -->
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="/login">
      <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="text" class="form-control" id="email" name="email" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
      <div class="text-center mt-3">
        <a href="/register" class="small">Create an account</a>
      </div>
    </form>
  </div>
</section>
