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
    'MOVIE_COLLECTIONS' => [
        'display_name' => 'Popular Picks',
        'Local' => [
            'url' => 'movie_collections',
            'parser_type' => 'movie_collections',
            'hidden' => false,
        ],
    ],
    'SEARCH_LINKS' => [
        'display_name' => 'All Movie Sites',
        'Dynamic' => [
            'url' => 'search_links',
            'parser_type' => 'search_links',
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
    'HDHub4u' => [
        'url' => 'https://hdhub4u.rehab/',
        'search_param' => 'q',
        'type' => 'api',
        'parser_type' => 'typesense',
        'api_url' => 'https://search.pingora.fyi/collections/post/documents/search',
        'referer' => 'https://hdhub4u.rehab/',
    ],
    'MoviesGod' => [
        'url' => 'https://moviesgod1.site/',
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

$MOVIE_COLLECTIONS_DATA = [
    'STRENGER_THINGS' => [
        'display_name' => 'Stranger Things',
        'cover_image' => '',
        'hidden' => false,
        'movies' => [
            0 => [
                'title' => 'Stranger Things (Season 5) WEB-DL [Hindi (DD5.1) & English] 4K 1080p 720p & 480p [x264/10Bit-HEVC] | NF Series | [VOL-1 Added]',
                'link' => 'https://hdhub4u.rehab/stranger-things-season-5-hindi-webrip-all-episodes/',
                'image' => 'https://image.tmdb.org/t/p/w500/AaLrOh33YLkK1WLEB8Uml7FL8fm.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            1 => [
                'title' => 'Stranger Things (S04-VOL 1 & 2) WEB-DL [Hindi DD5.1 & English] 1080p 720p 480p & 10Bit HEVC [ALL Episodes] | NF Series',
                'link' => 'https://hdhub4u.rehab/stranger-things-s04-hindi-webrip-all-episodes/',
                'image' => 'https://myimg.click/images/2022/05/27/Stranger-Things-S04-Hindi-HDRip-ALL-Episodes.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            2 => [
                'title' => 'Stranger Things (Season 3) WEB-DL [Hindi DD5.1 & English] 1080p 720p 480p & 10Bit HEVC [ALL Episodes] | NF Series',
                'link' => 'https://hdhub4u.rehab/stranger-things-season-3-hindi-webrip-all-episodes/',
                'image' => 'https://image.tmdb.org/t/p/w400/sDms9g40ZBhjMIfX9YqqaqId8sK.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            3 => [
                'title' => 'Stranger Things (Season 2) BluRay [Hindi (DD5.1) & English] 1080p 720p & 480p [x264/10Bit-HEVC] | NF Series',
                'link' => 'https://hdhub4u.rehab/stranger-things-season-2-hindi-webrip-all-episodes/',
                'image' => 'https://image.tmdb.org/t/p/w400/lXS60geme1LlEob5Wgvj3KilClA.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            4 => [
                'title' => 'Stranger Things (Season 1) BluRay [Hindi (DD5.1) & English] 1080p 720p & 480p [x264/10Bit-HEVC] | NF Series',
                'link' => 'https://hdhub4u.rehab/stranger-things-season-1-hindi-webrip-all-episodes/',
                'image' => 'https://image.tmdb.org/t/p/w400/qFR9az0RsVl93ESVleyl3O92vL.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
        ],
    ],
    'MONEY_HEIST' => [
        'display_name' => 'Money Heist',
        'cover_image' => '',
        'hidden' => false,
        'movies' => [
            0 => [
                'title' => 'Money Heist (Season 2) WEB-DL Dual Audio [Hindi DD5.1 & English] 1080p 720p 480p x264/10Bit HEVC [ALL Episodes] | NF Series',
                'link' => 'https://hdhub4u.rehab/money-heist-season-2-hindi-webrip-all-episodes/',
                'image' => 'https://myimg.click/images/2021/02/19/D_xzF1yXUAEGC2o.jpg',
                'language' => 'Dual Audio',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            1 => [
                'title' => 'Money Heist (Season 3) WEB-DL Dual Audio [Hindi DD5.1 & English] 1080p 720p 480p x264/10Bit HEVC [ALL Episodes] | NF Series',
                'link' => 'https://hdhub4u.rehab/money-heist-season-3-hindi-webrip-all-episodes/',
                'image' => 'https://myimg.click/images/2021/02/19/photo_2021-02-19_14-42-00.jpg',
                'language' => 'Dual Audio',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            2 => [
                'title' => 'Money Heist (Season 4) WEB-DL Dual Audio [Hindi DD5.1 & English] 1080p 720p 480p x264/10Bit HEVC [ALL Episodes] | NF Series',
                'link' => 'https://hdhub4u.rehab/money-heist-season-4-hindi-webrip-all-episodes/',
                'image' => 'https://myimg.click/images/2021/02/19/MV5BZDcxOGI0MDYtNTc5NS00NDUzLWFkOTItNDIxZjI0OTllNTljXkEyXkFqcGdeQXVyMTMxODk2OTU._V1_.jpg',
                'language' => 'Dual Audio',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            3 => [
                'title' => 'Money Heist (Season 5-VOL: 1) WEB-DL [Hindi DD5.1 & English] 1080p 720p 480p Dual Audio x264/10Bit HEVC |  [ALL Episodes]',
                'link' => 'https://hdhub4u.rehab/money-heist-season-5-webrip-all-episodes/',
                'image' => 'https://imagetot.com/images/2021/09/02/0a5189f6f1a33c2e5a9cf9b363387126.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            4 => [
                'title' => 'Money Heist (Season 5-VOL: 2) WEB-DL [Hindi DD5.1 & English] 1080p 720p 480p Dual Audio [x264/10Bit-HEVC] | NetFlix [ALL Episodes]',
                'link' => 'https://hdhub4u.rehab/money-heist-season-5-vol-2-hindi-webrip-all-episodes/',
                'image' => 'https://imagetot.com/images/2021/12/03/4a8a762bed99bcce2bbca3bd3ddbdb0e.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            5 => [
                'title' => 'Money Heist: Berlin (Season 1) WEB-DL [Hindi (ORG 5.1) & English 5.1] 1080p 720p & 480p [x264/10Bit-HEVC] | [ALL Episodes] | NF Series',
                'link' => 'https://hdhub4u.rehab/money-heist-berlin-season-1-hindi-webrip-all-episodes/',
                'image' => 'https://image.tmdb.org/t/p/w500/qggpZOGHps82F80lXPxtvtf9HnL.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            6 => [
                'title' => 'Money Heist S04 ( La Casa De Papel ) Complete WEB-DL 1080p 720p 480p Dual Audio [English + Spanish] ESubs | NF Series',
                'link' => 'https://hdhub4u.rehab/money-heist-season-4/',
                'image' => 'https://extraimage.net/images/2020/04/03/204d88de3c2a69fb0c7b59f172cff431.jpg',
                'language' => 'Dual Audio',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            7 => [
                'title' => 'Money Heist (Season 1) WEB-DL Dual Audio [Hindi DD5.1 & English] 1080p 720p 480p x264/10Bit HEVC [ALL Episodes] | NF Series',
                'link' => 'https://hdhub4u.rehab/money-heist-season-1-hindi-webrip-all-episodes/',
                'image' => 'https://imagetot.com/images/2021/02/19/4e8b9479c7f887192e99d0d3d9faf02e.jpg',
                'language' => 'Dual Audio',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            8 => [
                'title' => 'Money Heist (La Casa De Papel) Season 3 WEB-DL 720p & 480p Dual Audio [English + Spanish] | ALL Episodes',
                'link' => 'https://hdhub4u.rehab/money-heist-season-3/',
                'image' => 'https://extraimage.net/images/2020/03/11/35f29afd5b4e91e0a77b31fd2208bc33.jpg',
                'language' => 'Dual Audio',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            9 => [
                'title' => 'Money Heist (La Casa De Papel) Season 2 WEB-DL 720p & 480p Dual Audio [English + Spanish] | ALL Episodes',
                'link' => 'https://hdhub4u.rehab/money-heist-season-2/',
                'image' => 'https://extraimage.net/images/2020/03/11/e2afe375fa306e4c734ceff09fdb32b6.jpg',
                'language' => 'Dual Audio',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            10 => [
                'title' => 'Money Heist (La Casa De Papel) Season 1 WEB-DL 720p & 480p Dual Audio [English + Spanish] | ALL Episodes',
                'link' => 'https://hdhub4u.rehab/money-heist-season-1/',
                'image' => 'https://extraimage.net/images/2020/03/11/16cc7d37e90a6e749b24700d9ca6757b.jpg',
                'language' => 'Dual Audio',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
        ],
    ],
    'BREAKING_BAD' => [
        'display_name' => 'Breaking Bad',
        'cover_image' => '',
        'hidden' => false,
        'movies' => [
            0 => [
                'title' => 'Breaking Bad (Season 5) BluRay [Hindi (ORG DD2.0) & English 5.1] 1080p 720p & 480p [x264/10Bit-HEVC] | TVSeries [ALL Episodes]',
                'link' => 'https://hdhub4u.rehab/breaking-bad-season-5-hindi-bluray-all-episodes/',
                'image' => 'https://image.tmdb.org/t/p/w500/ggFHVNu6YYI5L9pCfOacjizRGt.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            1 => [
                'title' => 'Breaking Bad (Season 4) BluRay [Hindi (ORG DD2.0) & English 5.1] 1080p 720p & 480p [x264/10Bit-HEVC] | TVSeries [ALL Episodes]',
                'link' => 'https://hdhub4u.rehab/breaking-bad-season-4-hindi-bluray-all-episodes/',
                'image' => 'https://catimages.org/images/2023/10/13/Breaking-Bad-Season4-Hindi-Dubbed-HDRip-ALL-Episodes-HDHub4u.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            2 => [
                'title' => 'Breaking Bad (Season 3) BluRay [Hindi (ORG DD2.0) & English 5.1] 1080p 720p & 480p [x264/10Bit-HEVC] | TVSeries [ALL Episodes]',
                'link' => 'https://hdhub4u.rehab/breaking-bad-season-3-hindi-bluray-all-episodes/',
                'image' => 'https://catimages.org/images/2023/09/26/Breaking-Bad-Season-3-Hindi-Dubbed-BLuRay-ALL-Episodes-HDHub4u.Tv.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            3 => [
                'title' => 'Breaking Bad (Season 2) BluRay [Hindi (ORG DD2.0) & English 5.1] 1080p 720p & 480p [x264/10Bit-HEVC] | TVSeries [ALL Episodes]',
                'link' => 'https://hdhub4u.rehab/breaking-bad-season-2-hindi-bluray-all-episodes/',
                'image' => 'https://catimages.org/images/2023/09/07/927de02fccd2b8512c195c1171f58a49.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            4 => [
                'title' => 'Breaking Bad (Season 1) BluRay [Hindi (ORG 2.0) & English 5.1] 1080p 720p & 480p [x264/10Bit-HEVC] | TVSeries [ALL Episodes]',
                'link' => 'https://hdhub4u.rehab/breaking-bad-season-1-hindi-bluray-all-episodes/',
                'image' => 'https://catimages.org/images/2023/08/29/62462ad2ed2964881cadaf99d5c5f8e5.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
        ],
    ],
    'BAHUBALI' => [
        'display_name' => 'Bahubali',
        'cover_image' => '',
        'hidden' => false,
        'movies' => [
            0 => [
                'title' => 'Bahubali: The Epic (2025) HDTC [Hindi (LiNE)] 1080p 720p & 480p [x264] | Full Movie',
                'link' => 'https://hdhub4u.rehab/bahubali-the-epic-2025-hdtc-hindi-full-movie/',
                'image' => 'https://image.tmdb.org/t/p/w400/z9YIo2qscyaXYgRqIdRJtND3bw8.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            1 => [
                'title' => 'Bahubali 2: The Conclusion (2017) BluRay [Hindi DD5.1] 1080p 720p & 480p [x264/10Bit-HEVC] | Full Movie',
                'link' => 'https://hdhub4u.rehab/bahubali-2-the-conclusion-2017-hindi-bluray-full-movie/',
                'image' => 'https://image.tmdb.org/t/p/w400/21sC2assImQIYCEDA84Qh9d1RsK.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            2 => [
                'title' => 'Bahubali: The Beginning (2015) BluRay [Hindi DD5.1] 1080p 720p & 480p [x264/10Bit-HEVC] | Full Movie',
                'link' => 'https://hdhub4u.rehab/bahubali-the-beginning-2015-hindi-bluray-full-movie/',
                'image' => 'https://image.tmdb.org/t/p/w400/9BAjt8nSSms62uOVYn1t3C3dVto.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
        ],
    ],
    'PUSHPA' => [
        'display_name' => 'Pushpa',
        'cover_image' => '',
        'hidden' => false,
        'movies' => [
            0 => [
                'title' => 'Pushpa 2: The Rule (2024) V2 TRUE WEB-DL [Hindi ORG-DD5.1] 1080p 720p & 480p [x264/HEVC] | Full Movie [THEATRiCAL CUT]',
                'link' => 'https://hdhub4u.rehab/pushpa-2-the-rule-2024-hindi-webrip-full-movie/',
                'image' => 'https://image.tmdb.org/t/p/w500/iwPJwH7BeNBrY5lwaY6aBhR4ekh.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            1 => [
                'title' => 'Pushpa: The Rise - Part 1 (2021) DS4K WEB-DL [Hindi (ORG 5.1) & Telugu] 4K 1080p 720p & 480p Dual Audio [x264/10Bit-HEVC] | Full Movie',
                'link' => 'https://hdhub4u.rehab/pushpa-the-rise-part-1-2021-hindi-webrip-full-movie/',
                'image' => 'https://image.tmdb.org/t/p/w500/sLxYuCCnWW845rXni9he26gUZM9.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
        ],
    ],
    'KGF' => [
        'display_name' => 'Kgf',
        'cover_image' => '',
        'hidden' => false,
        'movies' => [
            0 => [
                'title' => 'K.G.F: Chapter 2 (2022) Hindi (ORG 5.1) WEB-DL 1080p 720p & 480p x264/10Bit-HEVC [ESubs] | Full Movie',
                'link' => 'https://hdhub4u.rehab/kgf-chapter-2-2022-hindi-org-webrip-full-movie/',
                'image' => 'https://myimg.click/images/2022/05/16/KGF-CHAPTER-2-2022-Hindi-ORG-WEBRip-Full-Movie.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
            1 => [
                'title' => 'K.G.F: Chapter 1 (2018) Hindi DD5.1 WEB-DL 1080p 720p 480p [x264/HEVC] ESubs HD | Full Movie',
                'link' => 'https://hdhub4u.rehab/kgf-chapter-1-2018-hindi-webrip-full-movie/',
                'image' => 'https://myimg.click/images/2021/01/09/KGF-Chapter.1-Poster-HDHub4u.uno.jpg',
                'language' => 'Hindi',
                'genre' => '',
                'website' => 'HDHub4u',
                'hidden' => false,
            ],
        ],
    ],
];

$SITE_SETTINGS = [
    'website_name' => 'FilmHaat',
    'logo_image' => 'attached_image/logo-image.webp',
    'background_image' => 'attached_image/background-image.webp',
];

?>
