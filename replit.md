# FilmHaat - Multi-Site Movie Search Aggregator

## Overview
FilmHaat is a PHP-based web application designed to aggregate movie search results from multiple websites into a single, user-friendly interface. Its primary purpose is to simplify movie discovery by allowing users to search across various sources simultaneously. The project aims to provide a centralized platform for finding movies, inspired by streaming service UIs, with a fully dynamic section and category system that can be configured without touching code. The business vision is to become a go-to platform for movie enthusiasts seeking a consolidated view of available titles across multiple sources.

## Recent Changes
- **2025-11-30**: Implemented search results pagination with infinite scroll:
  - Backend: Added `page` parameter support to `search-single.php` for all search types (HTML scraping, Typesense API, generic API)
  - Backend: Returns `hasMore` flag and `page` number to indicate if more results are available
  - Frontend: Search results now show first 10 results with a "Next" card at the end (styled like trending-next-card)
  - Frontend: Clicking Next loads the next page of results with smooth sliding animation and gradient border animation on new items
  - Frontend: Skeleton loading animation displayed while fetching more results
  - This allows users to browse all available search results instead of being limited to 10
- **2025-11-09**: Updated horizontal card layout gradient background for improved visual consistency:
  - Changed `.category-grid-item` background gradient from `rgba(26,26,26,0)` to `#101010` → now `transparent` to `#1a1a1a`
  - Applied same gradient to both normal and hover states for consistent appearance
  - Horizontal cards now feature a subtle top-to-bottom gradient (transparent → #1a1a1a) that enhances visual depth
- **2025-11-09**: Eliminated Google Fonts to achieve instant font rendering and save 1.8+ seconds on page load:
  - Replaced Inter font with native system font stack (-apple-system, BlinkMacSystemFont, Segoe UI, Roboto, etc.)
  - Removed all Google Fonts preconnect links and JavaScript loading from all pages (index, latest, loved, more, about)
  - Zero network requests for fonts, improving privacy and performance
  - Expected impact: Instant font rendering with zero latency, professional native look across all devices
- **2025-11-09**: Backend and frontend performance optimization to eliminate slow-loading resources:
  - Added server-side file caching to category-posters.php with 10-minute TTL, caching only successful responses to prevent empty poster data persistence
  - Reduced curl timeouts from 20s→8s (CURLOPT_TIMEOUT) and 5s→3s (CURLOPT_CONNECTTIMEOUT) to fail faster on slow external sources
  - Added `defer` attribute to script.js across all pages (index, latest, loved, more, about) to prevent render-blocking
  - Cache directory (`/cache/`) automatically created with proper permissions, already ignored in .gitignore
  - Expected impact: category-posters.php drops from 4+ seconds to instant on subsequent loads; script.js no longer blocks page rendering
- **2025-11-09**: Major network performance optimization to reduce critical path latency and improve page load speed:
  - Added preconnect and DNS prefetch hints for external image domains (catimages.org, image.tmdb.org) to establish early connections and reduce latency
  - Implemented HTTP caching headers across all API endpoints:
    - hero-carousel.php: 5-minute cache with 10-minute stale-while-revalidate
    - category-posters.php: 10-minute cache with 20-minute stale-while-revalidate
    - deduplicated-sections.php: 5-minute cache with 10-minute stale-while-revalidate
    - image-proxy.php: 24-hour cache with 48-hour stale-while-revalidate
  - Optimized JavaScript API calls for parallel execution using Promise.all() instead of sequential await
  - Enhanced category-posters loading to use cache-aware fetch instead of regular fetch
  - All optimizations work together to significantly reduce the 3,294ms critical path latency shown in performance audits
- **2025-11-09**: Eliminated render-blocking resources to improve page load performance by approximately 1.8 seconds. All pages (index.php, latest.php, loved.php, about.php, more.php) now use async loading for Google Fonts (via JavaScript with font-display swap) and styles.css (via preload with onload promotion). Critical CSS remains inline for immediate rendering. Includes noscript fallback for CSS to maintain compatibility with JavaScript-disabled browsers.
  - **Update**: Reverted styles.css to normal synchronous loading to prevent FOUC (Flash of Unstyled Content). Google Fonts remain async-loaded via JavaScript for optimal performance. This approach eliminates ~750ms of font render-blocking while maintaining proper page styling on initial load.
- **2025-11-08**: Improved accessibility and SEO by changing category links. Links now display "See All" visually (keeping UI clean since section name is already visible), while using `aria-label="See All [Category Name]"` for screen readers and search engines. This resolves content best practices warnings about links lacking descriptive text while maintaining a clean visual design.

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
- **Hero Carousel**: Full-width, auto-rotating carousel for trending movies with dynamic image-based shadow effects, touch/swipe/keyboard navigation, and responsive aspect ratios. Falls back to an animated vector illustration when carousel data is unavailable or hidden.
- **Search Placeholder**: Animated search placeholder displaying rotating trending movie titles.
- **Loading Indicators**: Real-time skeleton animations for improved user experience.
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
- **Dynamic Section Display Names**: Configurable via `config.php` with an "Edit Name" button in the config manager UI.
- **Search Logic**: `search.php` for multi-site searches; `search-single.php` for individual site searches.
- **Dynamic Category System**: Fully automated system where adding new categories only requires editing `config.php`; UI, buttons, sections, and API endpoints are dynamically generated.
- **Latest Section**: Dedicated "Latest" section with its own configuration (`$LATEST_WEBSITES`), mirroring `more.php` functionality, managed via `config-manager.php`, and protected as a built-in feature.
- **Content Sections**: Fully dynamic system using a single `generic-section.php` file for all content sections defined in `$ALL_SECTION_WEBSITES`.
- **Movie Deduplication**: Centralized `deduplicated-sections.php` ensures unique movies across sections.
- **Staged Loading Strategy**: Three-stage loading process (Priority sequential, Secondary parallel, Category lazy-loaded) for optimized page load.
- **Weekly Top 10 Tracking**: Server-side view tracking using `data/weekly_views.json` and `api-weekly-top10.php` with file locking and 7-day data cleanup.
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