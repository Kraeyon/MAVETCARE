<?php include '../app/Views/includes/header.php'; ?>

<link rel="stylesheet" href="/assets/css/login.css">

<section class="auth-section">
  <h2>login to system</h2>
  <form method="POST" action="#">
    <div class="form-group">
      <span class="form-label">Username or Email address</span>
      <input type="text" name="email">
    </div>
    <div class="form-group">
      <span class="form-label">Password</span>
      <input type="password" name="password">
    </div>
    <label><input type="checkbox"> Remember me</label>
    <a href="#" class="forgot-link">Forgot Password?</a>
    <a href="#" class="create-account">Create an account</a>
    <button type="submit">LOGIN</button>
  </form>
  
  <?php include '../app/Views/auth/register_form.php'; ?>
</section>

<?php include '../app/Views/includes/footer.php'; ?>