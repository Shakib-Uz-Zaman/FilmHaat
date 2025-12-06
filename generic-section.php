<?php

function fetchGenericSectionMovies($websiteName, $website, $page = 1) {
    $url = $website['url'];
    if ($page > 1) {
        $url = rtrim($url, '/') . '/page/' . $page . '/';
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
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
            'error' => $error ?: 'HTTP Error: ' . $httpCode
        ];
    }
    
    $results = parseGenericSectionResults($html, $website, $websiteName);
    
    return [
        'success' => true,
        'results' => $results,
        'count' => count($results)
    ];
}

function parseGenericSectionResults($html, $website, $websiteName = '') {
    $results = [];
    
    if (empty($html)) {
        return $results;
    }
    
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();
    
    $xpath = new DOMXPath($dom);
    
    $articles = $xpath->query("//article | //div[contains(@class, 'post')] | //div[contains(@class, 'item')] | //div[contains(@class, 'movie')] | //div[contains(@class, 'bw_postlist')] | //div[contains(@class, 'slide')] | //li[contains(@class, 'thumb')]");
    
    $count = 0;
    $parserType = isset($website['parser_type']) ? $website['parser_type'] : 'default';
    
    foreach ($articles as $article) {
        if ($count >= 8) break;
        
        $isSpecialParser = $parserType === 'li_thumb' && $article->nodeName === 'li' && $article->hasAttribute('class') && strpos($article->getAttribute('class'), 'thumb') !== false;
        
        if ($isSpecialParser) {
            $titleNodes = $xpath->query(".//figcaption//a", $article);
        } else {
            $titleNodes = $xpath->query(".//a[contains(@class, 'title') or contains(@class, 'post-title') or @rel='bookmark'] | .//h2//a | .//h3//a", $article);
        }
        
        if ($titleNodes->length === 0) {
            $titleNodes = $xpath->query(".//a", $article);
        }
        
        $title = '';
        $link = '';
        
        if ($titleNodes->length > 0) {
            $titleNode = $titleNodes->item(0);
            $title = trim($titleNode->textContent);
            $link = $titleNode->getAttribute('href');
        }
        
        if (empty($title) || empty($link)) {
            continue;
        }
        
        $imageUrl = '';
        if ($isSpecialParser) {
            $imageNodes = $xpath->query(".//figure//img", $article);
        } else {
            $imageNodes = $xpath->query(".//img", $article);
        }
        
        if ($imageNodes->length > 0) {
            $img = $imageNodes->item(0);
            
            $srcset = $img->getAttribute('srcset');
            $dataSrc = $img->getAttribute('data-src');
            $src = $img->getAttribute('src');
            
            if (!empty($srcset)) {
                $srcsetParts = explode(',', $srcset);
                if (!empty($srcsetParts)) {
                    $firstSrcset = trim($srcsetParts[0]);
                    $imageUrl = preg_replace('/\s+\d+w$/', '', $firstSrcset);
                }
            }
            
            if (empty($imageUrl) && !empty($dataSrc)) {
                $imageUrl = $dataSrc;
            }
            
            if (empty($imageUrl) && !empty($src)) {
                $imageUrl = $src;
            }
        }
        
        $language = '';
        $genre = '';
        
        $metaNodes = $xpath->query(".//span[contains(@class, 'cat') or contains(@class, 'tag') or contains(@class, 'label') or contains(@class, 'meta') or contains(@class, 'genre') or contains(@class, 'language')]", $article);
        
        foreach ($metaNodes as $meta) {
            $metaText = trim($meta->textContent);
            if (!empty($metaText)) {
                if (preg_match('/hindi|english|tamil|telugu|malayalam|bengali|punjabi|marathi|gujarati|kannada/i', $metaText)) {
                    $language = $metaText;
                } else if (!empty($genre)) {
                    $genre .= ' | ' . $metaText;
                } else {
                    $genre = $metaText;
                }
            }
        }
        
        if (empty($language)) {
            if (preg_match('/\b(hindi|english|tamil|telugu|malayalam|bengali|punjabi|marathi|gujarati|kannada|dual\s+audio)\b/i', $title, $matches)) {
                $language = $matches[1];
            }
        }
        
        $results[] = [
            'title' => $title,
            'link' => $link,
            'image' => $imageUrl,
            'language' => $language,
            'genre' => $genre,
            'website' => $websiteName
        ];
        $count++;
    }
    
    return $results;
}
