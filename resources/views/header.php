<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="<?=$_SERVER['PHP_SELF']?>">Storage Unit</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
    <?php
if (isloggedIn()):
?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Items
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <!-- Pointing the link to index.php to load the right script there-->
          <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF']?>?script=itemsList">View Items List</a>
          <a class="dropdown-item" href="<?=$_SERVER['PHP_SELF']?>?script=addItem">Add Item</a>
        </div>
      </li>
      <li class="nav-item">
      <?php
        $controller = new \StorageUnit\Controllers\ItemController;
        $conn = new \StorageUnit\Database\Connection;
        ?>
      <a class="nav-link">
          Total items
      <span class="badge badge-primary"><?=$controller->getItemsAmountTotal(1, $conn)?></span>
</a>
      <?php
      unsetVariables([$controller, $conn]);
      ?>
      </li>
      <?php endif;?>
      <?php
if (!isloggedIn()):
?>
      <li class="nav-item">
        <a class="nav-link" href="<?=$_SERVER['PHP_SELF']?>?script=signUp"><i class="fas fa-user-plus"></i> Sign Up</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?=$_SERVER['PHP_SELF']?>?script=signIn"><i class="fas fa-sign-in-alt"></i> Sign in</a>
      </li>
      <?php else: ?>
      <li class="nav-item">
        <a class="nav-link" href="<?=$_SERVER['PHP_SELF']?>?sign=out"> <i class="fas fa-sign-out-alt"></i> Sign out</a>
      </li>
      <?php endif;?>
    </ul>
    <?php
if (isloggedIn()):
?>
    <form class="form-inline my-2 my-lg-0" method="POST" action="<?=$_SERVER['PHP_SELF']?>?script=search">
      <input name="searchTerm" class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
<?php
endif;
?>
  </div>
</nav>