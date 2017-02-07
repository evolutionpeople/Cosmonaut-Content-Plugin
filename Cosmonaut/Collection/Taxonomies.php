<?php

namespace Cosmonaut\Collection;
use Cosmonaut\Model\Detail;

class Taxonomies
{
    use MagicMethods;
    protected $collection = [];
    public function __construct($taxonomy,array $args = [])
    {
        $default = [
            'taxonomy' => $taxonomy,
            'hide_empty' => true
        ];

        $new_args = array_merge($default,$args);

        $terms = get_terms($new_args);

        foreach ($terms as $term)
        {
            $detail = new Detail($term);
            $this->collection[] = $detail;
        }
        return $this->collection;
    }
}