<?php
// Include the AWS SDK using Composer's autoload
require '../vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Set the Content-Type header to application/json
header('Content-Type: application/json');

// AWS S3 Configuration
$bucket = $_ENV['BUCKET_NAME']; // Accessing the bucket name from an environment variable
$region = $_ENV['REGION'];       // Accessing the AWS region from an environment variable
$keyName = $_ENV['KEY_NAME'];    // Accessing the name of your JSON file from an environment variable


// Initialize the S3 client with your AWS credentials
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => $_ENV['REGION'], // Accessing the region from the environment variable
    'credentials' => [
        'key'    => $_ENV['AWS_ACCESS_KEY_ID'],     // Accessing the AWS Access Key ID from the environment variable
        'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'], // Accessing the AWS Secret Access Key from the environment variable
    ],
]);

try {
    // Retrieve the object from S3
$result = $s3->getObject([
    'Bucket' => $_ENV['BUCKET_NAME'], // Access the BUCKET_NAME from the environment variable
    'Key'    => $_ENV['KEY_NAME'],    // Access the KEY_NAME from the environment variable
]);

    // Get the contents of the object
    $data = $result['Body']->getContents();

    // Decode the JSON data into an associative array
    $json_data = json_decode($data, true);

    // Check for JSON decoding errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(500);
        echo json_encode(['error' => 'Error decoding JSON data']);
        exit;
    }
} catch (AwsException $e) {
    // Handle errors (e.g., network issues, permissions)
    http_response_code(500);
    echo json_encode(['error' => 'Failed to retrieve data from S3', 'message' => $e->getMessage()]);
    exit;
}

// Define the parameters to check
$parameters = ['utm_NA', 'fwlink', 'LinkId', 'nam11', 'safelink', 'walkaway'];

// Initialize the key as null
$key = null;

// Loop through the parameters to find the one that contains the key
foreach ($parameters as $param) {
    if (isset($_GET[$param])) {
        $key = substr($_GET[$param], 4); // Extract the key by removing the first 4 characters
        break; // Exit the loop once the key is found
    }
}

// Function to search for a token in the JSON data
function searchToken($data, $token) {
    foreach ($data as $item) {
        if (isset($item['Token']) && $item['Token'] === $token) {
            return $item;
        }
    }
    return null;
}

// Search for the matching entry
$entry = searchToken($json_data, $key);

// Determine the URL to redirect to based on the search result
if ($entry) {
    // If a matching entry is found, no need to URL-encode if it's already Base64
    $base64 = $entry['Base64']; // Assuming this is already Base64 encoded
    $redirectUrl = "detector.php?key=$base64";
} else {
    // If no matching entry is found, URL-encode the original key and set the redirect URL
    $redirectUrl = "detector.php?key=" . urlencode($key);
}

// Perform the redirection
header("Location: $redirectUrl");
exit;
?>
