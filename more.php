<?php 
require_once 'config.php';

// Get category from URL parameter
$category = isset($_GET['category']) ? strtoupper($_GET['category']) : '';

// Validate category exists
$allCategories = array_merge($ALL_SECTION_WEBSITES, $CATEGORIES_WEBSITES);
if (empty($category) || !isset($allCategories[$category])) {
    header('Location: index.php');
    exit;
}

// Get category display name
$categoryData = $allCategories[$category];
$displayName = isset($categoryData['display_name']) ? $categoryData['display_name'] : ucfirst(strtolower($category));
$categoryLower = strtolower($category);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta name="theme-color" content="#000000" id="themeColor">
    <title><?php echo htmlspecialchars($displayName); ?> - <?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
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
        }
        
        .category-page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                linear-gradient(to bottom, var(--gradient-color-top, rgba(0,0,0,1)) 0%, transparent 60%),
                rgba(0, 0, 0, 0.3);
            z-index: 1;
        }
        
        .category-page-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 95%;
            background: linear-gradient(to top, rgba(0,0,0,1) 0%, transparent 100%);
            z-index: 2;
        }
        
        .category-header-nav {
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            z-index: 3;
        }
        
        .category-page-title {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin: 0;
            position: relative;
            z-index: 3;
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
            border-radius: 50%;
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
            border-radius: 50%;
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
            }
            
            .category-page-content {
                padding: 30px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="category-page-header" id="categoryPageHeader">
        <div class="category-header-nav">
            <a href="index.php" class="back-button">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                    <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                </svg>
            </a>
            <button class="header-menu-button">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>
            </button>
        </div>
        <h1 class="category-page-title"><?php echo htmlspecialchars($displayName); ?></h1>
        <p class="category-page-subtitle">Browse all <?php echo htmlspecialchars($displayName); ?> movies and series</p>
    </div>

    <div id="searchPopupModal" class="search-popup-modal" style="display: none;">
        <div class="search-popup-content">
            <div class="search-popup-header">
                <div class="search-popup-header-container">
                    <div class="logo">
                        <img src="<?php echo htmlspecialchars($SITE_SETTINGS['logo_image']); ?>" alt="<?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?>" class="logo-image">
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
                        <a id="modalLink" href="#" target="_blank" class="modal-view-btn">View</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const CATEGORY = '<?php echo $categoryLower; ?>';
        const CATEGORY_DISPLAY = '<?php echo htmlspecialchars($displayName); ?>';
        let currentPage = 1;
        let isLoading = false;
        let hasMoreMovies = true;
        let allMovies = [];

        // Movie modal functions
        const movieModal = document.getElementById('movieModal');
        const closeModalBtn = document.querySelector('.close-modal');
        const modalImage = document.getElementById('modalImage');
        const modalTitle = document.getElementById('modalTitle');
        const modalLink = document.getElementById('modalLink');
        const modalLanguage = document.getElementById('modalLanguage');
        const modalWebsite = document.getElementById('modalWebsite');
        
        function getWebsiteNameFromUrl(url) {
            try {
                const urlObj = new URL(url);
                const hostname = urlObj.hostname;
                
                const domainMap = {
                    'hdhub4u.pictures': 'HDHub4u',
                    'hdhub4u.cologne': 'HDHub4u',
                    'moviesgod.live': 'MoviesGod',
                    'a.moviesgod.live': 'MoviesGod',
                    'movietp.com': 'MovieTP',
                    'yts.mx': 'YTS'
                };
                
                return domainMap[hostname] || hostname.split('.')[0].toUpperCase();
            } catch (e) {
                return '';
            }
        }
        
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
            
            modalTitle.textContent = title;
            modalLink.href = link;
            modalLanguage.textContent = language || '';
            modalWebsite.textContent = getWebsiteNameFromUrl(link);
            
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
        
        // Load next card background
        function loadNextCardBackground() {
            const nextCard = document.getElementById('categoryNextCard');
            if (nextCard && allMovies.length > 0) {
                const randomMovie = allMovies[Math.floor(Math.random() * allMovies.length)];
                if (randomMovie && randomMovie.image) {
                    nextCard.style.backgroundImage = `url('${randomMovie.image}')`;
                }
            }
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
                const response = await fetch(`category-data.php?category=${CATEGORY}&page=${page}`);
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
                            const websiteTag = movie.website ? `<span class="category-genre-tag category-website-tag">${escapeHtml(movie.website.toUpperCase())}</span>` : '';
                            
                            const itemHtml = `
                                <div class="category-grid-item new-item" 
                                     data-title="${escapeHtml(movie.title)}" 
                                     data-link="${escapeHtml(movie.link)}" 
                                     data-image="${escapeHtml(movie.image || '')}" 
                                     data-language="${escapeHtml(movie.language || '')}"
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
                                </div>
                            `;
                            categoryPageGrid.insertAdjacentHTML('beforeend', nextCardHtml);
                            loadNextCardBackground();
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
                        </div>
                    `;
                    categoryPageGrid.insertAdjacentHTML('beforeend', nextCardHtml);
                    loadNextCardBackground();
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
                const websiteTag = movie.website ? `<span class="category-genre-tag category-website-tag">${escapeHtml(movie.website.toUpperCase())}</span>` : '';
                
                html += `
                    <div class="category-grid-item" 
                         data-title="${escapeHtml(movie.title)}" 
                         data-link="${escapeHtml(movie.link)}" 
                         data-image="${escapeHtml(movie.image || '')}" 
                         data-language="${escapeHtml(movie.language || '')}"
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
            const movieItems = document.querySelectorAll('.category-grid-item:not(#categoryNextCard)');
            movieItems.forEach(item => {
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
                            themeColorMeta.setAttribute('content', hexColor);
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
                    themeColorMeta.setAttribute('content', '#000000');
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
    <script src="script.js"></script>
</body>
</html>
