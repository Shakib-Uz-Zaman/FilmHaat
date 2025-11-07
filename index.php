<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta name="theme-color" content="#000000" id="themeColor">
    <link rel="icon" type="image/webp" href="<?php echo htmlspecialchars($SITE_SETTINGS['logo_image']); ?>">
    <link rel="apple-touch-icon" href="<?php echo htmlspecialchars($SITE_SETTINGS['logo_image']); ?>">
    <title><?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?> - Movie & Series Search</title>
    <link rel="preload" as="image" href="<?php echo htmlspecialchars($SITE_SETTINGS['background_image']); ?>" type="image/webp">
    <link rel="preload" as="image" href="<?php echo htmlspecialchars($SITE_SETTINGS['logo_image']); ?>" type="image/webp">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
            background: rgba(20, 20, 20, 0.75);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
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
    <script>
        (function() {
            function setNavbarHeight() {
                var navbar = document.querySelector('.navbar');
                if (navbar) {
                    var height = navbar.offsetHeight;
                    document.documentElement.style.setProperty('--navbar-height', height + 'px');
                }
            }
            
            function checkScrollAndApply() {
                if (window.scrollY > 50) {
                    var navbar = document.querySelector('.navbar');
                    var themeColorMeta = document.getElementById('themeColor');
                    if (navbar) {
                        navbar.classList.add('scrolled');
                    }
                    if (themeColorMeta) {
                        themeColorMeta.setAttribute('content', '#000000');
                    }
                }
            }
            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setNavbarHeight();
                    checkScrollAndApply();
                });
            } else {
                setNavbarHeight();
                checkScrollAndApply();
            }
            
            window.addEventListener('scroll', checkScrollAndApply, { passive: true, once: true });
            window.addEventListener('resize', setNavbarHeight, { passive: true });
        })();
    </script>
</head>
<body>
    <!-- Bottom Navigation - Loaded at the start for instant rendering -->
    <nav class="bottom-nav">
        <a href="index.php" class="bottom-nav-item active" data-page="home">
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
        <a href="about.php" class="bottom-nav-item" data-page="about">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" fill="currentColor">
                <path d="M25,2C12.297,2,2,12.297,2,25s10.297,23,23,23s23-10.297,23-23S37.703,2,25,2z M25,11c1.657,0,3,1.343,3,3s-1.343,3-3,3 s-3-1.343-3-3S23.343,11,25,11z M29,38h-2h-4h-2v-2h2V23h-2v-2h2h4v2v13h2V38z"></path>
            </svg>
            <span>About</span>
        </a>
    </nav>
    
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <img src="<?php echo htmlspecialchars($SITE_SETTINGS['logo_image']); ?>" alt="" class="logo-image">
                <span class="logo-text"><?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?></span>
            </div>
            <div class="nav-links">
                <a href="index.php" class="nav-link active" data-page="home">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="currentColor">
                        <path d="M39.5,43h-9c-1.381,0-2.5-1.119-2.5-2.5v-9c0-1.105-0.895-2-2-2h-4c-1.105,0-2,0.895-2,2v9c0,1.381-1.119,2.5-2.5,2.5h-9   C7.119,43,6,41.881,6,40.5V21.413c0-2.299,1.054-4.471,2.859-5.893L23.071,4.321c0.545-0.428,1.313-0.428,1.857,0L39.142,15.52      C40.947,16.942,42,19.113,42,21.411V40.5C42,41.881,40.881,43,39.5,43z"></path>
                    </svg>
                    <span>Home</span>
                </a>
                <a href="latest.php?category=LATEST" class="nav-link" data-page="latest">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="currentColor">
                        <path d="M45,13c0,0-2,8-7,8c-11,0-7-18-7-18S21,9,18,25c-4-2-5-8-5-8s-6,7-6,18c0,21,20,23,20,23s-5-6-5-13c0-11,4.696-16,4.696-16 s1,11,7.304,11c4,0,6-3,6-3s4,4,4,11c0,6.348-5,10-5,10c10.478-2.106,18-11.79,18-22.862C57,22,45,13,45,13z"></path>
                    </svg>
                    <span><?php echo htmlspecialchars(isset($LATEST_WEBSITES['LATEST']['display_name']) ? $LATEST_WEBSITES['LATEST']['display_name'] : 'Latest'); ?></span>
                </a>
                <a href="loved.php" class="nav-link" data-page="loved">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="currentColor">
                        <path d="M 15 7 C 8.9424416 7 4 11.942442 4 18 C 4 22.096154 7.0876448 25.952899 10.851562 29.908203 C 14.615481 33.863507 19.248379 37.869472 22.939453 41.560547 A 1.50015 1.50015 0 0 0 25.060547 41.560547 C 28.751621 37.869472 33.384518 33.863507 37.148438 29.908203 C 40.912356 25.952899 44 22.096154 44 18 C 44 11.942442 39.057558 7 33 7 C 29.523564 7 26.496821 8.8664883 24 12.037109 C 21.503179 8.8664883 18.476436 7 15 7 z"></path>
                    </svg>
                    <span>Loved</span>
                </a>
                <a href="about.php" class="nav-link" data-page="about">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" fill="currentColor">
                        <path d="M25,2C12.297,2,2,12.297,2,25s10.297,23,23,23s23-10.297,23-23S37.703,2,25,2z M25,11c1.657,0,3,1.343,3,3s-1.343,3-3,3 s-3-1.343-3-3S23.343,11,25,11z M29,38h-2h-4h-2v-2h2V23h-2v-2h2h4v2v13h2V38z"></path>
                    </svg>
                    <span>About</span>
                </a>
            </div>
            <div class="nav-search">
                <button type="button" id="navSearchBtn" class="nav-search-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </button>
                <div class="nav-search-wrapper">
                    <div class="nav-animated-placeholder" id="navAnimatedPlaceholder">
                        <span class="nav-placeholder-text">Search Movies & Series</span>
                    </div>
                    <input 
                        type="text" 
                        id="navSearchInput" 
                        class="nav-search-input" 
                        placeholder="" 
                        autocomplete="off"
                        readonly
                    />
                </div>
            </div>
        </div>
    </nav>

    <div class="hero-carousel-section" id="heroCarouselSection">
        <div class="hero-carousel-skeleton" id="heroCarouselSkeleton">
            <div class="skeleton-shimmer"></div>
        </div>
        <div class="hero-carousel-container" style="display: none;">
            <button class="hero-carousel-btn hero-carousel-btn-prev" id="heroCarouselPrevBtn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                </svg>
            </button>
            <div class="hero-carousel-track" id="heroCarouselTrack">
            </div>
            <button class="hero-carousel-btn hero-carousel-btn-next" id="heroCarouselNextBtn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                </svg>
            </button>
            <div class="hero-carousel-dots" id="heroCarouselDots"></div>
        </div>
    </div>

    <div class="hero-illustration-section" id="heroIllustrationSection" style="display: none;">
        <div class="hero-illustration-content">
            <img src="attached_image/illustration/hero-illustration.webp" alt="Hero Illustration" class="hero-illustration-image">
            <p class="hero-illustration-message">Oops! No content available.</p>
        </div>
    </div>

    <div id="searchPopupModal" class="search-popup-modal" style="display: none;">
        <div class="search-popup-content">
            <div class="search-popup-header">
                <div class="search-popup-header-container">
                    <div class="logo">
                        <img src="<?php echo htmlspecialchars($SITE_SETTINGS['logo_image']); ?>" alt="" class="logo-image">
                        <span class="logo-text"><?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?></span>
                    </div>
                    <button class="search-popup-close" id="searchPopupClose">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="search-section" style="background: linear-gradient(rgba(0,0,0,1) 0%, rgba(0,0,0,0.80) 15%, rgba(0,0,0,0.50) 75%, rgba(0,0,0,0.50) 100%), url('<?php echo htmlspecialchars($SITE_SETTINGS['background_image']); ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
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
                                        <span class="placeholder-text"></span>
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

    <div id="pwaInstallBanner" class="pwa-install-banner" style="display: none;">
        <div class="pwa-banner-content">
            <div class="pwa-banner-logo">
                <img src="attached_image/pwa-logo.webp" alt="<?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?>" class="pwa-logo-img">
            </div>
            <div class="pwa-banner-info">
                <div class="pwa-banner-title"><?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?> App</div>
                <div class="pwa-banner-subtitle">Install the app for better experience</div>
            </div>
            <div class="pwa-banner-actions">
                <button id="pwaInstallBtn" class="pwa-install-btn">Install</button>
                <button id="pwaCloseBtn" class="pwa-close-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="categories-section" id="categoriesSection" style="display: block;">
        <div class="section-header">
            <h2 class="website-name">
                Categories
            </h2>
        </div>
        <div class="categories-carousel">
            <div class="carousel-container">
                <button class="carousel-btn carousel-btn-prev" data-carousel="categoriesTrack">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </button>
                <div class="categories-wrapper">
                    <div class="categories-track" id="categoriesTrack">
                        <button class="category-item active" data-category="all">All</button>
                        <?php foreach ($CATEGORIES_WEBSITES as $categoryKey => $categoryData): ?>
                            <?php
                                $hasVisibleWebsite = false;
                                foreach ($categoryData as $key => $value) {
                                    if ($key !== 'display_name' && is_array($value)) {
                                        if (!isset($value['hidden']) || $value['hidden'] !== true) {
                                            $hasVisibleWebsite = true;
                                            break;
                                        }
                                    }
                                }
                                
                                if ($hasVisibleWebsite):
                            ?>
                            <button class="category-item" data-category="<?php echo strtolower($categoryKey); ?>">
                                <?php echo isset($categoryData['display_name']) ? htmlspecialchars($categoryData['display_name']) : htmlspecialchars($categoryKey); ?>
                            </button>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button class="carousel-btn carousel-btn-next" data-carousel="categoriesTrack">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>


    <?php
    function generateSectionMapping($configKey, $displayName) {
        $keyLower = strtolower($configKey);
        
        return [
            'id' => $keyLower . 'Section',
            'carouselId' => $keyLower . 'Carousel',
            'trackId' => $keyLower . '-carousel-track',
            'moreBtnId' => $keyLower . 'MoreBtn',
            'name' => $displayName,
            'key' => $keyLower
        ];
    }
    
    $sectionMapping = [];
    foreach ($ALL_SECTION_WEBSITES as $configKey => $websites) {
        $displayName = isset($websites['display_name']) ? $websites['display_name'] : ucwords(str_replace('_', ' ', $configKey));
        $sectionMapping[$configKey] = generateSectionMapping($configKey, $displayName);
    }

    foreach ($ALL_SECTION_WEBSITES as $configKey => $websites) {
        if (isset($sectionMapping[$configKey])) {
            $hasVisibleWebsite = false;
            foreach ($websites as $key => $website) {
                if ($key !== 'display_name' && is_array($website)) {
                    if (!isset($website['hidden']) || !$website['hidden']) {
                        $hasVisibleWebsite = true;
                        break;
                    }
                }
            }
            if (!$hasVisibleWebsite) continue;
            
            $section = $sectionMapping[$configKey];
    ?>
    <div class="trending-section" id="<?php echo $section['id']; ?>" style="display: block;">
        <div class="section-header">
            <h2 class="website-name">
                <?php echo htmlspecialchars($section['name']); ?>
            </h2>
            <?php if ($configKey !== 'WEEKLY_TOP_10'): ?>
            <a href="more.php?category=<?php echo strtolower($configKey); ?>" class="more-btn" id="<?php echo $section['moreBtnId']; ?>">
                More<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                </svg>
            </a>
            <?php endif; ?>
        </div>
        <div id="<?php echo $section['carouselId']; ?>">
            <div class="carousel-container">
                <button class="carousel-btn carousel-btn-prev" data-carousel="<?php echo $section['trackId']; ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </button>
                <div class="carousel-wrapper">
                    <div class="carousel-track skeleton-carousel" id="<?php echo $section['trackId']; ?>">
                        <?php for ($i = 0; $i < 8; $i++): ?>
                        <div class="carousel-item-wrapper">
                            <div class="carousel-item skeleton-item">
                                <div class="skeleton-image" style="background: rgba(26, 26, 26);"></div>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <button class="carousel-btn carousel-btn-next" data-carousel="<?php echo $section['trackId']; ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <?php
        }
    }
    ?>


    <?php
    foreach ($CATEGORIES_WEBSITES as $categoryKey => $categoryData):
        $hasVisibleWebsite = false;
        foreach ($categoryData as $key => $value) {
            if ($key !== 'display_name' && is_array($value)) {
                if (!isset($value['hidden']) || $value['hidden'] !== true) {
                    $hasVisibleWebsite = true;
                    break;
                }
            }
        }
        if (!$hasVisibleWebsite) continue;
        
        $categoryLower = strtolower($categoryKey);
        $displayName = isset($categoryData['display_name']) ? htmlspecialchars($categoryData['display_name']) : htmlspecialchars($categoryKey);
        $sectionId = $categoryLower . 'Section';
        $carouselId = $categoryLower . 'Carousel';
        $trackId = $categoryLower . '-carousel-track';
        $moreBtnId = $categoryLower . 'MoreBtn';
    ?>
    <div class="trending-section" id="<?php echo $sectionId; ?>" style="display: none;">
        <div class="section-header">
            <h2 class="website-name">
                <?php echo $displayName; ?>
            </h2>
            <a href="more.php?category=<?php echo $categoryLower; ?>" class="more-btn" id="<?php echo $moreBtnId; ?>">
                More<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="vertical-align: middle;">
                    <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                </svg>
            </a>
        </div>
        <div id="<?php echo $carouselId; ?>">
            <div class="category-grid-container" id="<?php echo $trackId; ?>">
                <?php for ($i = 1; $i <= 12; $i++): ?>
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
    </div>
    <?php endforeach; ?>

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

    <script>
        window.CATEGORIES_CONFIG = <?php 
            $categoriesConfig = [];
            foreach ($CATEGORIES_WEBSITES as $categoryKey => $categoryData) {
                $categoriesConfig[] = [
                    'key' => $categoryKey,
                    'name' => strtolower($categoryKey),
                    'displayName' => isset($categoryData['display_name']) ? $categoryData['display_name'] : $categoryKey
                ];
            }
            echo json_encode($categoriesConfig);
        ?>;
        
        window.ALL_SECTIONS_CONFIG = <?php 
            $allSectionsConfig = [];
            foreach ($sectionMapping as $configKey => $section) {
                $allSectionsConfig[] = [
                    'key' => $configKey,
                    'id' => $section['id'],
                    'name' => $section['key'],
                    'displayName' => $section['name']
                ];
            }
            echo json_encode($allSectionsConfig);
        ?>;
    </script>
    <script src="script.js?v=<?php echo time(); ?>"></script>
    <script src="popup-manager.js?v=<?php echo time(); ?>"></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        registration.addEventListener('updatefound', () => {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    if (confirm('New version available! Reload to update?')) {
                                        window.location.reload();
                                    }
                                }
                            });
                        });
                    })
                    .catch(error => {
                        console.error('Service Worker registration failed:', error);
                    });
            });
        }
    </script>
</body>
</html>
