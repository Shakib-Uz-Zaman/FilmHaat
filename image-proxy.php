<?php
// Image proxy to bypass CORS restrictions for color extraction
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Get image URL from query parameter
$imageUrl = isset($_GET['url']) ? $_GET['url'] : '';

if (empty($imageUrl)) {
    http_response_code(400);
    echo 'Missing image URL';
    exit;
}

// Validate URL
if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo 'Invalid image URL';
    exit;
}

// Strict URL validation - only allow HTTP/HTTPS from specific trusted domains
$parsedUrl = parse_url($imageUrl);
if (!$parsedUrl || !in_array($parsedUrl['scheme'] ?? '', ['http', 'https'])) {
    http_response_code(400);
    echo 'Invalid URL scheme';
    exit;
}

// Prevent SSRF to local/private networks
$host = $parsedUrl['host'] ?? '';

// Block direct IP addresses in private ranges
if (filter_var($host, FILTER_VALIDATE_IP)) {
    if (!filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        http_response_code(403);
        echo 'Access to private IPs not allowed';
        exit;
    }
} else {
    // For domain names, resolve to IP and check if it's private
    $resolvedIp = gethostbyname($host);
    
    // Check if resolution was successful (gethostbyname returns the hostname if it fails)
    if ($resolvedIp !== $host) {
        // Check if resolved IP is private
        if (filter_var($resolvedIp, FILTER_VALIDATE_IP)) {
            if (!filter_var($resolvedIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                http_response_code(403);
                echo 'Domain resolves to private IP';
                exit;
            }
        }
    }
}

// Dynamic proxy: Allow all public domains (no hardcoded whitelist)
// Security is enforced through:
// 1. Private IP blocking (above)
// 2. Scheme validation (HTTP/HTTPS only)
// 3. Content-type validation (images only)
// 4. Size limits (5MB max)
// 5. Timeout limits
// 6. SSL verification

// Initialize cURL with strict security settings
$ch = curl_init($imageUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Disable redirects to prevent bypass
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); // 3 second connect timeout
curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 5 second total timeout
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Enable SSL verification
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
curl_setopt($ch, CURLOPT_MAXFILESIZE, 5 * 1024 * 1024); // 5MB max file size

// Execute request
$imageData = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

curl_close($ch);

// Check if request was successful
if ($httpCode !== 200 || empty($imageData)) {
    http_response_code(404);
    echo 'Failed to fetch image';
    exit;
}

// Strict content-type validation - only allow images
if (!$contentType || !preg_match('/^image\//i', $contentType)) {
    http_response_code(400);
    echo 'Invalid content type - images only';
    exit;
}

// Additional size check (after download)
if (strlen($imageData) > 5 * 1024 * 1024) {
    http_response_code(413);
    echo 'Image too large';
    exit;
}

// Set content type header
header("Content-Type: {$contentType}");

// Output image data
echo $imageData;
