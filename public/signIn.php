<?php
session_start();
//Evaluating signIn, signUp and signOut
include __DIR__ . '/../app/Helpers/helpers.php';
include __DIR__ . '/../app/Middleware/guards.php';
include __DIR__ . '/../config/app/autoload.php';
include __DIR__ . '/../app/signsHandlers.php';
// Connection class is now autoloaded
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Sign In - Storage Unit</title>
  <base href="/">

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Storage Unit Management System - Sign In">
  <link rel="icon" type="image/svg+xml" href="favicon.svg">
  <!-- Preconnect to external domains for performance -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://use.fontawesome.com">
  <link rel="preconnect" href="https://stackpath.bootstrapcdn.com">
  
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Rancho:wght@400&display=swap" rel="stylesheet">
  
  <!-- Icons -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
  
  <!-- CSS Framework -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
  
  <!-- Custom Styles -->
  <link rel="stylesheet" href="css/style.css">
  
  <!-- Fallback for no-JS -->
  <noscript>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Rancho:wght@400&display=swap" rel="stylesheet">
  </noscript>
</head>
<body>
  <a href="#main-content" class="skip-link">Skip to main content</a>
  <img class="bg" src="img/storage-unit.jpg" alt="background">
<?php
include __DIR__ . '/../resources/views/header.php';
?>
  <div class="container mt-4" id="main-content">
    <div class="col-sm-12">
      <?php include __DIR__ . '/../resources/views/login/signIn.php'; ?>
    </div>
  </div>
<?php
include __DIR__ . '/../resources/views/footer.php';
?>
<!-- Just for future references, NEVER USE JQUERY SLIM AGAIN, 
it cause some problems with fadeIn, hide('slow') and more  -->
<!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
<script src="js/card.js"></script>
<script src="js/main.js"></script>
<script src="js/upload-image-preview.js"></script>
</body>
</html>
