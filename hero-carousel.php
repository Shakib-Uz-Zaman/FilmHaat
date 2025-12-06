<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=300, stale-while-revalidate=600');
header('Vary: Accept-Encoding');

require_once 'config.php';
require_once 'generic-section.php';

$websites = array_filter($HERO_CAROUSEL_WEBSITES, function($website) {
    return !isset($website['hidden']) || $website['hidden'] !== true;
});

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$allResults = [];

foreach ($HERO_CAROUSEL_MANUAL_MOVIES as $manualMovie) {
    if (!isset($manualMovie['hidden']) || $manualMovie['hidden'] !== true) {
        $allResults[] = $manualMovie;
    }
}

foreach ($websites as $websiteName => $website) {
    $result = fetchGenericSectionMovies($websiteName, $website, $page);
    
    if ($result['success'] && !empty($result['results'])) {
        foreach ($result['results'] as $movie) {
            $allResults[] = $movie;
        }
    }
}

if (empty($allResults)) {
    echo json_encode([
        'success' => false,
        'error' => 'No hero carousel movies found',
        'count' => 0,
        'results' => []
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'success' => true,
    'count' => count($allResults),
    'results' => $allResults,
    'manual_count' => count($HERO_CAROUSEL_MANUAL_MOVIES)
], JSON_UNESCAPED_UNICODE);
