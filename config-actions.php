<?php

require_once 'config-helpers.php';

class ConfigActions {
    private $config;
    private $input;
    private $configType;

    public function __construct($config, $input) {
        $this->config = $config;
        $this->input = $input;
        $this->configType = $input['config_type'] ?? '';
    }

    public function add() {
        if ($this->configType === 'SEARCH_WEBSITES') {
            return $this->addSearchWebsite();
        } elseif ($this->configType === 'HERO_CAROUSEL_WEBSITES') {
            return $this->addHeroCarouselWebsite();
        } else {
            return $this->addSectionWebsite();
        }
    }

    private function addSearchWebsite() {
        $name = $this->input['name'] ?? '';
        $url = $this->input['url'] ?? '';
        $searchParam = $this->input['search_param'] ?? 's';
        $type = $this->input['type'] ?? 'html';
        $parserType = $this->input['parser_type'] ?? 'default';
        
        requireFields($this->input, ['name', 'url']);
        
        $this->config['SEARCH_WEBSITES'][$name] = [
            'url' => $url,
            'search_param' => $searchParam,
            'type' => $type,
            'parser_type' => $parserType
        ];
        
        if ($type === 'api' && !empty($this->input['api_url'])) {
            $this->config['SEARCH_WEBSITES'][$name]['api_url'] = $this->input['api_url'];
        }
        
        if ($type === 'api' && !empty($this->input['movie_base_url'])) {
            $this->config['SEARCH_WEBSITES'][$name]['movie_base_url'] = $this->input['movie_base_url'];
        }
        
        saveAndRespond($this->config, 'Config added successfully');
    }

    private function addHeroCarouselWebsite() {
        $name = $this->input['name'] ?? '';
        $url = $this->input['url'] ?? '';
        $parserType = $this->input['parser_type'] ?? 'li_thumb';
        
        requireFields($this->input, ['name', 'url']);
        
        $this->config['HERO_CAROUSEL_WEBSITES'][$name] = [
            'url' => $url,
            'parser_type' => $parserType
        ];
        
        saveAndRespond($this->config, 'Config added successfully');
    }

    private function addSectionWebsite() {
        $section = $this->input['section'] ?? '';
        $name = $this->input['name'] ?? '';
        $url = $this->input['url'] ?? '';
        $parserType = $this->input['parser_type'] ?? 'li_thumb';
        $displayName = $this->input['display_name'] ?? '';
        
        requireFields($this->input, ['section', 'name', 'url']);
        
        if (!isset($this->config[$this->configType][$section])) {
            $this->config[$this->configType][$section] = [];
        }
        
        if (!empty($displayName)) {
            $this->config[$this->configType][$section]['display_name'] = $displayName;
        }
        
        $this->config[$this->configType][$section][$name] = [
            'url' => $url,
            'parser_type' => $parserType
        ];
        
        $successMessage = 'Config added successfully';
        if ($this->configType === 'ALL_SECTION_WEBSITES' || $this->configType === 'CATEGORIES_WEBSITES') {
            $successMessage .= " to section '$section'. Scroll down to see it at the bottom of the list.";
        }
        
        saveAndRespond($this->config, $successMessage);
    }

    public function edit() {
        if ($this->configType === 'SEARCH_WEBSITES') {
            return $this->editSearchWebsite();
        } elseif ($this->configType === 'HERO_CAROUSEL_WEBSITES') {
            return $this->editHeroCarouselWebsite();
        } else {
            return $this->editSectionWebsite();
        }
    }

    private function editSearchWebsite() {
        $oldName = $this->input['old_name'] ?? '';
        $newName = $this->input['name'] ?? '';
        $url = $this->input['url'] ?? '';
        $searchParam = $this->input['search_param'] ?? 's';
        $type = $this->input['type'] ?? 'html';
        $parserType = $this->input['parser_type'] ?? 'default';
        
        if (empty($oldName) || empty($newName) || empty($url)) {
            throw new Exception('Name and URL are required');
        }
        
        $newData = [
            'url' => $url,
            'search_param' => $searchParam,
            'type' => $type,
            'parser_type' => $parserType
        ];
        
        if ($type === 'api' && !empty($this->input['api_url'])) {
            $newData['api_url'] = $this->input['api_url'];
        }
        
        if ($type === 'api' && !empty($this->input['movie_base_url'])) {
            $newData['movie_base_url'] = $this->input['movie_base_url'];
        }
        
        $newConfig = [];
        foreach ($this->config['SEARCH_WEBSITES'] as $key => $value) {
            if ($key === $oldName) {
                $newConfig[$newName] = $newData;
            } else {
                $newConfig[$key] = $value;
            }
        }
        $this->config['SEARCH_WEBSITES'] = $newConfig;
        
        saveAndRespond($this->config, 'Config updated successfully');
    }

    private function editHeroCarouselWebsite() {
        $oldName = $this->input['old_name'] ?? '';
        $newName = $this->input['name'] ?? '';
        $url = $this->input['url'] ?? '';
        $parserType = $this->input['parser_type'] ?? 'li_thumb';
        
        if (empty($oldName) || empty($newName) || empty($url)) {
            throw new Exception('Name and URL are required');
        }
        
        $newData = [
            'url' => $url,
            'parser_type' => $parserType
        ];
        
        $newConfig = [];
        foreach ($this->config['HERO_CAROUSEL_WEBSITES'] as $key => $value) {
            if ($key === $oldName) {
                $newConfig[$newName] = $newData;
            } else {
                $newConfig[$key] = $value;
            }
        }
        $this->config['HERO_CAROUSEL_WEBSITES'] = $newConfig;
        
        saveAndRespond($this->config, 'Config updated successfully');
    }

    private function editSectionWebsite() {
        $oldSection = $this->input['old_section'] ?? '';
        $oldName = $this->input['old_name'] ?? '';
        $newSection = $this->input['section'] ?? '';
        $newName = $this->input['name'] ?? '';
        $url = $this->input['url'] ?? '';
        $parserType = $this->input['parser_type'] ?? 'li_thumb';
        $displayName = $this->input['display_name'] ?? '';
        
        if (empty($oldSection) || empty($oldName) || empty($newSection) || empty($newName) || empty($url)) {
            throw new Exception('All fields are required');
        }
        
        $newData = [
            'url' => $url,
            'parser_type' => $parserType
        ];
        
        if ($oldSection === $newSection) {
            $newSectionData = [];
            $displayNameAdded = false;
            
            foreach ($this->config[$this->configType][$oldSection] as $key => $value) {
                if ($key === $oldName) {
                    $newSectionData[$newName] = $newData;
                } elseif ($key === 'display_name') {
                    $newSectionData[$key] = !empty($displayName) ? $displayName : $value;
                    $displayNameAdded = true;
                } else {
                    $newSectionData[$key] = $value;
                }
            }
            
            if (!$displayNameAdded && !empty($displayName)) {
                $newSectionData = array_merge(['display_name' => $displayName], $newSectionData);
            }
            
            $this->config[$this->configType][$oldSection] = $newSectionData;
        } else {
            unset($this->config[$this->configType][$oldSection][$oldName]);
            
            if (!isset($this->config[$this->configType][$newSection])) {
                $this->config[$this->configType][$newSection] = [];
            }
            
            if (!empty($displayName)) {
                $this->config[$this->configType][$newSection]['display_name'] = $displayName;
            }
            
            $this->config[$this->configType][$newSection][$newName] = $newData;
        }
        
        saveAndRespond($this->config, 'Config updated successfully');
    }

    public function delete() {
        if ($this->configType === 'SEARCH_WEBSITES') {
            return $this->deleteSearchWebsite();
        } elseif ($this->configType === 'HERO_CAROUSEL_WEBSITES') {
            return $this->deleteHeroCarouselWebsite();
        } else {
            return $this->deleteSectionWebsite();
        }
    }

    private function deleteSearchWebsite() {
        $name = $this->input['name'] ?? '';
        requireFields($this->input, ['name']);
        
        if (!isset($this->config['SEARCH_WEBSITES'][$name])) {
            throw new Exception('Config not found');
        }
        
        unset($this->config['SEARCH_WEBSITES'][$name]);
        saveAndRespond($this->config, 'Config deleted successfully');
    }

    private function deleteHeroCarouselWebsite() {
        $name = $this->input['name'] ?? '';
        requireFields($this->input, ['name']);
        
        if (!isset($this->config['HERO_CAROUSEL_WEBSITES'][$name])) {
            throw new Exception('Config not found');
        }
        
        unset($this->config['HERO_CAROUSEL_WEBSITES'][$name]);
        saveAndRespond($this->config, 'Config deleted successfully');
    }

    private function deleteSectionWebsite() {
        $section = $this->input['section'] ?? '';
        $name = $this->input['name'] ?? '';
        
        requireFields($this->input, ['section', 'name']);
        
        if (!isset($this->config[$this->configType][$section][$name])) {
            throw new Exception('Config not found');
        }
        
        if ($this->configType === 'LATEST_WEBSITES' && $section === 'LATEST') {
            throw new Exception('Cannot delete built-in feature. This is a system-protected category.');
        }
        
        if ($this->configType === 'ALL_SECTION_WEBSITES' && ($section === 'WEEKLY_TOP_10' || $section === 'MOVIE_COLLECTIONS')) {
            throw new Exception('Cannot delete built-in feature. This is a system-protected section.');
        }
        
        unset($this->config[$this->configType][$section][$name]);
        
        $remainingKeys = array_filter(
            array_keys($this->config[$this->configType][$section]),
            function($k) { return $k !== 'display_name'; }
        );
        
        if (empty($remainingKeys)) {
            unset($this->config[$this->configType][$section]);
        }
        
        saveAndRespond($this->config, 'Config deleted successfully');
    }

    public function toggleHide() {
        if ($this->configType === 'SEARCH_WEBSITES') {
            $name = $this->input['name'] ?? '';
            requireFields($this->input, ['name']);
            
            if (!isset($this->config['SEARCH_WEBSITES'][$name])) {
                throw new Exception('Config not found');
            }
            
            $isHidden = $this->config['SEARCH_WEBSITES'][$name]['hidden'] ?? false;
            $this->config['SEARCH_WEBSITES'][$name]['hidden'] = !$isHidden;
            
        } elseif ($this->configType === 'HERO_CAROUSEL_WEBSITES') {
            $name = $this->input['name'] ?? '';
            requireFields($this->input, ['name']);
            
            if (!isset($this->config['HERO_CAROUSEL_WEBSITES'][$name])) {
                throw new Exception('Config not found');
            }
            
            $isHidden = $this->config['HERO_CAROUSEL_WEBSITES'][$name]['hidden'] ?? false;
            $this->config['HERO_CAROUSEL_WEBSITES'][$name]['hidden'] = !$isHidden;
            
        } else {
            $section = $this->input['section'] ?? '';
            $name = $this->input['name'] ?? '';
            requireFields($this->input, ['section', 'name']);
            
            if (!isset($this->config[$this->configType][$section][$name])) {
                throw new Exception('Config not found');
            }
            
            $isHidden = $this->config[$this->configType][$section][$name]['hidden'] ?? false;
            $this->config[$this->configType][$section][$name]['hidden'] = !$isHidden;
        }
        
        saveAndRespond($this->config, 'Visibility toggled successfully');
    }

    public function moveSection() {
        $section = $this->input['section'] ?? '';
        $direction = $this->input['direction'] ?? '';
        
        if (empty($section) || empty($direction) || !in_array($direction, ['up', 'down'])) {
            throw new Exception('Section and valid direction (up/down) are required');
        }
        
        $validTypes = ['ALL_SECTION_WEBSITES', 'CATEGORIES_WEBSITES', 'LATEST_WEBSITES', 'SEARCH_WEBSITES'];
        if (!in_array($this->configType, $validTypes)) {
            throw new Exception('Move section is only supported for ALL_SECTION_WEBSITES, CATEGORIES_WEBSITES, LATEST_WEBSITES, and SEARCH_WEBSITES');
        }
        
        $this->config[$this->configType] = moveItemInArray($this->config[$this->configType], $section, $direction);
        saveAndRespond($this->config, 'Section moved successfully');
    }

    public function resetWeeklyViews() {
        $weeklyViewsFile = 'data/weekly_views.json';
        
        if (!file_exists('data')) {
            mkdir('data', 0755, true);
        }
        
        $result = file_put_contents($weeklyViewsFile, '[]');
        
        if ($result !== false) {
            sendSuccess('Weekly views data reset successfully! All tracking data has been cleared.');
        } else {
            throw new Exception('Failed to reset weekly views file');
        }
    }

    public function editDisplayName() {
        $section = $this->input['section'] ?? '';
        $displayName = $this->input['display_name'] ?? '';
        
        requireFields($this->input, ['section', 'display_name']);
        
        if ($this->configType === 'CATEGORIES_WEBSITES') {
            if (!isset($this->config['CATEGORIES_WEBSITES'][$section])) {
                throw new Exception('Category not found');
            }
            $this->config['CATEGORIES_WEBSITES'][$section]['display_name'] = $displayName;
        } elseif ($this->configType === 'LATEST_WEBSITES') {
            if (!isset($this->config['LATEST_WEBSITES'][$section])) {
                throw new Exception('Section not found');
            }
            $this->config['LATEST_WEBSITES'][$section]['display_name'] = $displayName;
        } else {
            if (!isset($this->config['ALL_SECTION_WEBSITES'][$section])) {
                throw new Exception('Section not found');
            }
            $this->config['ALL_SECTION_WEBSITES'][$section]['display_name'] = $displayName;
        }
        
        saveAndRespond($this->config, 'Display name updated successfully');
    }

    public function updateSiteSettings() {
        $websiteName = $this->input['website_name'] ?? '';
        requireFields($this->input, ['website_name']);
        
        $this->config['SITE_SETTINGS']['website_name'] = $websiteName;
        saveAndRespond($this->config, 'Site settings updated successfully');
    }
}

class HeroManualMovieActions {
    private $config;
    private $input;

    public function __construct($config, $input) {
        $this->config = $config;
        $this->input = $input;
    }

    public function add() {
        $movie = $this->input['movie'] ?? null;
        
        if (!$movie || !isset($movie['title']) || !isset($movie['link'])) {
            throw new Exception('Invalid movie data');
        }
        
        if (!isset($this->config['HERO_CAROUSEL_MANUAL_MOVIES']) || !is_array($this->config['HERO_CAROUSEL_MANUAL_MOVIES'])) {
            $this->config['HERO_CAROUSEL_MANUAL_MOVIES'] = [];
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
        
        array_unshift($this->config['HERO_CAROUSEL_MANUAL_MOVIES'], $movieData);
        saveAndRespond($this->config, 'Movie added to hero carousel successfully');
    }

    public function delete() {
        $index = $this->input['index'] ?? null;
        
        if (!isset($this->config['HERO_CAROUSEL_MANUAL_MOVIES']) || !is_array($this->config['HERO_CAROUSEL_MANUAL_MOVIES'])) {
            throw new Exception('No manual movies found');
        }
        
        if ($index === null || !isset($this->config['HERO_CAROUSEL_MANUAL_MOVIES'][$index])) {
            throw new Exception('Invalid movie index');
        }
        
        array_splice($this->config['HERO_CAROUSEL_MANUAL_MOVIES'], $index, 1);
        saveAndRespond($this->config, 'Movie removed from hero carousel successfully');
    }

    public function toggleVisibility() {
        if (!isset($this->config['HERO_CAROUSEL_MANUAL_MOVIES']) || !is_array($this->config['HERO_CAROUSEL_MANUAL_MOVIES'])) {
            throw new Exception('No manual movies found');
        }
        
        $anyVisible = false;
        foreach ($this->config['HERO_CAROUSEL_MANUAL_MOVIES'] as $movie) {
            if (!isset($movie['hidden']) || $movie['hidden'] !== true) {
                $anyVisible = true;
                break;
            }
        }
        
        $newHiddenState = $anyVisible;
        
        foreach ($this->config['HERO_CAROUSEL_MANUAL_MOVIES'] as &$movie) {
            $movie['hidden'] = $newHiddenState;
        }
        unset($movie);
        
        if (saveConfigToFile($this->config)) {
            $statusText = $newHiddenState ? 'hidden' : 'visible';
            sendSuccess('All manual movies are now ' . $statusText, ['hidden' => $newHiddenState]);
        } else {
            throw new Exception('Failed to save config file');
        }
    }
}

class CollectionActions {
    private $config;
    private $input;

    public function __construct($config, $input) {
        $this->config = $config;
        $this->input = $input;
    }

    public function create() {
        $collectionName = $this->input['collection_name'] ?? '';
        $displayName = $this->input['display_name'] ?? '';
        $coverImage = $this->input['cover_image'] ?? '';
        
        requireFields($this->input, ['collection_name']);
        
        $collectionKey = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '_', $collectionName));
        
        if (isset($this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey])) {
            throw new Exception('Collection already exists');
        }
        
        $newCollection = [
            'display_name' => !empty($displayName) ? $displayName : $collectionName,
            'cover_image' => $coverImage,
            'hidden' => false,
            'movies' => []
        ];
        
        $this->config['MOVIE_COLLECTIONS_DATA'] = array_merge(
            [$collectionKey => $newCollection],
            $this->config['MOVIE_COLLECTIONS_DATA']
        );
        
        saveAndRespond($this->config, 'Collection created successfully', ['collection_key' => $collectionKey]);
    }

    public function addMovie() {
        $collectionKey = $this->input['collection_key'] ?? '';
        $movie = $this->input['movie'] ?? null;
        
        if (empty($collectionKey) || !$movie) {
            throw new Exception('Collection key and movie data are required');
        }
        
        if (!isset($this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey])) {
            throw new Exception('Collection not found');
        }
        
        $movieTitle = trim($movie['title'] ?? '');
        $movieLink = trim($movie['link'] ?? '');
        $movieImage = trim($movie['image'] ?? '');
        
        if (empty($movieTitle) || empty($movieLink)) {
            throw new Exception('Movie title and link are required');
        }
        
        foreach ($this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey]['movies'] as $existingMovie) {
            if ($existingMovie['link'] === $movieLink) {
                throw new Exception('This movie is already in the collection');
            }
        }
        
        $movieData = [
            'title' => $movieTitle,
            'link' => $movieLink,
            'image' => $movieImage,
            'language' => trim($movie['language'] ?? ''),
            'genre' => trim($movie['genre'] ?? ''),
            'website' => trim($movie['website'] ?? ''),
            'hidden' => false
        ];
        
        $this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey]['movies'][] = $movieData;
        saveAndRespond($this->config, 'Movie added to collection successfully');
    }

    public function addMovies() {
        $collectionKey = $this->input['collection_key'] ?? '';
        $movies = $this->input['movies'] ?? [];
        
        if (empty($collectionKey) || empty($movies) || !is_array($movies)) {
            throw new Exception('Collection key and movies array are required');
        }
        
        if (!isset($this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey])) {
            throw new Exception('Collection not found');
        }
        
        $addedCount = 0;
        $skippedCount = 0;
        
        foreach ($movies as $movie) {
            $movieTitle = trim($movie['title'] ?? '');
            $movieLink = trim($movie['link'] ?? '');
            $movieImage = trim($movie['image'] ?? '');
            
            if (empty($movieTitle) || empty($movieLink)) {
                $skippedCount++;
                continue;
            }
            
            $isDuplicate = false;
            foreach ($this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey]['movies'] as $existingMovie) {
                if ($existingMovie['link'] === $movieLink) {
                    $isDuplicate = true;
                    $skippedCount++;
                    break;
                }
            }
            
            if (!$isDuplicate) {
                $movieData = [
                    'title' => $movieTitle,
                    'link' => $movieLink,
                    'image' => $movieImage,
                    'language' => trim($movie['language'] ?? ''),
                    'genre' => trim($movie['genre'] ?? ''),
                    'website' => trim($movie['website'] ?? ''),
                    'hidden' => false
                ];
                
                $this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey]['movies'][] = $movieData;
                $addedCount++;
            }
        }
        
        if ($addedCount === 0) {
            throw new Exception('No movies were added. They may already exist in the collection.');
        }
        
        $message = $addedCount . ' movie(s) added successfully';
        if ($skippedCount > 0) {
            $message .= ' (' . $skippedCount . ' skipped - duplicates or invalid)';
        }
        
        saveAndRespond($this->config, $message);
    }

    public function deleteMovie() {
        $collectionKey = $this->input['collection_key'] ?? '';
        $movieIndex = $this->input['movie_index'] ?? null;
        
        if (empty($collectionKey) || $movieIndex === null) {
            throw new Exception('Collection key and movie index are required');
        }
        
        if (!isset($this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey])) {
            throw new Exception('Collection not found');
        }
        
        if (!isset($this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey]['movies'][$movieIndex])) {
            throw new Exception('Movie not found in collection');
        }
        
        array_splice($this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey]['movies'], $movieIndex, 1);
        saveAndRespond($this->config, 'Movie removed from collection successfully');
    }

    public function deleteMovies() {
        $collectionKey = $this->input['collection_key'] ?? '';
        $movieIndexes = $this->input['movie_indexes'] ?? [];
        
        if (empty($collectionKey) || empty($movieIndexes) || !is_array($movieIndexes)) {
            throw new Exception('Collection key and movie indexes array are required');
        }
        
        if (!isset($this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey])) {
            throw new Exception('Collection not found');
        }
        
        rsort($movieIndexes);
        
        $removedCount = 0;
        foreach ($movieIndexes as $index) {
            if (isset($this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey]['movies'][$index])) {
                array_splice($this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey]['movies'], $index, 1);
                $removedCount++;
            }
        }
        
        if ($removedCount === 0) {
            throw new Exception('No movies were removed');
        }
        
        saveAndRespond($this->config, $removedCount . ' movie(s) removed successfully');
    }

    public function delete() {
        $collectionKey = $this->input['collection_key'] ?? '';
        requireFields($this->input, ['collection_key']);
        
        if (!isset($this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey])) {
            throw new Exception('Collection not found');
        }
        
        unset($this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey]);
        saveAndRespond($this->config, 'Collection deleted successfully');
    }

    public function toggleVisibility() {
        $collectionKey = $this->input['collection_key'] ?? '';
        requireFields($this->input, ['collection_key']);
        
        if (!isset($this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey])) {
            throw new Exception('Collection not found');
        }
        
        $isHidden = $this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey]['hidden'] ?? false;
        $this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey]['hidden'] = !$isHidden;
        
        saveAndRespond($this->config, 'Collection visibility toggled successfully');
    }

    public function edit() {
        $collectionKey = $this->input['collection_key'] ?? '';
        $displayName = $this->input['display_name'] ?? '';
        $coverImage = $this->input['cover_image'] ?? '';
        
        requireFields($this->input, ['collection_key']);
        
        if (!isset($this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey])) {
            throw new Exception('Collection not found');
        }
        
        if (!empty($displayName)) {
            $this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey]['display_name'] = $displayName;
        }
        
        if ($coverImage !== null) {
            $this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey]['cover_image'] = $coverImage;
        }
        
        saveAndRespond($this->config, 'Collection updated successfully');
    }

    public function move() {
        $collectionKey = $this->input['collection_key'] ?? '';
        $direction = $this->input['direction'] ?? '';
        
        if (empty($collectionKey) || empty($direction) || !in_array($direction, ['up', 'down'])) {
            throw new Exception('Collection key and valid direction (up/down) are required');
        }
        
        $this->config['MOVIE_COLLECTIONS_DATA'] = moveItemInArray($this->config['MOVIE_COLLECTIONS_DATA'], $collectionKey, $direction);
        saveAndRespond($this->config, 'Collection moved successfully');
    }

    public function moveMovie() {
        $collectionKey = $this->input['collection_key'] ?? '';
        $movieIndex = $this->input['movie_index'] ?? null;
        $direction = $this->input['direction'] ?? '';
        
        if (empty($collectionKey) || $movieIndex === null || empty($direction) || !in_array($direction, ['up', 'down'])) {
            throw new Exception('Collection key, movie index and valid direction (up/down) are required');
        }
        
        if (!isset($this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey])) {
            throw new Exception('Collection not found');
        }
        
        $this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey]['movies'] = moveItemInIndexedArray(
            $this->config['MOVIE_COLLECTIONS_DATA'][$collectionKey]['movies'],
            $movieIndex,
            $direction
        );
        
        saveAndRespond($this->config, 'Movie moved successfully');
    }
}

function handleAction($action, $config, $input) {
    $configActions = new ConfigActions($config, $input);
    $heroActions = new HeroManualMovieActions($config, $input);
    $collectionActions = new CollectionActions($config, $input);

    switch ($action) {
        case 'add':
            return $configActions->add();
        case 'edit':
            return $configActions->edit();
        case 'delete':
            return $configActions->delete();
        case 'toggle_hide':
            return $configActions->toggleHide();
        case 'move_section':
            return $configActions->moveSection();
        case 'reset_weekly_views':
            return $configActions->resetWeeklyViews();
        case 'edit_display_name':
            return $configActions->editDisplayName();
        case 'update_site_settings':
            return $configActions->updateSiteSettings();
        case 'add_hero_manual_movie':
            return $heroActions->add();
        case 'delete_hero_manual_movie':
            return $heroActions->delete();
        case 'toggle_hero_manual_movies_visibility':
            return $heroActions->toggleVisibility();
        case 'create_collection':
            return $collectionActions->create();
        case 'add_movie_to_collection':
            return $collectionActions->addMovie();
        case 'add_movies_to_collection':
            return $collectionActions->addMovies();
        case 'delete_movie_from_collection':
            return $collectionActions->deleteMovie();
        case 'delete_movies_from_collection':
            return $collectionActions->deleteMovies();
        case 'delete_collection':
            return $collectionActions->delete();
        case 'toggle_collection_visibility':
            return $collectionActions->toggleVisibility();
        case 'edit_collection':
            return $collectionActions->edit();
        case 'move_collection':
            return $collectionActions->move();
        case 'move_collection_movie':
            return $collectionActions->moveMovie();
        default:
            throw new Exception('Invalid action');
    }
}

?>
