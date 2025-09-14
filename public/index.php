<?php
session_start();

// Set cache headers to prevent caching of authentication-sensitive content
header('Cache-Control: no-cache, no-store, must-revalidate, private');
header('Pragma: no-cache');
header('Expires: 0');

//Evaluating signIn, signUp and signOut
include __DIR__ . '/../app/Helpers/helpers.php';
include __DIR__ . '/../app/Middleware/guards.php';
include __DIR__ . '/../config/app/autoload.php';
include __DIR__ . '/../app/signsHandlers.php';
// Connection class is now autoloaded


//Deleting an Item
if (isset($_POST['btn_delete'])):
    $id = $_GET['id'];
    //deleteItem(int $id):bool
    $controller = new \StorageUnit\Controllers\ItemController;
    $conn = new \StorageUnit\Database\Connection;
    $item = $controller->deleteItem($id, $conn);
    unsetVariables([$controller, $conn]);
    if ($item):
        $url = $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?script=itemsList';
        header("Location: http://$url");
        exit;
    endif;
endif;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Storage Unit</title>
  <base href="/">

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Storage Unit Management System">
  <link rel="icon" type="image/svg+xml" href="favicon.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Rancho&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
  <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
  <script src="js/touch-gestures.js?v=<?= time() ?>" defer></script>
  <script src="js/offline-manager.js?v=<?= time() ?>" defer></script>
  <script src="js/sw-update.js?v=<?= time() ?>" defer></script>
  <script>
    // Force cache refresh and clear all caches
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.getRegistrations().then(function(registrations) {
        for(let registration of registrations) {
          registration.unregister();
        }
      });
    }
    
    // Clear all caches
    if ('caches' in window) {
      caches.keys().then(function(cacheNames) {
        return Promise.all(
          cacheNames.map(function(cacheName) {
            return caches.delete(cacheName);
          })
        );
      });
    }
    
    // Clear storage
    localStorage.clear();
    sessionStorage.clear();
  </script>
  <noscript>
    <link href="https://fonts.googleapis.com/css2?family=Rancho&display=swap" rel="stylesheet">
  </noscript>
</head>
<body>
  <?php
  $script = $_GET['script'] ?? 'default';
  if($script == 'default'):
  ?>
  <img class="bg" src="img/storage-unit.jpg" alt="background">
  <?php
  endif;
  ?>
<?php
include __DIR__ . '/../resources/views/header.php';
?>
  <div class="container mt-4">
    <div class="row content">
      <div class="col-sm-12">
        <?php
//phpinfo(32);
include __DIR__ . '/../routes/routes.php';
?>
  </div>
  </div>
<?php
include __DIR__ . '/../resources/views/footer.php';
?>
<!-- Scripts are now loaded in footer.php to avoid duplication -->
<script src="js/card.js?v=<?= time() ?>"></script>
<script src="js/main.js?v=<?= time() ?>"></script>
<script src="js/upload-image-preview.js?v=<?= time() ?>"></script>
</body>
</html>
