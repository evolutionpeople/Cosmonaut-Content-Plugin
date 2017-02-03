<?php
return [
    /*
     *  Defines the list of social networks and methods to share on them.
     *
     *  @param string $key must be the slug of social network ie. 'facebook'
     *  @param $lables also accept 'gender' param. Accepted values are 'm' or 'f'. Default 'm'
     *  @param $slug must be defined as plural slug
     *
    */
    'facebook' => [
        'icon'           => 'fa-facebook',
        'app_id'         => '123456789',
        'base_share_url' => 'https://www.facebook.com/dialog/share?app_id={APP_ID}&display=popup&href={URL}'
    ],
    'twitter'  => [
        'base_share_url' => 'https://twitter.com/intent/tweet?text={TEXT}&url={URL}'
    ]
];