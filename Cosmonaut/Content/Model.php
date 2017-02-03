<?php
namespace Cosmonaut\Content;
class Model {

    protected $name;

    public function __construct($name)
    {
        $this->name = preg_replace('/(\W)/', '', $name);
    }

    function generate()
    {
        $model = COSMO_PLUGIN_PATH . 'Cosmonaut/Model/' . $this->name . '.php';
        if (!file_exists($model))
        {
            $template = COSMO_PLUGIN_PATH . 'config/model-base.txt';
            $current = file_get_contents($template);
            $current = preg_replace('/{NAME}/i', $this->name, $current);
            $current = preg_replace('/{DATE}/', date('Y-m-d H:i:s', time()), $current);
            if (file_put_contents($model, $current) === FALSE)
            {
                throw new \Exception('Unable to write a Cosmonaut model called: ' . $this->name);
            }
        }
        return $model;
    }
}