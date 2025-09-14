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
<!-- Just for future references, NEVER USE JQUERY SLIM AGAIN, 
it cause some problems with fadeIn, hide('slow') and more  -->
<!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
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
<script src="js/card.js?v=<?= time() ?>"></script>
<script src="js/main.js?v=<?= time() ?>"></script>
<script src="js/upload-image-preview.js?v=<?= time() ?>"></script>
</body>
</html>
