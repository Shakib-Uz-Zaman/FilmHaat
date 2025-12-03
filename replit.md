# FilmHaat - Multi-Site Movie Search Aggregator

## Overview
FilmHaat is a PHP-based web application designed to aggregate movie search results from multiple websites into a single, user-friendly interface. Its primary purpose is to simplify movie discovery by allowing users to search across various sources simultaneously. The project aims to provide a centralized platform for finding movies, inspired by streaming service UIs, with a fully dynamic section and category system that can be configured without touching code. The business vision is to become a go-to platform for movie enthusiasts seeking a consolidated view of available titles across multiple sources.

## User Preferences
I want to emphasize a few key preferences for how the agent should interact with this project:
- No changes to `config.php` unless explicitly requested: This file is central to the application's external integrations, and I want to maintain strict control over its contents.
- Prioritize UI/UX consistency: When implementing new features or making modifications, ensure the dark theme, color palette, and layout are maintained.
- Focus on performance: Any new features or modifications should consider performance implications, especially regarding data loading and rendering. Lazy loading and efficient data handling are crucial.
- Detailed explanations for complex changes: If a proposed change involves significant architectural modifications or new design patterns, please provide a detailed explanation of the rationale and impact before implementation.
- Iterative development: I prefer a step-by-step approach for larger features, allowing for review and feedback at each stage.

## System Architecture
The application uses a vanilla PHP backend and vanilla JavaScript/CSS frontend.

### UI/UX Decisions
- **Design Style**: Dark theme with a primary Red accent, Background Black, and White & Light Gray text.
- **Layout**: Prominent hero section with a large search bar, a fixed navigation bar with scroll effects, and a responsive grid for results.
- **Hero Carousel**: Full-width, auto-rotating carousel for trending movies with dynamic image-based shadow effects, touch/swipe/keyboard navigation, and responsive aspect ratios. Falls back to an animated vector illustration.
- **Search Placeholder**: Animated search placeholder displaying rotating trending movie titles.
- **Loading Indicators**: Real-time skeleton animations.
- **"Next" Cards**: Pagination with "Next" cards displaying blurred random movie posters.
- **Search Popup Modal**: Modal for search functionality with fade transitions and keyboard/click-outside-to-close support.
- **Dynamic Section Ordering**: Sections on the main page are dynamically ordered based on `config.php` configurations.
- **Modal Metadata Display**: Movie modals display Language, Genre, and IMDb Rating above the title with styled badges.
- **Weekly Top 10**: Dedicated section for most-viewed movies/series with large numbering, using localStorage for client-side tracking.
- **Responsive Navigation System**: Fixed bottom navigation bar for mobile with gradient accents and blur; header navigation for desktop with hover effects and active states.

### Technical Implementations
- **Backend**: Pure PHP 8.4, utilizing cURL for HTTP requests.
- **Frontend**: Vanilla JavaScript for dynamic content and UI; CSS for styling.
- **Configuration Management**: `config-manager.php` web UI and `config-api.php` REST API manage website sources and categories, saving changes to `config.php` without a database. Includes automatic backups and reordering functionalities.
- **Search Logic**: `search.php` for multi-site searches; `search-single.php` for individual site searches.
- **Dynamic Content System**: Fully automated category and section systems where content (sections, categories, latest, hero carousel) is dynamically generated based on `config.php` entries. Uses `generic-section.php` for all content sections.
- **Movie Deduplication**: Centralized `deduplicated-sections.php` ensures unique movies across sections.
- **Staged Loading Strategy**: Three-stage loading process (Priority sequential, Secondary parallel, Category lazy-loaded) for optimized page load.
- **Weekly Top 10 Tracking**: Server-side view tracking using `data/weekly_views.json` and `api-weekly-top10.php` with file locking and 7-day data cleanup.
- **Hero Illustration Fallback**: Displays an animated movie-themed vector illustration when hero carousel data is unavailable.
- **Universal Smart Navigation System**: SessionStorage-based navigation stack mimics browser back/forward behavior, restoring page states using `history.go()` for instant transitions and a native app-like experience.
- **Dynamic Favicon System**: All pages dynamically use the site logo as the browser favicon and Apple touch icon, updating automatically when the logo is changed.
- **Icon Font Optimization**: Bottom navigation bar icons use optimized loading with DNS prefetch, preconnect, and font preload directives for instant rendering.
- **Critical Bottom Nav Loading**: Bottom navigation bar loads before page content using inline critical CSS and HTML placement at the start of `<body>` for immediate rendering.
- **Progressive Web App (PWA)**: Full PWA support with `manifest.json` and Service Worker (`sw.js`) for installability, offline support (network-first for dynamic PHP, cache-first for static assets), and app-like experience.
- **Security**: XSS protection via HTML escaping, `urlencode()` for input sanitation, and SSL/TLS verification.

### External Dependencies
The application integrates with various external movie websites for content scraping, dynamically configured in `config.php` via arrays:
- **$SEARCH_WEBSITES**: Search sources.
- **$ALL_SECTION_WEBSITES**: Main page section content sources.
- **$CATEGORIES_WEBSITES**: Category page content sources.
- **$LATEST_WEBSITES**: Latest section page content sources.
- **$HERO_CAROUSEL_WEBSITES**: Hero carousel content sources.