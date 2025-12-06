<?php
header('Content-Type: application/json');
header('Cache-Control: public, max-age=600, stale-while-revalidate=1200');
header('Vary: Accept-Encoding');
require_once 'config.php';

$cacheDir = __DIR__ . '/cache';
$cacheFile = $cacheDir . '/category-posters-cache.json';
$cacheLifetime = 600;

if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
    $cachedData = file_get_contents($cacheFile);
    if ($cachedData) {
        echo $cachedData;
        exit;
    }
}

function fetchRandomPoster($websiteName, $website) {
    $url = $website['url'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    
    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error || $httpCode !== 200) {
        return null;
    }
    
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();
    
    $xpath = new DOMXPath($dom);
    
    $images = [];
    
    $articles = $xpath->query("//article | //div[contains(@class, 'post')] | //div[contains(@class, 'item')] | //div[contains(@class, 'movie')] | //li[contains(@class, 'thumb')]");
    
    foreach ($articles as $article) {
        $imgNodes = $xpath->query(".//img", $article);
        
        if ($imgNodes->length > 0) {
            $imgNode = $imgNodes->item(0);
            
            $image = $imgNode->getAttribute('data-src');
            if (empty($image) || strpos($image, 'data:image') === 0) {
                $image = $imgNode->getAttribute('data-lazy-src');
            }
            if (empty($image) || strpos($image, 'data:image') === 0) {
                $image = $imgNode->getAttribute('data-original');
            }
            if (empty($image) || strpos($image, 'data:image') === 0) {
                $image = $imgNode->getAttribute('data-wpfc-original-src');
            }
            if (empty($image) || strpos($image, 'data:image') === 0) {
                $image = $imgNode->getAttribute('data-lazy');
            }
            if (empty($image) || strpos($image, 'data:image') === 0) {
                $image = $imgNode->getAttribute('src');
            }
            
            if (!empty($image) && strpos($image, 'data:image') !== 0) {
                if (strpos($image, 'http') !== 0) {
                    $image = rtrim($website['url'], '/') . '/' . ltrim($image, '/');
                }
                $images[] = $image;
            }
        }
    }
    
    if (!empty($images)) {
        return $images[array_rand($images)];
    }
    
    return null;
}

$posters = ['all' => null];

foreach ($HERO_CAROUSEL_WEBSITES as $websiteName => $website) {
    $poster = fetchRandomPoster($websiteName, $website);
    if ($poster) {
        $posters['all'] = $poster;
        break;
    }
}

foreach ($CATEGORIES_WEBSITES as $categoryKey => $categoryData) {
    $categoryLower = strtolower($categoryKey);
    $posters[$categoryLower] = null;
    
    foreach ($categoryData as $key => $value) {
        if ($key === 'display_name' || !is_array($value)) {
            continue;
        }
        
        $poster = fetchRandomPoster($key, $value);
        if ($poster) {
            $posters[$categoryLower] = $poster;
            break;
        }
    }
}

$output = json_encode([
    'success' => true,
    'posters' => $posters
]);

$hasAnyPoster = false;
foreach ($posters as $poster) {
    if ($poster !== null) {
        $hasAnyPoster = true;
        break;
    }
}

if ($hasAnyPoster) {
    file_put_contents($cacheFile, $output);
}

echo $output;
