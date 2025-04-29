<link rel="stylesheet" href="/assets/css/register.css">

<div class="container">
  <h2>Create an account</h2>
  <form method="POST" action="#">
    <input type="text" name="first_name" placeholder="First Name" required>
    <input type="text" name="last_name" placeholder="Last Name" required>
    <input type="email" name="email" placeholder="Email address" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
    <button type="submit">Register</button>
  </form>
  <p>Already have an account? <a href="/login">Login here</a></p>
</div>
