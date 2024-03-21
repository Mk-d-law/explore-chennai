<?php
include 'db_connection.php';

// Read data with search and location filter
$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';
$locationFilter = isset($_GET['location_filter']) ? $_GET['location_filter'] : '';

// Base query for fetching places
$query = "SELECT * FROM places WHERE (name LIKE '%$searchQuery%'
           OR description LIKE '%$searchQuery%')";

// Add location filter to the query if a location is selected
if (!empty($locationFilter)) {
    $query .= " AND location = '$locationFilter'";
}

// Order the results based on the match with both search query and location
$query .= " ORDER BY CASE WHEN (name LIKE '%$searchQuery%'
           OR description LIKE '%$searchQuery%') AND location = '$locationFilter' THEN 1
           WHEN (name LIKE '%$searchQuery%'
           OR description LIKE '%$searchQuery%') THEN 2
           WHEN location = '$locationFilter' THEN 3
           ELSE 4 END";

$result = $connection->query($query);
$places = [];

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $places[] = $row;
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--=============== REMIXICONS ===============-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="assets/css/styles.css">

    <title>explore chennai</title>
</head>

<body>
    <!--==================== HEADER ====================-->
    <header class="header" id="header">
        <nav class="nav container">
            <a href="#" class="nav__logo">Chennai-contacts</a>

            <div class="nav__menu" id="nav-menu">
                <ul class="nav__list">
                    <li class="nav__item">
                        <a href="#" class="nav__link">Home</a>
                    </li>

                    <li class="nav__item">
                        <a href="#" class="nav__link">About Us</a>
                    </li>

                    <li class="nav__item">
                        <a href="#" class="nav__link">Contact us</a>
                    </li>

                    <li class="nav__item">
                        <a href="#" class="nav__link">Featured</a>
                    </li>

                    <li class="nav__item">
                        <a href="#" class="nav__link">Contact Me</a>
                    </li>
                </ul>

                <!-- Close button -->
                <div class="nav__close" id="nav-close">
                    <i class="ri-close-line"></i>
                </div>
            </div>

            <div class="nav__actions">
                <!-- Search button -->
                <i class="ri-search-line nav__search" id="search-btn"></i>

                <!-- Login button -->
                <i class="ri-user-line nav__login" id="login-btn"></i>

                <!-- Toggle button -->
                <div class="nav__toggle" id="nav-toggle">
                    <i class="ri-menu-line"></i>
                </div>
            </div>
        </nav>
    </header>

    <!--==================== SEARCH ====================-->
    <div class="search" id="search">
        <!-- Search Form -->
      
        <form method="get" action="index.php" class="search__form">
          <div class="search__input-container">
              <i class="ri-search-line search__icon"></i>
              <input type="text" class="search__input" name="q" id="search" placeholder="What are you looking for?" value="<?= $searchQuery; ?>">
          </div>
      
            
            <!-- Dropdown for selecting location -->
            <div class="search__input-container">
                <i class="ri-map-pin-line search__icon"></i>
                <select name="location_filter" class="search__input">
                    <option value="" selected>Select Location</option>
                    <?php
                    // Fetch distinct locations from the database
                    $locationQuery = "SELECT DISTINCT location FROM places";
                    $locationResult = $connection->query($locationQuery);
                    while ($locationRow = $locationResult->fetchArray(SQLITE3_ASSOC)) {
                        $selected = ($locationRow['location'] == $_GET['location_filter']) ? 'selected' : '';
                        echo "<option value='{$locationRow['location']}' $selected>{$locationRow['location']}</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="search__button">Search</button>
        </form>

        <i class="ri-close-line search__close" id="search-close"></i>
    </div>

    <!--==================== LOGIN ====================-->
    <div class="login" id="login">
        <form action="" class="login__form">
            <h2 class="login__title">Log In</h2>

            <div class="login__group">
                <div>
                    <label for="email" class="login__label">Email</label>
                    <input type="email" placeholder="Write your email" id="email" class="login__input">
                </div>

                <div>
                    <label for="password" class="login__label">Password</label>
                    <input type="password" placeholder="Enter your password" id="password" class="login__input">
                </div>
            </div>

            <div>
                <p class="login__signup">
                    You do not have an account? <a href="#">Sign up</a>
                </p>

                <a href="#" class="login__forgot">
                    You forgot your password
                </a>

                <button type="submit" class="login__button">Log In</button>
            </div>
        </form>

        <i class="ri-close-line login__close" id="login-close"></i>
    </div>

    <!--==================== MAIN ====================-->
  <div class="result">
      <!-- Main Search Results -->
      <div class="search-results">
          <!-- Display Data -->
          <div class="places">
              <?php foreach ($places as $place): ?>
                  <div class="place">
                    <a href="details.php?id=<?= $place['id']; ?>">
                      <img src="<?= $place['image_url']; ?>" alt="<?= $place['name']; ?>">
                      <h2><?= $place['name']; ?></h2>
                      <p><?= $place['description']; ?></p>
                      <p><?= $place['location']; ?></p>
                    </a>
                  </div>
              <?php endforeach; ?>
          </div>
      </div>

      <!-- Suggested Locations -->
      <div class="suggested-locations">
          <h3>Suggested Locations</h3>
          <?php
          // Fetch suggested locations from the database
          $suggestedLocationQuery = "SELECT DISTINCT location FROM places LIMIT 5";
          $suggestedLocationResult = $connection->query($suggestedLocationQuery);
          while ($suggestedLocationRow = $suggestedLocationResult->fetchArray(SQLITE3_ASSOC)) {
              echo "<p><a href='admin_panel.php?q={$suggestedLocationRow['location']}'>Search places in {$suggestedLocationRow['location']}</a></p>";
          }
          ?>
      </div>
  </div>


    <!--=============== MAIN JS ===============-->
    <script src="assets/js/main.js"></script>
</body>

</html>
