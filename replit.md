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
- **Design Style**: Netflix-inspired dark theme with a primary Netflix Red (#E50914) accent, Background Black (#141414), and White & Light Gray text.
- **Layout**: Features a prominent hero section with a large search bar, a fixed navigation bar with scroll effects, and a responsive grid for displaying results.
- **Hero Carousel**: A full-width, auto-rotating carousel for 10 trending movies with dynamic image-based shadow effects (dominant color extraction), touch/swipe/keyboard navigation, responsive aspect ratios (16:9 desktop, 1:1 mobile portrait), and navigation arrows (visible on desktop hover, tablet/landscape devices with always-visible semi-transparent design for touch devices).
- **Search Placeholder**: An animated search placeholder displaying rotating trending movie titles.
- **Loading Indicators**: Real-time skeleton animations for improved user experience during data fetching.
- **"Next" Cards**: Pagination with "Next" cards displaying blurred random movie posters to prompt loading more content.
- **Search Popup Modal**: A modal for search functionality, preserving the hero section's design, with fade transitions and keyboard/click-outside-to-close support.
- **Dynamic Section Ordering**: Sections on the main page are dynamically ordered based on configurations in `config.php`, affecting both API response and HTML rendering.
- **Modal Metadata Display**: Movie modals display Language, Genre, and IMDb Rating above the title with styled badges.
- **Weekly Top 10**: A dedicated section displaying the most-viewed movies/series from the last 7 days with Netflix-style large numbering (1, 2, 3...) on each card. Uses localStorage for client-side tracking without database dependency.

### Technical Implementations
- **Backend**: Pure PHP 8.4, utilizing cURL for HTTP requests.
- **Frontend**: Vanilla JavaScript for dynamic content, search, and UI interactions; CSS for styling and responsiveness.
- **Configuration Management**: A `config-manager.php` web UI and `config-api.php` REST API for managing website sources and categories, saving changes directly to `config.php` without a database. Includes automatic backups, safe PHP array serialization, and move up/down functionality for reordering sections, categories, and search websites.
- **Dynamic Section Display Names**: Section display names are fully configurable via `config.php` with a `display_name` field for each section. The config manager UI provides an "Edit Name" button for each section, allowing users to customize display names without touching code. Both `index.php` and `deduplicated-sections.php` dynamically read these names with automatic fallback to default values for backward compatibility.
- **Search Logic**: `search.php` handles multi-site searches; `search-single.php` for individual site searches.
- **Dynamic Category System**: A fully automated category system where adding a new category requires ONLY editing `config.php` - all UI, buttons, sections, and API endpoints are generated dynamically. The generic handler (`categories/index.php`) processes any category via URL parameters, while `index.php` and `script.js` build the UI from `$CATEGORIES_WEBSITES` config array. The `category-posters.php` endpoint also dynamically serves posters for all categories. This refactor reduced `script.js` by 3,422 lines (45%) by eliminating hardcoded category functions. Each category in config includes a `display_name` field for custom labeling.
- **Content Sections**: Fully dynamic section system using a single `generic-section.php` file that handles ALL content sections defined in `$ALL_SECTION_WEBSITES` config array. All sections are configured dynamically via `config.php` without hardcoding. Previously separate section-specific PHP files have been eliminated to reduce code duplication. The `trending.php` endpoint specifically serves the hero carousel using the same generic fetcher with data from `$HERO_CAROUSEL_WEBSITES`.
- **Movie Deduplication**: A centralized `deduplicated-sections.php` endpoint ensures unique movies across sections, prioritizing sections based on a defined order and fetching additional content to maintain counts. All sections now use `generic-section.php` with legacy key mapping to maintain frontend compatibility.
- **Staged Loading Strategy**: A three-stage loading process (Priority sections sequential, Secondary sections parallel, Category sections lazy-loaded via IntersectionObserver) optimizes initial page load.
- **Weekly Top 10 Tracking**: Server-side view tracking system using a JSON file (`data/weekly_views.json`) that aggregates all users' movie/series views. The `api-weekly-top10.php` API endpoint tracks views via POST requests and returns the top 10 most-viewed content via GET requests. Implements file locking (LOCK_EX) to prevent race conditions, validates required fields, and automatically cleans views older than 7 days. Displays results with Netflix-style large numbering overlay (1-10) on movie cards based on all users' combined viewing data.
- **Security**: XSS protection via HTML escaping, `urlencode()` for input sanitation, and SSL/TLS certificate verification for external requests.
- **File Structure**: Organized with core files (`index.php`, `search.php`, `script.js`, `styles.css`) and a `categories/` folder.

### Feature Specifications
- Simultaneous search across multiple movie websites.
- Dynamic trending movie display in a hero carousel and animated search placeholder.
- Comprehensive category filtering with lazy loading.
- Pagination for movie carousels with "Next" cards.
- Responsive UI for desktop and mobile.
- Error handling for failed website connections.

## External Dependencies
The application integrates with various external movie websites for content scraping:

All external website integrations are dynamically configured in `config.php` through three main configuration arrays:
- **$SEARCH_WEBSITES**: Search sources for the search functionality
- **$ALL_SECTION_WEBSITES**: Content sources for main page sections (fully dynamic)
- **$CATEGORIES_WEBSITES**: Content sources for category pages (fully dynamic)
- **$HERO_CAROUSEL_WEBSITES**: Content sources for the hero carousel

No hardcoded website lists exist in the codebase. All sources can be added, modified, or removed via the config manager UI or by editing `config.php` directly.