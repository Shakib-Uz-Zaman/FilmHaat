<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=300, stale-while-revalidate=600');
header('Vary: Accept-Encoding');

require_once 'config.php';
require_once 'generic-section.php';

function normalizeTitle($title) {
    $title = strtolower(trim($title));
    
    $title = preg_replace([
        '/\b(?:hindi|english|tamil|telugu|malayalam|kannada|bengali|punjabi|marathi|gujarati|dual\s*audio|multi\s*audio|[0-9]+p|web-dl|bluray|hdrip|webrip)\b/',
        '/\s+/'
    ], [
        '',
        ' '
    ], $title);
    
    return trim($title);
}

function deduplicateSections($page = 1, $specificSection = null) {
    $sectionsConfig = [];
    
    foreach ($GLOBALS['ALL_SECTION_WEBSITES'] as $configKey => $websites) {
        if ($configKey === 'SEARCH_LINKS' || $configKey === 'MOVIE_COLLECTIONS') {
            continue;
        }
        
        $sectionKey = strtolower($configKey);
        
        if ($specificSection !== null && $sectionKey !== $specificSection) {
            continue;
        }
        
        $displayName = isset($websites['display_name']) ? $websites['display_name'] : ucwords(str_replace('_', ' ', $configKey));
        
        $sectionsConfig[$sectionKey] = [
            'websites' => array_filter($websites, function($w, $k) { return $k !== 'display_name' && (!isset($w['hidden']) || !$w['hidden']); }, ARRAY_FILTER_USE_BOTH),
            'name' => $displayName
        ];
    }
    
    $allMoviesBySection = [];
    foreach ($sectionsConfig as $sectionKey => $config) {
        foreach ($config['websites'] as $websiteName => $website) {
            $result = fetchGenericSectionMovies($websiteName, $website, $page);
            
            if ($result['success'] && !empty($result['results'])) {
                foreach ($result['results'] as $movie) {
                    $allMoviesBySection[$sectionKey][] = $movie;
                }
            }
        }
    }
    
    $finalSections = [];
    
    foreach ($sectionsConfig as $sectionKey => $config) {
        $movies = isset($allMoviesBySection[$sectionKey]) ? $allMoviesBySection[$sectionKey] : [];
        $seenTitles = [];
        $uniqueMovies = [];
        
        foreach ($movies as $movie) {
            if (count($uniqueMovies) >= 12) {
                break;
            }
            
            $normalizedTitle = normalizeTitle($movie['title']);
            
            if (!isset($seenTitles[$normalizedTitle])) {
                $seenTitles[$normalizedTitle] = true;
                $uniqueMovies[] = $movie;
            }
        }
        
        $finalSections[$sectionKey] = [
            'success' => true,
            'name' => $config['name'],
            'results' => $uniqueMovies,
            'count' => count($uniqueMovies)
        ];
    }
    
    return $finalSections;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $section = isset($_GET['section']) ? $_GET['section'] : null;
    
    $result = deduplicateSections($page, $section);
    
    echo json_encode([
        'success' => true,
        'sections' => $result,
        'page' => $page
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method'
    ]);
}
