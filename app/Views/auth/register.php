<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Back to Home Link (Top Left of Page) -->
<div class="position-absolute" style="top: 1rem; left: 1rem;">
  <a href="/" class="text-muted text-decoration-none small">← Back to Home</a>
</div>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
  <div class="card p-4 shadow" style="width: 100%; max-width: 500px;">
    <h2 class="text-center mb-4">Create an Account</h2>

    <!-- Display error message if there is one -->
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger text-center">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- The registration form -->
    <form method="POST" action="/register">
      <div class="mb-3">
        <input type="text" class="form-control" name="first_name" placeholder="First Name" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <input type="email" class="form-control" name="email" placeholder="Email Address" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <input type="password" class="form-control" name="password" placeholder="Password" required>
      </div>
      <div class="mb-3">
        <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>
    <p class="text-center mt-3">
      Already have an account? <a href="/login">Login here</a>
    </p>
  </div>
</div>
