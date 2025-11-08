<?php
header('Content-Type: application/json');

// Protect all POST requests - Only authenticated admins can modify config
require_once 'auth-check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST requests allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

$action = $input['action'] ?? '';
$configType = $input['config_type'] ?? '';

require_once 'config.php';

$config = [
    'ALL_SECTION_WEBSITES' => $ALL_SECTION_WEBSITES,
    'CATEGORIES_WEBSITES' => $CATEGORIES_WEBSITES,
    'LATEST_WEBSITES' => $LATEST_WEBSITES,
    'SEARCH_WEBSITES' => $SEARCH_WEBSITES,
    'HERO_CAROUSEL_WEBSITES' => $HERO_CAROUSEL_WEBSITES,
    'HERO_CAROUSEL_MANUAL_MOVIES' => $HERO_CAROUSEL_MANUAL_MOVIES,
    'SITE_SETTINGS' => $SITE_SETTINGS
];

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
    
    // If display_name exists, output it first
    if (isset($array['display_name'])) {
        $keyStr = "'display_name'";
        $valueStr = "'" . escapePhpString($array['display_name']) . "'";
        $lines[] = $indentStr . '    ' . $keyStr . ' => ' . $valueStr . ',';
    }
    
    foreach ($array as $key => $value) {
        // Skip display_name as we already processed it
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

try {
    if ($action === 'add') {
        if ($configType === 'SEARCH_WEBSITES') {
            $name = $input['name'] ?? '';
            $url = $input['url'] ?? '';
            $searchParam = $input['search_param'] ?? 's';
            $type = $input['type'] ?? 'html';
            $parserType = $input['parser_type'] ?? 'default';
            
            if (empty($name) || empty($url)) {
                throw new Exception('Name and URL are required');
            }
            
            $config['SEARCH_WEBSITES'][$name] = [
                'url' => $url,
                'search_param' => $searchParam,
                'type' => $type,
                'parser_type' => $parserType
            ];
            
            if ($type === 'api' && !empty($input['api_url'])) {
                $config['SEARCH_WEBSITES'][$name]['api_url'] = $input['api_url'];
            }
            
            if ($type === 'api' && !empty($input['movie_base_url'])) {
                $config['SEARCH_WEBSITES'][$name]['movie_base_url'] = $input['movie_base_url'];
            }
            
        } elseif ($configType === 'HERO_CAROUSEL_WEBSITES') {
            $name = $input['name'] ?? '';
            $url = $input['url'] ?? '';
            $parserType = $input['parser_type'] ?? 'li_thumb';
            
            if (empty($name) || empty($url)) {
                throw new Exception('Name and URL are required');
            }
            
            $config['HERO_CAROUSEL_WEBSITES'][$name] = [
                'url' => $url,
                'parser_type' => $parserType
            ];
            
        } else {
            $section = $input['section'] ?? '';
            $name = $input['name'] ?? '';
            $url = $input['url'] ?? '';
            $parserType = $input['parser_type'] ?? 'li_thumb';
            $displayName = $input['display_name'] ?? '';
            
            if (empty($section) || empty($name) || empty($url)) {
                throw new Exception('Section, name and URL are required');
            }
            
            if (!isset($config[$configType][$section])) {
                $config[$configType][$section] = [];
            }
            
            if (!empty($displayName)) {
                $config[$configType][$section]['display_name'] = $displayName;
            }
            
            $config[$configType][$section][$name] = [
                'url' => $url,
                'parser_type' => $parserType
            ];
        }
        
        if (saveConfigToFile($config)) {
            $successMessage = 'Config added successfully';
            if ($configType === 'ALL_SECTION_WEBSITES' || $configType === 'CATEGORIES_WEBSITES') {
                $successMessage .= " to section '$section'. Scroll down to see it at the bottom of the list.";
            }
            echo json_encode(['success' => true, 'message' => $successMessage]);
        } else {
            throw new Exception('Failed to save config file');
        }
        
    } elseif ($action === 'edit') {
        if ($configType === 'SEARCH_WEBSITES') {
            $oldName = $input['old_name'] ?? '';
            $newName = $input['name'] ?? '';
            $url = $input['url'] ?? '';
            $searchParam = $input['search_param'] ?? 's';
            $type = $input['type'] ?? 'html';
            $parserType = $input['parser_type'] ?? 'default';
            
            error_log("SEARCH_WEBSITES Edit: oldName='$oldName', newName='$newName', url='$url'");
            
            if (empty($oldName) || empty($newName) || empty($url)) {
                throw new Exception('Name and URL are required');
            }
            
            // Create new data array
            $newData = [
                'url' => $url,
                'search_param' => $searchParam,
                'type' => $type,
                'parser_type' => $parserType
            ];
            
            if ($type === 'api' && !empty($input['api_url'])) {
                $newData['api_url'] = $input['api_url'];
            }
            
            if ($type === 'api' && !empty($input['movie_base_url'])) {
                $newData['movie_base_url'] = $input['movie_base_url'];
            }
            
            // Preserve order by rebuilding the array
            $newConfig = [];
            foreach ($config['SEARCH_WEBSITES'] as $key => $value) {
                if ($key === $oldName) {
                    // Replace the old item with new data at the same position
                    $newConfig[$newName] = $newData;
                } else {
                    // Keep existing items
                    $newConfig[$key] = $value;
                }
            }
            $config['SEARCH_WEBSITES'] = $newConfig;
            
            error_log("Keys after edit: " . implode(', ', array_keys($config['SEARCH_WEBSITES'])));
            
        } elseif ($configType === 'HERO_CAROUSEL_WEBSITES') {
            $oldName = $input['old_name'] ?? '';
            $newName = $input['name'] ?? '';
            $url = $input['url'] ?? '';
            $parserType = $input['parser_type'] ?? 'li_thumb';
            
            if (empty($oldName) || empty($newName) || empty($url)) {
                throw new Exception('Name and URL are required');
            }
            
            // Create new data array
            $newData = [
                'url' => $url,
                'parser_type' => $parserType
            ];
            
            // Preserve order by rebuilding the array
            $newConfig = [];
            foreach ($config['HERO_CAROUSEL_WEBSITES'] as $key => $value) {
                if ($key === $oldName) {
                    // Replace the old item with new data at the same position
                    $newConfig[$newName] = $newData;
                } else {
                    // Keep existing items
                    $newConfig[$key] = $value;
                }
            }
            $config['HERO_CAROUSEL_WEBSITES'] = $newConfig;
            
        } else {
            $oldSection = $input['old_section'] ?? '';
            $oldName = $input['old_name'] ?? '';
            $newSection = $input['section'] ?? '';
            $newName = $input['name'] ?? '';
            $url = $input['url'] ?? '';
            $parserType = $input['parser_type'] ?? 'li_thumb';
            $displayName = $input['display_name'] ?? '';
            
            if (empty($oldSection) || empty($oldName) || empty($newSection) || empty($newName) || empty($url)) {
                throw new Exception('All fields are required');
            }
            
            // Create new data array
            $newData = [
                'url' => $url,
                'parser_type' => $parserType
            ];
            
            if ($oldSection === $newSection) {
                // Editing within the same section - preserve item order
                $newSectionData = [];
                $displayNameAdded = false;
                
                foreach ($config[$configType][$oldSection] as $key => $value) {
                    if ($key === $oldName) {
                        // Replace the old item with new data at the same position
                        $newSectionData[$newName] = $newData;
                    } else if ($key === 'display_name') {
                        // Keep display_name and update if provided
                        if (!empty($displayName)) {
                            $newSectionData[$key] = $displayName;
                        } else {
                            $newSectionData[$key] = $value;
                        }
                        $displayNameAdded = true;
                    } else {
                        // Keep existing items
                        $newSectionData[$key] = $value;
                    }
                }
                
                // If display_name was not found but provided, add it at the beginning
                if (!$displayNameAdded && !empty($displayName)) {
                    $newSectionData = array_merge(['display_name' => $displayName], $newSectionData);
                }
                
                $config[$configType][$oldSection] = $newSectionData;
            } else {
                // Moving to a different section
                unset($config[$configType][$oldSection][$oldName]);
                
                if (!isset($config[$configType][$newSection])) {
                    $config[$configType][$newSection] = [];
                }
                
                if (!empty($displayName)) {
                    $config[$configType][$newSection]['display_name'] = $displayName;
                }
                
                $config[$configType][$newSection][$newName] = $newData;
            }
        }
        
        error_log("Attempting to save config file...");
        if (saveConfigToFile($config)) {
            error_log("Config file saved successfully");
            echo json_encode(['success' => true, 'message' => 'Config updated successfully']);
        } else {
            error_log("Failed to save config file");
            throw new Exception('Failed to save config file');
        }
        
    } elseif ($action === 'toggle_hide') {
        if ($configType === 'SEARCH_WEBSITES') {
            $name = $input['name'] ?? '';
            
            if (empty($name)) {
                throw new Exception('Name is required');
            }
            
            if (!isset($config['SEARCH_WEBSITES'][$name])) {
                throw new Exception('Config not found');
            }
            
            $isHidden = $config['SEARCH_WEBSITES'][$name]['hidden'] ?? false;
            $config['SEARCH_WEBSITES'][$name]['hidden'] = !$isHidden;
            
        } elseif ($configType === 'HERO_CAROUSEL_WEBSITES') {
            $name = $input['name'] ?? '';
            
            if (empty($name)) {
                throw new Exception('Name is required');
            }
            
            if (!isset($config['HERO_CAROUSEL_WEBSITES'][$name])) {
                throw new Exception('Config not found');
            }
            
            $isHidden = $config['HERO_CAROUSEL_WEBSITES'][$name]['hidden'] ?? false;
            $config['HERO_CAROUSEL_WEBSITES'][$name]['hidden'] = !$isHidden;
            
        } else {
            $section = $input['section'] ?? '';
            $name = $input['name'] ?? '';
            
            if (empty($section) || empty($name)) {
                throw new Exception('Section and name are required');
            }
            
            if (!isset($config[$configType][$section][$name])) {
                throw new Exception('Config not found');
            }
            
            $isHidden = $config[$configType][$section][$name]['hidden'] ?? false;
            $config[$configType][$section][$name]['hidden'] = !$isHidden;
        }
        
        if (saveConfigToFile($config)) {
            echo json_encode(['success' => true, 'message' => 'Visibility toggled successfully']);
        } else {
            throw new Exception('Failed to save config file');
        }
        
    } elseif ($action === 'move_section') {
        $section = $input['section'] ?? '';
        $direction = $input['direction'] ?? '';
        
        if (empty($section) || empty($direction) || !in_array($direction, ['up', 'down'])) {
            throw new Exception('Section and valid direction (up/down) are required');
        }
        
        if ($configType !== 'ALL_SECTION_WEBSITES' && $configType !== 'CATEGORIES_WEBSITES' && $configType !== 'LATEST_WEBSITES' && $configType !== 'SEARCH_WEBSITES') {
            throw new Exception('Move section is only supported for ALL_SECTION_WEBSITES, CATEGORIES_WEBSITES, LATEST_WEBSITES, and SEARCH_WEBSITES');
        }
        
        $keys = array_keys($config[$configType]);
        $currentIndex = array_search($section, $keys);
        
        if ($currentIndex === false) {
            throw new Exception('Section not found');
        }
        
        if ($direction === 'up' && $currentIndex === 0) {
            throw new Exception('Section is already at the top');
        }
        
        if ($direction === 'down' && $currentIndex === count($keys) - 1) {
            throw new Exception('Section is already at the bottom');
        }
        
        $newIndex = $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;
        
        $temp = $keys[$currentIndex];
        $keys[$currentIndex] = $keys[$newIndex];
        $keys[$newIndex] = $temp;
        
        $newConfig = [];
        foreach ($keys as $key) {
            $newConfig[$key] = $config[$configType][$key];
        }
        
        $config[$configType] = $newConfig;
        
        if (saveConfigToFile($config)) {
            echo json_encode(['success' => true, 'message' => 'Section moved successfully']);
        } else {
            throw new Exception('Failed to save config file');
        }
        
    } elseif ($action === 'reset_weekly_views') {
        $weeklyViewsFile = 'data/weekly_views.json';
        
        if (!file_exists('data')) {
            mkdir('data', 0755, true);
        }
        
        $result = file_put_contents($weeklyViewsFile, '[]');
        
        if ($result !== false) {
            error_log("Weekly Top 10: Data reset successfully");
            echo json_encode(['success' => true, 'message' => 'Weekly views data reset successfully! All tracking data has been cleared.']);
        } else {
            throw new Exception('Failed to reset weekly views file');
        }
        
    } elseif ($action === 'edit_display_name') {
        $section = $input['section'] ?? '';
        $displayName = $input['display_name'] ?? '';
        
        if (empty($section) || empty($displayName)) {
            throw new Exception('Section and display name are required');
        }
        
        if ($configType === 'CATEGORIES_WEBSITES') {
            if (!isset($config['CATEGORIES_WEBSITES'][$section])) {
                throw new Exception('Category not found');
            }
            
            $config['CATEGORIES_WEBSITES'][$section]['display_name'] = $displayName;
        } elseif ($configType === 'LATEST_WEBSITES') {
            if (!isset($config['LATEST_WEBSITES'][$section])) {
                throw new Exception('Section not found');
            }
            
            $config['LATEST_WEBSITES'][$section]['display_name'] = $displayName;
        } else {
            if (!isset($config['ALL_SECTION_WEBSITES'][$section])) {
                throw new Exception('Section not found');
            }
            
            $config['ALL_SECTION_WEBSITES'][$section]['display_name'] = $displayName;
        }
        
        if (saveConfigToFile($config)) {
            echo json_encode(['success' => true, 'message' => 'Display name updated successfully']);
        } else {
            throw new Exception('Failed to save config file');
        }
        
    } elseif ($action === 'update_site_settings') {
        $websiteName = $input['website_name'] ?? '';
        
        if (empty($websiteName)) {
            throw new Exception('Website name is required');
        }
        
        $config['SITE_SETTINGS']['website_name'] = $websiteName;
        
        if (saveConfigToFile($config)) {
            echo json_encode(['success' => true, 'message' => 'Site settings updated successfully']);
        } else {
            throw new Exception('Failed to save config file');
        }
        
    } elseif ($action === 'delete') {
        if ($configType === 'SEARCH_WEBSITES') {
            $name = $input['name'] ?? '';
            
            if (empty($name)) {
                throw new Exception('Name is required');
            }
            
            if (!isset($config['SEARCH_WEBSITES'][$name])) {
                throw new Exception('Config not found');
            }
            
            unset($config['SEARCH_WEBSITES'][$name]);
            
        } elseif ($configType === 'HERO_CAROUSEL_WEBSITES') {
            $name = $input['name'] ?? '';
            
            if (empty($name)) {
                throw new Exception('Name is required');
            }
            
            if (!isset($config['HERO_CAROUSEL_WEBSITES'][$name])) {
                throw new Exception('Config not found');
            }
            
            unset($config['HERO_CAROUSEL_WEBSITES'][$name]);
            
        } else {
            $section = $input['section'] ?? '';
            $name = $input['name'] ?? '';
            
            if (empty($section) || empty($name)) {
                throw new Exception('Section and name are required');
            }
            
            if (!isset($config[$configType][$section][$name])) {
                throw new Exception('Config not found');
            }
            
            // Check if this is a built-in feature (protect LATEST category in LATEST_WEBSITES)
            if ($configType === 'LATEST_WEBSITES' && $section === 'LATEST') {
                throw new Exception('Cannot delete built-in feature. This is a system-protected category.');
            }
            
            // Check if this is a built-in feature in ALL_SECTION_WEBSITES (protect WEEKLY_TOP_10)
            if ($configType === 'ALL_SECTION_WEBSITES' && $section === 'WEEKLY_TOP_10') {
                throw new Exception('Cannot delete built-in feature. This is a system-protected section.');
            }
            
            unset($config[$configType][$section][$name]);
            
            $remainingKeys = array_filter(
                array_keys($config[$configType][$section]), 
                function($k) { return $k !== 'display_name'; }
            );
            
            if (empty($remainingKeys)) {
                unset($config[$configType][$section]);
            }
        }
        
        if (saveConfigToFile($config)) {
            echo json_encode(['success' => true, 'message' => 'Config deleted successfully']);
        } else {
            throw new Exception('Failed to save config file');
        }
        
    } elseif ($action === 'add_hero_manual_movie') {
        $movie = $input['movie'] ?? null;
        
        if (!$movie || !isset($movie['title']) || !isset($movie['link'])) {
            throw new Exception('Invalid movie data');
        }
        
        if (!isset($config['HERO_CAROUSEL_MANUAL_MOVIES']) || !is_array($config['HERO_CAROUSEL_MANUAL_MOVIES'])) {
            $config['HERO_CAROUSEL_MANUAL_MOVIES'] = [];
        }
        
        $movieData = [
            'title' => $movie['title'],
            'link' => $movie['link'],
            'image' => $movie['image'] ?? '',
            'language' => $movie['language'] ?? '',
            'genre' => $movie['genre'] ?? '',
            'website' => $movie['website'] ?? '',
            'hidden' => false
        ];
        
        array_unshift($config['HERO_CAROUSEL_MANUAL_MOVIES'], $movieData);
        
        if (saveConfigToFile($config)) {
            echo json_encode(['success' => true, 'message' => 'Movie added to hero carousel successfully']);
        } else {
            throw new Exception('Failed to save config file');
        }
        
    } elseif ($action === 'delete_hero_manual_movie') {
        $index = $input['index'] ?? null;
        
        if (!isset($config['HERO_CAROUSEL_MANUAL_MOVIES']) || !is_array($config['HERO_CAROUSEL_MANUAL_MOVIES'])) {
            throw new Exception('No manual movies found');
        }
        
        if ($index === null || !isset($config['HERO_CAROUSEL_MANUAL_MOVIES'][$index])) {
            throw new Exception('Invalid movie index');
        }
        
        array_splice($config['HERO_CAROUSEL_MANUAL_MOVIES'], $index, 1);
        
        if (saveConfigToFile($config)) {
            echo json_encode(['success' => true, 'message' => 'Movie removed from hero carousel successfully']);
        } else {
            throw new Exception('Failed to save config file');
        }
        
    } elseif ($action === 'toggle_hero_manual_movies_visibility') {
        if (!isset($config['HERO_CAROUSEL_MANUAL_MOVIES']) || !is_array($config['HERO_CAROUSEL_MANUAL_MOVIES'])) {
            throw new Exception('No manual movies found');
        }
        
        $anyVisible = false;
        foreach ($config['HERO_CAROUSEL_MANUAL_MOVIES'] as $movie) {
            if (!isset($movie['hidden']) || $movie['hidden'] !== true) {
                $anyVisible = true;
                break;
            }
        }
        
        $newHiddenState = $anyVisible;
        
        foreach ($config['HERO_CAROUSEL_MANUAL_MOVIES'] as &$movie) {
            $movie['hidden'] = $newHiddenState;
        }
        unset($movie);
        
        if (saveConfigToFile($config)) {
            $statusText = $newHiddenState ? 'hidden' : 'visible';
            echo json_encode([
                'success' => true, 
                'message' => 'All manual movies are now ' . $statusText,
                'hidden' => $newHiddenState
            ]);
        } else {
            throw new Exception('Failed to save config file');
        }
        
    } else {
        throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
