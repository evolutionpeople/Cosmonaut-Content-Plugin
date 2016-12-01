<?php
namespace Cosmonaut\Content;
class Rewriter
{
    protected $add_rules;
    protected $remove_rules;
    protected $remove_rewrites;

    public function __construct()
    {
        $config = include COSMO_PLUGIN_PATH . 'config/rewrites.php';
        $this->add_rules        = $config['add_rules'];
        $this->remove_rules     = implode('|',$config['remove_rules']);
        $this->remove_rewrites  = implode('|',$config['remove_rewrites']);

        add_filter('rewrite_rules_array', [$this,'clean'], 1);

        $this->create_custom_rules();

    }

    public function create_custom_rules()
    {
        foreach ($this->add_rules as $rule)
        {
            add_rewrite_rule($rule[0],$rule[1],isset($rule[2])?$rule[2]:'top');
        }
    }

    public function clean($rules)
    {

        foreach ($rules as $rule => $rewrite) {
            if ( preg_match('/'.$this->remove_rules.'/',$rule) ) {
                unset($rules[$rule]);
            }
            if(preg_match('/'.$this->remove_rewrites.'/',$rewrite))
            {
                unset($rules[$rule]);
            }
        }
        return $rules;
    }
}