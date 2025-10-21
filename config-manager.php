<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once 'config.php';

function getCurrentConfig() {
    global $ALL_SECTION_WEBSITES, $CATEGORIES_WEBSITES, $SEARCH_WEBSITES, $HERO_CAROUSEL_WEBSITES, $SITE_SETTINGS;
    return [
        'ALL_SECTION_WEBSITES' => $ALL_SECTION_WEBSITES,
        'CATEGORIES_WEBSITES' => $CATEGORIES_WEBSITES,
        'SEARCH_WEBSITES' => $SEARCH_WEBSITES,
        'HERO_CAROUSEL_WEBSITES' => $HERO_CAROUSEL_WEBSITES,
        'SITE_SETTINGS' => $SITE_SETTINGS
    ];
}

$config = getCurrentConfig();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Config Manager</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        h1 {
            color: #667eea;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2.5rem;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e0e0e0;
            flex-wrap: wrap;
        }

        .tab {
            padding: 12px 24px;
            background: #f5f5f5;
            border: none;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
            color: #666;
        }

        .tab.active {
            background: #667eea;
            color: white;
        }

        .tab:hover {
            background: #764ba2;
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .add-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        .add-btn:hover {
            background: #45a049;
        }

        .config-grid {
            margin-top: 15px;
        }

        .config-card {
            margin-bottom: 25px;
        }

        .config-card h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .website-item {
            background: #f8f9fa;
            padding: 12px 15px;
            margin: 8px 0;
            border-radius: 6px;
            border-left: 3px solid #667eea;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            transition: all 0.2s;
        }

        .website-item:hover {
            background: #e9ecef;
            border-left-color: #764ba2;
        }

        .website-item.hidden {
            opacity: 0.5;
            background: #f0f0f0;
            border-left-color: #999;
        }

        .website-item.hidden:hover {
            opacity: 0.7;
        }

        .website-info {
            flex: 1;
            min-width: 0;
        }

        .website-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            font-size: 15px;
        }

        .website-details {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 5px;
        }

        .detail-row {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .detail-label {
            font-weight: 500;
            color: #666;
            font-size: 13px;
        }

        .detail-value {
            color: #333;
            font-size: 13px;
            word-break: break-word;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-group {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }

        .edit-btn, .delete-btn, .hide-btn {
            padding: 6px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
            font-weight: 500;
        }

        .edit-btn {
            background: #2196F3;
            color: white;
        }

        .edit-btn:hover {
            background: #0b7dda;
            transform: translateY(-1px);
        }

        .hide-btn {
            background: #FF9800;
            color: white;
        }

        .hide-btn:hover {
            background: #f57c00;
            transform: translateY(-1px);
        }

        .delete-btn {
            background: #f44336;
            color: white;
        }

        .delete-btn:hover {
            background: #da190b;
            transform: translateY(-1px);
        }

        .show-hidden-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .show-hidden-toggle label {
            font-size: 14px;
            color: #666;
            cursor: pointer;
            user-select: none;
        }

        .show-hidden-toggle input[type="checkbox"] {
            cursor: pointer;
            width: 18px;
            height: 18px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 24px;
            color: #667eea;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
        }

        .close-btn:hover {
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 16px;
            transition: border 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .save-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background 0.3s;
        }

        .save-btn:hover {
            background: #5568d3;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }

        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert.active {
            display: block;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 15px;
                border-radius: 10px;
            }

            h1 {
                font-size: 1.8rem;
                margin-bottom: 20px;
            }

            .tabs {
                gap: 5px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
            }

            .tab {
                padding: 10px 16px;
                font-size: 14px;
                white-space: nowrap;
                flex-shrink: 0;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .section-header h2 {
                font-size: 1.3rem;
            }

            .add-btn {
                width: 100%;
                padding: 12px 20px;
            }

            .config-card h3 {
                font-size: 1rem;
            }

            .website-item {
                flex-direction: column;
                align-items: flex-start;
                padding: 10px 12px;
                gap: 10px;
            }

            .website-info {
                width: 100%;
            }

            .website-name {
                font-size: 14px;
            }

            .website-details {
                gap: 10px;
            }

            .detail-value {
                max-width: 100%;
                white-space: normal;
            }

            .detail-label {
                font-size: 12px;
            }

            .detail-value {
                font-size: 12px;
            }

            .btn-group {
                width: 100%;
                gap: 8px;
            }

            .edit-btn, .delete-btn, .hide-btn {
                flex: 1;
                padding: 8px 12px;
                font-size: 13px;
            }

            .move-btn {
                width: 28px;
                height: 28px;
                font-size: 14px;
            }

            .modal-content {
                width: 95%;
                padding: 20px;
                max-height: 85vh;
            }

            .modal-title {
                font-size: 1.3rem;
            }

            .form-group {
                margin-bottom: 15px;
            }

            .form-label {
                font-size: 14px;
            }

            .form-input {
                padding: 10px;
                font-size: 16px;
            }

            .save-btn {
                padding: 12px 20px;
                font-size: 16px;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .container {
                padding: 25px;
            }

            h1 {
                font-size: 2rem;
            }

            .tab {
                padding: 10px 20px;
                font-size: 15px;
            }

            .detail-value {
                max-width: 250px;
            }

            .edit-btn, .delete-btn, .hide-btn {
                padding: 6px 12px;
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem;
            }

            .section-header h2 {
                font-size: 1.1rem;
            }

            .tab {
                padding: 8px 12px;
                font-size: 13px;
            }

            .modal-content {
                width: 98%;
                padding: 15px;
            }

            .close-btn {
                font-size: 24px;
            }
        }

        .tabs::-webkit-scrollbar {
            height: 6px;
        }

        .tabs::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .tabs::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 3px;
        }

        .tabs::-webkit-scrollbar-thumb:hover {
            background: #764ba2;
        }

        .section-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .section-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #667eea;
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .section-display-name {
            color: #333;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .edit-section-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
        }

        .edit-section-btn:hover {
            background: #5568d3;
            transform: translateY(-1px);
        }

        .move-buttons {
            display: flex;
            gap: 5px;
        }

        .move-btn {
            background: #667eea;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .move-btn:hover {
            background: #5568d3;
            transform: scale(1.1);
        }

        .move-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            opacity: 0.5;
        }

        .move-btn:disabled:hover {
            transform: scale(1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚙️ Config Manager</h1>
        
        <div id="alert" class="alert"></div>

        <div class="tabs">
            <button class="tab active" onclick="showTab('all-sections')">All Sections</button>
            <button class="tab" onclick="showTab('categories')">Categories</button>
            <button class="tab" onclick="showTab('search')">Search Websites</button>
            <button class="tab" onclick="showTab('hero')">Hero Carousel</button>
            <button class="tab" onclick="showTab('weekly-top10')">Weekly Top 10</button>
            <button class="tab" onclick="showTab('site-settings')">Site Settings</button>
        </div>

        <div id="all-sections" class="tab-content active">
            <div class="section-header">
                <h2>All Section Websites</h2>
                <button class="add-btn" onclick="openAddModal('ALL_SECTION_WEBSITES')">+ Add New</button>
            </div>
            <div class="show-hidden-toggle">
                <input type="checkbox" id="show-hidden-all-sections" onchange="toggleHiddenItems('all-sections')">
                <label for="show-hidden-all-sections">Show Hidden Items</label>
            </div>
            <div class="config-grid" id="all-sections-grid">
                <?php 
                $allSectionKeys = array_keys($config['ALL_SECTION_WEBSITES']);
                $allSectionCount = count($allSectionKeys);
                $allSectionIndex = 0;
                foreach($config['ALL_SECTION_WEBSITES'] as $section => $websites): 
                    $displayName = isset($websites['display_name']) ? $websites['display_name'] : $section;
                ?>
                    <div class="config-card">
                        <div class="section-card-header">
                            <h3 class="section-card-title">
                                <?php echo htmlspecialchars($section); ?> 
                                <span class="section-display-name">(<?php echo htmlspecialchars($displayName); ?>)</span>
                                <?php if ($section === 'WEEKLY_TOP_10'): ?>
                                    <span style="background: #4CAF50; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; margin-left: 10px;">🔒 Built-in Feature</span>
                                <?php endif; ?>
                                <button class="edit-section-btn" onclick='editSectionDisplayName("<?php echo $section; ?>", "<?php echo htmlspecialchars($displayName, ENT_QUOTES); ?>")'>✏️ Edit Name</button>
                            </h3>
                            <div class="move-buttons">
                                <button class="move-btn" onclick='moveSection("ALL_SECTION_WEBSITES", "<?php echo $section; ?>", "up")' <?php echo $allSectionIndex === 0 ? 'disabled' : ''; ?>>↑</button>
                                <button class="move-btn" onclick='moveSection("ALL_SECTION_WEBSITES", "<?php echo $section; ?>", "down")' <?php echo $allSectionIndex === $allSectionCount - 1 ? 'disabled' : ''; ?>>↓</button>
                            </div>
                        </div>
                        <?php foreach($websites as $name => $details): 
                            if ($name === 'display_name') continue;
                            $isBuiltIn = ($section === 'WEEKLY_TOP_10');
                        ?>
                            <?php $isHidden = isset($details['hidden']) && $details['hidden']; ?>
                            <div class="website-item <?php echo $isHidden ? 'hidden' : ''; ?>" data-hidden="<?php echo $isHidden ? 'true' : 'false'; ?>">
                                <div class="website-info">
                                    <div class="website-name">
                                        <?php echo htmlspecialchars($name); ?>
                                        <?php if ($isBuiltIn): ?>
                                            <span style="color: #4CAF50; font-size: 11px; margin-left: 8px;">(System Feature)</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="website-details">
                                        <div class="detail-row">
                                            <span class="detail-label">URL:</span>
                                            <span class="detail-value"><?php echo htmlspecialchars($details['url']); ?></span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Parser:</span>
                                            <span class="detail-value"><?php echo htmlspecialchars($details['parser_type']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <button class="hide-btn" onclick='toggleHideItem("ALL_SECTION_WEBSITES", "<?php echo $section; ?>", "<?php echo $name; ?>")'><?php echo $isHidden ? 'Unhide' : 'Hide'; ?></button>
                                    <?php if ($isBuiltIn): ?>
                                        <button class="edit-btn" disabled style="opacity: 0.5; cursor: not-allowed;" title="Built-in features cannot be edited">Edit</button>
                                        <button class="delete-btn" disabled style="opacity: 0.5; cursor: not-allowed;" title="Built-in features cannot be deleted">🔒 Protected</button>
                                    <?php else: ?>
                                        <button class="edit-btn" onclick='editItem("ALL_SECTION_WEBSITES", "<?php echo $section; ?>", "<?php echo $name; ?>", <?php echo json_encode($details); ?>)'>Edit</button>
                                        <button class="delete-btn" onclick='deleteItem("ALL_SECTION_WEBSITES", "<?php echo $section; ?>", "<?php echo $name; ?>")'>Delete</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php $allSectionIndex++; endforeach; ?>
            </div>
        </div>

        <div id="categories" class="tab-content">
            <div class="section-header">
                <h2>Categories Websites</h2>
                <button class="add-btn" onclick="openAddModal('CATEGORIES_WEBSITES')">+ Add New</button>
            </div>
            <div class="show-hidden-toggle">
                <input type="checkbox" id="show-hidden-categories" onchange="toggleHiddenItems('categories')">
                <label for="show-hidden-categories">Show Hidden Items</label>
            </div>
            <div class="config-grid" id="categories-grid">
                <?php 
                $categoriesKeys = array_keys($config['CATEGORIES_WEBSITES']);
                $categoriesCount = count($categoriesKeys);
                $categoriesIndex = 0;
                foreach($config['CATEGORIES_WEBSITES'] as $category => $websites): 
                    $displayName = isset($websites['display_name']) ? $websites['display_name'] : $category;
                ?>
                    <div class="config-card">
                        <div class="section-card-header">
                            <h3 class="section-card-title">
                                <?php echo htmlspecialchars($category); ?> 
                                <span class="section-display-name">(<?php echo htmlspecialchars($displayName); ?>)</span>
                                <button class="edit-section-btn" onclick='editCategoryDisplayName("<?php echo $category; ?>", "<?php echo htmlspecialchars($displayName, ENT_QUOTES); ?>")'>✏️ Edit Name</button>
                            </h3>
                            <div class="move-buttons">
                                <button class="move-btn" onclick='moveSection("CATEGORIES_WEBSITES", "<?php echo $category; ?>", "up")' <?php echo $categoriesIndex === 0 ? 'disabled' : ''; ?>>↑</button>
                                <button class="move-btn" onclick='moveSection("CATEGORIES_WEBSITES", "<?php echo $category; ?>", "down")' <?php echo $categoriesIndex === $categoriesCount - 1 ? 'disabled' : ''; ?>>↓</button>
                            </div>
                        </div>
                        <?php foreach($websites as $name => $details): 
                            if ($name === 'display_name') continue;
                        ?>
                            <?php $isHidden = isset($details['hidden']) && $details['hidden']; ?>
                            <div class="website-item <?php echo $isHidden ? 'hidden' : ''; ?>" data-hidden="<?php echo $isHidden ? 'true' : 'false'; ?>">
                                <div class="website-info">
                                    <div class="website-name"><?php echo htmlspecialchars($name); ?></div>
                                    <div class="website-details">
                                        <div class="detail-row">
                                            <span class="detail-label">URL:</span>
                                            <span class="detail-value"><?php echo htmlspecialchars($details['url']); ?></span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Parser:</span>
                                            <span class="detail-value"><?php echo htmlspecialchars($details['parser_type']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <button class="hide-btn" onclick='toggleHideItem("CATEGORIES_WEBSITES", "<?php echo $category; ?>", "<?php echo $name; ?>")'><?php echo $isHidden ? 'Unhide' : 'Hide'; ?></button>
                                    <button class="edit-btn" onclick='editItem("CATEGORIES_WEBSITES", "<?php echo $category; ?>", "<?php echo $name; ?>", <?php echo json_encode($details); ?>)'>Edit</button>
                                    <button class="delete-btn" onclick='deleteItem("CATEGORIES_WEBSITES", "<?php echo $category; ?>", "<?php echo $name; ?>")'>Delete</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php $categoriesIndex++; endforeach; ?>
            </div>
        </div>

        <div id="search" class="tab-content">
            <div class="section-header">
                <h2>Search Websites</h2>
                <button class="add-btn" onclick="openAddSearchModal()">+ Add New</button>
            </div>
            <div class="show-hidden-toggle">
                <input type="checkbox" id="show-hidden-search" onchange="toggleHiddenItems('search')">
                <label for="show-hidden-search">Show Hidden Items</label>
            </div>
            <div class="config-grid" id="search-grid">
                <?php 
                $searchKeys = array_keys($config['SEARCH_WEBSITES']);
                $searchCount = count($searchKeys);
                $searchIndex = 0;
                foreach($config['SEARCH_WEBSITES'] as $name => $details): 
                ?>
                    <?php $isHidden = isset($details['hidden']) && $details['hidden']; ?>
                    <div class="website-item <?php echo $isHidden ? 'hidden' : ''; ?>" data-hidden="<?php echo $isHidden ? 'true' : 'false'; ?>">
                        <div class="website-info">
                            <div class="website-name"><?php echo htmlspecialchars($name); ?></div>
                            <div class="website-details">
                                <div class="detail-row">
                                    <span class="detail-label">URL:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['url']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Param:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['search_param']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Type:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['type']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Parser:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['parser_type']); ?></span>
                                </div>
                                <?php if(isset($details['api_url'])): ?>
                                    <div class="detail-row">
                                        <span class="detail-label">API:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($details['api_url']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if(isset($details['movie_base_url'])): ?>
                                    <div class="detail-row">
                                        <span class="detail-label">Base:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($details['movie_base_url']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button class="move-btn" onclick='moveSearchWebsite("<?php echo $name; ?>", "up")' <?php echo $searchIndex === 0 ? 'disabled' : ''; ?>>↑</button>
                            <button class="move-btn" onclick='moveSearchWebsite("<?php echo $name; ?>", "down")' <?php echo $searchIndex === $searchCount - 1 ? 'disabled' : ''; ?>>↓</button>
                            <button class="hide-btn" onclick='toggleHideSearchItem("<?php echo $name; ?>")'><?php echo $isHidden ? 'Unhide' : 'Hide'; ?></button>
                            <button class="edit-btn" onclick='editSearchItem("<?php echo $name; ?>", <?php echo json_encode($details); ?>)'>Edit</button>
                            <button class="delete-btn" onclick='deleteSearchItem("<?php echo $name; ?>")'>Delete</button>
                        </div>
                    </div>
                <?php $searchIndex++; endforeach; ?>
            </div>
        </div>

        <div id="hero" class="tab-content">
            <div class="section-header">
                <h2>Hero Carousel Websites</h2>
                <button class="add-btn" onclick="openAddHeroModal()">+ Add New</button>
            </div>
            <div class="show-hidden-toggle">
                <input type="checkbox" id="show-hidden-hero" onchange="toggleHiddenItems('hero')">
                <label for="show-hidden-hero">Show Hidden Items</label>
            </div>
            <div class="config-grid" id="hero-grid">
                <?php foreach($config['HERO_CAROUSEL_WEBSITES'] as $name => $details): ?>
                    <?php $isHidden = isset($details['hidden']) && $details['hidden']; ?>
                    <div class="website-item <?php echo $isHidden ? 'hidden' : ''; ?>" data-hidden="<?php echo $isHidden ? 'true' : 'false'; ?>">
                        <div class="website-info">
                            <div class="website-name"><?php echo htmlspecialchars($name); ?></div>
                            <div class="website-details">
                                <div class="detail-row">
                                    <span class="detail-label">URL:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['url']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Parser:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['parser_type']); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button class="hide-btn" onclick='toggleHideHeroItem("<?php echo $name; ?>")'><?php echo $isHidden ? 'Unhide' : 'Hide'; ?></button>
                            <button class="edit-btn" onclick='editHeroItem("<?php echo $name; ?>", <?php echo json_encode($details); ?>)'>Edit</button>
                            <button class="delete-btn" onclick='deleteHeroItem("<?php echo $name; ?>")'>Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="weekly-top10" class="tab-content">
            <div class="section-header">
                <h2>Weekly Top 10 - View Tracking</h2>
            </div>
            
            <div class="config-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; margin-bottom: 20px;">
                <h3 style="color: white; display: flex; align-items: center; gap: 10px;">
                    🔒 Built-in Feature
                    <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 12px; font-size: 12px;">System Protected</span>
                </h3>
                <p style="margin: 10px 0; line-height: 1.6; opacity: 0.95;">
                    <strong>Weekly Top 10</strong> একটি built-in feature যা automatically সবচেয়ে জনপ্রিয় movies track করে।
                </p>
                <div style="background: rgba(255,255,255,0.1); padding: 12px; border-radius: 8px; margin-top: 10px;">
                    <div style="margin-bottom: 8px;">✅ <strong>যা করতে পারবেন:</strong></div>
                    <ul style="margin-left: 20px; line-height: 1.8;">
                        <li>Display Name পরিবর্তন করতে পারবেন</li>
                        <li>Hide/Unhide করতে পারবেন (homepage এ show/hide)</li>
                        <li>View data reset করতে পারবেন</li>
                        <li>Section এর position পরিবর্তন করতে পারবেন</li>
                    </ul>
                    <div style="margin: 12px 0 8px 0;">❌ <strong>যা করতে পারবেন না:</strong></div>
                    <ul style="margin-left: 20px; line-height: 1.8;">
                        <li>Delete করতে পারবেন না (এটি system feature)</li>
                        <li>URL/Parser পরিবর্তন করতে পারবেন না</li>
                    </ul>
                </div>
            </div>
            
            <?php
            $weeklyViewsFile = 'data/weekly_views.json';
            $weeklyData = [];
            $totalMovies = 0;
            $totalViews = 0;
            
            if (file_exists($weeklyViewsFile)) {
                $jsonContent = file_get_contents($weeklyViewsFile);
                $weeklyData = json_decode($jsonContent, true) ?? [];
                $totalMovies = count($weeklyData);
                foreach ($weeklyData as $movie) {
                    $totalViews += count($movie['views'] ?? []);
                }
            }
            ?>
            
            <div class="config-card">
                <h3>📊 Statistics</h3>
                <div class="website-details" style="padding: 15px 0;">
                    <div class="detail-row">
                        <span class="detail-label">Total Movies Tracked:</span>
                        <span class="detail-value"><strong><?php echo $totalMovies; ?></strong></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Views Recorded:</span>
                        <span class="detail-value"><strong><?php echo $totalViews; ?></strong></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Data File:</span>
                        <span class="detail-value"><?php echo $weeklyViewsFile; ?></span>
                    </div>
                </div>
            </div>

            <div class="config-card">
                <h3>🗑️ Reset Weekly Views Data</h3>
                <p style="color: #666; margin: 10px 0 15px 0; line-height: 1.6;">
                    এই অপশন ব্যবহার করে আপনি Weekly Top 10 এর সব ভিউ ডেটা মুছে ফেলতে পারবেন। 
                    এটি weekly_views.json ফাইলকে রিসেট করবে এবং সব ট্র্যাকিং ডেটা মুছে দেবে।
                </p>
                <button class="delete-btn" onclick="resetWeeklyViews()" style="width: auto; padding: 12px 30px; font-size: 16px;">
                    🔄 Reset All Weekly Views Data
                </button>
            </div>

            <?php if ($totalMovies > 0): ?>
            <div class="config-card">
                <h3>📋 Current Top Movies</h3>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php
                    // Sort movies by view count
                    usort($weeklyData, function($a, $b) {
                        return count($b['views'] ?? []) - count($a['views'] ?? []);
                    });
                    
                    $rank = 1;
                    foreach (array_slice($weeklyData, 0, 10) as $movie):
                        $viewCount = count($movie['views'] ?? []);
                    ?>
                    <div class="website-item" style="margin: 10px 0;">
                        <div class="website-info" style="flex: 1;">
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <span style="font-size: 20px; font-weight: bold; color: #667eea; min-width: 30px;">#<?php echo $rank; ?></span>
                                <div>
                                    <div class="website-name"><?php echo htmlspecialchars($movie['title'] ?? 'Unknown'); ?></div>
                                    <div class="detail-value" style="margin-top: 5px; color: #666;">
                                        <?php echo $viewCount; ?> view<?php echo $viewCount != 1 ? 's' : ''; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                        $rank++;
                    endforeach; 
                    ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div id="site-settings" class="tab-content">
            <div class="section-header">
                <h2>Site Settings</h2>
            </div>
            
            <div class="website-item" style="max-width: 800px; margin: 0 auto;">
                <div style="width: 100%;">
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label class="form-label" style="font-size: 16px; margin-bottom: 10px; display: block;">Website Name:</label>
                        <input type="text" id="website-name" class="form-input" value="<?php echo htmlspecialchars($config['SITE_SETTINGS']['website_name'] ?? 'FilmHaat'); ?>" style="width: 100%; padding: 12px; font-size: 15px;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label class="form-label" style="font-size: 16px; margin-bottom: 10px; display: block;">
                            Logo Image (150x150px, max 5KB, WebP only):
                        </label>
                        <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                            <img id="logo-preview" src="<?php echo htmlspecialchars($config['SITE_SETTINGS']['logo_image'] ?? 'attached_image/logo-image.webp'); ?>" alt="Logo" style="width: 150px; height: 150px; object-fit: cover; border: 2px solid #667eea; border-radius: 8px;">
                            <div>
                                <input type="file" id="logo-upload" accept=".webp" style="display: none;">
                                <button type="button" class="add-btn" onclick="document.getElementById('logo-upload').click()">Upload Logo</button>
                                <div id="logo-status" style="margin-top: 10px; font-size: 13px;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label class="form-label" style="font-size: 16px; margin-bottom: 10px; display: block;">
                            Background Image (16:9 ratio, max 550KB, WebP only):
                        </label>
                        <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                            <img id="background-preview" src="<?php echo htmlspecialchars($config['SITE_SETTINGS']['background_image'] ?? 'attached_image/background-image.webp'); ?>" alt="Background" style="width: 320px; height: 180px; object-fit: cover; border: 2px solid #667eea; border-radius: 8px;">
                            <div>
                                <input type="file" id="background-upload" accept=".webp" style="display: none;">
                                <button type="button" class="add-btn" onclick="document.getElementById('background-upload').click()">Upload Background</button>
                                <div id="background-status" style="margin-top: 10px; font-size: 13px;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="add-btn" onclick="saveSiteSettings()" style="width: 100%; padding: 15px; font-size: 16px;">
                        Save Settings
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modal-title">Add/Edit Config</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="config-form" onsubmit="saveConfig(event)">
                <div id="form-fields"></div>
                <button type="submit" class="save-btn">Save</button>
            </form>
        </div>
    </div>

    <script>
        let currentAction = 'add';
        let currentConfigType = '';
        let currentSection = '';
        let currentName = '';

        function showTab(tabName) {
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById(tabName).classList.add('active');
        }

        function showAlert(message, type) {
            const alert = document.getElementById('alert');
            alert.textContent = message;
            alert.className = `alert ${type} active`;
            setTimeout(() => alert.classList.remove('active'), 5000);
        }

        function openAddModal(configType) {
            currentAction = 'add';
            currentConfigType = configType;
            document.getElementById('modal-title').textContent = 'Add New Config';
            
            const formFields = `
                <div class="form-group">
                    <label class="form-label">Section/Category Name:</label>
                    <input type="text" class="form-input" id="section-name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Website Name:</label>
                    <input type="text" class="form-input" id="website-name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">URL:</label>
                    <input type="url" class="form-input" id="url" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Parser Type:</label>
                    <input type="text" class="form-input" id="parser-type" value="li_thumb" required>
                </div>
            `;
            
            document.getElementById('form-fields').innerHTML = formFields;
            document.getElementById('modal').classList.add('active');
        }

        function editItem(configType, section, name, details) {
            currentAction = 'edit';
            currentConfigType = configType;
            currentSection = section;
            currentName = name;
            
            document.getElementById('modal-title').textContent = 'Edit Config';
            
            const formFields = `
                <div class="form-group">
                    <label class="form-label">Section/Category Name:</label>
                    <input type="text" class="form-input" id="section-name" value="${section}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Website Name:</label>
                    <input type="text" class="form-input" id="website-name" value="${name}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">URL:</label>
                    <input type="url" class="form-input" id="url" value="${details.url}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Parser Type:</label>
                    <input type="text" class="form-input" id="parser-type" value="${details.parser_type}" required>
                </div>
            `;
            
            document.getElementById('form-fields').innerHTML = formFields;
            document.getElementById('modal').classList.add('active');
        }

        function openAddSearchModal() {
            currentAction = 'add';
            currentConfigType = 'SEARCH_WEBSITES';
            document.getElementById('modal-title').textContent = 'Add Search Website';
            
            const formFields = `
                <div class="form-group">
                    <label class="form-label">Website Name:</label>
                    <input type="text" class="form-input" id="website-name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">URL:</label>
                    <input type="url" class="form-input" id="url" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Search Parameter:</label>
                    <input type="text" class="form-input" id="search-param" value="s" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Type:</label>
                    <select class="form-input" id="type" required onchange="toggleApiFields()">
                        <option value="html">HTML</option>
                        <option value="api">API</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Parser Type:</label>
                    <input type="text" class="form-input" id="parser-type" required>
                </div>
                <div class="form-group" id="api-url-group" style="display:none;">
                    <label class="form-label">API URL:</label>
                    <input type="url" class="form-input" id="api-url">
                </div>
                <div class="form-group" id="movie-base-url-group" style="display:none;">
                    <label class="form-label">Movie Base URL:</label>
                    <input type="url" class="form-input" id="movie-base-url">
                </div>
            `;
            
            document.getElementById('form-fields').innerHTML = formFields;
            document.getElementById('modal').classList.add('active');
        }

        function editSearchItem(name, details) {
            currentAction = 'edit';
            currentConfigType = 'SEARCH_WEBSITES';
            currentName = name;
            
            document.getElementById('modal-title').textContent = 'Edit Search Website';
            
            const formFields = `
                <div class="form-group">
                    <label class="form-label">Website Name:</label>
                    <input type="text" class="form-input" id="website-name" value="${name}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">URL:</label>
                    <input type="url" class="form-input" id="url" value="${details.url}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Search Parameter:</label>
                    <input type="text" class="form-input" id="search-param" value="${details.search_param}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Type:</label>
                    <select class="form-input" id="type" required onchange="toggleApiFields()">
                        <option value="html" ${details.type === 'html' ? 'selected' : ''}>HTML</option>
                        <option value="api" ${details.type === 'api' ? 'selected' : ''}>API</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Parser Type:</label>
                    <input type="text" class="form-input" id="parser-type" value="${details.parser_type}" required>
                </div>
                <div class="form-group" id="api-url-group" style="display:${details.type === 'api' ? 'block' : 'none'};">
                    <label class="form-label">API URL:</label>
                    <input type="url" class="form-input" id="api-url" value="${details.api_url || ''}">
                </div>
                <div class="form-group" id="movie-base-url-group" style="display:${details.type === 'api' ? 'block' : 'none'};">
                    <label class="form-label">Movie Base URL:</label>
                    <input type="url" class="form-input" id="movie-base-url" value="${details.movie_base_url || ''}">
                </div>
            `;
            
            document.getElementById('form-fields').innerHTML = formFields;
            document.getElementById('modal').classList.add('active');
        }

        function openAddHeroModal() {
            currentAction = 'add';
            currentConfigType = 'HERO_CAROUSEL_WEBSITES';
            document.getElementById('modal-title').textContent = 'Add Hero Carousel Website';
            
            const formFields = `
                <div class="form-group">
                    <label class="form-label">Website Name:</label>
                    <input type="text" class="form-input" id="website-name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">URL:</label>
                    <input type="url" class="form-input" id="url" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Parser Type:</label>
                    <input type="text" class="form-input" id="parser-type" value="li_thumb" required>
                </div>
            `;
            
            document.getElementById('form-fields').innerHTML = formFields;
            document.getElementById('modal').classList.add('active');
        }

        function editHeroItem(name, details) {
            currentAction = 'edit';
            currentConfigType = 'HERO_CAROUSEL_WEBSITES';
            currentName = name;
            
            document.getElementById('modal-title').textContent = 'Edit Hero Carousel Website';
            
            const formFields = `
                <div class="form-group">
                    <label class="form-label">Website Name:</label>
                    <input type="text" class="form-input" id="website-name" value="${name}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">URL:</label>
                    <input type="url" class="form-input" id="url" value="${details.url}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Parser Type:</label>
                    <input type="text" class="form-input" id="parser-type" value="${details.parser_type}" required>
                </div>
            `;
            
            document.getElementById('form-fields').innerHTML = formFields;
            document.getElementById('modal').classList.add('active');
        }

        function toggleApiFields() {
            const type = document.getElementById('type').value;
            const apiUrlGroup = document.getElementById('api-url-group');
            const movieBaseUrlGroup = document.getElementById('movie-base-url-group');
            
            if (type === 'api') {
                apiUrlGroup.style.display = 'block';
                movieBaseUrlGroup.style.display = 'block';
            } else {
                apiUrlGroup.style.display = 'none';
                movieBaseUrlGroup.style.display = 'none';
            }
        }

        function editSectionDisplayName(section, currentDisplayName) {
            currentAction = 'edit_display_name';
            currentConfigType = 'ALL_SECTION_WEBSITES';
            currentSection = section;
            
            document.getElementById('modal-title').textContent = 'Edit Section Display Name';
            
            const formFields = `
                <div class="form-group">
                    <label class="form-label">Section Key:</label>
                    <input type="text" class="form-input" value="${section}" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Display Name:</label>
                    <input type="text" class="form-input" id="display-name" value="${currentDisplayName}" required>
                </div>
            `;
            
            document.getElementById('form-fields').innerHTML = formFields;
            document.getElementById('modal').classList.add('active');
        }

        function editCategoryDisplayName(category, currentDisplayName) {
            currentAction = 'edit_display_name';
            currentConfigType = 'CATEGORIES_WEBSITES';
            currentSection = category;
            
            document.getElementById('modal-title').textContent = 'Edit Category Display Name';
            
            const formFields = `
                <div class="form-group">
                    <label class="form-label">Category Key:</label>
                    <input type="text" class="form-input" value="${category}" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Display Name:</label>
                    <input type="text" class="form-input" id="display-name" value="${currentDisplayName}" required>
                </div>
            `;
            
            document.getElementById('form-fields').innerHTML = formFields;
            document.getElementById('modal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('modal').classList.remove('active');
        }

        async function saveConfig(event) {
            event.preventDefault();
            
            const modal = document.getElementById('modal');
            
            const data = {
                action: currentAction,
                config_type: currentConfigType,
                old_section: currentSection,
                old_name: currentName
            };

            if (currentAction === 'edit_display_name') {
                data.section = currentSection;
                data.display_name = modal.querySelector('#display-name').value;
            } else if (currentConfigType === 'SEARCH_WEBSITES') {
                data.name = modal.querySelector('#website-name').value;
                data.url = modal.querySelector('#url').value;
                data.search_param = modal.querySelector('#search-param').value;
                data.type = modal.querySelector('#type').value;
                data.parser_type = modal.querySelector('#parser-type').value;
                
                if (data.type === 'api') {
                    const apiUrl = modal.querySelector('#api-url').value;
                    const movieBaseUrl = modal.querySelector('#movie-base-url').value;
                    if (apiUrl) data.api_url = apiUrl;
                    if (movieBaseUrl) data.movie_base_url = movieBaseUrl;
                }
            } else if (currentConfigType === 'HERO_CAROUSEL_WEBSITES') {
                data.name = modal.querySelector('#website-name').value;
                data.url = modal.querySelector('#url').value;
                data.parser_type = modal.querySelector('#parser-type').value;
            } else {
                data.section = modal.querySelector('#section-name').value;
                data.name = modal.querySelector('#website-name').value;
                data.url = modal.querySelector('#url').value;
                data.parser_type = modal.querySelector('#parser-type').value;
            }

            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    closeModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error saving config: ' + error.message, 'error');
            }
        }

        async function deleteItem(configType, section, name) {
            if (!confirm(`Are you sure you want to delete ${name} from ${section}?`)) {
                return;
            }

            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        config_type: configType,
                        section: section,
                        name: name
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error deleting config: ' + error.message, 'error');
            }
        }

        async function deleteSearchItem(name) {
            if (!confirm(`Are you sure you want to delete ${name}?`)) {
                return;
            }

            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        config_type: 'SEARCH_WEBSITES',
                        name: name
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error deleting config: ' + error.message, 'error');
            }
        }

        async function deleteHeroItem(name) {
            if (!confirm(`Are you sure you want to delete ${name}?`)) {
                return;
            }

            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        config_type: 'HERO_CAROUSEL_WEBSITES',
                        name: name
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error deleting config: ' + error.message, 'error');
            }
        }

        async function toggleHideItem(configType, section, name) {
            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'toggle_hide',
                        config_type: configType,
                        section: section,
                        name: name
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 800);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error toggling visibility: ' + error.message, 'error');
            }
        }

        async function toggleHideSearchItem(name) {
            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'toggle_hide',
                        config_type: 'SEARCH_WEBSITES',
                        name: name
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 800);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error toggling visibility: ' + error.message, 'error');
            }
        }

        async function toggleHideHeroItem(name) {
            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'toggle_hide',
                        config_type: 'HERO_CAROUSEL_WEBSITES',
                        name: name
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 800);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error toggling visibility: ' + error.message, 'error');
            }
        }

        async function moveSection(configType, sectionName, direction) {
            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'move_section',
                        config_type: configType,
                        section: sectionName,
                        direction: direction
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error moving section: ' + error.message, 'error');
            }
        }

        async function moveSearchWebsite(websiteName, direction) {
            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'move_section',
                        config_type: 'SEARCH_WEBSITES',
                        section: websiteName,
                        direction: direction
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error moving search website: ' + error.message, 'error');
            }
        }

        function toggleHiddenItems(section) {
            const checkbox = document.getElementById(`show-hidden-${section}`);
            const sectionDiv = document.getElementById(section);
            const hiddenItems = sectionDiv.querySelectorAll('.website-item[data-hidden="true"]');
            
            hiddenItems.forEach(item => {
                if (checkbox.checked) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        document.getElementById('logo-upload').addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const statusDiv = document.getElementById('logo-status');
            statusDiv.textContent = 'Uploading...';
            statusDiv.style.color = '#667eea';
            
            const formData = new FormData();
            formData.append('image', file);
            formData.append('image_type', 'logo');
            
            try {
                const response = await fetch('upload-image-api.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('logo-preview').src = result.path + '?t=' + Date.now();
                    statusDiv.textContent = 'Logo uploaded successfully!';
                    statusDiv.style.color = 'green';
                    showAlert('Logo uploaded successfully!', 'success');
                } else {
                    statusDiv.textContent = result.message;
                    statusDiv.style.color = 'red';
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                statusDiv.textContent = 'Upload failed: ' + error.message;
                statusDiv.style.color = 'red';
                showAlert('Upload failed', 'error');
            }
            
            e.target.value = '';
        });
        
        document.getElementById('background-upload').addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const statusDiv = document.getElementById('background-status');
            statusDiv.textContent = 'Uploading...';
            statusDiv.style.color = '#667eea';
            
            const formData = new FormData();
            formData.append('image', file);
            formData.append('image_type', 'background');
            
            try {
                const response = await fetch('upload-image-api.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('background-preview').src = result.path + '?t=' + Date.now();
                    statusDiv.textContent = 'Background uploaded successfully!';
                    statusDiv.style.color = 'green';
                    showAlert('Background uploaded successfully!', 'success');
                } else {
                    statusDiv.textContent = result.message;
                    statusDiv.style.color = 'red';
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                statusDiv.textContent = 'Upload failed: ' + error.message;
                statusDiv.style.color = 'red';
                showAlert('Upload failed', 'error');
            }
            
            e.target.value = '';
        });
        
        async function saveSiteSettings() {
            const websiteName = document.querySelector('#site-settings #website-name').value.trim();
            
            if (!websiteName) {
                showAlert('Website name is required', 'error');
                return;
            }
            
            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'update_site_settings',
                        website_name: websiteName
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('Site settings saved successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error saving settings: ' + error.message, 'error');
            }
        }

        async function resetWeeklyViews() {
            if (!confirm('আপনি কি সত্যিই Weekly Top 10 এর সব ভিউ ডেটা রিসেট করতে চান? এই কাজটি পূর্বাবস্থায় ফেরানো যাবে না।')) {
                return;
            }

            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'reset_weekly_views'
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error resetting weekly views: ' + error.message, 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const sections = ['all-sections', 'categories', 'search', 'hero'];
            sections.forEach(section => {
                toggleHiddenItems(section);
            });
        });
    </script>
</body>
</html>
