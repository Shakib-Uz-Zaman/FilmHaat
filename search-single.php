<?php
header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

$websites = $SEARCH_WEBSITES;

function searchWebsite($websiteName, $website, $query) {
    if (isset($website['type']) && $website['type'] === 'api') {
        return searchAPI($websiteName, $website, $query);
    }
    
    $searchUrl = $website['url'] . '?' . $website['search_param'] . '=' . urlencode($query);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $searchUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    
    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error || $httpCode !== 200) {
        return [
            'success' => false,
            'website' => $websiteName,
            'error' => $error ?: 'HTTP Error: ' . $httpCode
        ];
    }
    
    $results = parseSearchResults($html, $website, $websiteName);
    
    return [
        'success' => true,
        'website' => $websiteName,
        'url' => $searchUrl,
        'results' => $results,
        'count' => count($results)
    ];
}

function searchAPI($websiteName, $website, $query) {
    $apiUrl = $website['api_url'] . '?query_term=' . urlencode($query) . '&limit=10';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error || $httpCode !== 200) {
        return [
            'success' => false,
            'website' => $websiteName,
            'error' => $error ?: 'HTTP Error: ' . $httpCode
        ];
    }
    
    $data = json_decode($response, true);
    $results = [];
    
    if (isset($data['data']['movies']) && is_array($data['data']['movies'])) {
        foreach ($data['data']['movies'] as $movie) {
            $language = isset($movie['language']) ? $movie['language'] : '';
            $genres = isset($movie['genres']) && is_array($movie['genres']) ? implode(', ', $movie['genres']) : '';
            $imdb = isset($movie['rating']) ? $movie['rating'] : '';
            
            $results[] = [
                'title' => $movie['title'] . ' (' . $movie['year'] . ')',
                'link' => $website['movie_base_url'] . $movie['slug'],
                'image' => $movie['medium_cover_image'] ?? '',
                'language' => $language,
                'genre' => $genres,
                'imdb' => $imdb
            ];
        }
    }
    
    return [
        'success' => true,
        'website' => $websiteName,
        'url' => $apiUrl,
        'results' => $results,
        'count' => count($results)
    ];
}

function parseSearchResults($html, $website, $websiteName) {
    $results = [];
    
    if (empty($html)) {
        return $results;
    }
    
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();
    
    $xpath = new DOMXPath($dom);
    
    $articles = $xpath->query("//article | //div[contains(@class, 'post')] | //div[contains(@class, 'item')] | //div[contains(@class, 'movie')] | //div[contains(@class, 'result')] | //div[contains(@class, 'bw_postlist')] | //li[contains(@class, 'thumb')]");
    
    $count = 0;
    $parserType = isset($website['parser_type']) ? $website['parser_type'] : 'default';
    
    foreach ($articles as $article) {
        if ($count >= 10) break;
        
        $isSpecialParser = $parserType === 'li_thumb' && $article->nodeName === 'li' && $article->hasAttribute('class') && strpos($article->getAttribute('class'), 'thumb') !== false;
        
        if ($isSpecialParser) {
            $titleNodes = $xpath->query(".//figcaption//a", $article);
        } else {
            $titleNodes = $xpath->query(".//a[.//h1[contains(@class, 'h1title')]] | .//h2//a | .//h3//a | .//a[contains(@class, 'title')] | .//h4//a | .//div[contains(@class, 'title')]//a", $article);
        }
        
        $linkNodes = $xpath->query(".//a[@href]", $article);
        $imageNodes = $xpath->query(".//img", $article);
        
        $title = '';
        $link = '';
        $image = '';
        
        if ($titleNodes->length > 0) {
            $titleNode = $titleNodes->item(0);
            $title = trim($titleNode->textContent);
            $link = $titleNode->getAttribute('href');
            
            if (empty($title) && $titleNode->hasAttribute('title')) {
                $title = trim($titleNode->getAttribute('title'));
            }
            if (empty($title) && $titleNode->hasAttribute('aria-label')) {
                $title = trim($titleNode->getAttribute('aria-label'));
            }
        } elseif ($linkNodes->length > 0) {
            $firstLink = $linkNodes->item(0);
            $link = $firstLink->getAttribute('href');
            
            $title = trim($firstLink->getAttribute('title'));
            if (empty($title)) {
                $title = trim($firstLink->getAttribute('aria-label'));
            }
            if (empty($title)) {
                $childImages = $xpath->query(".//img", $firstLink);
                if ($childImages->length > 0) {
                    $imgAlt = trim($childImages->item(0)->getAttribute('alt'));
                    if (!empty($imgAlt) && $imgAlt !== 'movie' && $imgAlt !== 'poster') {
                        $title = $imgAlt;
                    }
                }
            }
            if (empty($title)) {
                $title = trim($firstLink->textContent);
            }
        }
        
        if ($imageNodes->length > 0) {
            $imgNode = $imageNodes->item(0);
            
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
                $image = $imgNode->getAttribute('data-srcset');
                if (!empty($image) && strpos($image, ',') !== false) {
                    $srcsetParts = explode(',', $image);
                    $image = trim(explode(' ', trim($srcsetParts[0]))[0]);
                }
            }
            if (empty($image) || strpos($image, 'data:image') === 0) {
                $image = $imgNode->getAttribute('src');
            }
        }
        
        $title = preg_replace('/\s+/', ' ', $title);
        
        if (!empty($title) && !empty($link) && strlen($title) > 1 && strtolower($title) !== 'movie') {
            if (!preg_match('/^https?:\/\//', $link)) {
                $link = rtrim($website['url'], '/') . '/' . ltrim($link, '/');
            }
            
            if (!preg_match('/^https?:\/\//', $image) && !empty($image)) {
                $image = rtrim($website['url'], '/') . '/' . ltrim($image, '/');
            }
            
            $language = '';
            $genre = '';
            $imdb = '';
            
            if (preg_match('/\b(Hindi|English|Tamil|Telugu|Malayalam|Kannada|Bengali|Punjabi|Marathi|Gujarati|Dual Audio|Multi Audio)\b/i', $title, $matches)) {
                $language = $matches[1];
            }
            
            $genreNodes = $xpath->query(".//span[contains(@class, 'genre')] | .//div[contains(@class, 'genre')] | .//a[contains(@class, 'category')] | .//span[contains(@class, 'cat')]", $article);
            if ($genreNodes->length > 0) {
                $genreText = trim($genreNodes->item(0)->textContent);
                if (!empty($genreText) && strlen($genreText) < 50) {
                    $genre = $genreText;
                }
            }
            
            $ratingNodes = $xpath->query(".//span[contains(@class, 'rating')] | .//div[contains(@class, 'rating')] | .//span[contains(@class, 'imdb')] | .//div[contains(text(), 'IMDb')] | .//span[contains(text(), 'IMDb')]", $article);
            if ($ratingNodes->length > 0) {
                $ratingText = trim($ratingNodes->item(0)->textContent);
                if (preg_match('/(\d+\.?\d*)\s*\/?\s*10|(\d+\.?\d*)/', $ratingText, $ratingMatches)) {
                    $imdb = !empty($ratingMatches[1]) ? $ratingMatches[1] : $ratingMatches[2];
                }
            }
            
            $results[] = [
                'title' => $title,
                'link' => $link,
                'image' => $image,
                'language' => $language,
                'genre' => $genre,
                'imdb' => $imdb
            ];
            $count++;
        }
    }
    
    return $results;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query']) && isset($_GET['website'])) {
    $query = trim($_GET['query']);
    $websiteName = trim($_GET['website']);
    
    if (empty($query)) {
        echo json_encode([
            'success' => false,
            'error' => 'Search query cannot be empty'
        ]);
        exit;
    }
    
    if (!isset($websites[$websiteName])) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid website name'
        ]);
        exit;
    }
    
    $result = searchWebsite($websiteName, $websites[$websiteName], $query);
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request. Required parameters: query, website'
    ]);
}
?>
