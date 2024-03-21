<?php
include 'db_connection.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Details</title>
    <!-- Link to your CSS file -->
    <link rel="stylesheet" href="path/to/your/css/details.css">
    <!-- Link to a CDN for Font Awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-BEBFfBVs/BsH7YFMY5QbqegZAa7suk99NllT+WIwZdXtDaJYV/sCSdRFqUcDArrc" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <?php
        // Fetch and display place details from the database
        // Adjust this based on your database structure
        $placeId = $_GET['id'];
        $query = "SELECT * FROM places WHERE id = $placeId";
        $result = $connection->query($query);
        $place = $result->fetchArray(SQLITE3_ASSOC);
        ?>
        <div class="place-details">
            <img src="<?= $place['image_url']; ?>" alt="<?= $place['name']; ?>">
            <h2><?= $place['name']; ?></h2>
            <p class="description"><?= $place['description']; ?></p>
            <div class="location">
                <i class="fas fa-map-marker-alt"></i>
                <p><?= $place['location']; ?></p>
            </div>
            <p class="map-link">Find on <a href="#">Google Maps</a></p>
        </div>
    </div>
</body>
</html>
