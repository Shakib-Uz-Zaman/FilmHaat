<?php 
require_once 'config.php';
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
    <title>About - <?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?></title>
    <meta name="description" content="Learn about our movie search tool. It helps you look through multiple websites at once; we don't host or store content. No registration needed, and it's free.">
    <link rel="stylesheet" href="styles.css">
    <link rel="manifest" href="manifest.json">
    
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
            background-position: center top;
            background-repeat: no-repeat;
            background-image: url('<?php echo htmlspecialchars($SITE_SETTINGS['background_image']); ?>');
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
                linear-gradient(to bottom, rgba(15,15,15,1) 0%, transparent 60%),
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
        
        
        .category-page-title {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin: 0;
            position: relative;
            z-index: 3;
            transition: opacity 0.1s linear;
        }
        
        .category-page-subtitle {
            display: none;
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
        
        .faq-section {
            max-width: 1920px;
            margin: 0 auto;
            padding: 40px 20px;
            padding-bottom: calc(80px + env(safe-area-inset-bottom));
            background-color: #0f0f0f;
        }
        
        @media (max-width: 768px) and (orientation: portrait) {
            .faq-section {
                padding: 30px 15px;
                padding-bottom: calc(80px + env(safe-area-inset-bottom)) !important;
            }
        }
        
        @media (max-width: 926px) and (orientation: landscape) {
            .faq-section {
                padding: 25px 15px;
                padding-bottom: calc(80px + env(safe-area-inset-bottom)) !important;
            }
        }
        
        .contact-section {
            max-width: 1920px;
            margin: 0 auto;
            padding: 40px 20px;
            padding-bottom: calc(100px + env(safe-area-inset-bottom));
        }
        
        .contact-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .contact-title {
            font-size: 2rem;
            font-weight: 600;
            color: white;
            margin: 0 0 30px 0;
            text-align: center;
        }
        
        .contact-subtitle {
            display: none;
        }
        
        .contact-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .contact-card {
            background: #2d2d2d;
            padding: 20px 25px;
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: background 0.2s ease;
            text-align: left;
        }
        
        .contact-card:hover {
            background: #3d3d3d;
        }
        
        .contact-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .contact-icon svg {
            width: 24px;
            height: 24px;
        }
        
        .contact-card-content {
            flex: 1;
        }
        
        .contact-card-title {
            font-size: 1.1rem;
            font-weight: 500;
            margin: 0 0 5px 0;
            color: white;
        }
        
        .contact-card-text {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            margin: 0;
            word-break: break-word;
        }
        
        .contact-card-cta {
            display: none;
        }
        
        @media (max-width: 768px) and (orientation: portrait) {
            .contact-section {
                padding: 30px 15px;
                padding-bottom: calc(100px + env(safe-area-inset-bottom));
            }
            
            .contact-title {
                font-size: 1.5rem;
            }
            
            .contact-cards {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .contact-card {
                padding: 18px 20px;
            }
        }
        
        @media (max-width: 926px) and (orientation: landscape) {
            .contact-section {
                padding: 30px 15px;
                padding-bottom: calc(100px + env(safe-area-inset-bottom));
            }
            
            .contact-title {
                font-size: 1.5rem;
            }
            
            .contact-cards {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .contact-card {
                padding: 18px 20px;
            }
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
        <a href="latest.php?category=LATEST" class="bottom-nav-item" data-page="latest">
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
        <a href="about.php" class="bottom-nav-item active" data-page="about">
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
            <h2 class="category-nav-title">About</h2>
            <button class="header-menu-button" id="headerSearchButton">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>
            </button>
        </div>
        <h1 class="category-page-title">About</h1>
        <p class="category-page-subtitle">Learn more about our platform</p>
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
                                            foreach ($visibleSearchWebsites as $websiteName => $website): 
                                            ?>
                                            <label class="filter-option">
                                                <input type="checkbox" class="filter-checkbox" value="<?php echo htmlspecialchars($websiteName); ?>" checked>
                                                <span><?php echo htmlspecialchars($websiteName); ?></span>
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

    <script>
        let pageInitialized = false;
        
        // Fixed Header Scroll Logic
        const categoryHeaderNav = document.getElementById('categoryHeaderNav');
        const categoryPageHeader = document.getElementById('categoryPageHeader');
        const categoryPageTitle = document.querySelector('.category-page-title');
        
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
            if (!pageInitialized) return;
            
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const headerHeight = categoryPageHeader ? categoryPageHeader.offsetHeight : 300;
            const navHeight = 60;
            const threshold = headerHeight - navHeight - 20;
            const themeColorMeta = document.getElementById('themeColor');
            
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
                    themeColorMeta.setAttribute('content', '#0f0f0f');
                }
            }
        }
        
        let scrollTimeout;
        window.addEventListener('scroll', function() {
            if (scrollTimeout) {
                window.cancelAnimationFrame(scrollTimeout);
            }
            scrollTimeout = window.requestAnimationFrame(function() {
                handleHeaderScroll();
            });
        });
        
        setTimeout(function() {
            pageInitialized = true;
            handleHeaderScroll();
        }, 100);
        
        // Search button functionality
        const headerSearchButton = document.getElementById('headerSearchButton');
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
                
                // Load recent viewed movies
                if (typeof loadRecentViewedMovies === 'function') {
                    loadRecentViewedMovies();
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

        if (headerSearchButton) {
            headerSearchButton.addEventListener('click', openSearchPopup);
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

        // Bottom navigation search button
        const bottomNavSearch = document.getElementById('bottomNavSearch');
        if (bottomNavSearch) {
            bottomNavSearch.addEventListener('click', openSearchPopup);
        }
    </script>
    
    <section class="faq-section" id="about">
        <div class="faq-container">
            <h2 class="faq-title">Frequently Asked Questions</h2>
            
            <div class="faq-list">
                <div class="faq-item">
                        <button class="faq-question">
                            <span>What is <?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?>?</span>
                            <svg class="faq-icon" width="36" height="36" viewBox="0 0 36 36" fill="none">
                                <path d="M18 12V24M24 18H12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <div class="faq-answer">
                            <p><?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?> is a comprehensive movie search aggregator that allows you to search for movies and series across multiple websites simultaneously. Instead of visiting different websites individually, you can search them all at once from a single convenient interface.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">
                            <span>Does <?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?> host or store movies?</span>
                            <svg class="faq-icon" width="36" height="36" viewBox="0 0 36 36" fill="none">
                                <path d="M18 12V24M24 18H12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <div class="faq-answer">
                            <p>No, <?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?> does not host, store, or upload any movies or series. We are a search engine, similar to Google, that helps you find content across the internet. All search results link to third-party websites, and we have no control over the content hosted on those external sites.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">
                            <span>Is <?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?> a streaming service?</span>
                            <svg class="faq-icon" width="36" height="36" viewBox="0 0 36 36" fill="none">
                                <path d="M18 12V24M24 18H12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <div class="faq-answer">
                            <p>No, <?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?> is not a streaming or hosting platform. We are a search aggregator that helps you discover where movies and series are available on the internet. We simply provide links to external websites where content may be found.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">
                            <span>Do you provide or distribute movies?</span>
                            <svg class="faq-icon" width="36" height="36" viewBox="0 0 36 36" fill="none">
                                <path d="M18 12V24M24 18H12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <div class="faq-answer">
                            <p>No, we do not provide, upload, or distribute any movies or series. <?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?> only searches and indexes publicly available information on the internet, just like any other search engine. We are not responsible for the content on external websites that appear in search results.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">
                            <span>How do I search for movies?</span>
                            <svg class="faq-icon" width="36" height="36" viewBox="0 0 36 36" fill="none">
                                <path d="M18 12V24M24 18H12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <div class="faq-answer">
                            <p>Simply click on the search bar at the top of the page or in the main hero section. Type the name of the movie or series you're looking for, and <?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?> will search across all configured websites to show you the results. You can also filter which websites to search using the filter button.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">
                            <span>Is <?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?> free to use?</span>
                            <svg class="faq-icon" width="36" height="36" viewBox="0 0 36 36" fill="none">
                                <path d="M18 12V24M24 18H12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <div class="faq-answer">
                            <p>Yes, <?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?> is completely free to use. You can search for movies and series, browse categories, and discover trending movies without any registration or subscription fees.</p>
                        </div>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-section">
        <div class="contact-container">
            <h2 class="contact-title">Contact Us</h2>
            
            <div class="contact-cards">
                <a href="mailto:sbxgroup.feedback@gmail.com" class="contact-card">
                    <div class="contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                    <div class="contact-card-content">
                        <h3 class="contact-card-title">Email</h3>
                        <p class="contact-card-text">sbxgroup.feedback@gmail.com</p>
                    </div>
                </a>
                
                <a href="https://t.me/filmhaatofficial" target="_blank" rel="noopener noreferrer" class="contact-card">
                    <div class="contact-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295-.002 0-.003 0-.005 0l.213-3.054 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.64-.203-.658-.64.135-.954l11.566-4.458c.538-.196 1.006.128.832.941z"/>
                        </svg>
                    </div>
                    <div class="contact-card-content">
                        <h3 class="contact-card-title">Telegram</h3>
                        <p class="contact-card-text">t.me/filmhaatofficial</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

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
