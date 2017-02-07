<?php
/**
 * Created by PhpStorm.
 * User: roberto
 * Date: 07/02/17
 * Time: 15:28
 */

namespace Cosmonaut\Collection;


trait MagicMethods
{
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
}