<nav class="navbar navbar-expand-lg navbar-dark" role="navigation" aria-label="Main navigation">
  <div class="container">
    <!-- Brand -->
    <a class="navbar-brand" href="/index.php" aria-label="Storage Unit - Go to homepage">
      <i class="fas fa-warehouse mr-2" aria-hidden="true"></i>Storage Unit
    </a>
    
    <!-- Mobile Toggle -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" 
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation menu">
      <span class="navbar-toggler-icon" aria-hidden="true"></span>
    </button>

    <!-- Navigation Content -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <!-- Left Navigation -->
      <ul class="navbar-nav mr-auto" role="menubar">
        <?php if (isloggedIn()): ?>
          <!-- Items Dropdown -->
          <li class="nav-item dropdown" role="none">
            <a class="nav-link dropdown-toggle" href="#" id="itemsDropdown" role="menuitem" 
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
               aria-describedby="itemsDropdown-description">
              <i class="fas fa-boxes" aria-hidden="true"></i> Items
            </a>
            <div class="dropdown-menu" aria-labelledby="itemsDropdown" role="menu">
              <a class="dropdown-item" href="/index.php?script=itemsList" role="menuitem">
                <i class="fas fa-list" aria-hidden="true"></i> View Items List
              </a>
              <a class="dropdown-item" href="/index.php?script=addItem" role="menuitem">
                <i class="fas fa-plus" aria-hidden="true"></i> Add New Item
              </a>
            </div>
            <span id="itemsDropdown-description" class="sr-only">Items management menu</span>
          </li>
          
          <!-- Items Counter -->
          <li class="nav-item" role="none">
            <?php
              $controller = new \StorageUnit\Controllers\ItemController;
              $conn = new \StorageUnit\Database\Connection;
              $totalItems = $controller->getItemsAmountTotal(1, $conn);
            ?>
            <a class="nav-link" href="/index.php?script=itemsList" role="menuitem"
               aria-label="View all items, total count: <?=$totalItems?>">
              <i class="fas fa-chart-bar" aria-hidden="true"></i> Total Items
              <span class="badge badge-primary" aria-label="<?=$totalItems?> items"><?=$totalItems?></span>
            </a>
            <?php
              unsetVariables([$controller, $conn]);
            ?>
          </li>
        <?php endif; ?>
      </ul>

      <!-- Right Navigation -->
      <ul class="navbar-nav ml-auto" role="menubar">
        <?php if (!isloggedIn()): ?>
          <!-- Guest Navigation -->
          <li class="nav-item" role="none">
            <a class="nav-link" href="/signUp.php" role="menuitem">
              <i class="fas fa-user-plus" aria-hidden="true"></i> Sign Up
            </a>
          </li>
          <li class="nav-item" role="none">
            <a class="nav-link" href="/signIn.php" role="menuitem">
              <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Sign In
            </a>
          </li>
        <?php else: ?>
          <!-- Authenticated User Navigation -->
          <li class="nav-item" role="none">
            <a class="nav-link" href="/index.php?sign=out" role="menuitem">
              <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Sign Out
            </a>
          </li>
        <?php endif; ?>
      </ul>

      <!-- Search Form (for authenticated users) -->
      <?php if (isloggedIn()): ?>
        <form class="form-inline ml-3" method="POST" action="/index.php?script=search" role="search">
          <div class="input-group">
            <label for="searchInput" class="sr-only">Search items</label>
            <input name="searchTerm" id="searchInput" class="form-control" type="search" 
                   placeholder="Search items..." aria-label="Search items in storage unit"
                   autocomplete="off" spellcheck="false">
            <div class="input-group-append">
              <button class="btn btn-outline-success" type="submit" aria-label="Submit search">
                <i class="fas fa-search" aria-hidden="true"></i>
                <span class="sr-only">Search</span>
              </button>
            </div>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
</nav>