<?php
header('Content-Type: application/json');

// Check if the file was uploaded 
if (isset($_FILES['croppedImage']) && $_FILES['croppedImage']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    $fileTmpPath = $_FILES['croppedImage']['tmp_name'];
    $mimeType = mime_content_type($fileTmpPath);

    // Validate the file type
    if ($mimeType === 'image/jpeg') {
        $extension = '.jpg';
    } elseif ($mimeType === 'image/png') {
        $extension = '.png';
    } else {
        http_response_code(415);
        echo json_encode(['error' => 'Type not supported. Only JPG and PNG.']);
        exit;
    }

    // Create a unique file name
    $fileName = uniqid('cropped_', true) . $extension;
    $destPath = $uploadDir . $fileName;

    // make sure the upload directory exists
    // and is writable
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Move file to the destination
    if (move_uploaded_file($fileTmpPath, $destPath)) {
        echo json_encode([
            'success' => true,
            'message' => 'Image saved!!',
            'filename' => $fileName,
            'path' => $destPath
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Cant move the file.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded or there was an error uploading the file.']);
}
