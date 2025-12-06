<?php 
require_once 'config.php';
require_once 'config-helpers.php';

// Get category from URL parameter
$category = isset($_GET['category']) ? strtoupper($_GET['category']) : '';

// Validate category exists in LATEST_WEBSITES
if (empty($category) || !isset($LATEST_WEBSITES[$category])) {
    echo '<!DOCTYPE html><html><head><meta http-equiv="refresh" content="0;url=/"></head><body><script>window.location.href="/";</script></body></html>';
    exit;
}

// Get category display name
$categoryData = $LATEST_WEBSITES[$category];
$displayName = isset($categoryData['display_name']) ? $categoryData['display_name'] : ucfirst(strtolower($category));
$categoryLower = strtolower($category);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta name="theme-color" content="#0f0f0f" id="themeColor">
    <link rel="icon" type="image/webp" href="<?php echo htmlspecialchars($SITE_SETTINGS['logo_image']); ?>">
    <link rel="apple-touch-icon" href="<?php echo htmlspecialchars($SITE_SETTINGS['logo_image']); ?>">
    <title><?php echo htmlspecialchars($displayName); ?> Movies - <?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?></title>
    <meta name="description" content="Discover the latest movies and trending releases. Browse fresh content from multiple websites updated regularly. Find new movies to watch - completely free with no registration.">
    <link rel="stylesheet" href="styles.css">
    <link rel="manifest" href="manifest.php">
    
    <!-- Critical CSS for Bottom Navigation - Loaded before page content -->
    <style>
        .bottom-nav {
            position: fixed !important;
            bottom: 10px !important;
            left: 50% !important;
            transform: translateX(-50%) translateZ(0) !important;
            -webkit-transform: translateX(-50%) translateZ(0) !important;
            width: calc(100% - 30px) !important;
            max-width: calc(100% - 20px) !important;
            background: rgba(26, 26, 26, 0.85);
            display: flex;
            justify-content: space-around;
            align-items: stretch;
            padding: 7px 11px;
            z-index: 99999 !important;
            margin: 0 !important;
            border-radius: 50px;
            box-shadow: none;
        }

        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1px;
            padding: 5px 8px;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: flex-direction 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                        gap 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                        padding 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                        background 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                        color 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            flex: 1;
            font-family: inherit;
            border-radius: 50px;
            height: 41px;
            min-height: 41px;
            max-height: 41px;
            will-change: flex-direction, gap, padding;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .bottom-nav-item svg,
        .bottom-nav-item i {
            width: 28px;
            height: 28px;
            font-size: 28px;
            stroke-width: 2;
            transform: scale(1);
            transform-origin: center;
            transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                        color 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            will-change: transform;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }

        .bottom-nav-item span {
            font-size: 0.6rem;
            font-weight: 500;
            transform: scale(1);
            transform-origin: center;
            transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                        font-weight 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            display: inline-block;
            text-align: center;
            line-height: 1;
            will-change: transform;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            letter-spacing: -0.005em;
        }

        .bottom-nav-item.active {
            flex-direction: row;
            gap: 12px;
            padding: 9px 22px;
            background: linear-gradient(90deg, #df0033 0%, #bd284b 100%);
            color: white;
            position: relative;
            flex: 1.3;
            justify-content: center;
        }

        .bottom-nav-item.active svg,
        .bottom-nav-item.active i {
            transform: scale(0.85) translateX(-4px);
        }

        .bottom-nav-item.active span {
            transform: scale(1.5);
            font-weight: 700;
        }

        .bottom-nav-item.active svg {
            fill: white;
        }

        .bottom-nav-item.active i {
            color: white;
            background: transparent;
            -webkit-background-clip: unset;
            -webkit-text-fill-color: white;
            background-clip: unset;
        }

        .bottom-nav-item.active svg.gradient-fallback {
            fill: white;
        }

        .bottom-nav-item.active .bottom-nav-logo-rect {
            fill: white;
        }

        .bottom-nav-item:not(.active) .bottom-nav-logo-rect {
            fill: rgba(255, 255, 255, 0.6);
        }

        .bottom-nav.hidden {
            display: none !important;
        }

        @media (min-width: 769px) {
            .bottom-nav {
                display: none;
            }
        }

        @media (max-width: 768px) and (orientation: landscape) {
            .bottom-nav {
                display: none !important;
            }
        }
    </style>
    
    <!-- SVG Gradient for Bottom Navigation -->
    <svg width="0" height="0" style="position: absolute;">
        <defs>
            <linearGradient id="bottomNavGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" style="stop-color:#df0033;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#bd284b;stop-opacity:1" />
            </linearGradient>
        </defs>
    </svg>
    
    <script>
        window.SITE_SETTINGS = {
            website_name: <?php echo json_encode($SITE_SETTINGS['website_name']); ?>,
            logo_image: <?php echo json_encode($SITE_SETTINGS['logo_image']); ?>,
            background_image: <?php echo json_encode($SITE_SETTINGS['background_image']); ?>
        };
    </script>
    <style>
        /* Reset body/html margins for proper fixed header overlay */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .category-page-header {
            position: relative;
            width: 100%;
            aspect-ratio: 21 / 9;
            background-size: cover;
            background-position: center top 10%;
            background-repeat: no-repeat;
            display: flex;
            align-items: flex-end;
            padding: 0 20px 40px;
            overflow: hidden;
            margin: 0 !important;
        }
        
        .category-page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                linear-gradient(to bottom, var(--gradient-color-top, rgba(15,15,15,1)) 0%, transparent 60%),
                rgba(15, 15, 15, 0.3);
            z-index: 1;
        }
        
        .category-page-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 95%;
            background: linear-gradient(to top, rgba(15,15,15,1) 0%, transparent 100%);
            z-index: 2;
        }
        
        /* Fixed Header Nav */
        .category-header-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            background: transparent !important;
            transition: background 0.3s ease;
            will-change: background;
            z-index: 999;
        }
        
        .category-header-nav.scrolled {
            background: linear-gradient(to bottom, rgba(15, 15, 15, 1) 0%, rgba(15, 15, 15, 0.85) 100%) !important;
        }
        
        /* Title in navbar when scrolled */
        .category-nav-title {
            position: absolute;
            left: 80px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5rem;
            font-weight: 600;
            color: white;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        
        .category-header-nav.scrolled .category-nav-title {
            opacity: 1;
        }
        
        .category-page-title {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin: 0;
            position: relative;
            z-index: 3;
            transition: opacity 0.1s linear;
        }
        
        
        .category-page-content {
            max-width: 1920px;
            margin: 0 auto;
            padding: 40px 20px;
            padding-bottom: calc(80px + env(safe-area-inset-bottom));
        }
        
        .category-page-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: none;
            color: white;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .back-button svg {
            width: 35px;
            height: 35px;
        }
        
        .back-button:hover {
            opacity: 0.8;
        }
        
        .header-menu-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: none;
            color: white;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .header-menu-button:hover {
            opacity: 0.8;
        }
        
        .header-menu-button svg {
            width: 26px;
            height: 26px;
        }
        
        @media (max-width: 768px) and (orientation: portrait) {
            .category-page-header {
                aspect-ratio: 3 / 2;
                padding: 0 15px 30px;
                background-position: center top 10%;
            }
            
            .category-page-title {
                font-size: 1.5rem;
            }
            
            .category-header-nav {
                padding: 0 15px;
                height: 56px;
            }
            
            .category-page-content {
                padding: 30px 15px;
                padding-bottom: calc(80px + env(safe-area-inset-bottom)) !important;
            }
            
            .category-nav-title {
                left: 65px;
                font-size: 1.2rem;
            }
        }
        
        @media (max-width: 926px) and (orientation: landscape) {
            .category-page-header {
                aspect-ratio: 21 / 9;
                padding: 0 15px 20px;
                background-position: center top;
                margin-top: 0;
                min-height: 250px;
            }
            
            .category-page-title {
                font-size: 1.3rem;
            }
            
            .category-header-nav {
                padding: 0 15px;
                height: 48px;
            }
            
            .category-page-content {
                padding: 25px 15px;
                padding-bottom: calc(80px + env(safe-area-inset-bottom)) !important;
            }
            
            .category-nav-title {
                left: 60px;
                font-size: 1.1rem;
            }
            
            .back-button {
                width: 36px;
                height: 36px;
            }
            
            .back-button svg {
                width: 30px;
                height: 30px;
            }
            
            .header-menu-button {
                width: 36px;
                height: 36px;
            }
            
            .header-menu-button svg {
                width: 22px;
                height: 22px;
            }
        }
        
        /* Hide pagination numbers on latest.php */
        .category-movie-index {
            display: none !important;
        }
    </style>
</head>
<body>
    <!-- Bottom Navigation - Loaded at the start for instant rendering -->
    <nav class="bottom-nav">
        <a href="/" class="bottom-nav-item" data-page="home">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="currentColor">
                <path d="M39.5,43h-9c-1.381,0-2.5-1.119-2.5-2.5v-9c0-1.105-0.895-2-2-2h-4c-1.105,0-2,0.895-2,2v9c0,1.381-1.119,2.5-2.5,2.5h-9   C7.119,43,6,41.881,6,40.5V21.413c0-2.299,1.054-4.471,2.859-5.893L23.071,4.321c0.545-0.428,1.313-0.428,1.857,0L39.142,15.52      C40.947,16.942,42,19.113,42,21.411V40.5C42,41.881,40.881,43,39.5,43z"></path>
            </svg>
            <span>Home</span>
        </a>
        <a href="latest.php?category=LATEST" class="bottom-nav-item active" data-page="latest">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="currentColor">
                <path d="M45,13c0,0-2,8-7,8c-11,0-7-18-7-18S21,9,18,25c-4-2-5-8-5-8s-6,7-6,18c0,21,20,23,20,23s-5-6-5-13c0-11,4.696-16,4.696-16 s1,11,7.304,11c4,0,6-3,6-3s4,4,4,11c0,6.348-5,10-5,10c10.478-2.106,18-11.79,18-22.862C57,22,45,13,45,13z"></path>
            </svg>
            <span><?php echo htmlspecialchars(isset($LATEST_WEBSITES['LATEST']['display_name']) ? $LATEST_WEBSITES['LATEST']['display_name'] : 'Latest'); ?></span>
        </a>
        <a href="loved.php" class="bottom-nav-item" data-page="loved">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="currentColor">
                <path d="M 15 7 C 8.9424416 7 4 11.942442 4 18 C 4 22.096154 7.0876448 25.952899 10.851562 29.908203 C 14.615481 33.863507 19.248379 37.869472 22.939453 41.560547 A 1.50015 1.50015 0 0 0 25.060547 41.560547 C 28.751621 37.869472 33.384518 33.863507 37.148438 29.908203 C 40.912356 25.952899 44 22.096154 44 18 C 44 11.942442 39.057558 7 33 7 C 29.523564 7 26.496821 8.8664883 24 12.037109 C 21.503179 8.8664883 18.476436 7 15 7 z"></path>
            </svg>
            <span>Loved</span>
        </a>
        <a href="about.php" class="bottom-nav-item" data-page="about">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" fill="currentColor">
                <path d="M25,2C12.297,2,2,12.297,2,25s10.297,23,23,23s23-10.297,23-23S37.703,2,25,2z M25,11c1.657,0,3,1.343,3,3s-1.343,3-3,3 s-3-1.343-3-3S23.343,11,25,11z M29,38h-2h-4h-2v-2h2V23h-2v-2h2h4v2v13h2V38z"></path>
            </svg>
            <span>About</span>
        </a>
    </nav>
    
    <main>
    <div class="category-page-header" id="categoryPageHeader">
        <div class="category-header-nav" id="categoryHeaderNav">
            <button onclick="history.back()" class="back-button">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                    <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                </svg>
            </button>
            <h2 class="category-nav-title"><?php echo htmlspecialchars($displayName); ?></h2>
            <button class="header-menu-button" id="headerSearchButton">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>
            </button>
        </div>
        <h1 class="category-page-title"><?php echo htmlspecialchars($displayName); ?></h1>
    </div>

    <div id="searchPopupModal" class="search-popup-modal" style="display: none;">
        <div class="search-popup-content">
            <div class="search-popup-header">
                <div class="search-popup-header-container">
                    <div class="logo">
                        <img src="<?php echo htmlspecialchars($SITE_SETTINGS['logo_image']); ?>" alt="" class="logo-image" width="150" height="150">
                        <span class="logo-text"><?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?></span>
                    </div>
                    <button class="search-popup-close" id="searchPopupClose">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="search-section" style="background: linear-gradient(rgba(15,15,15,1) 0%, rgba(15,15,15,0.80) 15%, rgba(15,15,15,0.50) 75%, rgba(15,15,15,0.50) 100%), url('<?php echo htmlspecialchars($SITE_SETTINGS['background_image']); ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                <div class="hero-content">
                    <h1 class="hero-title">Find Your Favorite Movies & Series</h1>
                    <p class="hero-subtitle">Search Multiple Websites Together</p>
                    
                    <div class="search-form-wrapper">
                        <form id="searchForm">
                            <div class="search-box">
                                <div class="search-input-wrapper">
                                    <button type="button" id="filterBtn" class="filter-btn">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                            <circle cx="7" cy="7" r="4" fill="white"/>
                                            <rect x="1" y="6" width="5" height="2" rx="1" fill="white"/>
                                            <rect x="12" y="6" width="11" height="2" rx="1" fill="white"/>
                                            <circle cx="17" cy="17" r="4" fill="white"/>
                                            <rect x="1" y="16" width="11" height="2" rx="1" fill="white"/>
                                            <rect x="20" y="16" width="3" height="2" rx="1" fill="white"/>
                                        </svg>
                                    </button>
                                    <div id="filterDropdown" class="filter-dropdown" style="display: none;">
                                        <div class="filter-header">Filter Websites</div>
                                        <div class="filter-options">
                                            <?php 
                                            $visibleSearchWebsites = array_filter($SEARCH_WEBSITES, function($website) {
                                                return !isset($website['hidden']) || $website['hidden'] !== true;
                                            });
                                            foreach ($visibleSearchWebsites as $websiteKey => $website): 
                                            $domain = getDomainFromUrl($website['url']);
                                            $faviconUrl = getFaviconUrl($website['url']);
                                            ?>
                                            <label class="filter-option">
                                                <input type="checkbox" class="filter-checkbox" value="<?php echo htmlspecialchars($websiteKey); ?>" checked>
                                                <img class="filter-favicon" src="<?php echo htmlspecialchars($faviconUrl); ?>" alt="" onerror="this.style.display='none'">
                                                <span><?php echo htmlspecialchars($websiteKey); ?></span>
                                            </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="animated-placeholder" id="animatedPlaceholder">
                                        <span class="placeholder-text">Search Movies & Series</span>
                                    </div>
                                    <input 
                                        type="text" 
                                        id="searchInput" 
                                        name="query" 
                                        placeholder="" 
                                        required
                                        autocomplete="off"
                                    />
                                    <button type="button" id="clearBtn" class="clear-btn" style="display: none;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                        </svg>
                                    </button>
                                </div>
                                <button type="submit" id="searchBtn">
                                    <span class="btn-text">Search</span>
                                    <span class="loader" style="display: none;"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="hero-overlay"></div>
            </div>

            <div class="main-content">
                <div id="loadingMessage" class="loading-message" style="display: none;">
                    <div class="spinner"></div>
                    <p>Searching...</p>
                </div>

                <div id="errorMessage" class="error-message" style="display: none;"></div>

                <div id="resultsContainer" class="results-container" style="display: none;">
                    <div id="results"></div>
                </div>

                <div id="recentViewedContainer" class="recent-viewed-container" style="display: block;">
                    <div class="section-header">
                        <h2 class="website-name">Recent Viewed</h2>
                        <button id="clearRecentViewedBtn" class="clear-recent-btn" title="Clear All">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                            <span>Clear</span>
                        </button>
                    </div>
                    <div id="recentViewedGrid" class="recent-viewed-grid"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="category-page-content">

        <div id="categoryPageGrid" class="category-page-grid">
            <?php for ($i = 1; $i <= 16; $i++): ?>
            <div class="category-grid-item skeleton-item">
                <div class="skeleton-image" style="background: rgba(26, 26, 26);"></div>
                <div class="category-movie-info">
                    <div class="category-skeleton-title"></div>
                    <div class="category-skeleton-tags">
                        <div class="category-skeleton-tag"></div>
                        <div class="category-skeleton-tag"></div>
                    </div>
                </div>
                <div class="category-movie-index"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <div id="movieModal" class="movie-modal" style="display: none;">
        <div class="modal-wrapper">
            <button class="close-modal">&times;</button>
            <div class="modal-content">
                <div class="modal-image-wrapper">
                    <img id="modalImage" src="" alt="" class="modal-image lazy-modal-image">
                    <div class="modal-details">
                        <div class="modal-meta">
                            <span id="modalLanguage" class="modal-language"></span>
                            <span id="modalWebsite" class="modal-website"></span>
                        </div>
                        <h2 id="modalTitle" class="modal-title"></h2>
                        <div class="modal-actions">
                            <a id="modalLink" href="#" target="_blank" class="modal-view-btn">View</a>
                            <button id="modalLovedBtn" class="modal-loved-btn" title="Add to Loved">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <defs>
                                        <linearGradient id="heartGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" style="stop-color:#df0033;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#bd284b;stop-opacity:1" />
                                        </linearGradient>
                                    </defs>
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="confirmDialog" class="confirm-dialog-overlay" style="display: none;">
        <div class="confirm-dialog">
            <div class="confirm-dialog-content">
                <h3 class="confirm-dialog-title" id="confirmDialogTitle">Clear watch history?</h3>
                <p class="confirm-dialog-message" id="confirmDialogMessage">This will remove all recently viewed items from your history.</p>
            </div>
            <div class="confirm-dialog-actions">
                <button class="confirm-dialog-btn confirm-dialog-btn-cancel" id="confirmDialogCancel">Cancel</button>
                <button class="confirm-dialog-btn confirm-dialog-btn-confirm" id="confirmDialogConfirm">Clear watch History</button>
            </div>
        </div>
    </div>
    </main>

    <script>
        const CATEGORY = '<?php echo $categoryLower; ?>';
        const CATEGORY_DISPLAY = '<?php echo htmlspecialchars($displayName); ?>';
        const CATEGORY_SOURCE = 'LATEST_WEBSITES';
        let currentPage = 1;
        let isLoading = false;
        let hasMoreMovies = true;
        let allMovies = [];
        let headerOriginalThemeColor = '#0f0f0f';

        // Fixed Header Scroll Logic
        const categoryHeaderNav = document.getElementById('categoryHeaderNav');
        const categoryPageHeader = document.getElementById('categoryPageHeader');
        const categoryPageTitle = document.querySelector('.category-page-title');
        let pageInitialized = false;
        
        // Ensure page starts at top to prevent initial scrolled state
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }
        
        // Force remove scrolled class initially
        if (categoryHeaderNav) {
            categoryHeaderNav.classList.remove('scrolled');
        }
        
        // Scroll to top immediately
        document.documentElement.scrollTop = 0;
        document.body.scrollTop = 0;
        window.scrollTo(0, 0);
        
        function handleHeaderScroll() {
            // Don't apply scroll logic until page is fully initialized
            if (!pageInitialized) return;
            
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const headerHeight = categoryPageHeader ? categoryPageHeader.offsetHeight : 300;
            const navHeight = 60; // Fixed navbar height
            const threshold = headerHeight - navHeight - 20; // Show sticky title only after main header is out of view
            const themeColorMeta = document.getElementById('themeColor');
            
            // Fade out the main title as we scroll
            if (categoryPageTitle) {
                const fadeStart = headerHeight * 0.5;
                const fadeEnd = headerHeight - navHeight - 20;
                if (scrollTop < fadeStart) {
                    categoryPageTitle.style.opacity = '1';
                } else if (scrollTop >= fadeEnd) {
                    categoryPageTitle.style.opacity = '0';
                } else {
                    const fadeProgress = (scrollTop - fadeStart) / (fadeEnd - fadeStart);
                    categoryPageTitle.style.opacity = String(1 - fadeProgress);
                }
            }
            
            if (scrollTop > threshold) {
                categoryHeaderNav.classList.add('scrolled');
                if (themeColorMeta) {
                    themeColorMeta.setAttribute('content', '#0f0f0f');
                }
            } else {
                categoryHeaderNav.classList.remove('scrolled');
                if (themeColorMeta) {
                    themeColorMeta.setAttribute('content', headerOriginalThemeColor);
                }
            }
        }
        
        // Add scroll event listener with RAF for smooth performance
        let scrollTimeout;
        window.addEventListener('scroll', function() {
            if (scrollTimeout) {
                window.cancelAnimationFrame(scrollTimeout);
            }
            scrollTimeout = window.requestAnimationFrame(function() {
                handleHeaderScroll();
            });
        });
        
        // Initialize page after short delay to ensure everything is loaded
        setTimeout(function() {
            pageInitialized = true;
            handleHeaderScroll();
        }, 100);
        
        // Connect header search button to search popup
        const headerSearchButton = document.getElementById('headerSearchButton');
        if (headerSearchButton) {
            headerSearchButton.addEventListener('click', function() {
                const searchPopup = document.getElementById('searchPopupModal');
                if (searchPopup) {
                    searchPopup.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                    const searchInput = document.getElementById('searchInput');
                    if (searchInput) {
                        setTimeout(() => searchInput.focus(), 100);
                    }
                    
                    // Load recent viewed movies
                    if (typeof loadRecentViewedMovies === 'function') {
                        loadRecentViewedMovies();
                    }
                }
            });
        }

        // Color extraction utilities
        function rgbToHex(r, g, b) {
            return '#' + [r, g, b].map(x => {
                const hex = x.toString(16);
                return hex.length === 1 ? '0' + hex : hex;
            }).join('');
        }
        
        function extractDominantColor(imageUrl) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.crossOrigin = 'Anonymous';
                
                img.onload = function() {
                    try {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        
                        canvas.width = img.width;
                        canvas.height = img.height;
                        ctx.drawImage(img, 0, 0);
                        
                        const topHeight = Math.floor(canvas.height * 0.3);
                        const imageData = ctx.getImageData(0, 0, canvas.width, topHeight);
                        const data = imageData.data;
                        
                        let r = 0, g = 0, b = 0;
                        let count = 0;
                        
                        for (let i = 0; i < data.length; i += 40) {
                            r += data[i];
                            g += data[i + 1];
                            b += data[i + 2];
                            count++;
                        }
                        
                        r = Math.floor(r / count * 0.5);
                        g = Math.floor(g / count * 0.5);
                        b = Math.floor(b / count * 0.5);
                        
                        resolve({r, g, b});
                    } catch (error) {
                        reject(error);
                    }
                };
                
                img.onerror = function() {
                    reject(new Error('Failed to load image'));
                };
                
                img.src = imageUrl;
            });
        }
        
        // Set header background from loaded movies
        function setHeaderBackground() {
            if (allMovies.length > 0) {
                const randomMovie = allMovies[Math.floor(Math.random() * Math.min(5, allMovies.length))];
                if (randomMovie && randomMovie.image) {
                    categoryPageHeader.style.backgroundImage = `url('${randomMovie.image}')`;
                    
                    // Extract dominant color for dynamic gradient and theme color
                    extractDominantColor(randomMovie.image).then(color => {
                        categoryPageHeader.style.setProperty('--gradient-color-top', `rgba(${color.r}, ${color.g}, ${color.b}, 1)`);
                        
                        // Update browser theme color
                        const themeColorMeta = document.getElementById('themeColor');
                        if (themeColorMeta) {
                            const hexColor = rgbToHex(color.r, color.g, color.b);
                            headerOriginalThemeColor = hexColor;
                            // Only update if not scrolled
                            if (!categoryHeaderNav.classList.contains('scrolled')) {
                                themeColorMeta.setAttribute('content', hexColor);
                            }
                        }
                    }).catch(error => {
                        console.warn('Failed to extract color for category header, using default:', error);
                    });
                }
            }
        }

        // Movie modal functions
        const movieModal = document.getElementById('movieModal');
        const closeModalBtn = document.querySelector('.close-modal');
        const modalImage = document.getElementById('modalImage');
        const modalTitle = document.getElementById('modalTitle');
        const modalLink = document.getElementById('modalLink');
        const modalLanguage = document.getElementById('modalLanguage');
        const modalWebsite = document.getElementById('modalWebsite');
        
        
        function rgbToHex(r, g, b) {
            return '#' + [r, g, b].map(x => {
                const hex = x.toString(16);
                return hex.length === 1 ? '0' + hex : hex;
            }).join('');
        }
        
        function extractDominantColor(imageUrl) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.crossOrigin = 'Anonymous';
                
                img.onload = function() {
                    try {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        
                        canvas.width = img.width;
                        canvas.height = img.height;
                        ctx.drawImage(img, 0, 0);
                        
                        const topHeight = Math.floor(canvas.height * 0.3);
                        const imageData = ctx.getImageData(0, 0, canvas.width, topHeight);
                        const data = imageData.data;
                        
                        let r = 0, g = 0, b = 0;
                        let count = 0;
                        
                        for (let i = 0; i < data.length; i += 40) {
                            r += data[i];
                            g += data[i + 1];
                            b += data[i + 2];
                            count++;
                        }
                        
                        r = Math.floor(r / count * 0.5);
                        g = Math.floor(g / count * 0.5);
                        b = Math.floor(b / count * 0.5);
                        
                        resolve({ r, g, b });
                    } catch (error) {
                        reject(error);
                    }
                };
                
                img.onerror = () => reject(new Error('Failed to load image'));
                img.src = `image-proxy.php?url=${encodeURIComponent(imageUrl)}`;
            });
        }
        
        function openMovieModal(item) {
            const title = item.getAttribute('data-title');
            const link = item.getAttribute('data-link');
            const image = item.getAttribute('data-image');
            const language = item.getAttribute('data-language');
            const genre = item.getAttribute('data-genre') || '';
            const website = item.getAttribute('data-website') || '';
            
            // Save to recent viewed
            if (typeof saveToRecentViewed === 'function') {
                saveToRecentViewed(title, link, image, language, genre, website);
            }
            
            // Track for weekly top 10
            if (typeof trackMovieView === 'function') {
                trackMovieView(title, link, image, language, website);
            }
            
            modalTitle.textContent = title;
            modalLink.href = link;
            modalLanguage.textContent = language || '';
            
            const faviconUrl = getFaviconUrl(link);
            const displayName = website || getDomainFromUrl(link);
            if (displayName && faviconUrl) {
                modalWebsite.innerHTML = `<img class="modal-favicon" src="${escapeHtml(faviconUrl)}" alt="" onerror="this.style.display='none'"> ${escapeHtml(displayName)}`;
            } else {
                modalWebsite.textContent = displayName || '';
            }
            
            // Update loved button state
            const modalLovedBtn = document.getElementById('modalLovedBtn');
            if (modalLovedBtn) {
                modalLovedBtn.setAttribute('data-title', title);
                modalLovedBtn.setAttribute('data-link', link);
                modalLovedBtn.setAttribute('data-image', image);
                modalLovedBtn.setAttribute('data-language', language);
                modalLovedBtn.setAttribute('data-genre', genre);
                modalLovedBtn.setAttribute('data-website', website);
                
                // Check if movie is already loved
                let lovedMoviesData = [];
                try {
                    lovedMoviesData = JSON.parse(localStorage.getItem('lovedMoviesData') || '[]');
                } catch (error) {
                    console.warn('Could not read loved movies data');
                }
                
                const isLoved = lovedMoviesData.some(movie => movie.title === title);
                if (isLoved) {
                    modalLovedBtn.classList.add('loved');
                    modalLovedBtn.setAttribute('title', 'Remove from Loved');
                } else {
                    modalLovedBtn.classList.remove('loved');
                    modalLovedBtn.setAttribute('title', 'Add to Loved');
                }
            }
            
            if (image) {
                modalImage.classList.remove('loaded');
                movieModal.querySelector('.modal-content').classList.remove('image-loaded');
                
                modalImage.src = image;
                modalImage.style.display = 'block';
                
                extractDominantColor(image).then(color => {
                    movieModal.querySelector('.modal-content').style.setProperty('--modal-gradient-color-top', `rgba(${color.r}, ${color.g}, ${color.b}, 1)`);
                    movieModal.querySelector('.modal-content').style.setProperty('--modal-gradient-color-bottom', `rgba(${color.r}, ${color.g}, ${color.b}, 1)`);
                }).catch(error => {
                    console.warn('Failed to extract color for modal, using defaults:', error);
                });
                
                if (modalImage.complete && modalImage.naturalHeight !== 0) {
                    modalImage.classList.add('loaded');
                    movieModal.querySelector('.modal-content').classList.add('image-loaded');
                } else {
                    modalImage.onload = function() {
                        modalImage.classList.add('loaded');
                        movieModal.querySelector('.modal-content').classList.add('image-loaded');
                    };
                    modalImage.onerror = function() {
                        modalImage.classList.add('loaded');
                        movieModal.querySelector('.modal-content').classList.add('image-loaded');
                    };
                }
            } else {
                modalImage.style.display = 'none';
            }
            
            movieModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        
        function closeMovieModal() {
            if (movieModal) {
                movieModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }
        
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', closeMovieModal);
        }
        
        if (movieModal) {
            movieModal.addEventListener('click', (e) => {
                if (e.target === movieModal) {
                    closeMovieModal();
                }
            });
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && movieModal && movieModal.style.display === 'flex') {
                closeMovieModal();
            }
        });

        // Parse genres function
        function parseGenres(genreString, languageString) {
            const genres = [];
            
            if (genreString) {
                const genreList = genreString.split(/[,|\/]/);
                genreList.forEach(g => {
                    const trimmed = g.trim();
                    if (trimmed && !genres.includes(trimmed)) {
                        genres.push(trimmed);
                    }
                });
            }
            
            if (languageString) {
                const langList = languageString.split(/[,|\/]/);
                langList.forEach(l => {
                    const trimmed = l.trim();
                    if (trimmed && !genres.includes(trimmed)) {
                        genres.push(trimmed);
                    }
                });
            }
            
            return genres.slice(0, 4);
        }

        // Escape HTML function
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function getDomainFromUrl(url) {
            if (!url) return '';
            try {
                const parsedUrl = new URL(url);
                return parsedUrl.hostname;
            } catch (e) {
                return '';
            }
        }
        
        function getFaviconUrl(url) {
            const domain = getDomainFromUrl(url);
            if (!domain) return '';
            return 'https://www.google.com/s2/favicons?domain=' + encodeURIComponent(domain) + '&sz=16';
        }
        
        // Initialize lazy images
        function initializeLazyImages() {
            const lazyImages = document.querySelectorAll('.lazy-image');
            
            lazyImages.forEach(img => {
                const parentItem = img.closest('.carousel-item') || img.closest('.category-grid-item');
                
                if (img.complete && img.naturalHeight !== 0) {
                    img.classList.add('loaded');
                    if (parentItem) parentItem.classList.add('image-loaded');
                } else {
                    img.addEventListener('load', function() {
                        this.classList.add('loaded');
                        const parent = this.closest('.carousel-item') || this.closest('.category-grid-item');
                        if (parent) parent.classList.add('image-loaded');
                    });
                    
                    img.addEventListener('error', function() {
                        this.classList.add('loaded');
                        const parent = this.closest('.carousel-item') || this.closest('.category-grid-item');
                        if (parent) parent.classList.add('image-loaded');
                    });
                }
            });
        }
        

        // Load category movies
        async function loadCategoryMovies(page, append = false) {
            if (isLoading) return Promise.resolve();
            
            isLoading = true;
            const categoryPageGrid = document.getElementById('categoryPageGrid');
            
            // Add skeleton loaders when appending (load more)
            if (append) {
                const nextCard = document.getElementById('categoryNextCard');
                if (nextCard) {
                    nextCard.remove();
                }
                
                // Add skeleton loaders
                const skeletonCount = 8;
                for (let i = 0; i < skeletonCount; i++) {
                    const skeletonHtml = `
                        <div class="category-grid-item skeleton-item loading-skeleton">
                            <div class="skeleton-image" style="background: rgba(26, 26, 26);"></div>
                            <div class="category-movie-info">
                                <div class="category-skeleton-title"></div>
                                <div class="category-skeleton-tags">
                                    <div class="category-skeleton-tag"></div>
                                    <div class="category-skeleton-tag"></div>
                                </div>
                            </div>
                            <div class="category-movie-index">${String(allMovies.length + i + 1).padStart(2, '0')}</div>
                        </div>
                    `;
                    categoryPageGrid.insertAdjacentHTML('beforeend', skeletonHtml);
                }
            }
            
            try {
                const response = await fetch(`category-data.php?category=${CATEGORY}&page=${page}&source=${CATEGORY_SOURCE}`);
                const data = await response.json();
                
                // Always remove skeleton loaders first
                if (append) {
                    const skeletons = categoryPageGrid.querySelectorAll('.loading-skeleton');
                    skeletons.forEach(skeleton => skeleton.remove());
                }
                
                if (data.success && data.results.length > 0) {
                    if (append) {
                        const currentCount = allMovies.length;
                        allMovies = [...allMovies, ...data.results];
                        
                        data.results.forEach((movie, index) => {
                            const movieIndex = String(currentCount + index + 1).padStart(2, '0');
                            const genres = parseGenres(movie.genre, movie.language);
                            const genreTags = genres.map(g => `<span class="category-genre-tag">${escapeHtml(g)}</span>`).join('');
                            
                            const categoryTag = movie.category ? `<span class="category-genre-tag">${escapeHtml(movie.category)}</span>` : '';
                            
                            let websiteTag = '';
                            const wsDisplayName = movie.website || getDomainFromUrl(movie.link);
                            if (wsDisplayName) {
                                const wsFavicon = getFaviconUrl(movie.link);
                                websiteTag = `<span class="category-genre-tag category-website-tag"><img class="website-tag-favicon" src="${escapeHtml(wsFavicon)}" alt="" onerror="this.style.display='none'">${escapeHtml(wsDisplayName)}</span>`;
                            }
                            
                            const itemHtml = `
                                <div class="category-grid-item new-item" 
                                     data-title="${escapeHtml(movie.title)}" 
                                     data-link="${escapeHtml(movie.link)}" 
                                     data-image="${escapeHtml(movie.image || '')}" 
                                     data-language="${escapeHtml(movie.language || '')}"
                                     data-genre="${escapeHtml(movie.genre || '')}"
                                     data-website="${escapeHtml(movie.website || '')}"
                                     style="cursor: pointer;">
                                    ${movie.image ? `<img src="${escapeHtml(movie.image)}" alt="${escapeHtml(movie.title)}" class="result-image lazy-image" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23ddd%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2220%22%3ENo Image%3C/text%3E%3C/svg%3E'">` : '<div class="result-image"></div>'}
                                    <div class="category-movie-info">
                                        <h3 class="category-movie-title">${escapeHtml(movie.title)}</h3>
                                        <div class="category-movie-genres">${genreTags}${categoryTag}${websiteTag}</div>
                                    </div>
                                    <div class="category-movie-index">${movieIndex}</div>
                                </div>
                            `;
                            categoryPageGrid.insertAdjacentHTML('beforeend', itemHtml);
                        });
                        
                        setTimeout(() => {
                            const newItems = categoryPageGrid.querySelectorAll('.new-item');
                            newItems.forEach(item => item.classList.remove('new-item'));
                        }, 3000);
                    } else {
                        allMovies = data.results;
                        displayMovies(allMovies);
                    }
                    
                    hasMoreMovies = data.results.length >= 8;
                    
                    if (hasMoreMovies) {
                        const existingNextCard = document.getElementById('categoryNextCard');
                        if (!existingNextCard) {
                            const nextCardHtml = `
                                <div class="category-next-card" id="categoryNextCard">
                                    <span class="category-next-card-text">Load More</span>
                                    <span class="category-next-card-arrow">❯</span>
                                </div>
                            `;
                            categoryPageGrid.insertAdjacentHTML('beforeend', nextCardHtml);
                        }
                    } else if (append) {
                        // Show "No more movies available" message
                        const noMoreHtml = `
                            <div style="text-align: center; color: rgba(255,255,255,0.5); padding: 40px 20px; grid-column: 1 / -1; font-size: 1.1rem;">
                                No more movies available
                            </div>
                        `;
                        categoryPageGrid.insertAdjacentHTML('beforeend', noMoreHtml);
                    }
                    
                    initializeLazyImages();
                    initializeClicks();
                } else {
                    if (!append) {
                        categoryPageGrid.innerHTML = '<p style="text-align: center; color: rgba(255,255,255,0.7); padding: 60px 20px;">No movies found in this category.</p>';
                    } else {
                        // Show "No more movies available" message when no results
                        const noMoreHtml = `
                            <div style="text-align: center; color: rgba(255,255,255,0.5); padding: 40px 20px; grid-column: 1 / -1; font-size: 1.1rem;">
                                No more movies available
                            </div>
                        `;
                        categoryPageGrid.insertAdjacentHTML('beforeend', noMoreHtml);
                    }
                    hasMoreMovies = false;
                }
            } catch (error) {
                console.error('Error loading category movies:', error);
                
                // Always remove skeleton loaders on error
                if (append) {
                    const skeletons = categoryPageGrid.querySelectorAll('.loading-skeleton');
                    skeletons.forEach(skeleton => skeleton.remove());
                    
                    // Re-add Load More button so user can retry
                    const nextCardHtml = `
                        <div class="category-next-card" id="categoryNextCard">
                            <span class="category-next-card-text">Load More</span>
                            <span class="category-next-card-arrow">❯</span>
                        </div>
                    `;
                    categoryPageGrid.insertAdjacentHTML('beforeend', nextCardHtml);
                } else {
                    categoryPageGrid.innerHTML = '<p style="text-align: center; color: rgba(255,255,255,0.7); padding: 60px 20px;">Failed to load movies. Please try again.</p>';
                }
            } finally {
                isLoading = false;
            }
            
            return Promise.resolve();
        }

        // Display movies
        function displayMovies(movies) {
            const categoryPageGrid = document.getElementById('categoryPageGrid');
            let html = '';
            
            movies.forEach((movie, index) => {
                const movieIndex = String(index + 1).padStart(2, '0');
                const genres = parseGenres(movie.genre, movie.language);
                const genreTags = genres.map(g => `<span class="category-genre-tag">${escapeHtml(g)}</span>`).join('');
                
                const categoryTag = movie.category ? `<span class="category-genre-tag">${escapeHtml(movie.category)}</span>` : '';
                
                let websiteTag = '';
                const wsDisplayName = movie.website || getDomainFromUrl(movie.link);
                if (wsDisplayName) {
                    const wsFavicon = getFaviconUrl(movie.link);
                    websiteTag = `<span class="category-genre-tag category-website-tag"><img class="website-tag-favicon" src="${escapeHtml(wsFavicon)}" alt="" onerror="this.style.display='none'">${escapeHtml(wsDisplayName)}</span>`;
                }
                
                html += `
                    <div class="category-grid-item" 
                         data-title="${escapeHtml(movie.title)}" 
                         data-link="${escapeHtml(movie.link)}" 
                         data-image="${escapeHtml(movie.image || '')}" 
                         data-language="${escapeHtml(movie.language || '')}"
                         data-genre="${escapeHtml(movie.genre || '')}"
                         data-website="${escapeHtml(movie.website || '')}"
                         style="cursor: pointer;">
                        ${movie.image ? `<img src="${escapeHtml(movie.image)}" alt="${escapeHtml(movie.title)}" class="result-image lazy-image" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23ddd%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2220%22%3ENo Image%3C/text%3E%3C/svg%3E'">` : '<div class="result-image"></div>'}
                        <div class="category-movie-info">
                            <h3 class="category-movie-title">${escapeHtml(movie.title)}</h3>
                            <div class="category-movie-genres">${genreTags}${categoryTag}${websiteTag}</div>
                        </div>
                        <div class="category-movie-index">${movieIndex}</div>
                    </div>
                `;
            });
            
            categoryPageGrid.innerHTML = html;
            initializeLazyImages();
            initializeClicks();
        }
        
        // Initialize click events
        function initializeClicks() {
            const movieItems = document.querySelectorAll('.category-grid-item:not(#categoryNextCard):not([data-click-initialized])');
            movieItems.forEach(item => {
                item.setAttribute('data-click-initialized', 'true');
                item.addEventListener('click', function() {
                    openMovieModal(this);
                });
            });
            
            const nextCard = document.getElementById('categoryNextCard');
            if (nextCard) {
                nextCard.addEventListener('click', function() {
                    if (hasMoreMovies && !isLoading) {
                        currentPage++;
                        loadCategoryMovies(currentPage, true);
                    }
                });
            }
        }

        // Set header background from first movie
        function setHeaderBackground() {
            const categoryPageHeader = document.getElementById('categoryPageHeader');
            const themeColorMeta = document.getElementById('themeColor');
            
            if (categoryPageHeader && allMovies.length > 0) {
                const randomMovie = allMovies[Math.floor(Math.random() * Math.min(5, allMovies.length))];
                if (randomMovie && randomMovie.image) {
                    categoryPageHeader.style.backgroundImage = `url('${randomMovie.image}')`;
                    
                    // Extract dominant color for dynamic gradient and theme color
                    extractDominantColor(randomMovie.image).then(color => {
                        categoryPageHeader.style.setProperty('--gradient-color-top', `rgba(${color.r}, ${color.g}, ${color.b}, 1)`);
                        
                        // Update browser theme color
                        if (themeColorMeta) {
                            const hexColor = rgbToHex(color.r, color.g, color.b);
                            headerOriginalThemeColor = hexColor;
                            // Only update if not scrolled
                            if (!categoryHeaderNav.classList.contains('scrolled')) {
                                themeColorMeta.setAttribute('content', hexColor);
                            }
                        }
                    }).catch(error => {
                        console.warn('Failed to extract color for category header, using default:', error);
                    });
                }
            }
        }
        
        // Load initial page
        loadCategoryMovies(currentPage).then(() => {
            setHeaderBackground();
        });

        // Search button functionality - open search popup
        const headerSearchBtn = document.querySelector('.header-menu-button');
        const searchPopupModal = document.getElementById('searchPopupModal');
        const searchPopupClose = document.getElementById('searchPopupClose');
        const searchInput = document.getElementById('searchInput');
        let originalThemeColor = null;

        function openSearchPopup() {
            if (searchPopupModal) {
                const themeColorMeta = document.getElementById('themeColor');
                if (themeColorMeta) {
                    originalThemeColor = themeColorMeta.getAttribute('content');
                    themeColorMeta.setAttribute('content', '#0f0f0f');
                }
                
                searchPopupModal.style.display = 'block';
                setTimeout(() => {
                    searchPopupModal.classList.add('show');
                }, 10);
                document.body.style.overflow = 'hidden';
                if (searchInput) {
                    setTimeout(() => {
                        searchInput.focus();
                    }, 300);
                }
            }
        }

        function closeSearchPopup() {
            if (searchPopupModal) {
                const themeColorMeta = document.getElementById('themeColor');
                if (themeColorMeta && originalThemeColor) {
                    themeColorMeta.setAttribute('content', originalThemeColor);
                }
                
                searchPopupModal.classList.remove('show');
                setTimeout(() => {
                    searchPopupModal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }, 300);
            }
        }

        if (headerSearchBtn) {
            headerSearchBtn.addEventListener('click', openSearchPopup);
        }

        if (searchPopupClose) {
            searchPopupClose.addEventListener('click', closeSearchPopup);
        }

        if (searchPopupModal) {
            searchPopupModal.addEventListener('click', function(e) {
                if (e.target === searchPopupModal) {
                    closeSearchPopup();
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && searchPopupModal && searchPopupModal.classList.contains('show')) {
                closeSearchPopup();
            }
        });
    </script>

    <script src="script.js" defer></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('Service Worker registered successfully:', registration.scope);
                    })
                    .catch(error => {
                        console.log('Service Worker registration failed:', error);
                    });
            });
        }
    </script>
</body>
</html>
