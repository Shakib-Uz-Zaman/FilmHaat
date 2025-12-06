<?php

function escapePhpString($str) {
    return str_replace(
        ['\\', "'"],
        ['\\\\', "\\'"],
        $str
    );
}

function arrayToPhpCode($array, $indent = 0) {
    $indentStr = str_repeat('    ', $indent);
    $lines = [];
    $lines[] = '[';
    
    if (isset($array['display_name'])) {
        $keyStr = "'display_name'";
        $valueStr = "'" . escapePhpString($array['display_name']) . "'";
        $lines[] = $indentStr . '    ' . $keyStr . ' => ' . $valueStr . ',';
    }
    
    foreach ($array as $key => $value) {
        if ($key === 'display_name') {
            continue;
        }
        
        $keyStr = is_string($key) ? "'" . escapePhpString($key) . "'" : $key;
        
        if (is_array($value)) {
            $valueStr = arrayToPhpCode($value, $indent + 1);
        } elseif (is_string($value)) {
            $valueStr = "'" . escapePhpString($value) . "'";
        } elseif (is_bool($value)) {
            $valueStr = $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            $valueStr = 'null';
        } else {
            $valueStr = $value;
        }
        
        $lines[] = $indentStr . '    ' . $keyStr . ' => ' . $valueStr . ',';
    }
    
    $lines[] = $indentStr . ']';
    return implode("\n", $lines);
}

function saveConfigToFile($config) {
    $backupFile = 'config.backup.php';
    if (file_exists('config.php')) {
        copy('config.php', $backupFile);
    }
    
    $content = "<?php\n\n";
    $content .= "\$ALL_SECTION_WEBSITES = " . arrayToPhpCode($config['ALL_SECTION_WEBSITES']) . ";\n\n";
    $content .= "\$CATEGORIES_WEBSITES = " . arrayToPhpCode($config['CATEGORIES_WEBSITES']) . ";\n\n";
    $content .= "\$LATEST_WEBSITES = " . arrayToPhpCode($config['LATEST_WEBSITES']) . ";\n\n";
    $content .= "\$SEARCH_WEBSITES = " . arrayToPhpCode($config['SEARCH_WEBSITES']) . ";\n\n";
    $content .= "\$HERO_CAROUSEL_WEBSITES = " . arrayToPhpCode($config['HERO_CAROUSEL_WEBSITES']) . ";\n\n";
    $content .= "\$HERO_CAROUSEL_MANUAL_MOVIES = " . arrayToPhpCode($config['HERO_CAROUSEL_MANUAL_MOVIES']) . ";\n\n";
    $content .= "\$MOVIE_COLLECTIONS_DATA = " . arrayToPhpCode($config['MOVIE_COLLECTIONS_DATA']) . ";\n\n";
    $content .= "\$SITE_SETTINGS = " . arrayToPhpCode($config['SITE_SETTINGS']) . ";\n\n";
    $content .= "?>\n";
    
    $result = file_put_contents('config.php', $content);
    
    if ($result === false) {
        if (file_exists($backupFile)) {
            copy($backupFile, 'config.php');
        }
        return false;
    }
    
    if (function_exists('opcache_invalidate')) {
        opcache_invalidate('config.php', true);
    }
    
    return true;
}

function loadConfig() {
    require 'config.php';
    
    return [
        'ALL_SECTION_WEBSITES' => $ALL_SECTION_WEBSITES,
        'CATEGORIES_WEBSITES' => $CATEGORIES_WEBSITES,
        'LATEST_WEBSITES' => $LATEST_WEBSITES,
        'SEARCH_WEBSITES' => $SEARCH_WEBSITES,
        'HERO_CAROUSEL_WEBSITES' => $HERO_CAROUSEL_WEBSITES,
        'HERO_CAROUSEL_MANUAL_MOVIES' => $HERO_CAROUSEL_MANUAL_MOVIES,
        'MOVIE_COLLECTIONS_DATA' => $MOVIE_COLLECTIONS_DATA,
        'SITE_SETTINGS' => $SITE_SETTINGS
    ];
}

function getFaviconUrl($url) {
    if (empty($url)) {
        return '';
    }
    $parsedUrl = parse_url($url);
    if (!isset($parsedUrl['host'])) {
        return '';
    }
    $domain = $parsedUrl['host'];
    return 'https://www.google.com/s2/favicons?domain=' . urlencode($domain) . '&sz=16';
}

function getDomainFromUrl($url) {
    if (empty($url)) {
        return '';
    }
    $parsedUrl = parse_url($url);
    return isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
}

function sendSuccess($message, $extra = []) {
    $response = array_merge(['success' => true, 'message' => $message], $extra);
    echo json_encode($response);
    exit;
}

function sendError($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

function requireFields($input, $fields) {
    foreach ($fields as $field) {
        if (empty($input[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
    }
}

function saveAndRespond($config, $successMessage, $extra = []) {
    if (saveConfigToFile($config)) {
        sendSuccess($successMessage, $extra);
    } else {
        throw new Exception('Failed to save config file');
    }
}

function moveItemInArray($array, $key, $direction) {
    $keys = array_keys($array);
    $currentIndex = array_search($key, $keys);
    
    if ($currentIndex === false) {
        throw new Exception('Item not found');
    }
    
    if ($direction === 'up' && $currentIndex === 0) {
        throw new Exception('Item is already at the top');
    }
    
    if ($direction === 'down' && $currentIndex === count($keys) - 1) {
        throw new Exception('Item is already at the bottom');
    }
    
    $newIndex = $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;
    
    $temp = $keys[$currentIndex];
    $keys[$currentIndex] = $keys[$newIndex];
    $keys[$newIndex] = $temp;
    
    $newArray = [];
    foreach ($keys as $k) {
        $newArray[$k] = $array[$k];
    }
    
    return $newArray;
}

function moveItemInIndexedArray($array, $index, $direction) {
    if (!isset($array[$index])) {
        throw new Exception('Item not found');
    }
    
    if ($direction === 'up' && $index === 0) {
        throw new Exception('Item is already at the top');
    }
    
    if ($direction === 'down' && $index === count($array) - 1) {
        throw new Exception('Item is already at the bottom');
    }
    
    $newIndex = $direction === 'up' ? $index - 1 : $index + 1;
    
    $temp = $array[$index];
    $array[$index] = $array[$newIndex];
    $array[$newIndex] = $temp;
    
    return $array;
}

?>
