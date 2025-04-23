<!-- app/Views/auth/login_register.php -->
<?php include '../app/Views/includes/header.php'; ?>

<section class="auth-section">
  <?php include '../app/Views/auth/login_form.php'; ?>
  <hr>
  <?php include '../app/Views/auth/register_form.php'; ?>
</section>

<?php include '../app/Views/includes/footer.php'; ?>
php -S localhost:8000 -t public
