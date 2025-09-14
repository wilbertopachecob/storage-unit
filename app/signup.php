<?php
session_start();

// Set cache headers to prevent caching of authentication-sensitive content
header('Cache-Control: no-cache, no-store, must-revalidate, private');
header('Pragma: no-cache');
header('Expires: 0');

// Include necessary files
include __DIR__ . '/Helpers/helpers.php';
include __DIR__ . '/Middleware/guards.php';
include __DIR__ . '/signsHandlers.php';

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
  <title>Sign Up - Storage Unit</title>
  <base href="/">

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Create your Storage Unit Management System account">
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
              <h2 class="font-weight-bold text-primary mb-2">Create Account</h2>
              <p class="text-muted">Join us today</p>
            </div>

            <?php if (isset($_SESSION['signup_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo htmlspecialchars($_SESSION['signup_error']); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['signup_error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['signup_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo htmlspecialchars($_SESSION['signup_success']); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['signup_success']); ?>
            <?php endif; ?>

            <form method="post" action="signup.php" novalidate>
                <input type="hidden" name="sign" value="up">
                <div class="mb-4">
                    <label for="name" class="font-weight-bold">Full Name</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-user text-muted"></i>
                            </span>
                        </div>
                        <input name="name" type="text" class="form-control form-control-lg" id="name" 
                               placeholder="Enter your full name" required
                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                    </div>
                </div>

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
                        <input name="password" type="password" class="form-control form-control-lg" id="signupPassword" 
                               placeholder="Enter your password" required>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="toggleSignupPassword" 
                                    style="border-left: 0; border-color: #e3e6f0;">
                                <i class="fas fa-eye" id="toggleSignupPasswordIcon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <button type="submit" class="btn btn-primary btn-lg btn-block font-weight-bold" name="btn_submit">
                        <i class="fas fa-user-plus mr-2"></i>Create Account
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-muted mb-0">
                        Already have an account? 
                        <a href="signin.php" class="text-primary font-weight-bold">
                            Sign in here
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
  <script>
  // Load Popper.js with fallback chain
  (function() {
      const popperSources = [
          'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js',
          'https://unpkg.com/popper.js@1.14.6/dist/umd/popper.min.js',
          'js/popper.min.js'
      ];
      
      function loadPopper(index = 0) {
          if (index >= popperSources.length) {
              console.error('All Popper.js sources failed to load');
              return;
          }
          
          const script = document.createElement('script');
          script.src = popperSources[index];
          script.crossOrigin = 'anonymous';
          script.onload = function() {
              console.log('Popper.js loaded from:', popperSources[index]);
          };
          script.onerror = function() {
              console.warn('Failed to load Popper.js from:', popperSources[index]);
              loadPopper(index + 1);
          };
          document.head.appendChild(script);
      }
      
      loadPopper();
  })();
  </script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
      const toggleSignupPassword = document.getElementById('toggleSignupPassword');
      const signupPasswordInput = document.getElementById('signupPassword');
      const toggleSignupPasswordIcon = document.getElementById('toggleSignupPasswordIcon');
      
      toggleSignupPassword.addEventListener('click', function() {
          // Toggle the type attribute
          const type = signupPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          signupPasswordInput.setAttribute('type', type);
          
          // Toggle the icon
          if (type === 'password') {
              toggleSignupPasswordIcon.classList.remove('fa-eye-slash');
              toggleSignupPasswordIcon.classList.add('fa-eye');
          } else {
              toggleSignupPasswordIcon.classList.remove('fa-eye');
              toggleSignupPasswordIcon.classList.add('fa-eye-slash');
          }
      });
  });
  </script>
</body>
</html>
