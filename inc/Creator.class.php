<?php
/*
*  Cosmonaut content creator
*
* Creates the custom content for Cosmonaut
*
*  @class 		Creator
*  @package		Cosmonaut
*  @subpackage	Content
*/
namespace Cosmonaut\Content;
require_once 'Model.class.php';
class Creator
{
    protected $posts;
    protected $taxonomies;

    /**
     * Creator constructor.
     */
    public function __construct()
    {
        $this->posts = include COSMO_PLUGIN_PATH . 'config/posts.php';
        $this->taxonomies = include COSMO_PLUGIN_PATH . 'config/taxonomies.php';
        $this->create_custom_post_type();
        $this->create_custom_taxonomy();
    }

    /**
     * @param $singular
     * @param $plural
     * @param string $gender default value = 'm'
     * @return array
     */
    protected function create_post_type_label($singular, $plural, $gender = 'm')
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

    /**
     * @param $singular
     * @param $plural
     * @param string $gender default value = 'm'
     * @return array
     */
    protected function create_taxonomy_label($singular, $plural, $gender = 'm')
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

    /**
     * Create all post types according to posts.php config file
     */
    public function create_custom_post_type()
    {
        foreach ($this->posts as $name => $post_info) {
            $post_info['labels'] = $this->create_post_type_label(isset($post_info['labels']['singular']) ? $post_info['labels']['singular'] : $name, isset($post_info['labels']['plural']) ? $post_info['labels']['plural'] : $name, isset($post_info['labels']['gender']) ? $post_info['labels']['gender'] : 'm');
            register_post_type($name, $post_info);
            $model = new Model($post_info['labels']['singular_name']);
            $model->generate();
        }
    }

    /**
     * Create all taxonomies according to taxonomies.php config file
     */
    public function create_custom_taxonomy()
    {
        foreach ($this->taxonomies as $name => $tax_info) {
            $tax_info['labels'] = $this->create_taxonomy_label(isset($tax_info['labels']['singular']) ? $tax_info['labels']['singular'] : $name, isset($tax_info['labels']['plural']) ? $tax_info['labels']['plural'] : $name, isset($tax_info['labels']['gender']) ? $tax_info['labels']['gender'] : 'm');
            $post_type = isset($tax_info['post_type']) ? $tax_info['post_type'] : [];
            register_taxonomy($name, $post_type, $tax_info);

        }
    }
}