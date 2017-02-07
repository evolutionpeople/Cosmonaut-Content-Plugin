<?php
namespace Cosmonaut\Collection;


use Cosmonaut\Model\Post;

trait ClassName {

    protected function className($post_type)
    {
        switch ($post_type)
        {
            default:
                return Post::class;
        }
    }
}