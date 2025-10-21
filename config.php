<?php

$ALL_SECTION_WEBSITES = [
    'TRENDING_NOW' => [
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/',
            'parser_type' => 'li_thumb',
        ],
        'display_name' => 'Trending Now',
    ],
    'WEEKLY_TOP_10' => [
        'display_name' => 'Weekly Top 10',
        'Local' => [
            'url' => 'api-weekly-top10.php',
            'parser_type' => 'weekly_top10',
            'hidden' => false,
        ],
    ],
    'BOLLYWOOD' => [
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/bollywood-movies/',
            'parser_type' => 'li_thumb',
        ],
        'display_name' => 'Bollywood',
    ],
    'HOLLYWOOD' => [
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/hollywood-movies/',
            'parser_type' => 'li_thumb',
        ],
        'display_name' => 'Hollywood',
    ],
];

$CATEGORIES_WEBSITES = [
    'ACTION' => [
        'display_name' => 'Action',
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/action-movies/',
            'parser_type' => 'li_thumb',
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
        'HDHub4u' => [
            'url' => 'https://hdhub4u.pictures/category/documentary/',
            'parser_type' => 'li_thumb',
        ],
    ],
];

$SEARCH_WEBSITES = [
    'MoviesGod' => [
        'url' => 'https://a.moviesgod.live/',
        'search_param' => 's',
        'type' => 'html',
        'parser_type' => 'default',
    ],
    'MovieTP' => [
        'url' => 'https://movietp.com/',
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
    ],
];

$SITE_SETTINGS = [
    'website_name' => 'FilmHaat',
    'logo_image' => 'attached_image/logo-image.webp',
    'background_image' => 'attached_image/background-image.webp',
];

?>
