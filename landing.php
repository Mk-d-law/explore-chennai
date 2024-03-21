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

  <title>chennai</title>
  <link rel="stylesheet" href="landy.css">
  <link rel="stylesheet" href="node_modules/@glidejs/glide/dist/css/glide.core.min.css">
  <link rel="stylesheet" href="node_modules/@glidejs/glide/dist/css/glide.theme.min.css">
  <script src="https://cdn.jsdelivr.net/npm/@glidejs/glide"></script>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
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
  <div class="hero-container">
      <img class="hero-image" src="./public/image-1@2x.png" alt="Explore Chennai">
      <div class="hero-text">
          <h1>Explore Chennai</h1>
      </div>
  </div>
  <section>
  <h2 class="category-heading">Categories</h2>
  <div class="category-container">

      <div class="category" onclick="showPlaces('Malls')">Malls</div>
      <div class="category" onclick="showPlaces('Temples')">Temples</div>
      <div class="category" onclick="showPlaces('Hotels')">Hotels</div>
      <div class="category" onclick="showPlaces('Restaurants')">Restaurants</div>
      <div class="category" onclick="showPlaces('Cafes')">Cafes</div>
      <div class="category" onclick="showPlaces('Hospitals')">Hospitals</div>
      <div class="category" onclick="showPlaces('Malls')">Malls</div>
      <div class="category" onclick="showPlaces('Temples')">Temples</div>
      <div class="category" onclick="showPlaces('Hotels')">Hotels</div>
      <div class="category" onclick="showPlaces('Restaurants')">Restaurants</div>
      <div class="category" onclick="showPlaces('Cafes')">Cafes</div>
      <div class="category" onclick="showPlaces('Hospitals')">Hospitals</div>
      <div class="category" onclick="showPlaces('Malls')">Malls</div>
      <div class="category" onclick="showPlaces('Temples')">Temples</div>
      <div class="category" onclick="showPlaces('Hotels')">Hotels</div>
      <div class="category" onclick="showPlaces('Restaurants')">Restaurants</div>
      <div class="category" onclick="showPlaces('Cafes')">Cafes</div>
      <div class="category" onclick="showPlaces('Hospitals')">Hospitals</div>
      <div class="category" onclick="showPlaces('Malls')">Malls</div>
      <div class="category" onclick="showPlaces('Temples')">Temples</div>
      <div class="category" onclick="showPlaces('Hotels')">Hotels</div>
      <div class="category" onclick="showPlaces('Restaurants')">Restaurants</div>
      <div class="category" onclick="showPlaces('Cafes')">Cafes</div>
      <div class="category" onclick="showPlaces('Hospitals')">Hospitals</div>
      <div class="category" onclick="showPlaces('Malls')">Malls</div>
      <div class="category" onclick="showPlaces('Temples')">Temples</div>
      <div class="category" onclick="showPlaces('Hotels')">Hotels</div>
      <div class="category" onclick="showPlaces('Restaurants')">Restaurants</div>

  </div>


  <section>
      <h2 class="category-heading">visit</h2>
  <div class="card" style="width: 18rem;">
      <img src="mall.jpg" class="card-img-top" alt="mall">
      <div class="card-body">
        <h5 class="card-title">Mall</h5>
        <p class="card-text">Shop for the latest trends in mall for a great shopping experience.</p>
        <a href="#" class="btn btn-primary">Go shopping</a>
      </div>
    </div>
    <div class="card" style="width: 18rem;">
      <img src="chennai2.jpg" class="card-img-top" alt="Temples">
      <div class="card-body">
        <h5 class="card-title">Temple</h5>
        <p class="card-text">Shop for the latest trends in mall for a great shopping experience.</p>
        <a href="#" class="btn btn-primary">visit Temples</a>
      </div>
    </div>
    <div class="card" style="width: 18rem;">
      <img src="chennai1.jpg" class="card-img-top" alt="mall">
      <div class="card-body">
        <h5 class="card-title">Beach</h5>
        <p class="card-text">Shop for the latest trends in mall for a great shopping experience.</p>
        <a href="#" class="btn btn-primary">explore seashore</a>
      </div>
    </div>
    <div class="card" style="width: 18rem;">
      <img src="chennai3.jpg" class="card-img-top" alt="mall">
      <div class="card-body">
        <h5 class="card-title">Theatre</h5>
        <p class="card-text">Shop for the latest trends in mall for a great shopping experience.</p>
        <a href="#" class="btn btn-primary">Watch movies</a>
      </div>
    </div>
    </section>

  <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="./public/image-2@2x.png" class="d-block w-100" alt="slide1">
        </div>
        <div class="carousel-item">
          <img src="./public/image-3@2x.png" class="d-block w-100" alt="slide2">
        </div>
        <div class="carousel-item">
          <img src="./public/image-4@2x.png" class="d-block w-100" alt="slide3">
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>

  <!-- Placeholder for displaying places based on the selected category -->
  <div id="places-container" style="margin-top: 20px;"></div>



  <footer>

      <div class="footer-section">
          <h3>What's New</h3>
          <ul>

              <li><a href="#">Restaurants</a></li>
              <li><a href="#">Hospitals</a></li>
              <li><a href="#">Schools</a></li>
              <li><a href="#">Hotels</a></li>
              <li><a href="#">Real Estate</a></li>
              <li><a href="#">Bills & Recharge</a></li>
          </ul>
      </div>
      <div class="footer-section">
          <h3>Media</h3>
          <ul>
              <li><a href="#">Feedback</a></li>
              <li><a href="#">How Site Works</a></li>
          </ul>
      </div>
      <div class="footer-section">
          <h3>Quick Links</h3>
          <ul>
              <li><a href="#">Site Support</a></li>
              <li><a href="#">About Us</a></li>
              <li><a href="#">Privacy Policy</a></li>
              <li><a href="#">Contact Us</a></li>
          </ul>
      </div>
      <div class="footer-section">
          <h3>Customer Care</h3>
          <ul>
              <li><a href="#">Feedback</a></li>
              <li><a href="#">Post Ads</a></li>
          </ul>
      </div>



  </footer>


    <!--=============== MAIN JS ===============-->
    <script src="assets/js/main.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>
