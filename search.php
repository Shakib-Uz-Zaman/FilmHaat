<?php
header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

$websites = array_filter($SEARCH_WEBSITES, function($website) {
    return !isset($website['hidden']) || $website['hidden'] !== true;
});

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

function searchTypesenseAPI($websiteName, $website, $query) {
    $apiUrl = $website['api_url'] . '?q=' . urlencode($query) . '&query_by=post_title&sort_by=sort_by_date:desc&limit=10&highlight_fields=none&use_cache=true&page=1';
    
    $refererUrl = isset($website['referer']) ? $website['referer'] : $website['url'];
    
    $parsedUrl = parse_url($refererUrl);
    $originUrl = ($parsedUrl['scheme'] ?? 'https') . '://' . ($parsedUrl['host'] ?? '');
    if (isset($parsedUrl['port']) && $parsedUrl['port'] != 80 && $parsedUrl['port'] != 443) {
        $originUrl .= ':' . $parsedUrl['port'];
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_REFERER, $refererUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Origin: ' . $originUrl,
        'Accept: application/json',
        'Accept-Language: en-US,en;q=0.9'
    ]);
    
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
    
    if (isset($data['hits']) && is_array($data['hits'])) {
        foreach ($data['hits'] as $hit) {
            if (!isset($hit['document'])) continue;
            
            $doc = $hit['document'];
            $title = isset($doc['post_title']) ? $doc['post_title'] : '';
            $link = isset($doc['permalink']) ? $doc['permalink'] : '';
            $image = isset($doc['post_thumbnail']) ? $doc['post_thumbnail'] : '';
            
            if (empty($title) || empty($link)) continue;
            
            if (!preg_match('/^https?:\/\//', $link)) {
                $link = rtrim($website['url'], '/') . '/' . ltrim($link, '/');
            }
            
            $language = '';
            if (preg_match('/\b(Hindi|English|Tamil|Telugu|Malayalam|Kannada|Bengali|Punjabi|Marathi|Gujarati|Dual Audio|Multi Audio)\b/i', $title, $matches)) {
                $language = $matches[1];
            }
            
            $results[] = [
                'title' => $title,
                'link' => $link,
                'image' => $image,
                'language' => $language,
                'genre' => '',
                'imdb' => '',
                'website' => $websiteName
            ];
            
            if (count($results) >= 10) break;
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

function searchAPI($websiteName, $website, $query) {
    $parserType = isset($website['parser_type']) ? $website['parser_type'] : 'api';
    
    if ($parserType === 'typesense') {
        return searchTypesenseAPI($websiteName, $website, $query);
    }
    
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
                'imdb' => $imdb,
                'website' => $websiteName
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
    
    $articles = $xpath->query("//article | //div[contains(@class, 'post')] | //div[contains(@class, 'item')] | //div[contains(@class, 'movie')] | //div[contains(@class, 'result')] | //div[contains(@class, 'bw_postlist')] | //li[contains(@class, 'thumb')] | //a[contains(@class, 'movie-card')]");
    
    $count = 0;
    $parserType = isset($website['parser_type']) ? $website['parser_type'] : 'default';
    
    foreach ($articles as $article) {
        if ($count >= 10) break;
        
        $isMovieCardParser = $parserType === 'movie_card' && $article->nodeName === 'a' && $article->hasAttribute('class') && strpos($article->getAttribute('class'), 'movie-card') !== false;
        $isLiThumbParser = $parserType === 'li_thumb' && $article->nodeName === 'li' && $article->hasAttribute('class') && strpos($article->getAttribute('class'), 'thumb') !== false;
        
        if ($isMovieCardParser) {
            $titleNodes = $xpath->query(".//h3[contains(@class, 'movie-card-title')]", $article);
        } elseif ($isLiThumbParser) {
            $titleNodes = $xpath->query(".//figcaption//a", $article);
        } else {
            $titleNodes = $xpath->query(".//a[.//h1[contains(@class, 'h1title')]] | .//h2//a | .//h3//a | .//a[contains(@class, 'title')] | .//h4//a | .//div[contains(@class, 'title')]//a", $article);
        }
        
        $linkNodes = $xpath->query(".//a[@href]", $article);
        $imageNodes = $xpath->query(".//img", $article);
        
        $title = '';
        $link = '';
        $image = '';
        
        if ($isMovieCardParser) {
            if ($titleNodes->length > 0) {
                $title = trim($titleNodes->item(0)->textContent);
            }
            $link = $article->getAttribute('href');
        } elseif ($titleNodes->length > 0) {
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
        
        // Extract metadata (language, genre, IMDb rating)
        $language = '';
        $genre = '';
        $imdb = '';
        
        // For movie-card parser, extract from format spans
        if ($isMovieCardParser) {
            $formatNodes = $xpath->query(".//span[contains(@class, 'movie-card-format')]", $article);
            $genres = [];
            $languages = [];
            
            foreach ($formatNodes as $formatNode) {
                $formatText = trim($formatNode->textContent);
                // Check if it's a language
                if (preg_match('/^(Hindi|English|Tamil|Telugu|Malayalam|Kannada|Bengali|Punjabi|Marathi|Gujarati|Dual Audio|Multi Audio|Chinese|Korean|Japanese)$/i', $formatText)) {
                    $languages[] = $formatText;
                }
                // Check if it's a genre (not a quality or other format)
                elseif (!preg_match('/^(480p|720p|1080p|2160p|4K|HDR|WEB-DL|BluRay|HEVC|Movies|Series)$/i', $formatText)) {
                    $genres[] = $formatText;
                }
            }
            
            $language = !empty($languages) ? implode(', ', $languages) : '';
            $genre = !empty($genres) ? implode(', ', array_slice($genres, 0, 3)) : '';
        }
        
        // Try to extract language from title if not found
        if (empty($language) && preg_match('/\b(Hindi|English|Tamil|Telugu|Malayalam|Kannada|Bengali|Punjabi|Marathi|Gujarati|Dual Audio|Multi Audio)\b/i', $title, $matches)) {
            $language = $matches[1];
        }
        
        // Try to extract genre from article metadata if not found
        if (empty($genre)) {
            $genreNodes = $xpath->query(".//span[contains(@class, 'genre')] | .//div[contains(@class, 'genre')] | .//a[contains(@class, 'category')] | .//span[contains(@class, 'cat')]", $article);
            if ($genreNodes->length > 0) {
                $genreText = trim($genreNodes->item(0)->textContent);
                if (!empty($genreText) && strlen($genreText) < 50) {
                    $genre = $genreText;
                }
            }
        }
        
        // Try to extract IMDb rating
        $ratingNodes = $xpath->query(".//span[contains(@class, 'rating')] | .//div[contains(@class, 'rating')] | .//span[contains(@class, 'imdb')] | .//div[contains(text(), 'IMDb')] | .//span[contains(text(), 'IMDb')]", $article);
        if ($ratingNodes->length > 0) {
            $ratingText = trim($ratingNodes->item(0)->textContent);
            if (preg_match('/(\d+\.?\d*)\s*\/?\s*10|(\d+\.?\d*)/', $ratingText, $ratingMatches)) {
                $imdb = !empty($ratingMatches[1]) ? $ratingMatches[1] : $ratingMatches[2];
            }
        }
        
        // Filter out logos and invalid entries
        $isLogo = stripos($title, 'logo') !== false;
        $isHomePage = rtrim($link, '/') === rtrim($website['url'], '/');
        
        if (!empty($title) && !empty($link) && strlen($title) > 1 && strtolower($title) !== 'movie' && !$isLogo && !$isHomePage) {
            if (!preg_match('/^https?:\/\//', $link)) {
                $link = rtrim($website['url'], '/') . '/' . ltrim($link, '/');
            }
            
            if (!preg_match('/^https?:\/\//', $image) && !empty($image)) {
                $image = rtrim($website['url'], '/') . '/' . ltrim($image, '/');
            }
            
            $results[] = [
                'title' => $title,
                'link' => $link,
                'image' => $image,
                'language' => $language,
                'genre' => $genre,
                'imdb' => $imdb,
                'website' => $websiteName
            ];
            $count++;
        }
    }
    
    return $results;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = trim($_GET['query']);
    
    if (empty($query)) {
        echo json_encode([
            'success' => false,
            'error' => 'Search query cannot be empty'
        ]);
        exit;
    }
    
    $allResults = [];
    
    foreach ($websites as $websiteName => $website) {
        $result = searchWebsite($websiteName, $website, $query);
        $allResults[] = $result;
    }
    
    echo json_encode([
        'success' => true,
        'query' => $query,
        'results' => $allResults
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request'
    ]);
}
?>
