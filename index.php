<?php
session_start();
//Evaluating signIn, signUp and signOut
include './lib/helpers.php';
include './lib/signsHandlers.php';
include './lib/guards.php';
include './lib/db/Controllers/ItemController.php';
if (!isFileIncluded('connection.php')) {
  include 'lib/db/connection.php';
}


//Deleting an Item
if (isset($_POST['btn_delete'])):
    $id = $_GET['id'];
    //deleteItem(int $id):bool
    $controller = new ItemController;
    $conn = new Connection;
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
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link href="https://fonts.googleapis.com/css?family=Rancho&effect=shadow-multiple" rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
  <link rel="stylesheet" href="/storageUnit/public/css/style.css">
</head>
<body>
  <?php
  $script = $_GET['script'] ?? 'default';
  if($script == 'default'):
  ?>
  <img class="bg" src="/storageUnit/public/img/storage-unit.jpg" alt="background">
  <?php
  endif;
  ?>
<?php
include './partials/header.php';
?>
  <div class="container mt-4">
    <div class="row content">
      <div class="col-sm-12">
        <?php
//phpinfo(32);
include './lib/routes.php';
?>
  </div>
  </div>
<?php
include './partials/footer.php';
?>
<!-- Just for future references, NEVER USE JQUERY SLIM AGAIN, 
it cause some problems with fadeIn, hide('slow') and more  -->
<!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
<script src="/storageUnit/public/js/card.js"></script>
<script src="/storageUnit/public/js/main.js"></script>
<script src="/storageUnit/public/js/upload-image-preview.js"></script>
</body>
</html>
