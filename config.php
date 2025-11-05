<?php

$ALL_SECTION_WEBSITES = [
    'TRENDING_NOW' => [
        'display_name' => 'Trending Now',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/',
            'parser_type' => 'li_thumb',
            'hidden' => false,
        ],
    ],
    'BOLLYWOOD' => [
        'display_name' => 'Bollywood',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/bollywood-movies/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'WEEKLY_TOP_10' => [
        'display_name' => 'Weekly Top 10',
        'Local' => [
            'url' => 'api-weekly-top10.php',
            'parser_type' => 'weekly_top10',
            'hidden' => false,
        ],
    ],
    'HOLLYWOOD' => [
        'display_name' => 'Hollywood',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/hollywood-movies/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'HINDI_DUBBED' => [
        'display_name' => 'Hindi',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/hindi-dubbed/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'TRAILER' => [
        'display_name' => 'Trailer',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/trailers/',
            'parser_type' => 'li_thumb',
        ],
    ],
];

$CATEGORIES_WEBSITES = [
    'ACTION' => [
        'display_name' => 'Action',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/action-movies/',
            'parser_type' => 'li_thumb',
            'hidden' => false,
        ],
    ],
    'ANIMATION' => [
        'display_name' => 'Animation',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/animated-movies/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'COMEDY' => [
        'display_name' => 'Comedy',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/comedy-movies/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'ROMANCE' => [
        'display_name' => 'Romance',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/romantic-movies/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'CRIME' => [
        'display_name' => 'Crime',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/crime/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'FANTASY' => [
        'display_name' => 'Fantasy',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/fantasy/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'HORROR' => [
        'display_name' => 'Horror',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/horror-movies/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'SCIFI' => [
        'display_name' => 'Sci-Fi',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/sci-fi/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'THRILLER' => [
        'display_name' => 'Thriller',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/thriller/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'DRAMA' => [
        'display_name' => 'Drama',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/drama/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'FAMILY' => [
        'display_name' => 'Family',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/family/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'ADVENTURE' => [
        'display_name' => 'Adventure',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/adventure/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'BIOGRAPHY' => [
        'display_name' => 'Biography',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/biography/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'WAR' => [
        'display_name' => 'War',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/war/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'DOCUMENTARY' => [
        'display_name' => 'Documentary',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/documentary/',
            'parser_type' => 'li_thumb',
        ],
    ],
];

$LATEST_WEBSITES = [
    'LATEST' => [
        'display_name' => 'Latest',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/',
            'parser_type' => 'li_thumb',
            'hidden' => false,
        ],
    ],
];

$SEARCH_WEBSITES = [
    'MoviesGod' => [
        'url' => 'https://a.moviesgod.live/',
        'search_param' => 's',
        'type' => 'html',
        'parser_type' => 'default',
        'hidden' => false,
    ],
    'MyFlix BD' => [
        'url' => 'https://myflixbd.to/',
        'search_param' => 's',
        'type' => 'html',
        'parser_type' => 'default',
    ],
    'YTS' => [
        'url' => 'https://yts.mx/',
        'search_param' => 's',
        'type' => 'api',
        'parser_type' => 'api',
        'api_url' => 'https://yts.mx/api/v2/list_movies.json',
        'movie_base_url' => 'https://yts.mx/movies/',
    ],
    'HDHub4u' => [
        'url' => 'https://hdhub4u.pictures/',
        'search_param' => 's',
        'type' => 'html',
        'parser_type' => 'li_thumb',
    ],
];

$HERO_CAROUSEL_WEBSITES = [
    'HDHub4u' => [
        'url' => 'https://hdhub4u.pictures/',
        'parser_type' => 'li_thumb',
        'hidden' => false,
    ],
];

$HERO_CAROUSEL_MANUAL_MOVIES = [
    0 => [
        'title' => 'Superman (2025) iMAX-WEB-DL Dual Audio {Hindi-English} 480p [460MB] | 720p [1.2GB] | 1080p [2.5GB] | 2160p [23GB] 4K-SDR',
        'link' => 'https://o.moviesgod.live/superman-2025-imax-web-dl-dual-audio-hindi-english-480p-460mb-720p-1-2gb-1080p-2-5gb-2160p-23gb-4k-sdr/',
        'image' => 'https://image.tmdb.org/t/p/w400/ombsmhYUqR4qqOLOxAyr5V8hbyv.jpg',
        'language' => 'Dual Audio',
        'genre' => '',
        'website' => 'MoviesGod',
        'hidden' => false,
    ],
    1 => [
        'title' => 'Borbaad (2025) Bengali Chorki WEB-DL Full Movie 480p [400MB] | 720p [1.1GB] | 1080p [2.2GB]',
        'link' => 'https://o.moviesgod.live/borbaad-2025-bengali-chorki-web-dl-full-movie-480p-400mb-720p-1-1gb-1080p-2-2gb/',
        'image' => 'https://iili.io/F2aav14.jpg',
        'language' => 'Bengali',
        'genre' => '',
        'website' => 'MoviesGod',
        'hidden' => false,
    ],
];

$SITE_SETTINGS = [
    'website_name' => 'FilmHaat',
    'logo_image' => 'attached_image/logo-image.webp',
    'background_image' => 'attached_image/background-image.webp',
];

$POPUP_CONFIG = [
    'enabled' => true,
    'image_path' => 'attached_image/popup/popup-image.webp',
    'target_url' => 'https://youtube.com/@sb-tunes-update?si=0mzIOnoJNHV49SVW',
    'show_delay' => 500,
    'countdown_duration' => 5,
    'hidden' => true,
];

?>
