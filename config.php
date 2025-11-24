<?php

$ALL_SECTION_WEBSITES = [
    'TRENDING_NOW' => [
        'display_name' => 'Trending Now',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/',
            'parser_type' => 'li_thumb',
            'hidden' => false,
        ],
    ],
    'BOLLYWOOD' => [
        'display_name' => 'Bollywood',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/bollywood-movies/',
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
            'url' => 'https://hdhub4u.rehab/category/hollywood-movies/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'SOUTH_INDIAN' => [
        'display_name' => 'South Indian',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/south-hindi-movies/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'HINDI_DUBBED' => [
        'display_name' => 'Hindi Dubbed',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/hindi-dubbed/',
            'parser_type' => 'li_thumb',
        ],
    ],
];

$CATEGORIES_WEBSITES = [
    'ACTION' => [
        'display_name' => 'Action',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/action-movies/',
            'parser_type' => 'li_thumb',
            'hidden' => false,
        ],
    ],
    'ANIMATION' => [
        'display_name' => 'Animation',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/animated-movies/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'COMEDY' => [
        'display_name' => 'Comedy',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/comedy-movies/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'ROMANCE' => [
        'display_name' => 'Romance',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/romantic-movies/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'CRIME' => [
        'display_name' => 'Crime',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/crime/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'FANTASY' => [
        'display_name' => 'Fantasy',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/fantasy/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'HORROR' => [
        'display_name' => 'Horror',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/horror-movies/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'SCIFI' => [
        'display_name' => 'Sci-Fi',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/sci-fi/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'THRILLER' => [
        'display_name' => 'Thriller',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/thriller/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'DRAMA' => [
        'display_name' => 'Drama',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/drama/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'FAMILY' => [
        'display_name' => 'Family',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/family/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'ADVENTURE' => [
        'display_name' => 'Adventure',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/adventure/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'BIOGRAPHY' => [
        'display_name' => 'Biography',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/biography/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'WAR' => [
        'display_name' => 'War',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/war/',
            'parser_type' => 'li_thumb',
        ],
    ],
    'DOCUMENTARY' => [
        'display_name' => 'Documentary',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/category/documentary/',
            'parser_type' => 'li_thumb',
        ],
    ],
];

$LATEST_WEBSITES = [
    'LATEST' => [
        'display_name' => 'Latest',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.rehab/',
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
    'HDHub4u' => [
        'url' => 'https://hdhub4u.rehab/',
        'search_param' => 'q',
        'type' => 'api',
        'parser_type' => 'typesense',
        'api_url' => 'https://search.pingora.fyi/collections/post/documents/search',
        'referer' => 'https://hdhub4u.rehab/',
    ],
    'MyFlix BD' => [
        'url' => 'https://myflixbd.to/',
        'search_param' => 's',
        'type' => 'html',
        'parser_type' => 'default',
    ],
    'YTS' => [
        'url' => 'https://yts.lt/',
        'search_param' => 's',
        'type' => 'api',
        'parser_type' => 'api',
        'api_url' => 'https://yts.lt/api/v2/list_movies.json',
        'movie_base_url' => 'https://yts.lt/movies/',
    ],
    '4kHub4u' => [
        'url' => 'https://4khdhub.fans/',
        'search_param' => 's',
        'type' => 'html',
        'parser_type' => 'movie_card',
    ],
];

$HERO_CAROUSEL_WEBSITES = [
    'HDHub4u' => [
        'url' => 'https://hdhub4u.rehab/',
        'parser_type' => 'li_thumb',
        'hidden' => false,
    ],
];

$HERO_CAROUSEL_MANUAL_MOVIES = [
];

$SITE_SETTINGS = [
    'website_name' => 'FilmHaat',
    'logo_image' => 'attached_image/logo-image.webp',
    'background_image' => 'attached_image/background-image.webp',
];

?>
