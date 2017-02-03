<?php
namespace Cosmonaut\Model;


trait Social {

    protected $social;

    private function config()
    {
        return $this->social = include COSMO_PLUGIN_PATH . 'config/socialnetworks.php';
    }

    public function socialNetworks(PostInterface $post)
    {
        $this->config();
        foreach ($this->social as &$social)
        {
            $social['base_share_url'] = preg_replace('/{url}/i', $post->permalink, $social['base_share_url']);
            $social['base_share_url'] = preg_replace('/{url}/i', $post->permalink, $social['base_share_url']);
            if (isset($social['app_id']))
            {
                $social['base_share_url'] = preg_replace('/{app_id}/i', $social['app_id'], $social['base_share_url']);
            }

            $social = (object)$social;
        }
        return (object)$this->social;
    }

}