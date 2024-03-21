<?php
include 'db_connection.php';



// CRUD operations
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $location = $_POST['location'];

    // Handle file upload
    $targetDirectory = "uploads/";
    $targetFile = $targetDirectory . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the image file is a valid image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["image"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow only certain file formats
    $allowedFormats = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowedFormats)) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imageUrl = $targetFile;

            $query = "INSERT INTO places (name, description, location, image_url) VALUES ('$name', '$description', '$location', '$imageUrl')";
            $connection->exec($query);
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}


if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $location = $_POST['location'];

    // Handle file upload
    $targetDirectory = "uploads/";
    $targetFile = $targetDirectory . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if ($_FILES["image"]["size"] > 0) {
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        if ($_FILES["image"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        $allowedFormats = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowedFormats)) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
    }

    if ($uploadOk == 1) {
        if ($_FILES["image"]["size"] > 0) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $imageUrl = $targetFile;
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            // No new image provided, retain the existing one
            $imageUrl = $placeToEdit['image_url'];
        }

        $query = "UPDATE places SET name='$name', description='$description', location='$location', image_url='$imageUrl' WHERE id=$id";
        $connection->exec($query);
    }
}


if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    $query = "DELETE FROM places WHERE id=$id";
    $connection->exec($query);
}

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
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Admin Panel</h1>

        <!-- Search Form -->
        <form method="get" action="admin_panel.php" class="form">
            <label for="search">Search:</label>
            <input type="text" name="q" id="search" value="<?= $searchQuery; ?>">
            <!-- Dropdown for selecting location -->
            <label for="location">Location:</label>
            <select name="location_filter">
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

            <button type="submit">Search</button>
        </form>

        <!-- Create Form -->
        <form method="post" action="admin_panel.php" class="form" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" name="name" required>

            <label for="description">Description:</label>
            <textarea name="description" required></textarea>

            <label for="location">Location:</label>
            <input type="text" name="location" required>

          <label for="image">Image:</label>
          <input type="file" name="image" accept="image/*" required>

            <button type="submit" name="submit">Add Place</button>
        </form>

        <!-- Display Data -->
        <div class="places">
            <?php foreach ($places as $place): ?>
                <div class="place">
                    <h2><?= $place['name']; ?></h2>
                    <p><?= $place['description']; ?></p>
                    <p><?= $place['location']; ?></p>
                    <img src="<?= $place['image_url']; ?>" alt="<?= $place['name']; ?>">
                    <!-- Edit button -->
                    <a href="admin_panel.php?edit=<?= $place['id']; ?>">Edit</a>
                    <!-- Delete form -->
                    <form method="post" action="admin_panel.php">
                        <input type="hidden" name="id" value="<?= $place['id']; ?>">
                        <button type="submit" name="delete">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <?php
        // Edit form
        if (isset($_GET['edit'])) {
            $editId = $_GET['edit'];
            $query = "SELECT * FROM places WHERE id=$editId";
            $result = $connection->query($query);
            $placeToEdit = $result->fetchArray(SQLITE3_ASSOC);
        ?>
            <!-- Edit Form -->
          <form method="post" action="admin_panel.php" class="form" enctype="multipart/form-data">
              <input type="hidden" name="id" value="<?= $placeToEdit['id']; ?>">

              <label for="name">Name:</label>
              <input type="text" name="name" value="<?= $placeToEdit['name']; ?>" required>

              <label for="description">Description:</label>
              <textarea name="description" required><?= $placeToEdit['description']; ?></textarea>

              <label for="location">Location:</label>
              <input type="text" name="location" value="<?= $placeToEdit['location']; ?>" required>

              <label for="image">Image:</label>
              <input type="file" name="image" accept="image/*">

              <button type="submit" name="update">Update Place</button>
          </form>
        <?php
        }
        ?>
    </div>
</body>
</html>
