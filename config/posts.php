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
        'labels'             => [
            'singular' => _x('offer', 'boilerplate-cpt', 'cosmonaut'),
            'plural'   => _x('offers', 'boilerplate-cpt', 'cosmonaut'),
            'gender'   => 'f'
        ],
        'supports'           => ['title', 'thumbnail', 'editor', 'page-attributes', 'revisions'],
        'menu_position'      => 10,
        'menu_icon'          => 'dashicons-star-filled',
        'public'             => TRUE,
        'publicly_queryable' => TRUE,
        'show_ui'            => TRUE,
        'show_in_menu'       => TRUE,
        'query_var'          => TRUE,
        'rewrite'            => [
            'slug' => _x('offers', 'boilerplate-cpt', 'cosmonaut')
        ],
        'has_archive'        => FALSE,
        'hierarchical'       => FALSE
    ]
];