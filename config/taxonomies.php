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
        'post_type'          => ['offer'],
        'labels'             => [
            'singular' => _x('offer type', 'boilerplate-cpt', 'cosmonaut'),
            'plural'   => _x('offer types', 'boilerplate-cpt', 'cosmonaut'),
            'gender'   => 'f'
        ],
        'public'             => TRUE,
        'show_ui'            => TRUE,
        'show_in_menu'       => TRUE,
        'show_in_nav_menus'  => FALSE,
        'show_tagcloud'      => FALSE,
        'show_in_quick_edit' => TRUE,
        'meta_box_cb'        => NULL,
        'show_admin_column'  => TRUE,
        'hierarchical'       => TRUE,
        'rewrite'            => [
            'slug' => '%post_type%/' . _x('offer-type', 'boilerplate-cpt', 'cosmonaut'),
        ],
        'query_var'          => _x('offer-type', 'boilerplate-cpt', 'cosmonaut')
    ]
];