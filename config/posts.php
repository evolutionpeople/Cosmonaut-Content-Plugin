<?php
return [
    /*
     * Defines the list of custom post type according Wordpress documentation @link https://codex.wordpress.org/Function_Reference/register_post_type
     * @param $key must be the singular name slug
     * @param $lables also accept 'gender' param. Accepted values are 'm' or 'f'. Default 'm'
     * @param $slug must be defined as plural slug
     *
    */
    'offer' => [
        'labels' => [
            'singular'  => _x('offer', 'boilerplate-cpt', 'cosmonaut'),
            'plural'    => _x('offers', 'boilerplate-cpt', 'cosmonaut'),
            'gender'    => 'f'
        ],
        'supports'              => ['title', 'thumbnail','editor', 'page-attributes','revisions'],
        'menu_position'         => 10,
        'menu_icon'             => 'dashicons-star-filled',
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'query_var'             => true,
        'rewrite' => [
            'slug'              => _x('offers','boilerplate-cpt','cosmonaut')
        ],
        'has_archive'           => false,
        'hierarchical'          => false
    ]
];