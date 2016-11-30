<?php
namespace EP;

class CustomTaxonomies
{
    public function __construct()
    {
        add_action('init', [$this, 'init']);
    }

    public function init()
    {
        $this->registerTaxonomies();
        add_filter('term_link', [$this, 'taxonomy_term_url'], 10, 3);
    }

    public function taxonomy_term_url($url, $term, $taxonomy)
    {
        switch ($taxonomy) {
            case 'topic':
                $tax = get_taxonomy($term->taxonomy);
                $post_type = get_post_type_object($tax->object_type[0]);
                $post_type_slug = $post_type->rewrite['slug'];
                $post_type_slug = str_replace('/%topic%', '', $post_type_slug);
                $url = str_replace('%post_type%', $post_type_slug, $url);
                break;
            case 'event_category':
                $tax = get_taxonomy($term->taxonomy);
                $post_type = get_post_type_object($tax->object_type[0]);
                $slugArray = ['en' => 'events', 'it' => 'eventi'];
                $slug = isset($slugArray[ICL_LANGUAGE_CODE]) ? $slugArray[ICL_LANGUAGE_CODE] : $slugArray['it'];
                $url = str_replace('%post_type%', $slug, $url);
                break;
            case 'news_category':
                $tax = get_taxonomy($term->taxonomy);
                $post_type = get_post_type_object($tax->object_type[0]);
                $slugArray = ['en' => 'news', 'it' => 'news'];
                $slug = isset($slugArray[ICL_LANGUAGE_CODE]) ? $slugArray[ICL_LANGUAGE_CODE] : $slugArray['it'];
                $url = str_replace('%post_type%', $slug, $url);
            case 'press_release_category':
            case 'press_release_person':
            case 'press_release_event':
            case 'press_release_wine':
                $tax = get_taxonomy($term->taxonomy);
                $post_type = get_post_type_object($tax->object_type[0]);
                $url = str_replace('%post_type%', _x('comunicati-stampa','cpt','cosmonaut'), $url);
            case 'press_review_event':
            case 'press_review_person':
            case 'press_review_source':
            case 'press_review_author':
            case 'press_review_wine':
            case 'press_review_language':
                $tax = get_taxonomy($term->taxonomy);
                $post_type = get_post_type_object($tax->object_type[0]);
                $url = str_replace('%post_type%', _x('rassegna-stampa','cpt','cosmonaut'), $url);
            case 'gallery_type':
                $tax = get_taxonomy($term->taxonomy);
                $post_type = get_post_type_object($tax->object_type[0]);
                $url = str_replace('%post_type%', _x('foto-gallery','cpt','cosmonaut'), $url);
                break;
            case 'award_year':
            case 'award_publication':
            case 'award_award':
                $tax = get_taxonomy($term->taxonomy);
                $post_type = get_post_type_object($tax->object_type[0]);
                $url = str_replace('%post_type%', _x('premi-e-guide','cpt','cosmonaut'), $url);
                break;
            default:

                break;
        }
        return $url;

    }

    private function generateLabels($singular, $plural, $gender = 'm')
    {
        if ($gender == 'm') {
            $labels = [
                'all_items' => sprintf(_x('Tutti i %s', 'cpt', 'cosmonaut'), $plural),
                'view_item' => sprintf(_x('Visualizza il %s', 'cpt', 'cosmonaut'), $singular),
                'add_new_item' => sprintf(_x('Aggiungi un nuovo %s', 'cpt', 'cosmonaut'), $singular),
                'new_item_name' => sprintf(_x('Aggiungi un nuovo %s', 'cpt', 'cosmonaut'), $singular),
                'separate_items_with_commas' => sprintf(_x('Separa i %s con la virgola', 'cpt', 'cosmonaut'), $plural),
                'add_or_remove_items' => sprintf(_x('Aggiungi o rimuovi un %s', 'cpt', 'cosmonaut'), $plural),
                'choose_from_most_used' => sprintf(_x('Seleziona tra i %s più utlizzati', 'cpt', 'cosmonaut'), $plural),
                'not_found' => sprintf(_x('Nessun %s trovato.', 'cpt', 'cosmonaut'), $plural),
            ];
        } elseif ($gender == 'f') {
            $labels = [
                'all_items' => sprintf(_x('Tutte le %s', 'cpt', 'cosmonaut'), $plural),
                'view_item' => sprintf(_x('Visualizza la %s', 'cpt', 'cosmonaut'), $singular),
                'add_new_item' => sprintf(_x('Aggiungi una nuova %s', 'cpt', 'cosmonaut'), $singular),
                'new_item_name' => sprintf(_x('Aggiungi una nuova %s', 'cpt', 'cosmonaut'), $singular),
                'separate_items_with_commas' => sprintf(_x('Separa le %s con la virgola', 'cpt', 'cosmonaut'), $plural),
                'add_or_remove_items' => sprintf(_x('Aggiungi o rimuovi una %s', 'cpt', 'cosmonaut'), $plural),
                'choose_from_most_used' => sprintf(_x('Seleziona tra le %s più utlizzate', 'cpt', 'cosmonaut'), $plural),
                'not_found' => sprintf(_x('Nessuna %s trovata.', 'cpt', 'cosmonaut'), $plural),
            ];
        }
        $labels['name'] = ucfirst($plural);
        $labels['singular_name'] = ucfirst($singular);
        $labels['menu_name'] = ucfirst($plural);
        $labels['edit_item'] = sprintf(_x('Modifica %s', 'cpt', 'cosmonaut'), $singular);
        $labels['update_item'] = sprintf(_x('Aggiorna %s', 'cpt', 'cosmonaut'), $singular);
        $labels['search_items'] = sprintf(_x('Cerca %s', 'cpt', 'cosmonaut'), $plural);
        $labels['popular_items'] = sprintf(_x('%s popolari', 'cpt', 'cosmonaut'), ucfirst($plural));
        $labels['parent_item_colon'] = sprintf(_x('%s genitore', 'cpt', 'cosmonaut'), ucfirst($singular));
        $labels['parent_item'] = sprintf(_x('%s genitore', 'cpt', 'cosmonaut'), ucfirst($singular));
        return $labels;
    }

    public function registerTaxonomies()
    {
        register_taxonomy('wine_type', ['wine', 'type'],
            array(
                'labels' => $this->generateLabels(_x('tipologia', 'cpt', 'cosmonaut'), _x('tipologie', 'cpt', 'cosmonaut'), 'f'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => true,
                'query_var' => true,
                'capabilities' => array(
                    'manage_terms' => 'manage_categories',
                    'edit_terms' => 'manage_categories',
                    'delete_terms' => 'manage_categories',
                    'assign_terms' => 'edit_posts'
                )
            )
        );

        register_taxonomy('wine_collection', ['wine', 'collection'],
            array(
                'labels' => $this->generateLabels(_x('collezione', 'cpt', 'cosmonaut'), _x('collezioni', 'cpt', 'cosmonaut'), 'f'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => true,
                'query_var' => true,

                'capabilities' => array(
                    'manage_terms' => 'manage_categories',
                    'edit_terms' => 'manage_categories',
                    'delete_terms' => 'manage_categories',
                    'assign_terms' => 'edit_posts'
                )
            )
        );

        register_taxonomy('wine_denomination', ['wine'],
            array(
                'labels' => $this->generateLabels(_x('denominazione', 'cpt', 'cosmonaut'), _x('denominazioni', 'cpt', 'cosmonaut'), 'f'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => false,
                //'meta_box_cb' => null,
                'show_admin_column' => false,
                'hierarchical' => true,
                'rewrite' => false,
                'query_var' => false,
            )
        );

        register_taxonomy('wine_area', ['area', 'wine', 'vintage', 'variety'],
            array(
                'labels' => $this->generateLabels(_x('zona di produzione', 'cpt', 'cosmonaut'), _x('zone di produzione', 'cpt', 'cosmonaut'), 'f'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => false,
                'query_var' => false,
            )
        );

        register_taxonomy('wine_variety', ['variety'],
            array(
                'labels' => $this->generateLabels(_x('varietà', 'cpt', 'cosmonaut'), _x('varietà', 'cpt', 'cosmonaut'), 'f'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => false,
                'query_var' => false,
            )
        );


        $slugArray = ['en' => 'news-category', 'it' => 'categoria-news'];
        $slug = isset($slugArray[ICL_LANGUAGE_CODE]) ? $slugArray[ICL_LANGUAGE_CODE] : $slugArray['en'];

        register_taxonomy('news_category', ['news'],
            array(
                'labels' => $this->generateLabels(_x('categoria news', 'cpt', 'cosmonaut'), _x('categorie news', 'cpt', 'cosmonaut'), 'f'),
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
                    'slug' => '%post_type%/' . _x('categoria-news','cpt','cosmonaut'),
                    'with_front' => false
                ],
                'query_var' => true,
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );

        $slugArray = ['en' => 'events-category', 'it' => 'categoria-eventi'];
        $slug = isset($slugArray[ICL_LANGUAGE_CODE]) ? $slugArray[ICL_LANGUAGE_CODE] : $slugArray['en'];

        register_taxonomy('event_category', ['event'],
            array(
                'labels' => $this->generateLabels(_x('categoria eventi', 'cpt', 'cosmonaut'), _x('categorie eventi', 'cpt', 'cosmonaut'), 'f'),
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
                    'slug' => '%post_type%/' . _x('categoria-eventi','cpt','cosmonaut'),
                    'with_front' => false
                ],
                'query_var' => true,
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );

        register_taxonomy('artist', ['label'],
            array(
                'labels' => $this->generateLabels(_x('artista', 'cpt', 'cosmonaut'), _x('artisti', 'cpt', 'cosmonaut'), 'f'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => false,
                'query_var' => true,
            )
        );

        register_taxonomy('award_country', ['award'],
            array(
                'labels' => $this->generateLabels(_x('nazione', 'cpt', 'cosmonaut'), _x('nazioni', 'cpt', 'cosmonaut')),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => false,
                'query_var' => true,
            )
        );

        register_taxonomy('award_year', ['award'],
            array(
                'labels' => $this->generateLabels(_x('anno', 'cpt', 'cosmonaut'), _x('anni', 'cpt', 'cosmonaut')),
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
                    'slug' => '%post_type%/' . _x('anno-premio','cpt','cosmonaut'),
                ],
                'query_var' => _x('anno-premio','cpt','cosmonaut'),
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );

        //La traduzione del nome deve essere "awards" per evitare che vada in conflitto con il post type "award"
        register_taxonomy('award_award', ['award'],
            array(
                'labels' => $this->generateLabels(_x('premio', 'cpt', 'cosmonaut'), _x('premi', 'cpt', 'cosmonaut')),
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
                    'slug' => '%post_type%/' . _x('premio','cpt','cosmonaut'),
                ],
                'query_var' => _x('premio','cpt','cosmonaut'),
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );

        register_taxonomy('award_publication', ['award'],
            array(
                'labels' => $this->generateLabels(_x('pubblicazione', 'cpt', 'cosmonaut'), _x('pubblicazioni', 'cpt', 'cosmonaut')),
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
                    'slug' => '%post_type%/' . _x('pubblicazione','cpt','cosmonaut'),
                ],
                'query_var' => _x('pubblicazione','cpt','cosmonaut'),
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );



        /*
         * Press Area
         */


        /*COMUNICATI STAMPA*/

        register_taxonomy('press_release_event', ['press-release'],
            array(
                'labels' => $this->generateLabels(_x('evento', 'cpt', 'cosmonaut'), _x('eventi', 'cpt', 'cosmonaut'), 'm'),
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
                    'slug' => '%post_type%/' . _x('evento','cpt','cosmonaut'),
                ],
                'query_var' => _x('evento','cpt','cosmonaut'),
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );

        register_taxonomy('press_release_person', ['press-release'],
            array(
                'labels' => $this->generateLabels(_x('persona', 'cpt', 'cosmonaut'), _x('persone', 'cpt', 'cosmonaut'), 'f'),
                'query_var' => _x('persona','cpt','cosmonaut'),
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
                    'slug'  => '%post_type%/'._x('persona','cpt','cosmonaut'),
                ],
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );

        register_taxonomy('press_release_category', ['press-release'],
            array(
                'labels' => $this->generateLabels(_x('corporate', 'cpt', 'cosmonaut'), _x('corporate', 'cpt', 'cosmonaut'), 'f'),
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
                    'slug' => '%post_type%/' . _x('categoria','cpt','cosmonaut'),
                ],
                'query_var' => _x('categoria','cpt','cosmonaut'),
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );

        register_taxonomy('press_release_wine', ['press-release'],
            array(
                'labels' => $this->generateLabels(_x('vino', 'cpt', 'cosmonaut'), _x('vini', 'cpt', 'cosmonaut'), 'm'),
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
                    'slug' => '%post_type%/' . _x('vino','cpt','cosmonaut'),
                ],
                'query_var' => _x('vino','cpt','cosmonaut'),
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );

        /*RASSEGNA STAMPA*/

        register_taxonomy('press_review_event', ['press-review'],
            array(
                'labels' => $this->generateLabels(_x('evento', 'cpt', 'cosmonaut'), _x('eventi', 'cpt', 'cosmonaut'), 'm'),
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
                    'slug' => '%post_type%/' . _x('evento','cpt','cosmonaut'),
                ],
                'query_var' => _x('evento','cpt','cosmonaut'),
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );

        register_taxonomy('press_review_person', ['press-review'],
            array(
                'labels' => $this->generateLabels(_x('persona', 'cpt', 'cosmonaut'), _x('persone', 'cpt', 'cosmonaut'), 'f'),
                'query_var' => _x('persona','cpt','cosmonaut'),
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
                    'slug'  => '%post_type%/'._x('persona','cpt','cosmonaut')
                ],
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );

        register_taxonomy('press_review_source', ['press-review'],
            array(
                'labels' => $this->generateLabels(_x('testata', 'cpt', 'cosmonaut'), _x('testate', 'cpt', 'cosmonaut'), 'f'),
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
                    'slug' => '%post_type%/' . _x('testata','cpt','cosmonaut'),
                ],
                'query_var' => _x('testata','cpt','cosmonaut'),
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );

        register_taxonomy('press_review_author', ['press-review'],
            array(
                'labels' => $this->generateLabels(_x('autore', 'cpt', 'cosmonaut'), _x('autori', 'cpt', 'cosmonaut'), 'm'),
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
                    'slug' => '%post_type%/' . _x('autore','cpt','cosmonaut'),
                ],
                'query_var' => _x('autore','cpt','cosmonaut'),
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );

        register_taxonomy('press_review_wine', ['press-review'],
            array(
                'labels' => $this->generateLabels(_x('vino', 'cpt', 'cosmonaut'), _x('vini', 'cpt', 'cosmonaut'), 'm'),
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
                    'slug' => '%post_type%/' . _x('vino','cpt','cosmonaut'),
                ],
                'query_var' => _x('vino','cpt','cosmonaut'),
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );

        register_taxonomy('press_review_language', ['press-review'],
            array(
                'labels' => $this->generateLabels(_x('lingua', 'cpt', 'cosmonaut'), _x('lingue', 'cpt', 'cosmonaut'), 'f'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => true,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => [
                    'slug' => '%post_type%/' . _x('lingua','cpt','cosmonaut'),
                ],
                'query_var' => _x('lingua','cpt','cosmonaut'),
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );

        register_taxonomy('file_type', 'attachment',
            array(
                'labels' => $this->generateLabels(_x('tipologia file', 'cpt', 'cosmonaut'), _x('tiplogie file', 'cpt', 'cosmonaut'), 'f'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => false,
                'rewrite' => false,
                'query_var' => true,
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );

        register_taxonomy('gallery_type', 'gallery',
            array(
                'labels' => $this->generateLabels(_x('tipologia', 'cpt', 'cosmonaut'), _x('tiplogie', 'cpt', 'cosmonaut'), 'f'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => true,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => [
                    'slug' => '%post_type%/' . _x('tipologia','cpt','cosmonaut'),
                ],
                'query_var' => _x('tipologia','cpt','cosmonaut'),
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );

        //SEARCH ENHANCEMENT
        register_taxonomy('search_enhancement', ['wine', 'page', 'news', 'event', 'area', 'gallery', 'award', 'press-kit', 'event-kit', 'press-release', 'press-review'],
            array(
                'labels' => $this->generateLabels(_x('miglioramento ricerca', 'cpt', 'cosmonaut'), _x('miglioramenti ricerca', 'cpt', 'cosmonaut'), 'm'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => false,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => false,
                'query_var' => true,
                'capabilities' => array(
                    'manage_terms'  => 'manage_press_terms',
                    'edit_terms'    => 'edit_press_terms',
                    'delete_terms'  => 'delete_press_terms',
                    'assign_terms'  => 'assign_press_terms'
                )
            )
        );

        //Store Locator
        register_taxonomy('store_wine', ['store'],
            array(
                'labels' => $this->generateLabels(_x('vino', 'cpt', 'cosmonaut'), _x('vini', 'cpt', 'cosmonaut'), 'm'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => false,
                'query_var' => true,
            )
        );
        register_taxonomy('store_region', ['store'],
            array(
                'labels' => $this->generateLabels(_x('regione', 'cpt', 'cosmonaut'), _x('regioni', 'cpt', 'cosmonaut'), 'm'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => false,
                'query_var' => true,
            )
        );
        register_taxonomy('store_district', ['store'],
            array(
                'labels' => $this->generateLabels(_x('provincia', 'cpt', 'cosmonaut'), _x('provincie', 'cpt', 'cosmonaut'), 'm'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => false,
                'query_var' => true,
            )
        );
        register_taxonomy('store_type', ['store'],
            array(
                'labels' => $this->generateLabels(_x('tipologia', 'cpt', 'cosmonaut'), _x('tiplogie', 'cpt', 'cosmonaut'), 'm'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => false,
                'query_var' => true,
            )
        );


        register_taxonomy('agent_region', ['agent'],
            array(
                'labels' => $this->generateLabels(_x('regione', 'cpt', 'cosmonaut'), _x('regioni', 'cpt', 'cosmonaut'), 'm'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => false,
                'query_var' => true,
            )
        );

        register_taxonomy('agent_district', ['agent'],
            array(
                'labels' => $this->generateLabels(_x('provincia', 'cpt', 'cosmonaut'), _x('provincie', 'cpt', 'cosmonaut'), 'm'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => false,
                'query_var' => true,
            )
        );



        register_taxonomy('importer_country', ['importer'],
            array(
                'labels' => $this->generateLabels(_x('paese', 'cpt', 'cosmonaut'), _x('paesi', 'cpt', 'cosmonaut'), 'm'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => false,
                'query_var' => true,
            )
        );
        register_taxonomy('importer_continent', ['importer'],
            array(
                'labels' => $this->generateLabels(_x('continente', 'cpt', 'cosmonaut'), _x('continenti', 'cpt', 'cosmonaut'), 'm'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => false,
                'query_var' => true,
            )
        );
        register_taxonomy('importer_area', ['importer'],
            array(
                'labels' => $this->generateLabels(_x('area', 'cpt', 'cosmonaut'), _x('aree', 'cpt', 'cosmonaut'), 'm'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => false,
                'query_var' => true,
            )
        );
        register_taxonomy('ecommerce_country', ['ecommerce'],
            array(
                'labels' => $this->generateLabels(_x('paese', 'cpt', 'cosmonaut'), _x('paesi', 'cpt', 'cosmonaut'), 'm'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => false,
                'query_var' => true,
            )
        );
        register_taxonomy('ecommerce_continent', ['ecommerce'],
            array(
                'labels' => $this->generateLabels(_x('continente', 'cpt', 'cosmonaut'), _x('continenti', 'cpt', 'cosmonaut'), 'm'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,
                'meta_box_cb' => null,
                'show_admin_column' => true,
                'hierarchical' => true,
                'rewrite' => false,
                'query_var' => true,
            )
        );
    }
}

$epctx = new CustomTaxonomies();