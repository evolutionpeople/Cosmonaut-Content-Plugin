<?php
namespace EP;
class CustomRoles{
    public function __construct()
    {
        //self::add_roles();
    }

    public static function map_capability($cap)
    {

    }

    public static function add_roles()
    {
        remove_role('df_basic');
        add_role( 'df_basic', __('Donnafugata (press,news,eventi)','cosmonaut'), array(
            'wpml_manage_string_translation' => true,
            'read'  => true
        ) );
        //'delete_others','delete_private',
        $methods = ['delete_published','publish','edit','edit_others','edit_private','edit_published','read_private'];
        $types = ['press'];

        $role_list = ['administrator','editor','df_basic'];
        foreach ($role_list as $role)
        {
            $c_role = get_role($role);
            $c_role->add_cap('upload_files');

            $c_role->add_cap('manage_press_terms');
            $c_role->add_cap('edit_press_terms');
            $c_role->add_cap('delete_press_terms');
            $c_role->add_cap('assign_press_terms');
            //$c_role->add_cap('manage_options');


            foreach ($types as $type)
            {
                foreach ($methods as $method)
                {
                    $c_role->add_cap( "{$method}_{$type}" );
                    $c_role->add_cap( "{$method}_{$type}s" );
                }
            }
        }
    }
}

//$customRoles = new CustomRoles();