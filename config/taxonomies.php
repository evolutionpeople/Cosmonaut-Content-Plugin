<?php
return [
    /*
     * Defines the list of custom taxonomies according Wordpress documentation @link https://codex.wordpress.org/Function_Reference/register_taxonomy
     * @param $key must be the singular name slug
     * @param $lables also accept 'gender' param. Accepted values are 'm' or 'f'. Default 'm'
     * @param $slug must be defined as plural slug
     *
    */
    'offer-type' => [
        'post_type' => ['offer'],
        'labels' => [
            'singular'  => _x('offer type', 'boilerplate-cpt', 'cosmonaut'),
            'plural'    => _x('offer types', 'boilerplate-cpt', 'cosmonaut'),
            'gender'    => 'f'
        ],
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => false,
        'show_tagcloud' => false,
        'show_in_quick_edit' => true,
        'meta_box_cb' => null,
        'show_admin_column' => true,
        'hierarchical' => true,
        'rewrite' => [
            'slug' => '%post_type%/' . _x('offer-type','boilerplate-cpt','cosmonaut'),
        ],
        'query_var' => _x('offer-type','boilerplate-cpt','cosmonaut')
    ]
];