<?php
namespace Cosmonaut\Collection;
/**
 * Class Posts
 * @package Cosmonaut\Collection
 * @property array $collection
 */
class Posts
{
    use MagicMethods, ClassName;
    protected $collection = [];
    public function __construct($post_type,array $args = [])
    {
        if(is_array($post_type))
        {
            $post_type_args = $post_type;
        }else{
            $post_type_args = [$post_type];
        }
        $default = [
            'post_type' => $post_type_args,
            'post_status' => 'publish'
        ];

        $new_args = array_merge($default,$args);

        $query_posts = new \WP_Query($new_args);

        if($query_posts->found_posts)
        {
            foreach ($query_posts->posts as $item)
            {
                $classname = $this->className($item->post_type);
                $this->collection[] = new $classname($item);
            }
        }
        return $this->collection;
    }
}