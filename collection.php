<?php 
require_once 'config.php';
require_once 'config-helpers.php';

$collectionKey = isset($_GET['collection']) ? $_GET['collection'] : '';

if (empty($collectionKey) || !isset($MOVIE_COLLECTIONS_DATA[$collectionKey])) {
    echo '<!DOCTYPE html><html><head><meta http-equiv="refresh" content="0;url=/"></head><body><script>window.location.href="/";</script></body></html>';
    exit;
}

$collectionData = $MOVIE_COLLECTIONS_DATA[$collectionKey];
$displayName = isset($collectionData['display_name']) ? $collectionData['display_name'] : ucfirst(str_replace('_', ' ', $collectionKey));
$movies = isset($collectionData['movies']) ? $collectionData['movies'] : [];
$coverImage = isset($collectionData['cover_image']) && !empty($collectionData['cover_image']) ? $collectionData['cover_image'] : '';

$visibleMovies = array_filter($movies, function($movie) {
    return !isset($movie['hidden']) || !$movie['hidden'];
});
$visibleMovies = array_values($visibleMovies);
$totalMovies = count($visibleMovies);
$moviesPerPage = 16;
$initialMovies = array_slice($visibleMovies, 0, $moviesPerPage);
$hasMoreMovies = $totalMovies > $moviesPerPage;

if (empty($coverImage) && count($movies) > 0 && isset($movies[0]['image'])) {
    $coverImage = $movies[0]['image'];
}
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
    <title><?php echo htmlspecialchars($displayName); ?> Series - <?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?></title>
    <meta name="description" content="Watch all movies in <?php echo htmlspecialchars($displayName); ?> series. Browse the complete collection - completely free with no sign-up or subscription required.">
    <link rel="stylesheet" href="styles.css">
    <link rel="manifest" href="manifest.php">
    <script>
        window.SITE_SETTINGS = {
            website_name: <?php echo json_encode($SITE_SETTINGS['website_name']); ?>,
            logo_image: <?php echo json_encode($SITE_SETTINGS['logo_image']); ?>,
            background_image: <?php echo json_encode($SITE_SETTINGS['background_image']); ?>
        };
        window.COLLECTION_COVER_IMAGE = <?php echo json_encode(!empty($coverImage) ? $coverImage : ''); ?>;
        window.COLLECTION_MOVIES = <?php echo json_encode($visibleMovies); ?>;
        window.MOVIES_PER_PAGE = <?php echo $moviesPerPage; ?>;
        window.TOTAL_MOVIES = <?php echo $totalMovies; ?>;
    </script>
    <style>
        html, body {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .collection-page-header {
            position: relative;
            width: 100%;
            aspect-ratio: 21 / 9;
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
            display: flex;
            align-items: flex-end;
            padding: 0 20px 40px;
            overflow: hidden;
            margin: 0 !important;
        }
        
        .collection-page-header::before {
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
        
        .collection-page-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 95%;
            background: linear-gradient(to top, rgba(15,15,15,1) 0%, transparent 100%);
            z-index: 2;
        }
        
        .collection-page-title {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin: 0;
            position: relative;
            z-index: 3;
            transition: opacity 0.1s linear;
        }
        
        .collection-page-subtitle {
            color: rgba(255,255,255,0.7);
            font-size: 1rem;
            margin: 8px 0 0 0;
            position: relative;
            z-index: 3;
        }
        
        .collection-page-content {
            max-width: 1920px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .collection-page-grid {
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
            transition: all 0.3s ease;
            cursor: pointer;
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
        
        .collection-header-nav {
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
        
        .collection-header-nav.scrolled {
            background: linear-gradient(to bottom, rgba(15, 15, 15, 1) 0%, rgba(15, 15, 15, 0.85) 100%) !important;
        }
        
        .collection-nav-title {
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
        
        .collection-header-nav.scrolled .collection-nav-title {
            opacity: 1;
        }
        
        .empty-collection {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255, 255, 255, 0.5);
        }
        
        .empty-collection svg {
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-collection h3 {
            font-size: 1.5rem;
            margin: 0 0 10px 0;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .empty-collection p {
            font-size: 1rem;
            margin: 0;
        }
        
        @media (max-width: 768px) and (orientation: portrait) {
            .collection-page-header {
                aspect-ratio: 3 / 2;
                padding: 0 15px 30px;
                background-position: center top 10%;
            }
            
            .collection-page-title {
                font-size: 1.5rem;
            }
            
            .collection-header-nav {
                padding: 0 15px;
                height: 56px;
            }
            
            .collection-page-content {
                padding: 30px 15px;
            }
            
            .collection-nav-title {
                left: 65px;
                font-size: 1.2rem;
            }
        }
        
        @media (max-width: 926px) and (orientation: landscape) {
            .collection-page-header {
                aspect-ratio: 21 / 9;
                padding: 0 15px 20px;
                background-position: center top;
                margin-top: 0;
                min-height: 250px;
            }
            
            .collection-page-title {
                font-size: 1.3rem;
            }
            
            .collection-header-nav {
                padding: 0 15px;
                height: 48px;
            }
            
            .collection-page-content {
                padding: 25px 15px;
            }
            
            .collection-nav-title {
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
    </style>
</head>
<body>
    <main>
    <div class="collection-page-header" id="collectionPageHeader" style="background-image: url('<?php echo !empty($coverImage) ? "image-proxy.php?url=" . urlencode($coverImage) : htmlspecialchars($SITE_SETTINGS['background_image']); ?>');">
        <div class="collection-header-nav" id="collectionHeaderNav">
            <button onclick="history.back()" class="back-button">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                    <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                </svg>
            </button>
            <h2 class="collection-nav-title"><?php echo htmlspecialchars($displayName); ?></h2>
            <button class="header-menu-button" id="headerSearchButton">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>
            </button>
        </div>
        <div style="position: relative; z-index: 3;">
            <h1 class="collection-page-title"><?php echo htmlspecialchars($displayName); ?></h1>
            <p class="collection-page-subtitle"><?php echo count($movies); ?> movies in this collection</p>
        </div>
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

    <div class="collection-page-content">
        <?php if (count($movies) === 0): ?>
            <div class="empty-collection">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <path d="M21 15l-5-5L5 21"/>
                </svg>
                <h3>No Movies Yet</h3>
                <p>This collection doesn't have any movies yet.</p>
            </div>
        <?php else: ?>
            <div class="collection-page-grid category-page-grid" id="collectionGrid">
                <?php 
                foreach($initialMovies as $movie): 
                    $genres = [];
                    if (!empty($movie['genre'])) {
                        $genreList = preg_split('/[,|\/]/', $movie['genre']);
                        foreach ($genreList as $g) {
                            $trimmed = trim($g);
                            if ($trimmed && !in_array($trimmed, $genres)) {
                                $genres[] = $trimmed;
                            }
                        }
                    }
                    if (!empty($movie['language'])) {
                        $langList = preg_split('/[,|\/]/', $movie['language']);
                        foreach ($langList as $l) {
                            $trimmed = trim($l);
                            if ($trimmed && !in_array($trimmed, $genres)) {
                                $genres[] = $trimmed;
                            }
                        }
                    }
                    $genres = array_slice($genres, 0, 4);
                ?>
                    <div class="category-grid-item" 
                         onclick="openMovieModal(this)"
                         data-title="<?php echo htmlspecialchars($movie['title'] ?? ''); ?>"
                         data-link="<?php echo htmlspecialchars($movie['link'] ?? ''); ?>"
                         data-image="<?php echo htmlspecialchars($movie['image'] ?? ''); ?>"
                         data-language="<?php echo htmlspecialchars($movie['language'] ?? ''); ?>"
                         data-genre="<?php echo htmlspecialchars($movie['genre'] ?? ''); ?>"
                         data-website="<?php echo htmlspecialchars($movie['website'] ?? ''); ?>">
                        <img class="result-image lazy-image" 
                             src="image-proxy.php?url=<?php echo urlencode($movie['image'] ?? ''); ?>" 
                             alt="<?php echo htmlspecialchars($movie['title'] ?? ''); ?>"
                             loading="lazy"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22150%22 height=%22200%22%3E%3Crect fill=%22%231a1a1a%22 width=%22150%22 height=%22200%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23666%22 font-family=%22sans-serif%22 font-size=%2214%22%3ENo Image%3C/text%3E%3C/svg%3E'">
                        <div class="category-movie-info">
                            <h3 class="category-movie-title"><?php echo htmlspecialchars($movie['title'] ?? ''); ?></h3>
                            <div class="category-movie-genres">
                                <?php foreach($genres as $genre): ?>
                                    <span class="category-genre-tag"><?php echo htmlspecialchars($genre); ?></span>
                                <?php endforeach; ?>
                                <?php if(!empty($movie['link']) || !empty($movie['website'])): ?>
                                    <span class="category-genre-tag category-website-tag">
                                        <img class="website-tag-favicon" src="<?php echo htmlspecialchars(getFaviconUrl($movie['link'])); ?>" alt="" onerror="this.style.display='none'">
                                        <?php echo htmlspecialchars(!empty($movie['website']) ? $movie['website'] : getDomainFromUrl($movie['link'])); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php 
                endforeach; ?>
                <?php if($hasMoreMovies): ?>
                    <div class="category-next-card" id="collectionLoadMore">
                        <span class="category-next-card-text">Load More</span>
                        <span class="category-next-card-arrow">❯</span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
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
        const collectionHeaderNav = document.getElementById('collectionHeaderNav');
        const collectionPageHeader = document.getElementById('collectionPageHeader');
        const collectionPageTitle = document.querySelector('.collection-page-title');
        let pageInitialized = false;
        
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }
        
        if (collectionHeaderNav) {
            collectionHeaderNav.classList.remove('scrolled');
        }
        
        document.documentElement.scrollTop = 0;
        document.body.scrollTop = 0;
        window.scrollTo(0, 0);
        
        let extractedThemeColor = '#0f0f0f';
        
        function handleHeaderScroll() {
            if (!pageInitialized) return;
            
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const headerHeight = collectionPageHeader ? collectionPageHeader.offsetHeight : 300;
            const navHeight = 60;
            const threshold = headerHeight - navHeight - 20;
            const themeColorMeta = document.getElementById('themeColor');
            
            if (collectionPageTitle) {
                const fadeStart = headerHeight * 0.5;
                const fadeEnd = headerHeight - navHeight - 20;
                if (scrollTop < fadeStart) {
                    collectionPageTitle.style.opacity = '1';
                } else if (scrollTop >= fadeEnd) {
                    collectionPageTitle.style.opacity = '0';
                } else {
                    const fadeProgress = (scrollTop - fadeStart) / (fadeEnd - fadeStart);
                    collectionPageTitle.style.opacity = String(1 - fadeProgress);
                }
            }
            
            if (scrollTop > threshold) {
                collectionHeaderNav.classList.add('scrolled');
                if (themeColorMeta) {
                    themeColorMeta.setAttribute('content', '#0f0f0f');
                }
            } else {
                collectionHeaderNav.classList.remove('scrolled');
                if (themeColorMeta && extractedThemeColor) {
                    themeColorMeta.setAttribute('content', extractedThemeColor);
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
        
        // Search popup functionality
        const headerSearchBtn = document.getElementById('headerSearchButton');
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
        
        function initializeLazyImages() {
            const lazyImages = document.querySelectorAll('.lazy-image');
            
            lazyImages.forEach(img => {
                const parentItem = img.closest('.category-grid-item');
                
                if (img.complete && img.naturalHeight !== 0) {
                    img.classList.add('loaded');
                    if (parentItem) parentItem.classList.add('image-loaded');
                } else {
                    img.addEventListener('load', function() {
                        this.classList.add('loaded');
                        const parent = this.closest('.category-grid-item');
                        if (parent) parent.classList.add('image-loaded');
                    });
                    
                    img.addEventListener('error', function() {
                        this.classList.add('loaded');
                        const parent = this.closest('.category-grid-item');
                        if (parent) parent.classList.add('image-loaded');
                    });
                }
            });
        }
        
        let currentMoviesShown = window.MOVIES_PER_PAGE;
        const collectionGrid = document.getElementById('collectionGrid');
        
        function createMovieCard(movie) {
            let genres = [];
            if (movie.genre) {
                const genreList = movie.genre.split(/[,|\/]/);
                genreList.forEach(g => {
                    const trimmed = g.trim();
                    if (trimmed && !genres.includes(trimmed)) {
                        genres.push(trimmed);
                    }
                });
            }
            if (movie.language) {
                const langList = movie.language.split(/[,|\/]/);
                langList.forEach(l => {
                    const trimmed = l.trim();
                    if (trimmed && !genres.includes(trimmed)) {
                        genres.push(trimmed);
                    }
                });
            }
            genres = genres.slice(0, 4);
            
            const faviconUrl = getFaviconUrl(movie.link || '');
            const websiteName = movie.website || getDomainFromUrl(movie.link || '');
            
            let genreTags = genres.map(g => `<span class="category-genre-tag">${escapeHtml(g)}</span>`).join('');
            if (movie.link || movie.website) {
                genreTags += `<span class="category-genre-tag category-website-tag">
                    <img class="website-tag-favicon" src="${escapeHtml(faviconUrl)}" alt="" onerror="this.style.display='none'">
                    ${escapeHtml(websiteName)}
                </span>`;
            }
            
            const card = document.createElement('div');
            card.className = 'category-grid-item';
            card.setAttribute('onclick', 'openMovieModal(this)');
            card.setAttribute('data-title', movie.title || '');
            card.setAttribute('data-link', movie.link || '');
            card.setAttribute('data-image', movie.image || '');
            card.setAttribute('data-language', movie.language || '');
            card.setAttribute('data-genre', movie.genre || '');
            card.setAttribute('data-website', movie.website || '');
            
            card.innerHTML = `
                <img class="result-image lazy-image" 
                     src="image-proxy.php?url=${encodeURIComponent(movie.image || '')}" 
                     alt="${escapeHtml(movie.title || '')}"
                     loading="lazy"
                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22150%22 height=%22200%22%3E%3Crect fill=%22%231a1a1a%22 width=%22150%22 height=%22200%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23666%22 font-family=%22sans-serif%22 font-size=%2214%22%3ENo Image%3C/text%3E%3C/svg%3E'">
                <div class="category-movie-info">
                    <h3 class="category-movie-title">${escapeHtml(movie.title || '')}</h3>
                    <div class="category-movie-genres">${genreTags}</div>
                </div>
            `;
            
            return card;
        }
        
        function loadMoreCollectionMovies() {
            const loadMoreBtn = document.getElementById('collectionLoadMore');
            if (!loadMoreBtn || !collectionGrid) return;
            
            loadMoreBtn.remove();
            
            const movies = window.COLLECTION_MOVIES || [];
            const nextBatch = movies.slice(currentMoviesShown, currentMoviesShown + window.MOVIES_PER_PAGE);
            
            nextBatch.forEach(movie => {
                const card = createMovieCard(movie);
                collectionGrid.appendChild(card);
            });
            
            currentMoviesShown += nextBatch.length;
            
            if (currentMoviesShown < window.TOTAL_MOVIES) {
                const newLoadMoreBtn = document.createElement('div');
                newLoadMoreBtn.className = 'category-next-card';
                newLoadMoreBtn.id = 'collectionLoadMore';
                newLoadMoreBtn.innerHTML = `
                    <span class="category-next-card-text">Load More</span>
                    <span class="category-next-card-arrow">❯</span>
                `;
                newLoadMoreBtn.addEventListener('click', loadMoreCollectionMovies);
                collectionGrid.appendChild(newLoadMoreBtn);
            }
            
            initializeLazyImages();
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            initializeLazyImages();
            
            const loadMoreBtn = document.getElementById('collectionLoadMore');
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', loadMoreCollectionMovies);
            }
        });
        
        const movieModal = document.getElementById('movieModal');
        const closeModalBtn = document.querySelector('.close-modal');
        const modalImage = document.getElementById('modalImage');
        const modalTitle = document.getElementById('modalTitle');
        const modalLink = document.getElementById('modalLink');
        const modalLanguage = document.getElementById('modalLanguage');
        const modalWebsite = document.getElementById('modalWebsite');
        
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
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function openMovieModal(item) {
            const title = item.getAttribute('data-title');
            const link = item.getAttribute('data-link');
            const image = item.getAttribute('data-image');
            const language = item.getAttribute('data-language');
            const genre = item.getAttribute('data-genre') || '';
            const website = item.getAttribute('data-website') || '';
            
            saveToRecentViewed(title, link, image, language, genre, website);
            
            trackMovieView(title, link, image, language, website);
            
            modalTitle.textContent = title;
            modalLink.href = link;
            modalLanguage.textContent = language || '';
            
            const faviconUrl = getFaviconUrl(link);
            const displayName = website || getDomainFromUrl(link);
            if (displayName && faviconUrl && modalWebsite) {
                modalWebsite.innerHTML = `<img class="modal-favicon" src="${escapeHtml(faviconUrl)}" alt="" onerror="this.style.display='none'"> ${escapeHtml(displayName)}`;
            } else if (modalWebsite) {
                modalWebsite.textContent = displayName || '';
            }
            
            const modalLovedBtn = document.getElementById('modalLovedBtn');
            if (modalLovedBtn) {
                modalLovedBtn.setAttribute('data-title', title);
                modalLovedBtn.setAttribute('data-link', link);
                modalLovedBtn.setAttribute('data-image', image);
                modalLovedBtn.setAttribute('data-language', language);
                modalLovedBtn.setAttribute('data-genre', genre);
                modalLovedBtn.setAttribute('data-website', website);
                
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
                modalImage.src = '';
                
                const proxyUrl = `image-proxy.php?url=${encodeURIComponent(image)}`;
                const tempImg = new Image();
                tempImg.onload = function() {
                    modalImage.src = proxyUrl;
                    modalImage.classList.add('loaded');
                };
                tempImg.onerror = function() {
                    modalImage.src = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22300%22%3E%3Crect fill=%22%231a1a1a%22 width=%22200%22 height=%22300%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23666%22 font-family=%22sans-serif%22 font-size=%2216%22%3ENo Image%3C/text%3E%3C/svg%3E';
                    modalImage.classList.add('loaded');
                };
                tempImg.src = proxyUrl;
            }
            
            movieModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal() {
            movieModal.style.display = 'none';
            document.body.style.overflow = '';
        }
        
        closeModalBtn.addEventListener('click', closeModal);
        movieModal.addEventListener('click', function(e) {
            if (e.target === movieModal) {
                closeModal();
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && movieModal.style.display === 'flex') {
                closeModal();
            }
        });
        
        function saveToRecentViewed(title, link, image, language, genre, website) {
            try {
                let recentViewed = JSON.parse(localStorage.getItem('recentViewedMovies') || '[]');
                recentViewed = recentViewed.filter(movie => movie.title !== title);
                recentViewed.unshift({ title, link, image, language, genre, website, timestamp: Date.now() });
                recentViewed = recentViewed.slice(0, 20);
                localStorage.setItem('recentViewedMovies', JSON.stringify(recentViewed));
            } catch (error) {
                console.warn('Could not save to recent viewed');
            }
        }
        
        async function trackMovieView(title, link, image, language, website) {
            if (!title || !link) {
                return { success: false, error: 'Missing required fields' };
            }
            try {
                const response = await fetch('api-weekly-top10.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'track',
                        title: title,
                        link: link,
                        image: image,
                        language: language,
                        website: website
                    })
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return await response.json();
            } catch (error) {
                console.error('Error tracking movie view:', error);
                return { success: false, error: error.message };
            }
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

                        r = Math.floor(r / count);
                        g = Math.floor(g / count);
                        b = Math.floor(b / count);

                        r = Math.floor(r * 0.5);
                        g = Math.floor(g * 0.5);
                        b = Math.floor(b * 0.5);

                        const color = { r, g, b };
                        resolve(color);
                    } catch (error) {
                        reject(error);
                    }
                };

                img.onerror = () => reject(new Error('Failed to load image'));
                img.src = `image-proxy.php?url=${encodeURIComponent(imageUrl)}`;
            });
        }
        
        function rgbToHex(r, g, b) {
            return '#' + [r, g, b].map(x => {
                const hex = x.toString(16);
                return hex.length === 1 ? '0' + hex : hex;
            }).join('');
        }
        
        function applyDynamicColors() {
            const coverImage = window.COLLECTION_COVER_IMAGE;
            if (!coverImage) return;
            
            const header = document.getElementById('collectionPageHeader');
            const themeColorMeta = document.getElementById('themeColor');
            
            extractDominantColor(coverImage).then(color => {
                if (header) {
                    header.style.setProperty('--gradient-color-top', `rgba(${color.r}, ${color.g}, ${color.b}, 1)`);
                }
                
                const hexColor = rgbToHex(color.r, color.g, color.b);
                extractedThemeColor = hexColor;
                originalThemeColor = hexColor;
                
                if (themeColorMeta) {
                    themeColorMeta.setAttribute('content', hexColor);
                }
            }).catch(error => {
                console.warn('Could not extract dominant color:', error);
            });
        }
        
        document.addEventListener('DOMContentLoaded', applyDynamicColors);
    </script>
    <script src="script.js" defer></script>
</body>
</html>
