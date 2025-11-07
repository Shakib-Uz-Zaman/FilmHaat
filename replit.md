# FilmHaat - Multi-Site Movie Search Aggregator

## Overview
FilmHaat is a PHP-based web application designed to aggregate movie search results from multiple websites into a single, user-friendly interface. Its primary purpose is to simplify movie discovery by allowing users to search across various sources simultaneously. The project aims to provide a centralized platform for finding movies, inspired by streaming service UIs, with a fully dynamic section and category system that can be configured without touching code. The business vision is to become a go-to platform for movie enthusiasts seeking a consolidated view of available titles across multiple sources.

## User Preferences
I want to emphasize a few key preferences for how the agent should interact with this project:
- No changes to `config.php` unless explicitly requested: This file is central to the application's external integrations, and I want to maintain strict control over its contents.
- Prioritize UI/UX consistency: When implementing new features or making modifications, ensure the Netflix-inspired dark theme, color palette, and layout are maintained.
- Focus on performance: Any new features or modifications should consider performance implications, especially regarding data loading and rendering. Lazy loading and efficient data handling are crucial.
- Detailed explanations for complex changes: If a proposed change involves significant architectural modifications or new design patterns, please provide a detailed explanation of the rationale and impact before implementation.
- Iterative development: I prefer a step-by-step approach for larger features, allowing for review and feedback at each stage.

## System Architecture
The application uses a vanilla PHP backend and vanilla JavaScript/CSS frontend.

### UI/UX Decisions
- **Design Style**: Netflix-inspired dark theme with a primary Netflix Red accent, Background Black, and White & Light Gray text.
- **Layout**: Prominent hero section with a large search bar, a fixed navigation bar with scroll effects, and a responsive grid for results.
- **Hero Carousel**: Full-width, auto-rotating carousel for trending movies with dynamic image-based shadow effects, touch/swipe/keyboard navigation, and responsive aspect ratios. Falls back to an animated vector illustration when carousel data is unavailable or hidden.
- **Search Placeholder**: Animated search placeholder displaying rotating trending movie titles.
- **Loading Indicators**: Real-time skeleton animations for improved user experience.
- **"Next" Cards**: Pagination with "Next" cards displaying blurred random movie posters.
- **Search Popup Modal**: Modal for search functionality with fade transitions and keyboard/click-outside-to-close support.
- **Dynamic Section Ordering**: Sections on the main page are dynamically ordered based on `config.php` configurations.
- **Modal Metadata Display**: Movie modals display Language, Genre, and IMDb Rating above the title with styled badges.
- **Weekly Top 10**: Dedicated section for most-viewed movies/series with Netflix-style large numbering, using localStorage for client-side tracking.
- **Responsive Navigation System**: Fixed bottom navigation bar for mobile with gradient accents and blur; header navigation for desktop with hover effects and active states.
- **Promotional Popup System**: Page-load promotional popup displaying a clickable image (WebP format) with a gradient-styled close button, managed via `config-manager.php`.

### Technical Implementations
- **Backend**: Pure PHP 8.4, utilizing cURL for HTTP requests.
- **Frontend**: Vanilla JavaScript for dynamic content and UI; CSS for styling.
- **Configuration Management**: `config-manager.php` web UI and `config-api.php` REST API manage website sources and categories, saving changes to `config.php` without a database. Includes automatic backups and reordering functionalities.
- **Dynamic Section Display Names**: Configurable via `config.php` with an "Edit Name" button in the config manager UI.
- **Search Logic**: `search.php` for multi-site searches; `search-single.php` for individual site searches.
- **Dynamic Category System**: Fully automated system where adding new categories only requires editing `config.php`; UI, buttons, sections, and API endpoints are dynamically generated.
- **Latest Section**: Dedicated "Latest" section with its own configuration (`$LATEST_WEBSITES`), mirroring `more.php` functionality, managed via `config-manager.php`, and protected as a built-in feature.
- **Content Sections**: Fully dynamic system using a single `generic-section.php` file for all content sections defined in `$ALL_SECTION_WEBSITES`.
- **Movie Deduplication**: Centralized `deduplicated-sections.php` ensures unique movies across sections.
- **Staged Loading Strategy**: Three-stage loading process (Priority sequential, Secondary parallel, Category lazy-loaded) for optimized page load.
- **Weekly Top 10 Tracking**: Server-side view tracking using `data/weekly_views.json` and `api-weekly-top10.php` with file locking and 7-day data cleanup.
- **Promotional Popup System**: Configured via `$POPUP_CONFIG` in `config.php`, fetched by `popup-manager.js`, with image validation (WebP format only) and session-based tracking.
- **Hero Illustration Fallback**: When hero carousel data is unavailable or all sources are hidden, displays an animated movie-themed vector illustration with welcome text, maintaining visual consistency and preventing blank hero sections.
- **Universal Smart Navigation System**: Comprehensive SessionStorage-based navigation stack system that mimics browser back/forward behavior across all navigation elements throughout the application. When users click on previously-visited pages via any navigation link (desktop header nav, mobile bottom nav, or "More" buttons), the system uses `history.go()` to restore pages from browser cache (bfcache) instead of reloading. This preserves scroll positions, loaded content, and complete page state. The navigation stack tracks all visited pages and intelligently routes between them, providing instant page transitions and a native app-like experience across the entire site.
- **Dynamic Favicon System**: All pages dynamically use the site logo as the browser favicon and Apple touch icon. The favicon automatically updates when the logo is changed through the config manager, ensuring brand consistency across all pages (index.php, about.php, latest.php, loved.php, more.php, config-manager.php, and login.php).
- **Icon Font Optimization**: Bottom navigation bar icons use optimized loading with DNS prefetch, preconnect, and font preload directives for instant rendering. All pages (index.php, latest.php, loved.php, about.php) preload the critical .woff2 font files (uicons-solid-rounded, uicons-regular-rounded, uicons-bold-rounded) with proper crossorigin attributes, ensuring navigation icons appear instantly without any loading delay.
- **Critical Bottom Nav Loading**: Bottom navigation bar loads before page content using inline critical CSS in `<head>` and HTML placement at the start of `<body>`. The SVG gradient definition is also in `<head>` for instant rendering. This ensures the navigation bar appears immediately on page load, providing instant UI feedback to users on mobile devices.
- **Progressive Web App (PWA)**: Full PWA support with `manifest.json` and Service Worker (`sw.js`). Features include installability on mobile/desktop devices, offline support with intelligent caching strategy (network-first for dynamic PHP pages, cache-first for static assets), and app-like experience. Icons stored in `/icons/` directory with multiple sizes (48x48, 72x72, 96x96, 144x144, 192x192, 512x512). Service worker automatically caches static resources while ensuring dynamic movie data always stays fresh from the network.
- **Security**: XSS protection via HTML escaping, `urlencode()` for input sanitation, and SSL/TLS verification.
- **File Structure**: Organized with core files, a `categories/` folder, and an `icons/` folder for PWA app icons.

### Feature Specifications
- Simultaneous search across multiple movie websites.
- Dynamic trending movie display in hero carousel and animated search placeholder.
- Comprehensive category filtering with lazy loading.
- Pagination for movie carousels with "Next" cards.
- Responsive UI for desktop and mobile.
- Error handling for failed website connections.

## External Dependencies
The application integrates with various external movie websites for content scraping, dynamically configured in `config.php` via arrays:
- **$SEARCH_WEBSITES**: Search sources.
- **$ALL_SECTION_WEBSITES**: Main page section content sources.
- **$CATEGORIES_WEBSITES**: Category page content sources.
- **$LATEST_WEBSITES**: Latest section page content sources.
- **$HERO_CAROUSEL_WEBSITES**: Hero carousel content sources.

All sources are managed via the config manager UI or by editing `config.php` directly.