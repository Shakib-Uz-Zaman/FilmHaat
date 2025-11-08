<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

const VIEWS_FILE = 'data/weekly_views.json';
const SEVEN_DAYS_SECONDS = 7 * 24 * 60 * 60;

function ensureDataDirectory() {
    if (!file_exists('data')) {
        mkdir('data', 0755, true);
    }
}

function cleanOldViews($views) {
    $cutoffTime = time() - SEVEN_DAYS_SECONDS;
    return array_filter($views, function($view) use ($cutoffTime) {
        return $view['timestamp'] > $cutoffTime;
    });
}

function loadViews() {
    ensureDataDirectory();
    if (!file_exists(VIEWS_FILE)) {
        return [];
    }
    
    $content = file_get_contents(VIEWS_FILE);
    $views = json_decode($content, true);
    return is_array($views) ? $views : [];
}

function saveViews($views) {
    ensureDataDirectory();
    $fp = fopen(VIEWS_FILE, 'c');
    if ($fp === false) {
        return false;
    }
    
    if (flock($fp, LOCK_EX)) {
        ftruncate($fp, 0);
        fwrite($fp, json_encode($views));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;
    } else {
        fclose($fp);
        return false;
    }
}

function trackView($title, $link, $image, $language) {
    if (empty($title) || empty($link)) {
        error_log("Weekly Top 10: Missing title or link");
        return ['success' => false, 'error' => 'Title and link are required'];
    }
    
    $views = loadViews();
    $views = cleanOldViews($views);
    
    $newView = [
        'title' => $title,
        'link' => $link,
        'image' => $image,
        'language' => $language,
        'timestamp' => time()
    ];
    
    $views[] = $newView;
    $saved = saveViews($views);
    
    if (!$saved) {
        error_log("Weekly Top 10: Failed to save view - " . substr($title, 0, 50));
        return ['success' => false, 'error' => 'Failed to save view'];
    }
    
    error_log("Weekly Top 10: View tracked successfully - " . substr($title, 0, 50));
    return ['success' => true];
}

function getTop10() {
    $views = loadViews();
    $views = cleanOldViews($views);
    
    $movieCounts = [];
    
    foreach ($views as $view) {
        $movieKey = $view['title'] . '|' . $view['link'];
        
        if (!isset($movieCounts[$movieKey])) {
            $movieCounts[$movieKey] = [
                'title' => $view['title'],
                'link' => $view['link'],
                'image' => $view['image'],
                'language' => $view['language'],
                'count' => 0,
                'lastViewed' => $view['timestamp']
            ];
        }
        
        $movieCounts[$movieKey]['count']++;
        if ($view['timestamp'] > $movieCounts[$movieKey]['lastViewed']) {
            $movieCounts[$movieKey]['lastViewed'] = $view['timestamp'];
        }
    }
    
    $moviesArray = array_values($movieCounts);
    
    usort($moviesArray, function($a, $b) {
        return $b['count'] - $a['count'];
    });
    
    return array_slice($moviesArray, 0, 10);
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    if ($input === null) {
        error_log("Weekly Top 10: Invalid JSON received - " . substr($rawInput, 0, 100));
        echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
        exit;
    }
    
    if (isset($input['action']) && $input['action'] === 'track') {
        $result = trackView(
            $input['title'] ?? '',
            $input['link'] ?? '',
            $input['image'] ?? '',
            $input['language'] ?? ''
        );
        echo json_encode($result);
    } else {
        error_log("Weekly Top 10: Invalid action - " . ($input['action'] ?? 'none'));
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} elseif ($method === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'top10') {
        $top10 = getTop10();
        echo json_encode($top10);
    } else {
        echo json_encode(['error' => 'Invalid action']);
    }
} else {
    echo json_encode(['error' => 'Method not allowed']);
}
