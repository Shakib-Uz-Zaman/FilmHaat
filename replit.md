# FilmHaat - Multi-Site Movie Search Aggregator

## Overview
FilmHaat is a PHP-based web application that aggregates movie search results from multiple websites into a single, user-friendly interface. It aims to simplify movie discovery by providing a centralized platform inspired by streaming service UIs, featuring a fully dynamic section and category system configurable without code changes. The business vision is to become a primary platform for movie enthusiasts to find titles across various sources.

## User Preferences
- No changes to `config.php` unless explicitly requested.
- Prioritize UI/UX consistency, especially maintaining the dark theme, color palette, and layout.
- Focus on performance; new features should consider implications for data loading and rendering, utilizing lazy loading and efficient data handling.
- Detailed explanations for complex changes: If a proposed change involves significant architectural modifications or new design patterns, please provide a detailed explanation of the rationale and impact before implementation.
- Iterative development: I prefer a step-by-step approach for larger features, allowing for review and feedback at each stage.

## System Architecture
The application uses a vanilla PHP backend and vanilla JavaScript/CSS frontend.

### UI/UX Decisions
- **Design Style**: Dark theme with a red accent, black background, and white/light gray text.
- **Layout**: Features a prominent hero section with a large search bar, a fixed navigation bar with scroll effects, and a responsive grid for results.
- **Hero Carousel**: Full-width, auto-rotating carousel for trending movies with dynamic image effects, comprehensive navigation, and responsive aspect ratios, falling back to an animated vector illustration.
- **Search Placeholder**: Animated placeholder displaying rotating trending movie titles.
- **Loading Indicators**: Real-time skeleton animations for content loading.
- **"Next" Cards**: Pagination with "Next" cards featuring blurred random movie posters.
- **Search Popup Modal**: Modal with fade transitions and close support.
- **Dynamic Section Ordering**: Main page sections are dynamically ordered based on `config.php`.
- **Modal Metadata Display**: Movie modals display Language, Genre, and IMDb Rating with styled badges.
- **Weekly Top 10**: Dedicated section for most-viewed movies/series with large numbering.
- **Responsive Navigation System**: Fixed bottom navigation for mobile with gradient accents and blur; header navigation for desktop with hover effects.

### Technical Implementations
- **Backend**: Pure PHP 8.4, using cURL for HTTP requests.
- **Frontend**: Vanilla JavaScript for dynamic content; CSS for styling.
- **Configuration Management**: `config-manager.php` (web UI) and `config-api.php` (REST API) manage website sources and categories, saving to `config.php` without a database. Includes automatic backups and reordering.
- **Search Logic**: `search.php` for multi-site searches; `search-single.php` for individual site searches.
- **Dynamic Content System**: Automated category and section systems based on `config.php` entries, using `generic-section.php`.
- **Movie Deduplication**: `deduplicated-sections.php` ensures unique movies across sections.
- **Staged Loading Strategy**: Three-stage loading (Priority sequential, Secondary parallel, Category lazy-loaded) for optimized page load.
- **Weekly Top 10 Tracking**: Server-side view tracking using `data/weekly_views.json` and `api-weekly-top10.php` with file locking and 7-day cleanup.
- **Hero Illustration Fallback**: Animated vector illustration displays when carousel data is unavailable.
- **Universal Smart Navigation System**: `SessionStorage`-based navigation stack for app-like back/forward behavior.
- **Dynamic Favicon System**: Pages use the site logo as the favicon and Apple touch icon, updating automatically.
- **Icon Font Optimization**: Bottom navigation bar icons use optimized loading with DNS prefetch, preconnect, and font preload.
- **Critical Bottom Nav Loading**: Bottom navigation bar loads before page content using inline critical CSS and HTML for immediate rendering.
- **Progressive Web App (PWA)**: Full PWA support with `manifest.php` and Service Worker (`sw.js`) for installability and offline support.
- **Security**: XSS protection via HTML escaping, `urlencode()` for input sanitation, and SSL/TLS verification.
- **Popular Picks System**: Full CRUD management for movie series/franchises, with a dedicated display page (`collection.php`) and a main page carousel section.

## External Dependencies
The application integrates with various external movie websites for content, configured dynamically in `config.php` via arrays:
- **$SEARCH_WEBSITES**: Sources for movie searches.
- **$ALL_SECTION_WEBSITES**: Sources for main page content sections.
- **$CATEGORIES_WEBSITES**: Sources for category page content.
- **$LATEST_WEBSITES**: Sources for the "Latest" section.
- **$HERO_CAROUSEL_WEBSITES**: Sources for the hero carousel.
- **$MOVIE_COLLECTIONS_DATA**: Stores movie series/franchise collections data.