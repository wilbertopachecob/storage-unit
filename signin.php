<?php
session_start();

// Include necessary files
include './lib/helpers.php';
include './lib/guards.php';
include './lib/signsHandlers.php';

// If user is already logged in, redirect to items list
if (isloggedIn()) {
    header("Location: index.php?script=itemsList");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Sign In - Storage Unit</title>
  <base href="/">

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Sign in to Storage Unit Management System">
  <link rel="icon" type="image/svg+xml" href="favicon.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Rancho&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
  <link rel="stylesheet" href="/public/css/style.css">
  <noscript>
    <link href="https://fonts.googleapis.com/css2?family=Rancho&display=swap" rel="stylesheet">
  </noscript>
</head>
<body>
  <?php include './partials/header.php'; ?>
  
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card shadow border-0">
          <div class="card-body p-5">
            <div class="text-center mb-4">
              <h2 class="font-weight-bold text-primary mb-2">Welcome Back</h2>
              <p class="text-muted">Sign in to your account</p>
            </div>

            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo htmlspecialchars($_SESSION['login_error']); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['login_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo htmlspecialchars($_SESSION['login_success']); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['login_success']); ?>
            <?php endif; ?>

            <form method="post" action="signin.php" novalidate>
                <input type="hidden" name="sign" value="in">
                <div class="mb-4">
                    <label for="email" class="font-weight-bold">Email Address</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-envelope text-muted"></i>
                            </span>
                        </div>
                        <input name="email" type="email" class="form-control form-control-lg" id="email" 
                               placeholder="Enter your email" required 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="font-weight-bold">Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                        </div>
                        <input name="password" type="password" class="form-control form-control-lg" id="password" 
                               placeholder="Enter your password" required>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword" 
                                    style="border-left: 0; border-color: #e3e6f0;">
                                <i class="fas fa-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <button type="submit" class="btn btn-primary btn-lg btn-block font-weight-bold" name="btn_submit">
                        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-muted mb-0">
                        Don't have an account? 
                        <a href="signup.php" class="text-primary font-weight-bold">
                            Sign up here
                        </a>
                    </p>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include './partials/footer.php'; ?>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
      const togglePassword = document.getElementById('togglePassword');
      const passwordInput = document.getElementById('password');
      const togglePasswordIcon = document.getElementById('togglePasswordIcon');
      
      togglePassword.addEventListener('click', function() {
          // Toggle the type attribute
          const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);
          
          // Toggle the icon
          if (type === 'password') {
              togglePasswordIcon.classList.remove('fa-eye-slash');
              togglePasswordIcon.classList.add('fa-eye');
          } else {
              togglePasswordIcon.classList.remove('fa-eye');
              togglePasswordIcon.classList.add('fa-eye-slash');
          }
      });
  });
  </script>
</body>
</html>
