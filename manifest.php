<?php
header('Content-Type: application/manifest+json');
header('Cache-Control: max-age=86400'); // Cache for 1 day

$manifest = [
    "name" => "FilmHaat - Watch your favorite movies and series",
    "short_name" => "FilmHaat",
    "description" => "Find your favorite movies and series from multiple websites",
    "start_url" => "/",
    "display" => "standalone",
    "background_color" => "#000000",
    "theme_color" => "#000000",
    "orientation" => "portrait-primary",
    "scope" => "/",
    "icons" => [
        [
            "src" => "attached_image/pwa-logo.webp",
            "sizes" => "48x48",
            "type" => "image/webp",
            "purpose" => "any"
        ],
        [
            "src" => "attached_image/pwa-logo.webp",
            "sizes" => "72x72",
            "type" => "image/webp",
            "purpose" => "any"
        ],
        [
            "src" => "attached_image/pwa-logo.webp",
            "sizes" => "96x96",
            "type" => "image/webp",
            "purpose" => "any"
        ],
        [
            "src" => "attached_image/pwa-logo.webp",
            "sizes" => "144x144",
            "type" => "image/webp",
            "purpose" => "any"
        ],
        [
            "src" => "attached_image/pwa-logo.webp",
            "sizes" => "192x192",
            "type" => "image/webp",
            "purpose" => "any"
        ],
        [
            "src" => "attached_image/pwa-logo.webp",
            "sizes" => "512x512",
            "type" => "image/webp",
            "purpose" => "any"
        ]
    ],
    "screenshots" => [],
    "categories" => ["entertainment", "movies"],
    "dir" => "ltr",
    "lang" => "en",
    "prefer_related_applications" => false,
    "shortcuts" => [
        [
            "name" => "Home",
            "short_name" => "Home",
            "description" => "Go to home page",
            "url" => "/index.php",
            "icons" => [
                [
                    "src" => "attached_image/shortcut-home.svg",
                    "sizes" => "any",
                    "type" => "image/svg+xml"
                ]
            ]
        ],
        [
            "name" => "Latest",
            "short_name" => "Latest",
            "description" => "Browse latest movies and series",
            "url" => "/latest.php?category=LATEST",
            "icons" => [
                [
                    "src" => "attached_image/shortcut-latest.svg",
                    "sizes" => "any",
                    "type" => "image/svg+xml"
                ]
            ]
        ],
        [
            "name" => "Loved",
            "short_name" => "Loved",
            "description" => "View your favorite movies and series",
            "url" => "/loved.php",
            "icons" => [
                [
                    "src" => "attached_image/shortcut-loved.svg",
                    "sizes" => "any",
                    "type" => "image/svg+xml"
                ]
            ]
        ],
        [
            "name" => "Search",
            "short_name" => "Search",
            "description" => "Search movies and series",
            "url" => "/index.php",
            "icons" => [
                [
                    "src" => "attached_image/shortcut-search.svg",
                    "sizes" => "any",
                    "type" => "image/svg+xml"
                ]
            ]
        ]
    ]
];

echo json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
