<?php
namespace Cosmonaut\Model;

/**
 * Class Detail
 *
 * @property string       $is Type of Detail
 * @property array        $fields
 * @property string|null  $url url of term if is public queriable
 * @property boolean|null $not_linked term is not a link (in Production Area/variety)
 */
class Detail {

    //public $not_linked = false;
    /**
     * Detail constructor.
     *
     * @param mixed $obj
     */
    public function __construct($obj)
    {
        if ($obj instanceof \WP_Term)
        {
            $this->is = 'term';
            $this->term_id = $obj->term_id;
            $this->content = $obj->name;
            $this->slug = $obj->slug;
            $this->taxonomy = $obj->taxonomy;
            $this->description = $obj->description;
            $tax = get_taxonomy($obj->taxonomy);
            if ($tax->publicly_queryable && $tax->rewrite)
            {
                $this->url = get_term_link($obj);
            } else
            {
                $this->url = NULL;
            }
            $fields = get_fields($obj);
            if ($fields)
            {
                $this->fields = (object)$fields;
            }
        } else
        {
            //è un ACF
            $this->is = 'acf';
            $this->content = $obj;
        }
    }

    /**
     * Fetch information if not already and return value of property.
     *
     * @param string $var Property name
     * @return mixed Value of property
     */
    public function __get($var)
    {
        if (isset($this->$var))
        {
            return $this->$var;
        } else
        {
            $this->$var = NULL;

            if (method_exists($this, $var))
            {
                $this->$var();
            }

            return $this->$var;
        }
    }

    public function not_linked()
    {
        if ($this->is == 'term')
        {
            if (get_field('not_linked', $this->taxonomy . '_' . $this->term_id) == TRUE)
            {
                $this->not_linked = TRUE;
            } else
            {
                $this->not_linked = FALSE;
            }
        }
        return $this->not_linked;
    }

    public function __toString()
    {
        if (!$this->content)
        {
            if (defined('GLOBAL_DEBUG') && GLOBAL_DEBUG)
            {
                return 'CAMPO NON PRESENTE';
            } else
            {
                return '';
            }

        }
        return $this->content;
    }

}