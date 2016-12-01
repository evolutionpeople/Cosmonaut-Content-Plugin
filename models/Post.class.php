<?php
namespace Cosmonaut\Models;
require_once 'PostInterface.interface.php';
require_once 'Social.trait.php';
/**
 * @property integer $ID Post ID
 * @property integer $menu_order Post menu order
 * @property string $post_title Post Title
 * @property string $post_name Post Name
 * @property string $post_date Post Date
 * @property string $post_status Post Status
 * @property string $post_type Post Type
 * @property string $content Post Content
 * @property string $permalink Post Permalink
 * @property string $excerpt Post excerpt
 * @property boolean $has_thumbnail If Post Has Thumbnail
 * @property array $taxonomies Array of possible taxonomies
 * @property array $fields Array of possible ACF
 * @property array|null $share Array of social share urls and params
 */
class Post implements PostInterface
{
    use Social;
    /**
     * @var bool
     * true if all details have been fetched
     */
    protected $allDetailsFetched = false;

    protected $excerpt_length = 140;

    /**
     * Check if the Post exists and Retrive common data.
     * @param int|\WP_Post $post Post ID or post object
     * @return Post|null Return null if Post does not exist
     */
    public function __construct($post)
    {
        $post = get_post($post);
        if (!$post) {
            return null;
        }
        $this->ID           = $post->ID;
        $this->menu_order   = intval($post->menu_order);
        $this->post_date    = $post->post_date;
        $this->post_status  = $post->post_status;
        $this->post_title   = $post->post_title;
        $this->post_name    = $post->post_name;
        $this->post_type    = get_post_type($this->ID);
        $this->content      = apply_filters('the_content',$post->post_content);
        $this->permalink    = get_the_permalink($this->ID);
        if(strlen($post->post_excerpt) > $this->excerpt_length)
        {
            $excerpt = preg_replace('/\s+?(\S+)?$/', '', substr($post->post_excerpt, 0, $this->excerpt_length+1)).' ...';
        }else{
            $excerpt = $post->post_excerpt;
        }
        $this->excerpt = apply_filters('the_content',$excerpt);
        $this->thumb_id = get_post_thumbnail_id($this->ID);
        if ($this->thumb_id) {
            $this->has_thumbnail = true;
        } else {
            $this->has_thumbnail = false;
        }

        $this->taxonomies = get_object_taxonomies($this->post_type);

        if(gettype($this->taxonomies) !== 'array')
        {
            $this->taxonomies = [];
        }
        $this->share = $this->socialNetworks($this);
        $this->fields = $this->get_object_fields();
    }

    /**
     * Retrive all Terms and ACF.
     * @return Post
     */
    public function withDetails()
    {
        if(!empty($this->ID))
        {
            if (!$this->allDetailsFetched) {
                foreach ($this->taxonomies as $tax) {
                    $terms = wp_get_post_terms($this->ID, $tax);

                    $termArray = [];
                    foreach ($terms as $term) {
                        $termArray[] = new Detail($term);
                    }

                    if(count($termArray) == 0){
                        $this->$tax = null;
                    }elseif(count($termArray) == 1){
                        $this->$tax = $termArray[0];
                    }else{
                        $this->$tax = $termArray;
                    }
                }
                if(function_exists('get_fields'))
                {
                    $fields = get_fields($this->ID);
                    if(count($fields) > 0 && $fields)
                    {
                        foreach ($fields as $key => $field) {
                            $fieldArray = new Detail($field);
                            $this->$key = $fieldArray;

                            if (method_exists($this, $key)) {
                                $this->$key();
                            }
                        }
                    }
                }
                return $this;
            }
        }

    }

    /**
     * Fetch information if not already and return value of property.
     * @param string $var Property name
     * @return mixed Value of property
     */
    public function __get($var)
    {
        if (isset($this->$var)) {
            return $this->$var;
        } else {
            $this->$var = null;

            if (in_array($var, $this->taxonomies)) {
                $this->getTerm($var);
            } elseif (in_array($var, $this->fields)) {
                $this->getField($var);
            }

            if (method_exists($this, $var)) {
                $this->$var();
            }

            return $this->$var;
        }
    }

    /**
     * @param string $format Format of Thumbnail, default 'full'
     * @param bool $full_info false = url, true = array info
     * @return array|string|null
     */
    public function thumbnail($format = 'full', $full_info = false)
    {
        if ($this->has_thumbnail) {
            $thumb = wp_get_attachment_image_src($this->thumb_id, $format);
            if ($full_info) {
                return $thumb;
            } else {
                return $thumb[0];
            }
        } else {
            return null;
        }
    }

    private function getTerm($tax)
    {
        $terms = wp_get_post_terms($this->ID, $tax);
        $termArray = [];
        foreach ($terms as $term) {
            $termArray[] = new Detail($term);
        }

        if(count($termArray) == 0){
            $this->$tax = null;
        }elseif(count($termArray) == 1){
            $this->$tax = $termArray[0];
        }else{
            $this->$tax = $termArray;
        }
    }

    private function getField($key)
    {
        $field = get_field($key, $this->ID);
        $fieldArray = new Detail($field);
        $this->$key = $fieldArray;
    }

    private function get_object_fields()
    {
        $fields = [];
        $meta = get_post_meta($this->ID);
        if (is_array($meta)) {
            foreach ($meta as $k => $v) {
                if ($k[0] === '_') continue;
                if (!array_key_exists("_{$k}", $meta)) continue;

                $fields[] = $k;
            }
        }

        return $fields;
    }
}