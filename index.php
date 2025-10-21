<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta name="theme-color" content="#141414" id="themeColor">
    <title><?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?> - Movie & Series Search</title>
    <link rel="preload" as="image" href="<?php echo htmlspecialchars($SITE_SETTINGS['background_image']); ?>" type="image/webp">
    <link rel="preload" as="image" href="<?php echo htmlspecialchars($SITE_SETTINGS['logo_image']); ?>" type="image/webp">
    <link rel="stylesheet" href="styles.css">
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
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <img src="<?php echo htmlspecialchars($SITE_SETTINGS['logo_image']); ?>" alt="<?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?>" class="logo-image">
                <span class="logo-text"><?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?></span>
            </div>
            <div class="nav-search">
                <button type="button" id="navSearchBtn" class="nav-search-btn">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7" fill="none"/>
                        <path d="M21 21l-4.35-4.35" fill="none"/>
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
                        <div class="carousel-item skeleton-item">
                            <div class="skeleton-image" style="background: rgba(26, 26, 26);"></div>
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

    <div class="bottom-section">
        <section class="faq-section">
        <div class="faq-container">
            <h2 class="faq-title">Frequently Asked Questions</h2>
            
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-question">
                        <span>What is FilmHaat?</span>
                        <svg class="faq-icon" width="36" height="36" viewBox="0 0 36 36" fill="none">
                            <path d="M18 12V24M24 18H12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>FilmHaat is a comprehensive movie search aggregator that allows you to search for movies and series across multiple websites simultaneously. Instead of visiting different websites individually, you can search them all at once from a single convenient interface.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>Does FilmHaat host or store movies?</span>
                        <svg class="faq-icon" width="36" height="36" viewBox="0 0 36 36" fill="none">
                            <path d="M18 12V24M24 18H12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>No, FilmHaat does not host, store, or upload any movies or series. We are a search engine, similar to Google, that helps you find content across the internet. All search results link to third-party websites, and we have no control over the content hosted on those external sites.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>Is FilmHaat a streaming service?</span>
                        <svg class="faq-icon" width="36" height="36" viewBox="0 0 36 36" fill="none">
                            <path d="M18 12V24M24 18H12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>No, FilmHaat is not a streaming or hosting platform. We are a search aggregator that helps you discover where movies and series are available on the internet. We simply provide links to external websites where content may be found.</p>
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
                        <p>No, we do not provide, upload, or distribute any movies or series. FilmHaat only searches and indexes publicly available information on the internet, just like any other search engine. We are not responsible for the content on external websites that appear in search results.</p>
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
                        <p>Simply click on the search bar at the top of the page or in the main hero section. Type the name of the movie or series you're looking for, and FilmHaat will search across all configured websites to show you the results. You can also filter which websites to search using the filter button.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>Is FilmHaat free to use?</span>
                        <svg class="faq-icon" width="36" height="36" viewBox="0 0 36 36" fill="none">
                            <path d="M18 12V24M24 18H12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>Yes, FilmHaat is completely free to use. You can search for movies and series, browse categories, and discover trending movies without any registration or subscription fees.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
    <script src="script.js"></script>
</body>
</html>
