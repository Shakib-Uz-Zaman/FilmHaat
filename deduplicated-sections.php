<?php
header('Content-Type: application/json; charset=utf-8');

define('DEDUPLICATION_MODE', true);

require_once 'config.php';
require_once 'generic-section.php';

function normalizeTitle($title) {
    $title = strtolower(trim($title));
    $title = preg_replace('/\s+/', ' ', $title);
    $title = preg_replace('/\b(hindi|english|tamil|telugu|malayalam|kannada|bengali|punjabi|marathi|gujarati|dual audio|multi audio|720p|1080p|480p|web-dl|bluray|hdrip|webrip)\b/i', '', $title);
    $title = preg_replace('/\s+/', ' ', trim($title));
    return $title;
}

function getMovieIdentifier($movie) {
    return md5(normalizeTitle($movie['title']) . '|' . parse_url($movie['link'], PHP_URL_PATH));
}

function deduplicateSections($page = 1, $specificSection = null) {
    $sectionPriority = [];
    $sectionsConfig = [];
    $priority = 1;
    
    foreach ($GLOBALS['ALL_SECTION_WEBSITES'] as $configKey => $websites) {
        $sectionKey = strtolower($configKey);
        
        if ($specificSection !== null && $sectionKey !== $specificSection) {
            continue;
        }
        
        $sectionPriority[$sectionKey] = $priority++;
        
        $displayName = isset($websites['display_name']) ? $websites['display_name'] : ucwords(str_replace('_', ' ', $configKey));
        
        $sectionsConfig[$sectionKey] = [
            'websites' => array_filter($websites, function($w, $k) { return $k !== 'display_name' && (!isset($w['hidden']) || !$w['hidden']); }, ARRAY_FILTER_USE_BOTH),
            'fetch_func' => 'fetchGenericSectionMovies',
            'name' => $displayName
        ];
    }
    
    $allMoviesBySection = [];
    
    foreach ($sectionsConfig as $sectionKey => $config) {
        $allMoviesBySection[$sectionKey] = [];
        
        foreach ($config['websites'] as $websiteName => $website) {
            $currentPage = $page;
            $moviesNeeded = 20;
            
            while (count($allMoviesBySection[$sectionKey]) < $moviesNeeded && $currentPage <= ($page + 3)) {
                $result = call_user_func($config['fetch_func'], $websiteName, $website, $currentPage);
                
                if ($result['success'] && !empty($result['results'])) {
                    foreach ($result['results'] as $movie) {
                        $allMoviesBySection[$sectionKey][] = $movie;
                    }
                }
                
                if ($result['success'] && $result['count'] < 8) {
                    break;
                }
                
                $currentPage++;
            }
        }
    }
    
    $movieToSectionMap = [];
    $globalSeenMovies = [];
    
    foreach ($sectionPriority as $sectionKey => $priority) {
        if (!isset($allMoviesBySection[$sectionKey])) continue;
        
        foreach ($allMoviesBySection[$sectionKey] as $movie) {
            $identifier = getMovieIdentifier($movie);
            
            if (!isset($globalSeenMovies[$identifier])) {
                $globalSeenMovies[$identifier] = true;
                $movieToSectionMap[$identifier] = [
                    'section' => $sectionKey,
                    'movie' => $movie
                ];
            }
        }
    }
    
    $deduplicatedSections = [];
    foreach (array_keys($sectionsConfig) as $sectionKey) {
        $deduplicatedSections[$sectionKey] = [];
    }
    
    foreach ($movieToSectionMap as $identifier => $data) {
        $deduplicatedSections[$data['section']][] = $data['movie'];
    }
    
    $finalSections = [];
    foreach ($sectionsConfig as $sectionKey => $config) {
        if (isset($deduplicatedSections[$sectionKey])) {
            $movies = $deduplicatedSections[$sectionKey];
            shuffle($movies);
            $movies = array_slice($movies, 0, 12);
            
            $finalSections[$sectionKey] = [
                'success' => true,
                'name' => $config['name'],
                'results' => $movies,
                'count' => count($movies)
            ];
        } else {
            $finalSections[$sectionKey] = [
                'success' => true,
                'name' => $config['name'],
                'results' => [],
                'count' => 0
            ];
        }
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
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method'
    ]);
}
?>
