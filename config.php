<?php
$envFile = __DIR__ . '/.env';
$envKey = 'JWT_SECRET';

    // Use of preg_match(pattern, subject match) 
    // 1. "/^$envKey=(.*)$/m"  -> This is the regular expression (regex) pattern used to search for a match.
    // 2. $envContent -> This is the string where we search for a match.
    // 3. $matches -> An array that will be filled with the results of the search. 

// Check if the .env file exists and contains the JWT_SECRET key
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);      // Only Read the file contents from the .env
    if (preg_match("/^$envKey=(.*)$/m", $envContent, $matches) && !empty(trim($matches[1]))) {
        $secretKey = trim($matches[1]); // Use the existing key from the .env file
    } else {
        // If no key exists, generate a new one and store it
        $secretKey = bin2hex(random_bytes(32)); // Generate a new secret key
        file_put_contents($envFile, "$envKey=$secretKey\n", LOCK_EX); // Write the key into .env file
    }
} else {
    // If the .env file doesn't exist, generate a new secret key and create the file
    $secretKey = bin2hex(random_bytes(32)); // Generate a new secret key
    file_put_contents($envFile, "$envKey=$secretKey\n", LOCK_EX); // Create .env and save the key
}

// Set JWT Constants
define('SECRET_KEY', $secretKey);
define('ALGORITHM', 'HS256');
define('TOKEN_EXPIRATION', 86400); // 1 day
?>