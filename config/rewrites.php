<?php
global $wp_rewrite;
$wp_rewrite->pagination_base = 'page';
$pagination = 'page\/(\d+)\/';
$date_parts = '(\d{4})\/(\d{1,2})\/(\d{1,2})\/';
$year_month = '(\d{4})\/(\d{1,2})\/';
$year = '(\d{4})\/';
$post_slug = '([a-z-0-9]*)\/';
$end_url = '?$';

return [
    'add_rules' => [
        /*[
            '^(pippoplutopaperino)' . '\/(categoria-news|news-category)\/' . $post_slug .$pagination. $end_url,
            'index.php?post_type=news&news_category=$matches[3]&paged=$matches[4]',
            'top'
        ]*/
    ],
    'remove_rules' => [
        'feed',
        'embed',
        'attachment',
        'trackback',
        'comment-page',
        'search',
        'tag'
    ],
    'remove_rewrites' => [
        'attachment='
    ]
];