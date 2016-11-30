<?php
namespace EP;

/**
 * Created by PhpStorm.
 * User: francescocolonna
 * Date: 19/04/16
 * Time: 14:41
 */
class CustomPostType
{
    public static $domain = 'cosmonaut';

    private static $rewrite_slug = [
        'event' => [
            'eventi', 'events'
        ],
        'news' => [
            'news'
        ],
        'gallery' => [
            'foto-gallery','photo-gallery'
        ],
    ];

    private static $press_release_position = 20;

    private static $press_release_separator_position = 20;

    public function __construct()
    {
        add_action('init', [$this, 'init']);
        //add_filter('init', [$this,'custom_query_vars']);
        add_filter('rewrite_rules_array', [$this,'cleanRewrite'],1);
        add_filter('query_vars', [$this,'query_vars_filter'] );
        add_filter('redirect_canonical', [$this, 'ep_no_redirect']);
        add_action('admin_menu', [$this, 'removeDefaults']);
        add_action('pre_get_posts', [$this, 'ep_query_posts']);
        //add_filter('post_type_link', [$this, 'post_type_url'], 1, 3);
        add_action('admin_init', [$this,'add_menu_separator'],10);
        add_action('admin_menu', [$this,'set_admin_menu_separator']);
        add_action('admin_head', [$this,'custom_css']);
        //add_action( 'parse_request', [$this,'parse_request'] );
        //add_action('save_post',[$this,'save_wine']);
        //add_action( 'plugins_loaded', [$this,'plugins_loaded'] );
    }
    public function parse_request($query)
    {
        d($query);
    }
    public function init()
    {
        $this->postTypeRewriteRules();
        $this->registerCustomPostType();
    }

    public function plugins_loaded()
    {
        $options = get_option('cpto_options');
        if($options['capability'] !== 'edit_press')
        {
            $options['capability'] = 'bau';
            update_option('cpto_options', $options);
        }
    }

    public function custom_css()
    {
        echo '<style>
                #adminmenu li.wp-menu-separator{
                    border-bottom: 1px solid #666;
                    margin: 20px auto;
                }
                #adminmenu li.wp-menu-separator.press-release, #adminmenu li.wp-menu-separator.donnafugata,#adminmenu li.wp-menu-separator.storelocator{
                    margin-bottom: 34px;
                }
                #adminmenu li.wp-menu-separator.donnafugata{
                    
                }
                #adminmenu li.wp-menu-separator.press-release::after, #adminmenu li.wp-menu-separator.donnafugata::after,#adminmenu li.wp-menu-separator.storelocator::after{
                    padding: 5px 5px 5px 20px;
                    display: block;
                    color: #666;
                    border-bottom: 1px solid #666;
                    font-style: italic;
                }
                #adminmenu li.wp-menu-separator.donnafugata::after
                {
                    content: "Contenuto";
                }
                #adminmenu li.wp-menu-separator.storelocator::after
                {
                    content: "Storelocator";
                }
                #adminmenu li.wp-menu-separator.press-release::after{
                    content: "Press & trade room";                    
                }
            </style>';
    }

    public function set_admin_menu_separator()
    {
        /*do_action( 'admin_init',0);
        do_action( 'admin_init', self::$press_release_separator_position);*/
    }

    public function add_menu_separator($position)
    {
        global $current_user;
        if(current_user_can('edit_posts'))
        {
            global $menu;

            if($menu)
            {
                foreach ($menu as $k=>$v)
                {
                    if($v[0] === '')
                    {
                        unset($menu[$k]);
                    }
                }
                $separators = [
                    [
                        0	=>	'',
                        1	=>	'read',
                        2	=>	'separator' . $position,
                        3	=>	'',
                        4	=>	'wp-menu-separator storelocator'
                    ]
                ];
                array_splice( $menu, 0, 0, $separators );

                $separators = [
                    [
                        0	=>	'',
                        1	=>	'read',
                        2	=>	'separator' . $position,
                        3	=>	'',
                        4	=>	'wp-menu-separator donnafugata'
                    ]
                ];
                array_splice( $menu, 5, 0, $separators );

                $separators = [
                    [
                        0	=>	'',
                        1	=>	'read',
                        2	=>	'separator' . $position,
                        3	=>	'',
                        4	=>	'wp-menu-separator press-release'
                    ]
                ];
                array_splice( $menu, self::$press_release_separator_position, 0, $separators );

                $separators = [
                    [
                        0	=>	'',
                        1	=>	'read',
                        2	=>	'separator' . $position,
                        3	=>	'',
                        4	=>	'wp-menu-separator'
                    ]
                ];
                array_splice( $menu, self::$press_release_separator_position+10, 0, $separators );
                array_splice( $menu, 80, 0, $separators );
            }

        }
    }

    public function query_vars_filter($vars)
    {
        $vars[] = 'wine_name';
        $vars[] = 'award_year';
        //$vars[] = 'award_publication';
        $vars[] = 'press_year';
        $vars[] = 'press_month';
        return $vars;
    }

    public function cleanRewrite($rules)
    {
        foreach ($rules as $rule => $rewrite) {
            if ( preg_match('/feed|embed|attachment|trackback|comment-page|search\/|%post_type%|%post_date%|tag\//',$rule) ) {
                unset($rules[$rule]);
            }
            if(preg_match('/attachment=/',$rewrite))
            {
                unset($rules[$rule]);
            }
        }
        return $rules;

    }



    public function ep_no_redirect($redirect_url)
    {
        if (is_404()) {
            return false;
        }
        return $redirect_url;
    }

    public function removeDefaults()
    {
        remove_menu_page('index.php');
        remove_menu_page('edit.php');
        remove_menu_page('edit-comments.php');
    }

    public function generateLabel($singular, $plural, $gender = 'm')
    {
        if ($gender == 'm') {
            $labels = [
                'all_items' => sprintf(_x('Tutti i %s', 'cpt', 'cosmonaut'), $plural),
                'view_item' => sprintf(_x('Visualizza il %s', 'cpt', 'cosmonaut'), $singular),
                'add_new_item' => sprintf(_x('Aggiungi un nuovo %s', 'cpt', 'cosmonaut'), $singular),
                'add_new' => sprintf(_x('Aggiungi un nuovo %s', 'cpt', 'cosmonaut'), $singular),
                'not_found' => sprintf(_x('Nessun %s trovato.', 'cpt', 'cosmonaut'), $plural),
                'not_found_in_trash' => sprintf(_x('Nessun %s trovato nel cestino.', 'cpt', 'cosmonaut'), $plural)
            ];
        } elseif ($gender == 'f') {
            $labels = [
                'all_items' => sprintf(_x('Tutte le %s', 'cpt', 'cosmonaut'), $plural),
                'view_item' => sprintf(_x('Visualizza la %s', 'cpt', 'cosmonaut'), $singular),
                'add_new_item' => sprintf(_x('Aggiungi una nuova %s', 'cpt', 'cosmonaut'), $singular),
                'add_new' => sprintf(_x('Aggiungi una nuova %s', 'cpt', 'cosmonaut'), $singular),
                'not_found' => sprintf(_x('Nessuna %s trovata.', 'cpt', 'cosmonaut'), $plural),
                'not_found_in_trash' => sprintf(_x('Nessuna %s trovata nel cestino.', 'cpt', 'cosmonaut'), $plural)
            ];
        }
        $labels['name'] = ucfirst($plural);
        $labels['singular_name'] = ucfirst($singular);
        $labels['menu_name'] = ucfirst($plural);
        $labels['edit_item'] = sprintf(_x('Modifica %s', 'cpt', 'cosmonaut'), $singular);
        $labels['update_item'] = sprintf(_x('Aggiorna %s', 'cpt', 'cosmonaut'), $singular);
        $labels['search_items'] = sprintf(_x('Cerca %s', 'cpt', 'cosmonaut'), $plural);

        return $labels;
    }

    private function kitCapabilities()
    {
        return [
            'edit_post'             => 'edit_kit',
            'read_post'             => 'read_kit',
            'delete_post'           => 'delete_kits',

            'edit_posts'            => 'edit_kits',
            'edit_published_posts'  => 'edit_published_kits',
            'edit_private_posts'    => 'edit_private_kits',
            'edit_others_posts'     => 'edit_others_kits',
            'publish_posts'         => 'publish_kits',

            'read_private_posts'    => 'read_private_kits'
        ];
    }

    private function registerCustomPostType()
    {

        /*Store locator*/

        register_post_type('store',
            array(
                'labels' => $this->generateLabel(_x('store', 'cpt', 'cosmonaut'), _x('stores', 'cpt', 'cosmonaut')),
                'supports' => array('title'),
                'menu_position' => 1,
                'menu_icon' => 'dashicons-location-alt',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'rewrite' => false,
                'query_var' => true,
                'has_archive' => false,
                'hierarchical' => false,
            )
        );
        register_post_type('importer',
            array(
                'labels' => $this->generateLabel(_x('importatore', 'cpt', 'cosmonaut'), _x('importatori', 'cpt', 'cosmonaut')),
                'supports' => array('title'),
                'menu_position' => 2,
                'menu_icon' => 'dashicons-download',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'rewrite' => false,
                'query_var' => true,
                'has_archive' => false,
                'hierarchical' => false,
            )
        );
        register_post_type('agent',
            array(
                'labels' => $this->generateLabel(_x('agente', 'cpt', 'cosmonaut'), _x('agenti', 'cpt', 'cosmonaut')),
                'supports' => array('title'),
                'menu_position' => 3,
                'menu_icon' => 'dashicons-download',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'rewrite' => false,
                'query_var' => true,
                'has_archive' => false,
                'hierarchical' => false,
            )
        );
        register_post_type('ecommerce',
            array(
                'labels' => $this->generateLabel(_x('e-commerce', 'cpt', 'cosmonaut'), _x('e-commerce', 'cpt', 'cosmonaut')),
                'supports' => array('title'),
                'menu_position' => 4,
                'menu_icon' => 'dashicons-download',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'rewrite' => false,
                'query_var' => true,
                'has_archive' => false,
                'hierarchical' => false,
            )
        );

        register_post_type('wine',
            array(
                'labels' => $this->generateLabel(_x('vino', 'cpt', 'cosmonaut'), _x('vini', 'cpt', 'cosmonaut')),
                'supports' => array('title', 'thumbnail', 'editor', 'page-attributes'),
                'menu_position' => 6,
                'menu_icon' => 'dashicons-image-filter',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => [
                    'slug'  => _x('i-vini','cpt','cosmonaut')
                ],
                'has_archive' => false,
                'hierarchical' => false
            )
        );


        register_post_type('collection',
            array(
                'labels' => $this->generateLabel(_x('collezione', 'cpt', 'cosmonaut'), _x('collezioni', 'cpt', 'cosmonaut'), 'f'),
                'supports' => array('title', 'thumbnail', 'editor', 'page-attributes'),
                'menu_position' => 7,
                'menu_icon' => 'dashicons-grid-view',
                'public' => true,
                'publicly_queryable' => false,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => false,
                'has_archive' => false,
                'hierarchical' => false
            )
        );


        register_post_type('type',
            array(
                'labels' => $this->generateLabel(_x('tipologia', 'cpt', 'cosmonaut'), _x('tipologie', 'cpt', 'cosmonaut'), 'f'),
                'supports' => array('title', 'thumbnail', 'editor', 'page-attributes'),
                'menu_position' => 8,
                'menu_icon' => 'dashicons-tag',
                'public' => true,
                'publicly_queryable' => false,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => false,
                'has_archive' => false,
                'hierarchical' => false,
                'exclude_from_search' => true
            )
        );

        register_post_type('label',
            array(
                'labels' => $this->generateLabel(_x('etichetta', 'cpt', 'cosmonaut'), _x('etichette', 'cpt', 'cosmonaut'), 'f'),
                'supports' => array('title', 'thumbnail', 'editor', 'page-attributes'),
                'menu_position' => 9,
                'menu_icon' => 'dashicons-art',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => [
                    'slug'  => _x('etichette-autore','cpt','cosmonaut')
                ],
                'has_archive' => false,
                'hierarchical' => false
            )
        );


        register_post_type('recipe',
            array(
                'labels' => $this->generateLabel(_x('ricetta', 'cpt', 'cosmonaut'), _x('ricette', 'cpt', 'cosmonaut'), 'f'),
                'supports' => array('title', 'thumbnail', 'editor', 'page-attributes'),
                'menu_position' => 10,
                'menu_icon' => 'dashicons-carrot',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => [
                    'slug'  => _x('ricette','cpt','cosmonaut')
                ],
                'has_archive' => false,
                'hierarchical' => false
            )
        );



        register_post_type('area',
            array(
                'labels' => $this->generateLabel(_x('zona di produzione', 'cpt', 'cosmonaut'), _x('zone di produzione', 'cpt', 'cosmonaut'), 'f'),
                'supports' => array('title', 'thumbnail', 'editor', 'page-attributes', 'excerpt'),
                'menu_position' => 11,
                'menu_icon' => 'dashicons-admin-site',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => [
                    'slug'  => _x('la-nostra-terra','cpt','cosmonaut')
                ],
                'has_archive' => false,
                'hierarchical' => false
            )
        );


        register_post_type('variety',
            array(
                'labels' => $this->generateLabel(_x('varietà', 'cpt', 'cosmonaut'), _x('varietà', 'cpt', 'cosmonaut'), 'f'),
                'supports' => array('title', 'editor', 'thumbnail', 'page-attributes'),
                'menu_position' => 12,
                'menu_icon' => 'dashicons-clipboard',
                'public' => false,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => false,
                'has_archive' => false,
                'hierarchical' => false
            )
        );

        register_post_type('news',
            array(
                'labels' => $this->generateLabel(_x('news', 'cpt', 'cosmonaut'), _x('news', 'cpt', 'cosmonaut'), 'f'),
                'supports' => array('title', 'editor', 'thumbnail', 'page-attributes', 'excerpt'),
                'menu_position' => 13,
                'menu_icon' => 'dashicons-align-left',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => true,
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'has_archive' => false,
                'hierarchical' => false
            )
        );

        register_post_type('event',
            array(
                'labels' => $this->generateLabel(_x('evento', 'cpt', 'cosmonaut'), _x('eventi', 'cpt', 'cosmonaut'), 'm'),
                'supports' => array('title', 'editor', 'thumbnail', 'page-attributes', 'excerpt'),
                'menu_position' => 14,
                'menu_icon' => 'dashicons-calendar-alt',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => [
                    'slug'  => _x('eventi','cpt','cosmonaut')
                ],
                /*'capability_type' => 'press',*/
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'has_archive' => false,
                'hierarchical' => false
            )
        );

        register_post_type('vintage',
            array(
                'labels' => $this->generateLabel(_x('annata metereologica', 'cpt', 'cosmonaut'), _x('annate metereologiche', 'cpt', 'cosmonaut'), 'f'),
                'supports' => array('title', 'thumbnail', 'editor', 'page-attributes'),
                'menu_position' => 15,
                'menu_icon' => 'dashicons-calendar',
                'public' => false,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => false,
                'has_archive' => false,
                'hierarchical' => false
            )
        );


        register_post_type('page-image-header',
            array(
                'labels' => $this->generateLabel(_x('page header', 'cpt', 'cosmonaut'), _x('page headers', 'cpt', 'cosmonaut')),
                'supports' => array('title'),
                'menu_position' => 16,
                'menu_icon' => 'dashicons-images-alt2',
                'public' => false,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => false,
                'has_archive' => false,
                'hierarchical' => false
            )
        );


        register_post_type('timeline',
            array(
                'labels' => $this->generateLabel(_x('timeline', 'cpt', 'cosmonaut'), _x('timelines', 'cpt', 'cosmonaut')),
                'supports' => array('title', 'thumbnail', 'editor'),
                'menu_position' => 16,
                'menu_icon' => 'dashicons-backup',
                'public' => false,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => false,
                'has_archive' => false,
                'hierarchical' => false
            )
        );


        register_post_type('live',
            array(
                'labels' => $this->generateLabel(_x('live', 'cpt', 'cosmonaut'), _x('live', 'cpt', 'cosmonaut')),
                'supports' => array('title', 'thumbnail', 'editor'),
                'menu_position' => 17,
                'menu_icon' => 'dashicons-format-audio',
                'public' => false,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => false,
                'has_archive' => false,
                'hierarchical' => false
            )
        );



        /*
         * Press Area
         */

        register_post_type('award',
            array(
                'labels' => $this->generateLabel(_x('premio', 'cpt', 'cosmonaut'), _x('premi', 'cpt', 'cosmonaut')),
                'supports' => array('title', 'thumbnail','editor', 'page-attributes','revisions'),
                'menu_position' => self::$press_release_position+1,
                'menu_icon' => 'dashicons-star-filled',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => [
                    'slug'  => _x('premi-e-guide','cpt','cosmonaut')
                ],
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'has_archive' => false,
                'hierarchical' => false
            )
        );

        register_post_type('gallery',
            array(
                'labels' => $this->generateLabel(_x('gallery', 'cpt', 'cosmonaut'), _x('gallery', 'cpt', 'cosmonaut'),'f'),
                'supports' => array('title', 'thumbnail', 'editor', 'page-attributes'),
                'menu_position' => self::$press_release_position+2,
                'menu_icon' => 'dashicons-format-gallery',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => [
                    'slug'  => _x('foto-gallery','cpt','cosmonaut')
                ],
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'has_archive' => false,
                'hierarchical' => false
            )
        );



        register_post_type('press-kit',
            array(
                'labels' => $this->generateLabel(_x('press kit', 'cpt', 'cosmonaut'), _x('press kit', 'cpt', 'cosmonaut')),
                'supports' => array('title', 'thumbnail', 'page-attributes'),
                'menu_position' => self::$press_release_position+3,
                'menu_icon' => 'dashicons-portfolio',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => true,
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'has_archive' => false,
                'hierarchical' => false,
            )
        );

        register_post_type('event-kit',
            array(
                'labels' => $this->generateLabel(_x('event kit', 'cpt', 'cosmonaut'), _x('event kit', 'cpt', 'cosmonaut')),
                'supports' => array('title', 'thumbnail', 'page-attributes'),
                'menu_position' => self::$press_release_position+4,
                'menu_icon' => 'dashicons-calendar-alt',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => true,
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'has_archive' => false,
                'hierarchical' => false
            )
        );

        register_post_type('trade-kit',
            array(
                'labels' => $this->generateLabel(_x('trade kit', 'cpt', 'cosmonaut'), _x('trade kit', 'cpt', 'cosmonaut')),
                'supports' => array('title', 'thumbnail', 'editor', 'page-attributes'),
                'menu_position' => self::$press_release_position+5,
                'menu_icon' => 'dashicons-cart',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => true,
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'has_archive' => false,
                'hierarchical' => false
            )
        );

        register_post_type('press-release',
            array(
                'labels' => $this->generateLabel(_x('comunicato stampa', 'cpt', 'cosmonaut'), _x('comunicati stampa', 'cpt', 'cosmonaut')),
                'supports' => array('title', 'thumbnail', 'editor', 'page-attributes', 'excerpt'),
                'menu_position' => self::$press_release_position+6,
                'menu_icon' => 'dashicons-megaphone',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => [
                    'slug'  => _x('comunicati-stampa','cpt','cosmonaut')
                ],
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'has_archive' => false,
                'hierarchical' => false
            )
        );

        register_post_type('press-review',
            array(
                'labels'        => $this->generateLabel(_x('rassegna stampa', 'cpt', 'cosmonaut'), _x('rassegna stampa', 'cpt', 'cosmonaut'),'f'),
                'supports'      => array('title', 'thumbnail', 'editor', 'page-attributes'),
                'menu_position' => self::$press_release_position+7,
                'menu_icon'     => 'dashicons-format-aside',
                'public'        => true,
                'publicly_queryable' => true,
                'show_ui'       => true,
                'show_in_menu'  => true,
                'query_var'     => true,
                'rewrite' => [
                    'slug'  => _x('rassegna-stampa','cpt','cosmonaut')
                ],
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'has_archive'   => false,
                'hierarchical'  => false,
            )
        );

        /*$valori = '';
        foreach ($GLOBALS['wp_post_types']['press-review']->cap as $k=>$cap)
        {
            $valori .= '$c_role->add_cap(\''.$cap.'\');
            ';
        }
        d($valori);

        global $current_user;
        ddd($current_user);*/




        if (GLOBAL_DEBUG) {
            flush_rewrite_rules();
        }
    }

    public function postTypeRewriteRules()
    {
        global $wp_rewrite;

        $wp_rewrite->pagination_base = 'page';

        $pagination = 'page\/(\d+)\/';

        $date_parts = '(\d{4})\/(\d{1,2})\/(\d{1,2})\/';

        $year_month = '(\d{4})\/(\d{1,2})\/';

        $year = '(\d{4})\/';

        $post_slug = '([a-z-0-9]*)\/';

        $end_url = '?$';


        //Premi e guide
        $award_tax = '(pubblicazione|publication|wine|vino|premio|awards)\/';
        $award_slug = '^(premi-e-guide|awards-and-guides)\/';
        add_rewrite_rule(
            $award_slug.$year.$award_tax.$post_slug.$award_tax.$post_slug.$pagination.$end_url,
            'index.php?pagename=$matches[1]&'._x('anno-premio','cpt','cosmonaut').'=$matches[2]&$matches[3]=$matches[4]&$matches[5]=$matches[6]&paged=$matches[7]',
            'top'
        );
        add_rewrite_rule(
            $award_slug.$year.$award_tax.$post_slug.$award_tax.$post_slug.$end_url,
            'index.php?pagename=$matches[1]&'._x('anno-premio','cpt','cosmonaut').'=$matches[2]&$matches[3]=$matches[4]&$matches[5]=$matches[6]',
            'top'
        );
        add_rewrite_rule(
            $award_slug.$award_tax.$post_slug.$award_tax.$post_slug.$pagination.$end_url,
            'index.php?pagename=$matches[1]&$matches[2]=$matches[3]&$matches[4]=$matches[5]&paged=$matches[6]',
            'top'
        );
        add_rewrite_rule(
            $award_slug.$award_tax.$post_slug.$award_tax.$post_slug.$end_url,
            'index.php?pagename=$matches[1]&$matches[2]=$matches[3]&$matches[4]=$matches[5]',
            'top'
        );
        add_rewrite_rule(
            $award_slug.$year.$award_tax.$post_slug.$pagination.$end_url,
            'index.php?pagename=$matches[1]&'._x('anno-premio','cpt','cosmonaut').'=$matches[2]&$matches[3]=$matches[4]&paged=$matches[5]',
            'top'
        );
        add_rewrite_rule(
            $award_slug.$year.$award_tax.$post_slug.$end_url,
            'index.php?pagename=$matches[1]&'._x('anno-premio','cpt','cosmonaut').'=$matches[2]&$matches[3]=$matches[4]',
            'top'
        );
        add_rewrite_rule(
            $award_slug.$year.$pagination.$end_url,
            'index.php?pagename=$matches[1]&press_year=$matches[2]&paged=$matches[3]',
            'top'
        );
        add_rewrite_rule(
            $award_slug.$year.$end_url,
            'index.php?pagename=$matches[1]&'._x('anno-premio','cpt','cosmonaut').'=$matches[2]',
            'top'
        );
        add_rewrite_rule(
            $award_slug.$award_tax.$post_slug.$pagination.$end_url,
            'index.php?pagename=$matches[1]&$matches[2]=$matches[3]&paged=$matches[4]',
            'top'
        );

        add_rewrite_rule(
            $award_slug.$award_tax.$post_slug.$end_url,
            'index.php?pagename=$matches[1]&$matches[2]=$matches[3]',
            'top'
        );
        add_rewrite_rule(
            $award_slug.$pagination. $end_url,
            'index.php?pagename=$matches[1]&paged=$matches[2]',
            'top'
        );
        //Premi e guide


        //Gallery

        add_rewrite_rule(
            '^(foto-gallery|photo-gallery)\/'.'(tipologia)\/'. $post_slug .$end_url,
            'index.php?pagename=$matches[1]&$matches[2]=$matches[3]',
            'top'
        );
        add_rewrite_rule(
            '^(foto-gallery|photo-gallery)\/'.'(tipologia)\/'.$end_url,
            'index.php?pagename=$matches[1]',
            'top'
        );
        //Gallery

        //Comunicati e Rassegna stampa

        $release_review_tax = '(persona|person|evento|event|autore|author|categoria|category|source|testata|vino|wine|lingua|language)\/';

        $release_type_slug = '^(comunicati-stampa|rassegna-stampa|press-releases|press-review)\/';

        add_rewrite_rule(
            $release_type_slug.$year_month.$release_review_tax.$post_slug.$release_review_tax.$post_slug.$pagination.$end_url,
            'index.php?pagename=$matches[1]&press_year=$matches[2]&press_month=$matches[3]&$matches[4]=$matches[5]&$matches[6]=$matches[7]&paged=$matches[8]',
            'top'
        );
        add_rewrite_rule(
            $release_type_slug.$year_month.$release_review_tax.$post_slug.$release_review_tax.$post_slug.$end_url,
            'index.php?pagename=$matches[1]&press_year=$matches[2]&press_month=$matches[3]&$matches[4]=$matches[5]&$matches[6]=$matches[7]',
            'top'
        );
        add_rewrite_rule(
            $release_type_slug.$year.$release_review_tax.$post_slug.$release_review_tax.$post_slug.$pagination.$end_url,
            'index.php?pagename=$matches[1]&press_year=$matches[2]&$matches[3]=$matches[4]&$matches[5]=$matches[6]&paged=$matches[7]',
            'top'
        );
        add_rewrite_rule(
            $release_type_slug.$year.$release_review_tax.$post_slug.$release_review_tax.$post_slug.$end_url,
            'index.php?pagename=$matches[1]&press_year=$matches[2]&$matches[3]=$matches[4]&$matches[5]=$matches[6]',
            'top'
        );
        add_rewrite_rule(
            $release_type_slug.$release_review_tax.$post_slug.$release_review_tax.$post_slug.$pagination.$end_url,
            'index.php?pagename=$matches[1]&$matches[2]=$matches[3]&$matches[4]=$matches[5]&paged=$matches[6]',
            'top'
        );
        add_rewrite_rule(
            $release_type_slug.$release_review_tax.$post_slug.$release_review_tax.$post_slug.$end_url,
            'index.php?pagename=$matches[1]&$matches[2]=$matches[3]&$matches[4]=$matches[5]',
            'top'
        );
        add_rewrite_rule(
            $release_type_slug.$year_month.$release_review_tax.$post_slug.$pagination.$end_url,
            'index.php?pagename=$matches[1]&press_year=$matches[2]&press_month=$matches[3]&$matches[4]=$matches[5]&paged=$matches[6]',
            'top'
        );
        add_rewrite_rule(
            $release_type_slug.$year_month.$release_review_tax.$post_slug.$end_url,
            'index.php?pagename=$matches[1]&press_year=$matches[2]&press_month=$matches[3]&$matches[4]=$matches[5]',
            'top'
        );
        add_rewrite_rule(
            $release_type_slug.$year.$release_review_tax.$post_slug.$pagination.$end_url,
            'index.php?pagename=$matches[1]&press_year=$matches[2]&$matches[3]=$matches[4]&paged=$matches[5]',
            'top'
        );
        add_rewrite_rule(
            $release_type_slug.$year.$release_review_tax.$post_slug.$end_url,
            'index.php?pagename=$matches[1]&press_year=$matches[2]&$matches[3]=$matches[4]',
            'top'
        );

        add_rewrite_rule(
            $release_type_slug.$release_review_tax.$post_slug.$pagination.$end_url,
            'index.php?pagename=$matches[1]&$matches[2]=$matches[3]&paged=$matches[4]',
            'top'
        );
        add_rewrite_rule(
            $release_type_slug.$release_review_tax.$post_slug.$end_url,
            'index.php?pagename=$matches[1]&$matches[2]=$matches[3]',
            'top'
        );
        add_rewrite_rule(
            $release_type_slug.$year_month.$pagination.$end_url,
            'index.php?pagename=$matches[1]&press_year=$matches[2]&press_month=$matches[3]&paged=$matches[4]',
            'top'
        );
        add_rewrite_rule(
            $release_type_slug.$year_month.$end_url,
            'index.php?pagename=$matches[1]&press_year=$matches[2]&press_month=$matches[3]',
            'top'
        );
        add_rewrite_rule(
            $release_type_slug.$year.$pagination.$end_url,
            'index.php?pagename=$matches[1]&press_year=$matches[2]&paged=$matches[3]',
            'top'
        );
        add_rewrite_rule(
            $release_type_slug.$year.$end_url,
            'index.php?pagename=$matches[1]&press_year=$matches[2]',
            'top'
        );
        add_rewrite_rule(
            $release_type_slug.$pagination.$end_url,
            'index.php?pagename=$matches[1]&paged=$matches[2]',
            'top'
        );

        add_rewrite_rule(
            '^(.*kit)\/'.$year_month.$end_url,
            'index.php?pagename=$matches[1]&press_year=$matches[2]&press_month=$matches[3]',
            'top'
        );
        add_rewrite_rule(
            '^(.*kit)\/'.$year.$end_url,
            'index.php?pagename=$matches[1]&press_year=$matches[2]',
            'top'
        );

        //Comunicati e Rassegna stampa


        //News & Events
        add_rewrite_rule(
            '^(news)' . '\/(categoria-news|news-category)\/' . $post_slug .$pagination. $end_url,
            'index.php?post_type=news&news_category=$matches[3]&paged=$matches[4]',
            'top'
        );
        add_rewrite_rule(
            '^(news)' . '\/(categoria-news|news-category)\/' . $post_slug . $end_url,
            'index.php?post_type=news&news_category=$matches[3]',
            'top'
        );
        add_rewrite_rule(
            '^(eventi|events)' . '\/(categoria-eventi|events-category)\/' . $post_slug .$pagination. $end_url,
            'index.php?post_type=event&event_category=$matches[3]&paged=$matches[4]',
            'top'
        );
        add_rewrite_rule(
            '^(eventi|events)' . '\/(categoria-eventi|events-category)\/' . $post_slug . $end_url,
            'index.php?post_type=event&event_category=$matches[3]',
            'top'
        );
        foreach (self::$rewrite_slug as $k => $v) {
            add_rewrite_rule(
                '^(' . implode('|', $v) . ')\/' . $date_parts . $post_slug . $end_url,
                'index.php?post_type=' . $k . '&name=$matches[5]',
                'top'
            );
            add_rewrite_rule(
                '^(' . implode('|', $v) . ')\/' . $date_parts . $pagination.$end_url,
                'index.php?post_type=' . $k . '&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]&paged=$matches[5]',
                'top'
            );
            add_rewrite_rule(
                '^(' . implode('|', $v) . ')\/' . $date_parts . $end_url,
                'index.php?post_type=' . $k . '&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]',
                'top'
            );
            add_rewrite_rule(
                '^(' . implode('|', $v) . ')\/'.$year_month .$pagination.$end_url,
                'index.php?post_type=' . $k . '&year=$matches[2]&monthnum=$matches[3]&paged=$matches[4]',
                'top'
            );
            add_rewrite_rule(
                '^(' . implode('|', $v) . ')\/'.$year_month . $end_url,
                'index.php?post_type=' . $k . '&year=$matches[2]&monthnum=$matches[3]',
                'top'
            );
            add_rewrite_rule(
                '^(' . implode('|', $v) . ')\/'.$year .$pagination. $end_url,
                'index.php?post_type=' . $k . '&year=$matches[2]&paged=$matches[3]',
                'top'
            );
            add_rewrite_rule(
                '^(' . implode('|', $v) . ')\/'.$year . $end_url,
                'index.php?post_type=' . $k . '&year=$matches[2]',
                'top'
            );

            add_rewrite_rule(
                '^(' . implode('|', $v) . ')\/'.$pagination. $end_url,
                'index.php?pagename=$matches[1]&paged=$matches[2]',
                'top'
            );
        }

    }

    public function ep_query_posts($query)
    {
        if(isset($query->query['wine_name']))
        {
            unset($query->query['wine_name']);
        }
        if(isset($query->query['store_type']))
        {
            unset($query->query['store_type']);
        }
        if(isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'award')
        {
            $query->set('orderby', 'date');
            $query->set('order', 'desc');
        }

        if (!is_admin()) {
            if (!$query->is_main_query) {
                if (isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'timeline') {

                } elseif (isset($query->query_vars['post_type']) && in_array($query->query_vars['post_type'], ['event', 'news']) && isset($query->query_vars['year']) && !empty($query->query_vars['year']) && (!isset($query->query_vars['name']) || empty($query->query_vars['name']))) {


                    $month = '[0-9]{2}';
                    $day = '[0-9]{2}';

                    $year = (isset($query->query_vars['year']) && !empty($query->query_vars['year'])) ? $query->query_vars['year'] : date('Y');


                    if (isset($query->query_vars['monthnum']) && !empty($query->query_vars['monthnum'])) {
                        $month = str_pad($query->query_vars['monthnum'], 2, '0', STR_PAD_LEFT);
                    }
                    if (isset($query->query_vars['day']) && !empty($query->query_vars['day'])) {
                        $day = str_pad($query->query_vars['day'], 2, '0', STR_PAD_LEFT);
                    }


                    if ($query->query_vars['post_type'] == 'event') {
                        $year_month_day = $year . $month . $day;

                        $query->set('year', '');
                        $query->set('monthnum', '');

                        $query->set('meta_query', array(
                                array(
                                    'key' => 'event_start',
                                    'compare' => 'REGEXP',
                                    'value' => $year_month_day,
                                )
                            )
                        );
                        $query->set('meta_key','event_start');
                        $query->set('orderby','meta_value_num');
                        $query->set('order','desc');
                    }
                } elseif ($query->is_tax && isset($query->query_vars['post_type']) && ($query->query_vars['post_type'] == 'event' || $query->query_vars['post_type'] == 'news')) {
                    $query->set('post_fields', 'ids');
                    if($query->query_vars['post_type'] == 'event')
                    {
                        $query->set('meta_key','event_start');
                        $query->set('orderby','meta_value_num');
                        $query->set('order','desc');
                    }elseif ($query->query_vars['post_type'] == 'event')
                    {
                        $query->set('orderby','date');
                        $query->set('order','desc');
                    }
                } elseif (isset($query->query_vars['post_type'])  && ($query->query_vars['post_type'] == 'event' || $query->query_vars['post_type'] == 'news')) {
                    //$query->set('posts_per_page', 12);
                    if ($query->query_vars['post_type'] == 'event') {
                        $query->set('meta_key', 'event_start');
                        $query->set('orderby', 'meta_value_num');
                        $query->set('order', 'desc');
                    } elseif ($query->query_vars['post_type'] == 'event') {
                        $query->set('orderby', 'date');
                        $query->set('order', 'desc');
                    }
                }
            }
        }
    }
}

$epcpt = new CustomPostType();