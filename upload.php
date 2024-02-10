<?php
// Increase the upload file size limit to 10MB
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');

// Function to check if the uploaded image contains at least 95% of the body with clothes
function isBodyClothed($imagePath) {
    // Load the image
    $image = @imagecreatefromjpeg($imagePath); // Use @ to suppress warning and handle error manually
    
    // Check if image creation was successful
    if (!$image) {
        die("Failed to load the image. Please check the file path.");
    }
    
    // Get image dimensions
    $width = imagesx($image);
    $height = imagesy($image);

    // Initialize variables to count pixels
    $totalPixels = $width * $height;
    $clothedPixels = 0;

    // Loop through each pixel to count clothed pixels
    for ($x = 0; $x < $width; $x++) {
        for ($y = 0; $y < $height; $y++) {
            // Get the RGB color of the pixel
            $rgb = imagecolorat($image, $x, $y);
            $colors = imagecolorsforindex($image, $rgb);

            // Assuming clothes are non-white pixels, adjust this condition based on your requirement
            if ($colors['red'] < 240 || $colors['green'] < 240 || $colors['blue'] < 240) {
                $clothedPixels++;
            }
        }
    }

    // Calculate the percentage of clothed pixels
    $percentageClothed = ($clothedPixels / $totalPixels) * 100;

    // If 95% or more of the body is clothed, return true, otherwise return false
    if ($percentageClothed >= 95) {
        return true;
    } else {
        return false;
    }
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if a file was uploaded
    if (!isset($_FILES["image"])) {
        die("No image uploaded.");
    }
    
    // Check for upload errors
    if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
        die("Error uploading file. Error code: " . $_FILES["image"]["error"]);
    }
    
    // Get the temporary file path
    $tmpFilePath = $_FILES["image"]["tmp_name"];
    
    // Check if the file was uploaded successfully
    if (!is_uploaded_file($tmpFilePath)) {
        die("Failed to upload the image.");
    }
    
    // Check if the uploaded file is an image
    if (!getimagesize($tmpFilePath)) {
        die("Uploaded file is not an image.");
    }
    
    // Check if the uploaded image meets the clothing requirement
    if (isBodyClothed($tmpFilePath)) {
        echo "Image uploaded successfully!";
    } else {
        echo "Image rejected. Please ensure that at least 95% of the body is clothed.";
    }
}
?>
