<?php
header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';
require_once 'generic-section.php';

$websites = array_filter($HERO_CAROUSEL_WEBSITES, function($website) {
    return !isset($website['hidden']) || $website['hidden'] !== true;
});

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$allResults = [];

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
    'results' => $allResults
], JSON_UNESCAPED_UNICODE);
?>
