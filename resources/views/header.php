<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Storage Unit Management System</title>
  
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Rancho&display=swap" rel="stylesheet">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
</head>
<body>

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
          
          <!-- Analytics Link -->
          <li class="nav-item" role="none">
            <a class="nav-link" href="/index.php?script=analytics" role="menuitem">
              <i class="fas fa-chart-line" aria-hidden="true"></i> Analytics
            </a>
          </li>
          
          <!-- Export Link -->
          <li class="nav-item" role="none">
            <a class="nav-link" href="/index.php?script=export" role="menuitem">
              <i class="fas fa-download" aria-hidden="true"></i> Export
            </a>
          </li>
        <?php endif; ?>
      </ul>

      <!-- Center Search Form (for authenticated users) -->
      <?php if (isloggedIn()): ?>
        <div class="navbar-search-container">
          <form class="form-inline" method="POST" action="/index.php?script=search" role="search">
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
        </div>
      <?php endif; ?>

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
          <!-- User Profile Dropdown -->
          <li class="nav-item dropdown" role="none">
            <?php 
              $currentUser = \StorageUnit\Models\User::getCurrentUser();
            ?>
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="menuitem" 
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <!-- Profile Picture -->
              <?php if ($currentUser && $currentUser->getProfilePicture()): ?>
                <img src="/uploads/profiles/<?= htmlspecialchars($currentUser->getProfilePicture()) ?>" 
                     alt="Profile Picture" 
                     class="profile-picture mr-2">
              <?php else: ?>
                <div class="profile-picture-placeholder mr-2">
                  <i class="fas fa-user"></i>
                </div>
              <?php endif; ?>
              <!-- User Name -->
              <span class="d-none d-md-inline"><?= htmlspecialchars($currentUser->getName()) ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown" role="menu">
              <!-- Storage Unit Info -->
              <?php if ($currentUser && $currentUser->getStorageUnitName()): ?>
                <div class="dropdown-header">
                  <i class="fas fa-warehouse mr-2"></i>
                  <strong><?= htmlspecialchars($currentUser->getStorageUnitName()) ?></strong>
                </div>
                <div class="dropdown-divider"></div>
              <?php endif; ?>
              <!-- User Actions -->
              <a class="dropdown-item" href="/profile.php" role="menuitem">
                <i class="fas fa-user-circle" aria-hidden="true"></i> Profile Settings
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="/index.php?sign=out" role="menuitem">
                <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Sign Out
              </a>
            </div>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>