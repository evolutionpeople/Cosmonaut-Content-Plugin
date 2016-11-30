<?php
namespace EP;

class Validator
{
    private $errors = [];
    private $warnings = [];
    private static $required = [
        'wine' => [
            'wine_type' => [
                'type' => 'tax_term',
                'max' => 1,
                'level' => 'error',
                'message' => ''
            ],
            'wine_collection' => [
                'type' => 'tax_term',
                'max' => 1,
                'level' => 'error'
            ],
            'wine_area' => [
                'type' => 'tax_term',
                'max' => 1,
                'level' => 'error'
            ]
        ],
        'vintage' => [
            'post_title' => [
                'type'  => 'post_field',
                'regex' => '/^\d{4}$/',
                'level' => 'error',
                'regex_type' => '4 digits number'
            ],
            'wine_area' => [
                'type' => 'tax_term',
                'max' => 1,
                'level' => 'error'
            ]
        ],
        'collection' => [
            'wine_collection' => [
                'type' => 'tax_term',
                'max' => 1,
                'level' => 'error'
            ],
        ],
        'type' => [
            'wine_type' => [
                'type' => 'tax_term',
                'max' => 1,
                'level' => 'error'
            ]
        ],
        'variety' => [
            'wine_variety' => [
                'type' => 'tax_term',
                'max' => 1,
                'level' => 'error'
            ],
        ],
        'area' => [
            'wine_area' => [
                'type' => 'tax_term',
                'max' => 1,
                'level' => 'error'
            ],
        ],
        'news' => [
            'news_category' => [
                'type' => 'tax_term',
                'max' => 1,
                'level' => 'error'
            ],
        ]
    ];

    public function __construct()
    {
        add_action('admin_notices', [$this, 'errorHandler']);
        add_action('admin_notices', [$this, 'warningHandler']);
        add_action('save_post', [$this, 'validatePost'], 10, 3);
    }

    public function errorHandler()
    {
        global $post;
        global $pagenow;
        if (isset($pagenow) && $pagenow == 'post.php' && isset($post->ID) && isset($_COOKIE['df_admin_errors_' . $post->ID])) {
            $return = '<div class="notice notice-error">';
            $return .= '<p>' . $_COOKIE['df_admin_errors_' . $post->ID] . '</p>';
            $return .= '</div>';

            echo $return;
        }
    }

    public function warningHandler()
    {
        global $post;
        global $pagenow;
        if (isset($pagenow) && $pagenow == 'post.php' && isset($post->ID) && isset($_COOKIE['df_admin_warnings_' . $post->ID])) {
            $return = '<div class="notice notice-warning">';
            $return .= '<p>' . $_COOKIE['df_admin_warnings_' . $post->ID] . '</p>';
            $return .= '</div>';
            echo $return;
        }
    }

    public function validatePost($post_id, $post, $update)
    {
        if ($post && isset(self::$required[$post->post_type])) {
            switch ($post->post_type) {
                case 'wine':
                    $post_class = new Wine($post);
                    break;
                case 'vintage':
                    $post_class = new WeatherVintage($post);
                    break;
                case 'collection':
                    $post_class = new Collection($post);
                    break;
                case 'type':
                    $post_class = new Type($post);
                    break;
                case 'variety':
                    $post_class = new Variety($post);
                    break;
                case 'area':
                    $post_class = new Area($post);
                    break;

                default:
                    $post_class = new Post($post);
            }

            foreach (self::$required[$post->post_type] as $k => $v) {
                if ($v['type'] == 'tax_term') {
                    $taxonomy = get_taxonomy($k);
                }

                if(isset($v['regex']))
                {
                    if(!preg_match($v['regex'],$post_class->$k))
                    {
                        switch ($v['regex_type'])
                        {
                            case '4 digits number':
                                $type = __('un numero di 4 cifre','ep_validator','cosmonaut');
                                break;
                        }
                        $error = sprintf(__('Il campo <strong>%s</strong> deve essere %s ', 'ep_validator', 'cosmonaut'), $k,$type);
                    }

                }

                if (isset($v['max']) && count($post_class->$k) > $v['max']) {
                    $error = sprintf(__('Devi selezionare al massimo %d %s', 'ep_validator', 'cosmonaut'), $v['max'], $taxonomy->labels->singular_name);
                } elseif (!$post_class->$k) {
                    $error = sprintf(__('Devi selezionare almeno 1 %s', 'ep_validator', 'cosmonaut'), $taxonomy->labels->singular_name);
                }
                if (isset($error)) {
                    switch ($v['level']) {
                        case 'error':
                            $this->errors[] = $error;
                            if ($post->post_status == 'publish') {
                                global $wpdb;
                                $wpdb->update($wpdb->posts, array('post_status' => 'pending'), array('ID' => $post_id));
                                if (isset($_POST['post_status'])) {
                                    $_POST['post_status'] = 'pending';
                                }
                            }
                            break;
                        case 'warning':
                            $this->warnings[] = $error;
                            break;
                    }
                    $error = null;
                }
            }
            if (!empty($this->errors)) {
                $message = implode('<br/>', $this->errors);
                $message = '<h2>'.__('Errore','ep_validator','cosmonaut').'!</h2> ' . $message;
                setcookie('df_admin_errors_' . $post_id, $message, 0);
            } else {
                if (isset($_COOKIE['df_admin_errors_' . $post_id])) {
                    unset($_COOKIE['df_admin_errors_' . $post_id]);
                    setcookie('df_admin_errors_' . $post_id, '', time() - (15 * 60));
                }
            }
            if (!empty($this->warnings)) {
                $message = implode('<br/>', $this->warnings);
                $message = '<strong>'.__('Attenzione','ep_validator','cosmonaut').':</strong> ' . $message;
                setcookie('df_admin_warnings_' . $post_id, $message, 0);
            } else {
                if (isset($_COOKIE['df_admin_warnings_' . $post_id])) {
                    unset($_COOKIE['df_admin_warnings_' . $post_id]);
                    setcookie('df_admin_warnings_' . $post_id, '', time() - (15 * 60));
                }
            }
        }
    }
}

if (is_admin()) {
    $epvalidator = new Validator();
}