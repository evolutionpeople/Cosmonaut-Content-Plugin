<?php
namespace Cosmonaut;
/*
Plugin Name: Cosmonaut
Description: Definisce la struttura dei custom post type e della custom taxonomy, oltre ad eventuali regole di riscrittura
Version: 1.0.1
Author: EvolutionPeople
Author URI: http://www.evolutionpeople.it
*/

use Cosmonaut\Content\Creator;
use Cosmonaut\Content\Rewriter;

if(!defined( 'ABSPATH' ) ) exit;

if(!defined('BLP_PLUGIN_PATH'))
    define('BLP_PLUGIN_PATH', plugin_dir_path( __FILE__ ));



//require_once BLP_PLUGIN_PATH.'vendor/autoload.php';



class Cosmonaut {
    function initialize()
    {
        add_action('admin_init', [$this,'acf_exists']);
        add_action('init', [$this, 'init'], 5);
    }
    function acf_exists()
    {
        if(!is_plugin_active('advanced-custom-fields-pro/acf.php') && !is_plugin_active('advanced-custom-fields/acf.php'))
        {
            add_action( 'admin_notices', [$this,'acf_admin_notice']);
        }
    }
    function acf_admin_notice()
    {
        $class = 'notice notice-warning';
        $call_to_action_url = 'https://www.advancedcustomfields.com/';
        $message = __( 'Cosmonaut requires <strong>"Advanced Custom Fields"</strong> to rocks!', 'cosmonaut' );
        $call_to_action = 'Visit the <a target="_blank" href="'.$call_to_action_url.'">ACF page</a> for more informations.';
        printf( '<div class="%1$s"><p>%2$s</p><p>%3$s</p></div>', $class, $message,$call_to_action );
    }
    function init() {
        // bail early if a plugin called get_field early
        if( !did_action('plugins_loaded') ) return;

        require_once BLP_PLUGIN_PATH.'inc/Creator.class.php';
        new Creator();

        require_once BLP_PLUGIN_PATH.'inc/Rewriter.class.php';
        new Rewriter();

        spl_autoload_register([$this,'autoload']);
    }

    protected function autoload($classname)
    {
        $params = explode('\\',$classname);
        if(isset($params[1]))
        {
            switch ($params[1])
            {
                case 'Models':
                    require_once BLP_PLUGIN_PATH.'models/'.$params[2].'.class.php';
                    break;
            }
        }


    }
}

function cosmonaut() {
    global $cosmonaut;
    if( !isset($cosmonaut) ) {
        $cosmonaut = new Cosmonaut();
        $cosmonaut->initialize();
    }
    return $cosmonaut;
}
cosmonaut();

