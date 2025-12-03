<?php 
require_once 'config.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$websiteName = isset($_GET['website']) ? trim($_GET['website']) : '';

if (empty($query) || empty($websiteName)) {
    echo '<!DOCTYPE html><html><head><meta http-equiv="refresh" content="0;url=/"></head><body><script>window.location.href="/";</script></body></html>';
    exit;
}

if (!isset($SEARCH_WEBSITES[$websiteName])) {
    echo '<!DOCTYPE html><html><head><meta http-equiv="refresh" content="0;url=/"></head><body><script>window.location.href="/";</script></body></html>';
    exit;
}

$displayName = "\"" . htmlspecialchars($query) . "\" - " . htmlspecialchars($websiteName);
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
    <title><?php echo $displayName; ?> - <?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?></title>
    <meta name="description" content="Search results for <?php echo htmlspecialchars($query); ?> on <?php echo htmlspecialchars($websiteName); ?>">
    <link rel="stylesheet" href="styles.css">
    <link rel="manifest" href="manifest.json">
    <script>
        window.SITE_SETTINGS = {
            website_name: <?php echo json_encode($SITE_SETTINGS['website_name']); ?>,
            logo_image: <?php echo json_encode($SITE_SETTINGS['logo_image']); ?>,
            background_image: <?php echo json_encode($SITE_SETTINGS['background_image']); ?>
        };
        window.SEARCH_QUERY = <?php echo json_encode($query); ?>;
        window.WEBSITE_NAME = <?php echo json_encode($websiteName); ?>;
    </script>
    <style>
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
        
        .category-page-content {
            max-width: 1920px;
            margin: 0 auto;
            padding: 40px 20px;
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
            max-width: calc(100% - 160px);
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
            
            .category-page-content {
                padding: 30px 15px;
            }
            
            .category-nav-title {
                left: 65px;
                font-size: 1.2rem;
                max-width: calc(100% - 130px);
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
    </style>
</head>
<body>
    <main>
    <div class="category-page-header" id="categoryPageHeader">
        <div class="category-header-nav" id="categoryHeaderNav">
            <button onclick="history.back()" class="back-button" aria-label="Go back">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                    <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                </svg>
            </button>
            <h2 class="category-nav-title"><?php echo $displayName; ?></h2>
            <button class="header-menu-button" id="headerSearchButton" aria-label="Search">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>
            </button>
        </div>
        <h1 class="category-page-title"><?php echo $displayName; ?></h1>
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
                                            foreach ($visibleSearchWebsites as $wsName => $website): 
                                            ?>
                                            <label class="filter-option">
                                                <input type="checkbox" class="filter-checkbox" value="<?php echo htmlspecialchars($wsName); ?>" checked>
                                                <span><?php echo htmlspecialchars($wsName); ?></span>
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
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <div id="movieModal" class="movie-modal" style="display: none;">
        <div class="modal-wrapper">
            <button class="close-modal" aria-label="Close modal">&times;</button>
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
    document.addEventListener('DOMContentLoaded', function() {
        const categoryHeaderNav = document.getElementById('categoryHeaderNav');
        const categoryPageHeader = document.getElementById('categoryPageHeader');
        const categoryPageTitle = document.querySelector('.category-page-title');
        
        let currentPage = 1;
        let isLoading = false;
        let hasMoreResults = true;
        let allResults = [];
        let headerOriginalThemeColor = '#0f0f0f';
        
        function handleScroll() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const headerHeight = categoryPageHeader ? categoryPageHeader.offsetHeight : 300;
            const navHeight = 60;
            const threshold = headerHeight - navHeight - 20;
            const themeColorMeta = document.getElementById('themeColor');
            
            if (scrollTop >= threshold) {
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
        }
        
        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll();
        
        const movieModal = document.getElementById('movieModal');
        const modalImage = document.getElementById('modalImage');
        const modalTitle = document.getElementById('modalTitle');
        const modalLink = document.getElementById('modalLink');
        const modalLanguage = document.getElementById('modalLanguage');
        const modalWebsite = document.getElementById('modalWebsite');
        const closeModalBtn = document.querySelector('.close-modal');
        const modalLovedBtn = document.getElementById('modalLovedBtn');
        
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
        
        function isLoved(movieData) {
            try {
                const lovedMovies = JSON.parse(localStorage.getItem('lovedMovies') || '[]');
                return lovedMovies.some(m => m.link === movieData.link);
            } catch (e) {
                return false;
            }
        }
        
        function toggleLoved(movieData) {
            try {
                let lovedMovies = JSON.parse(localStorage.getItem('lovedMovies') || '[]');
                const existingIndex = lovedMovies.findIndex(m => m.link === movieData.link);
                
                if (existingIndex > -1) {
                    lovedMovies.splice(existingIndex, 1);
                } else {
                    lovedMovies.unshift({
                        title: movieData.title,
                        link: movieData.link,
                        image: movieData.image,
                        language: movieData.language,
                        website: movieData.website,
                        addedAt: Date.now()
                    });
                }
                
                localStorage.setItem('lovedMovies', JSON.stringify(lovedMovies));
                return !isLoved(movieData);
            } catch (e) {
                return false;
            }
        }
        
        function updateLovedButton(movieData) {
            if (modalLovedBtn) {
                const loved = isLoved(movieData);
                const heartPath = modalLovedBtn.querySelector('path');
                if (loved) {
                    modalLovedBtn.classList.add('loved');
                    if (heartPath) {
                        heartPath.setAttribute('fill', 'url(#heartGradient)');
                        heartPath.setAttribute('stroke', 'none');
                    }
                } else {
                    modalLovedBtn.classList.remove('loved');
                    if (heartPath) {
                        heartPath.setAttribute('fill', 'none');
                        heartPath.setAttribute('stroke', 'currentColor');
                    }
                }
            }
        }
        
        function openMovieModal(movieData) {
            if (!movieModal) return;
            
            modalTitle.textContent = movieData.title || '';
            modalLink.href = movieData.link || '#';
            modalLanguage.textContent = movieData.language || '';
            modalWebsite.textContent = movieData.website || window.WEBSITE_NAME || '';
            
            updateLovedButton(movieData);
            
            if (modalLovedBtn) {
                modalLovedBtn.onclick = function() {
                    toggleLoved(movieData);
                    updateLovedButton(movieData);
                };
            }
            
            const image = movieData.image || '';
            if (image) {
                modalImage.src = image;
                modalImage.alt = movieData.title || '';
                modalImage.style.display = 'block';
                modalImage.classList.remove('loaded');
                movieModal.querySelector('.modal-content').classList.remove('image-loaded');
                
                extractDominantColor(image).then(color => {
                    movieModal.querySelector('.modal-content').style.setProperty('--modal-gradient-color-top', `rgba(${color.r}, ${color.g}, ${color.b}, 1)`);
                    movieModal.querySelector('.modal-content').style.setProperty('--modal-gradient-color-bottom', `rgba(${color.r}, ${color.g}, ${color.b}, 1)`);
                }).catch(() => {});
                
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
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
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
        
        function loadNextCardBackground() {
            const nextCard = document.getElementById('categoryNextCard');
            if (nextCard && allResults.length > 0) {
                const randomResult = allResults[Math.floor(Math.random() * allResults.length)];
                if (randomResult && randomResult.image) {
                    nextCard.style.backgroundImage = `url('${randomResult.image}')`;
                }
            }
        }
        
        function initializeClicks() {
            document.querySelectorAll('.category-grid-item').forEach(item => {
                if (item.classList.contains('skeleton-item')) return;
                
                item.addEventListener('click', function() {
                    const movieData = {
                        title: this.dataset.title,
                        link: this.dataset.link,
                        image: this.dataset.image,
                        language: this.dataset.language,
                        website: window.WEBSITE_NAME
                    };
                    openMovieModal(movieData);
                });
            });
            
            const nextCard = document.getElementById('categoryNextCard');
            if (nextCard) {
                nextCard.addEventListener('click', function() {
                    if (!isLoading && hasMoreResults) {
                        currentPage++;
                        loadSearchResults(currentPage, true);
                    }
                });
            }
        }
        
        function displayResults(results) {
            const categoryPageGrid = document.getElementById('categoryPageGrid');
            categoryPageGrid.innerHTML = '';
            
            if (results.length === 0) {
                categoryPageGrid.innerHTML = '<p style="text-align: center; color: rgba(255,255,255,0.7); padding: 60px 20px;">No results found for this search.</p>';
                return;
            }
            
            results.forEach((item, index) => {
                const genres = parseGenres(item.genre, item.language);
                const genreTags = genres.map(g => `<span class="category-genre-tag">${escapeHtml(g)}</span>`).join('');
                
                const itemHtml = `
                    <div class="category-grid-item" 
                         data-title="${escapeHtml(item.title)}" 
                         data-link="${escapeHtml(item.link)}" 
                         data-image="${escapeHtml(item.image || '')}" 
                         data-language="${escapeHtml(item.language || '')}"
                         style="cursor: pointer;">
                        ${item.image ? `<img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.title)}" class="result-image lazy-image" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23ddd%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2220%22%3ENo Image%3C/text%3E%3C/svg%3E'">` : '<div class="result-image"></div>'}
                        <div class="category-movie-info">
                            <h3 class="category-movie-title">${escapeHtml(item.title)}</h3>
                            <div class="category-movie-genres">${genreTags}</div>
                        </div>
                    </div>
                `;
                categoryPageGrid.insertAdjacentHTML('beforeend', itemHtml);
            });
            
            if (hasMoreResults) {
                const nextCardHtml = `
                    <div class="category-next-card" id="categoryNextCard">
                        <span class="category-next-card-text">Load More</span>
                    </div>
                `;
                categoryPageGrid.insertAdjacentHTML('beforeend', nextCardHtml);
                loadNextCardBackground();
            }
            
            initializeLazyImages();
            initializeClicks();
        }
        
        async function loadSearchResults(page, append = false) {
            if (isLoading) return;
            
            isLoading = true;
            const categoryPageGrid = document.getElementById('categoryPageGrid');
            
            if (append) {
                const nextCard = document.getElementById('categoryNextCard');
                if (nextCard) {
                    nextCard.remove();
                }
                
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
                            <div class="category-movie-index">${String(allResults.length + i + 1).padStart(2, '0')}</div>
                        </div>
                    `;
                    categoryPageGrid.insertAdjacentHTML('beforeend', skeletonHtml);
                }
            }
            
            try {
                const response = await fetch(`search-single.php?query=${encodeURIComponent(window.SEARCH_QUERY)}&website=${encodeURIComponent(window.WEBSITE_NAME)}&page=${page}`);
                const data = await response.json();
                
                if (append) {
                    const skeletons = categoryPageGrid.querySelectorAll('.loading-skeleton');
                    skeletons.forEach(skeleton => skeleton.remove());
                }
                
                if (data.success && data.results && data.results.length > 0) {
                    hasMoreResults = data.hasMore || false;
                    
                    if (append) {
                        const currentCount = allResults.length;
                        allResults = [...allResults, ...data.results];
                        
                        data.results.forEach((item, index) => {
                            const genres = parseGenres(item.genre, item.language);
                            const genreTags = genres.map(g => `<span class="category-genre-tag">${escapeHtml(g)}</span>`).join('');
                            
                            const itemHtml = `
                                <div class="category-grid-item new-item" 
                                     data-title="${escapeHtml(item.title)}" 
                                     data-link="${escapeHtml(item.link)}" 
                                     data-image="${escapeHtml(item.image || '')}" 
                                     data-language="${escapeHtml(item.language || '')}"
                                     style="cursor: pointer;">
                                    ${item.image ? `<img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.title)}" class="result-image lazy-image" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23ddd%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2220%22%3ENo Image%3C/text%3E%3C/svg%3E'">` : '<div class="result-image"></div>'}
                                    <div class="category-movie-info">
                                        <h3 class="category-movie-title">${escapeHtml(item.title)}</h3>
                                        <div class="category-movie-genres">${genreTags}</div>
                                    </div>
                                </div>
                            `;
                            categoryPageGrid.insertAdjacentHTML('beforeend', itemHtml);
                        });
                        
                        setTimeout(() => {
                            const newItems = categoryPageGrid.querySelectorAll('.new-item');
                            newItems.forEach(item => item.classList.remove('new-item'));
                        }, 3000);
                    } else {
                        allResults = data.results;
                        displayResults(allResults);
                    }
                    
                    if (hasMoreResults) {
                        const existingNextCard = document.getElementById('categoryNextCard');
                        if (!existingNextCard) {
                            const nextCardHtml = `
                                <div class="category-next-card" id="categoryNextCard">
                                    <span class="category-next-card-text">Load More</span>
                                </div>
                            `;
                            categoryPageGrid.insertAdjacentHTML('beforeend', nextCardHtml);
                            loadNextCardBackground();
                        }
                    } else if (append) {
                        const noMoreHtml = `
                            <div style="text-align: center; color: rgba(255,255,255,0.5); padding: 40px 20px; grid-column: 1 / -1; font-size: 1.1rem;">
                                No more results available
                            </div>
                        `;
                        categoryPageGrid.insertAdjacentHTML('beforeend', noMoreHtml);
                    }
                    
                    initializeLazyImages();
                    initializeClicks();
                    
                    if (!append && allResults.length > 0) {
                        const firstImage = allResults[0].image;
                        if (firstImage) {
                            const header = document.getElementById('categoryPageHeader');
                            const themeColorMeta = document.getElementById('themeColor');
                            if (header) {
                                header.style.backgroundImage = `url('${firstImage}')`;
                                
                                extractDominantColor(firstImage).then(color => {
                                    header.style.setProperty('--gradient-color-top', `rgba(${color.r}, ${color.g}, ${color.b}, 1)`);
                                    
                                    const hexColor = rgbToHex(color.r, color.g, color.b);
                                    headerOriginalThemeColor = hexColor;
                                    
                                    if (themeColorMeta) {
                                        themeColorMeta.setAttribute('content', hexColor);
                                    }
                                }).catch(() => {});
                            }
                        }
                    }
                } else {
                    if (!append) {
                        categoryPageGrid.innerHTML = '<p style="text-align: center; color: rgba(255,255,255,0.7); padding: 60px 20px;">No results found for this search.</p>';
                    } else {
                        const noMoreHtml = `
                            <div style="text-align: center; color: rgba(255,255,255,0.5); padding: 40px 20px; grid-column: 1 / -1; font-size: 1.1rem;">
                                No more results available
                            </div>
                        `;
                        categoryPageGrid.insertAdjacentHTML('beforeend', noMoreHtml);
                    }
                    hasMoreResults = false;
                }
            } catch (error) {
                console.error('Error loading search results:', error);
                if (!append) {
                    categoryPageGrid.innerHTML = '<p style="text-align: center; color: rgba(255,255,255,0.7); padding: 60px 20px;">Error loading search results. Please try again.</p>';
                }
            }
            
            isLoading = false;
        }
        
        loadSearchResults(1);
    });
    </script>
    <script src="script.js"></script>
</body>
</html>
